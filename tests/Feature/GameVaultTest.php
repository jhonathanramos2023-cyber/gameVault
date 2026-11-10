<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\ImagenVideojuego;
use App\Models\Plataforma;
use App\Models\Referencia;
use App\Models\User;
use App\Models\Videojuego;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class GameVaultTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_sign_in(): void
    {
        $admin = User::factory()->create(['rol' => 'administrador']);

        $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password',
        ])->assertRedirect(route('dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_collaborator_creates_draft_and_sends_it_for_review(): void
    {
        Storage::fake('local');
        $colaborador = User::factory()->create(['rol' => 'colaborador']);
        $categoria = Categoria::factory()->create();
        $plataforma = Plataforma::factory()->create();

        $this->actingAs($colaborador)->post(route('videojuegos.store'), $this->datosVideojuego($categoria, $plataforma))
            ->assertRedirect();

        $videojuego = Videojuego::firstOrFail();
        $this->assertSame('borrador', $videojuego->estado);
        $this->assertSame($colaborador->id, $videojuego->user_id);
        $this->assertDatabaseHas('imagenes_videojuego', ['videojuego_id' => $videojuego->id]);

        $this->post(route('videojuegos.enviar-revision', $videojuego))
            ->assertSessionHas('success');

        $this->assertSame('en_revision', $videojuego->fresh()->estado);
    }

    public function test_administrator_publishes_a_reviewed_record_and_visitors_can_find_it(): void
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $categoria = Categoria::factory()->create();
        $plataforma = Plataforma::factory()->create();
        $videojuego = Videojuego::factory()->create([
            'titulo' => 'Proyecto ScummVM',
            'slug' => 'proyecto-scummvm',
            'estado' => 'en_revision',
            'categoria_id' => $categoria->id,
        ]);
        $videojuego->plataformas()->attach($plataforma);

        $this->actingAs($admin)->patch(route('videojuegos.publicar', $videojuego))
            ->assertSessionHas('success');
        $this->assertSame('publicado', $videojuego->fresh()->estado);

        auth()->logout();
        $this->get(route('catalogo.index', ['q' => 'SCUMM', 'categoria_id' => $categoria->id, 'plataforma_id' => $plataforma->id]))
            ->assertOk()
            ->assertSee('Proyecto ScummVM');
        $this->get(route('catalogo.show', $videojuego->slug))
            ->assertOk()
            ->assertSee('Publicado')
            ->assertSee('referencias externas', false);
    }

    public function test_invalid_images_and_malformed_reference_urls_are_rejected(): void
    {
        $colaborador = User::factory()->create(['rol' => 'colaborador']);
        $categoria = Categoria::factory()->create();
        $plataforma = Plataforma::factory()->create();
        $payload = $this->datosVideojuego($categoria, $plataforma);
        $payload['imagenes'] = [UploadedFile::fake()->create('archivo.exe', 200, 'application/octet-stream')];

        $this->actingAs($colaborador)
            ->post(route('videojuegos.store'), $payload)
            ->assertSessionHasErrors('imagenes.0');

        $videojuego = Videojuego::factory()->create([
            'user_id' => $colaborador->id,
            'categoria_id' => $categoria->id,
            'estado' => 'borrador',
        ]);
        $this->post(route('referencias.store', $videojuego), [
            'titulo' => 'Enlace inválido',
            'url' => 'javascript:alert(1)',
            'tipo_fuente' => 'Sitio oficial',
        ])->assertSessionHasErrors('url');
    }

    public function test_valid_image_is_accepted_and_size_limit_is_enforced(): void
    {
        Storage::fake('local');
        $colaborador = User::factory()->create(['rol' => 'colaborador']);
        $categoria = Categoria::factory()->create();
        $plataforma = Plataforma::factory()->create();
        $payload = $this->datosVideojuego($categoria, $plataforma);

        $this->actingAs($colaborador)->post(route('videojuegos.store'), $payload)->assertRedirect();
        $this->assertDatabaseCount('imagenes_videojuego', 1);

        $videojuego = Videojuego::firstOrFail();
        $this->post(route('imagenes.store', $videojuego), [
            'imagenes' => [UploadedFile::fake()->image('demasiado-grande.png')->size(2049)],
        ])->assertSessionHasErrors('imagenes.0');
    }

    public function test_inactive_user_cannot_sign_in(): void
    {
        $usuario = User::factory()->create(['activo' => false]);

        $this->post(route('login'), [
            'email' => $usuario->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_deleting_game_removes_its_images_files_and_references(): void
    {
        Storage::fake('local');
        $admin = User::factory()->create(['rol' => 'administrador']);
        $categoria = Categoria::factory()->create();
        $videojuego = Videojuego::factory()->create(['categoria_id' => $categoria->id]);
        $ruta = 'videojuegos/'.$videojuego->id.'/captura.png';
        Storage::disk('local')->put($ruta, 'imagen de prueba');
        $imagen = ImagenVideojuego::create([
            'videojuego_id' => $videojuego->id,
            'ruta' => $ruta,
            'texto_alternativo' => 'Captura de prueba',
            'orden' => 0,
        ]);
        $referencia = Referencia::factory()->create(['videojuego_id' => $videojuego->id]);

        $this->actingAs($admin)->delete(route('videojuegos.destroy', $videojuego))
            ->assertRedirect(route('videojuegos.index'));

        $this->assertDatabaseMissing('imagenes_videojuego', ['id' => $imagen->id]);
        $this->assertDatabaseMissing('referencias', ['id' => $referencia->id]);
        Storage::disk('local')->assertMissing($ruta);
    }

    public function test_collaborator_cannot_edit_another_users_record_or_open_admin_pages(): void
    {
        $colaborador = User::factory()->create(['rol' => 'colaborador']);
        $categoria = Categoria::factory()->create();
        $otro = User::factory()->create(['rol' => 'colaborador']);
        $videojuego = Videojuego::factory()->create([
            'user_id' => $otro->id,
            'categoria_id' => $categoria->id,
        ]);

        $this->actingAs($colaborador)
            ->get(route('videojuegos.edit', $videojuego))
            ->assertForbidden();
        $this->get(route('usuarios.index'))->assertForbidden();
    }

    public function test_search_is_case_insensitive(): void
    {
        $categoria = Categoria::factory()->create();
        Videojuego::factory()->create([
            'titulo' => 'Proyecto ScummVM',
            'estado' => 'publicado',
            'categoria_id' => $categoria->id,
        ]);

        $this->get(route('catalogo.index', ['q' => 'scummvm']))
            ->assertOk()
            ->assertSee('Proyecto ScummVM');
        $this->get(route('catalogo.index', ['q' => 'SCUMMVM']))
            ->assertOk()
            ->assertSee('Proyecto ScummVM');
        $this->get(route('catalogo.index', ['q' => 'ScUmMvM']))
            ->assertOk()
            ->assertSee('Proyecto ScummVM');
    }

    public function test_collaborator_cannot_delete_images_or_references_from_others_records(): void
    {
        Storage::fake('local');
        $colaborador = User::factory()->create(['rol' => 'colaborador']);
        $otro = User::factory()->create(['rol' => 'colaborador']);
        $categoria = Categoria::factory()->create();
        $videojuegoAjeno = Videojuego::factory()->create([
            'user_id' => $otro->id,
            'categoria_id' => $categoria->id,
            'estado' => 'borrador',
        ]);
        $imagen = ImagenVideojuego::factory()->create(['videojuego_id' => $videojuegoAjeno->id]);
        $referencia = Referencia::factory()->create(['videojuego_id' => $videojuegoAjeno->id]);

        $this->actingAs($colaborador)
            ->delete(route('imagenes.destroy', $imagen))
            ->assertForbidden();

        $this->actingAs($colaborador)
            ->delete(route('referencias.destroy', [$videojuegoAjeno, $referencia]))
            ->assertForbidden();
    }

    public function test_collaborator_cannot_delete_records_own_or_others(): void
    {
        $colaborador = User::factory()->create(['rol' => 'colaborador']);
        $categoria = Categoria::factory()->create();
        $videojuegoPropio = Videojuego::factory()->create([
            'user_id' => $colaborador->id,
            'categoria_id' => $categoria->id,
        ]);
        $otro = User::factory()->create(['rol' => 'colaborador']);
        $videojuegoAjeno = Videojuego::factory()->create([
            'user_id' => $otro->id,
            'categoria_id' => $categoria->id,
        ]);

        $this->actingAs($colaborador)
            ->delete(route('videojuegos.destroy', $videojuegoPropio))
            ->assertForbidden();

        $this->actingAs($colaborador)
            ->delete(route('videojuegos.destroy', $videojuegoAjeno))
            ->assertForbidden();
    }

    public function test_collaborator_panel_list_shows_only_own_records(): void
    {
        $colaborador = User::factory()->create(['rol' => 'colaborador']);
        $categoria = Categoria::factory()->create();
        $videojuegoPropio = Videojuego::factory()->create([
            'user_id' => $colaborador->id,
            'categoria_id' => $categoria->id,
            'titulo' => 'Ficha propia',
        ]);
        $otro = User::factory()->create(['rol' => 'colaborador']);
        Videojuego::factory()->create([
            'user_id' => $otro->id,
            'categoria_id' => $categoria->id,
            'titulo' => 'Ficha ajena',
        ]);

        $this->actingAs($colaborador)
            ->get(route('videojuegos.index'))
            ->assertOk()
            ->assertSee('Ficha propia')
            ->assertDontSee('Ficha ajena');
    }

    public function test_image_and_reference_routes_verify_resource_belongs_to_authorized_videojuego(): void
    {
        Storage::fake('local');
        $colaborador = User::factory()->create(['rol' => 'colaborador']);
        $categoria = Categoria::factory()->create();
        $videojuegoPropio = Videojuego::factory()->create([
            'user_id' => $colaborador->id,
            'categoria_id' => $categoria->id,
            'estado' => 'borrador',
        ]);
        $otro = User::factory()->create(['rol' => 'colaborador']);
        $videojuegoAjeno = Videojuego::factory()->create([
            'user_id' => $otro->id,
            'categoria_id' => $categoria->id,
        ]);
        $imagenAjena = ImagenVideojuego::factory()->create(['videojuego_id' => $videojuegoAjeno->id]);
        $referenciaAjena = Referencia::factory()->create(['videojuego_id' => $videojuegoAjeno->id]);

        $this->actingAs($colaborador)
            ->delete(route('imagenes.destroy', $imagenAjena))
            ->assertForbidden();

        $this->actingAs($colaborador)
            ->delete(route('referencias.destroy', [$videojuegoPropio, $referenciaAjena]))
            ->assertNotFound();
    }

    public function test_image_upload_succeeds_with_storage_fake(): void
    {
        Storage::fake('local');
        $colaborador = User::factory()->create(['rol' => 'colaborador']);
        $categoria = Categoria::factory()->create();
        $plataforma = Plataforma::factory()->create();
        $payload = $this->datosVideojuego($categoria, $plataforma);

        $this->actingAs($colaborador)
            ->post(route('videojuegos.store'), $payload)
            ->assertRedirect();

        $videojuego = Videojuego::firstOrFail();
        $this->assertDatabaseHas('imagenes_videojuego', ['videojuego_id' => $videojuego->id]);
        Storage::disk('local')->assertExists('videojuegos/'.$videojuego->id);
    }

    public function test_database_seeder_uses_default_email_when_not_provided(): void
    {
        // En entorno de test (no producción), usa el valor por defecto si ADMIN_EMAIL no está definido
        putenv('ADMIN_PASSWORD=test_password');
        putenv('ADMIN_EMAIL=');

        $seeder = new \Database\Seeders\DatabaseSeeder();
        $seeder->run();

        $this->assertDatabaseHas('users', [
            'email' => 'admin@gamevault.local',
            'rol' => 'administrador',
        ]);
    }

    private function datosVideojuego(Categoria $categoria, Plataforma $plataforma): array
    {
        return [
            'titulo' => 'Ficha de prueba',
            'descripcion' => 'Descripción documental del proyecto.',
            'informacion_tecnica' => 'Información técnica de prueba.',
            'requisitos_conocidos' => 'Requisitos documentados.',
            'formato' => 'preservación digital',
            'tipo_proyecto' => 'preservación digital',
            'advertencia' => 'Referencia informativa; no hay archivos alojados.',
            'fecha_lanzamiento' => null,
            'categoria_id' => $categoria->id,
            'plataformas' => [$plataforma->id],
            'imagenes' => [UploadedFile::fake()->image('portada.png')],
            'textos_alternativos' => ['Portada de prueba'],
        ];
    }
}
