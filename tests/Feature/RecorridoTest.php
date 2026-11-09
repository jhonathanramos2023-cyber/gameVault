<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Plataforma;
use App\Models\User;
use App\Models\Videojuego;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RecorridoTest extends TestCase
{
    use RefreshDatabase;

    private array $rutasGet = [
        'catalogo.index',
        'dashboard',
        'videojuegos.index',
        'videojuegos.create',
        'categorias.index',
        'categorias.create',
        'plataformas.index',
        'plataformas.create',
        'usuarios.index',
        'usuarios.create',
    ];

    public function test_visitante_accede_a_rutas_publicas()
    {
        $this->get(route('catalogo.index'))->assertOk();
    }

    public function test_visitante_no_accede_a_rutas_protegidas()
    {
        foreach ($this->rutasGet as $ruta) {
            if ($ruta === 'catalogo.index') continue;
            $this->get(route($ruta))->assertRedirectToRoute('login');
        }
    }

    public function test_colaborador_accede_a_sus_rutas()
    {
        $colaborador = User::factory()->create(['rol' => 'colaborador']);
        $videojuego = Videojuego::factory()->create([
            'user_id' => $colaborador->id,
            'estado' => 'borrador',
        ]);

        $this->actingAs($colaborador);
        $this->get(route('dashboard'))->assertOk();
        $this->get(route('videojuegos.index'))->assertOk();
        $this->get(route('videojuegos.create'))->assertOk();
        $this->get(route('videojuegos.edit', $videojuego))->assertOk();
        $this->get(route('videojuegos.show', $videojuego))->assertOk();

        $this->get(route('categorias.index'))->assertForbidden();
        $this->get(route('plataformas.index'))->assertForbidden();
        $this->get(route('usuarios.index'))->assertForbidden();
    }

    public function test_administrador_accede_a_todas_las_rutas()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $videojuego = Videojuego::factory()->create(['estado' => 'borrador']);

        $this->actingAs($admin);
        $this->get(route('dashboard'))->assertOk();
        $this->get(route('videojuegos.index'))->assertOk();
        $this->get(route('videojuegos.create'))->assertOk();
        $this->get(route('videojuegos.edit', $videojuego))->assertOk();
        $this->get(route('videojuegos.show', $videojuego))->assertOk();
        $this->get(route('categorias.index'))->assertOk();
        $this->get(route('plataformas.index'))->assertOk();
        $this->get(route('usuarios.index'))->assertOk();
    }

    public function test_flujo_editorial_completo_colaborador()
    {
        Storage::fake('local');
        $colaborador = User::factory()->create(['rol' => 'colaborador']);
        $categoria = Categoria::factory()->create();
        $plataforma = Plataforma::factory()->create();

        $this->actingAs($colaborador)
            ->post(route('videojuegos.store'), [
                'titulo' => 'Ficha de prueba',
                'descripcion' => 'Descripción',
                'formato' => 'preservación digital',
                'tipo_proyecto' => 'preservación digital',
                'advertencia' => 'Advertencia',
                'categoria_id' => $categoria->id,
                'plataformas' => [$plataforma->id],
                'imagenes' => [\Illuminate\Http\UploadedFile::fake()->image('test.png')],
            ])->assertRedirect();

        $videojuego = Videojuego::first();
        $this->assertSame('borrador', $videojuego->estado);

        $this->actingAs($colaborador)
            ->post(route('videojuegos.enviar-revision', $videojuego))
            ->assertSessionHas('success');

        $this->assertSame('en_revision', $videojuego->fresh()->estado);

        $this->actingAs($colaborador)
            ->patch(route('videojuegos.publicar', $videojuego))
            ->assertForbidden();

        $this->actingAs($colaborador)
            ->patch(route('videojuegos.devolver', $videojuego))
            ->assertForbidden();
    }

    public function test_flujo_editorial_completo_administrador()
    {
        Storage::fake('local');
        $admin = User::factory()->create(['rol' => 'administrador']);
        $categoria = Categoria::factory()->create();
        $plataforma = Plataforma::factory()->create();

        $this->actingAs($admin)
            ->post(route('videojuegos.store'), [
                'titulo' => 'Ficha admin',
                'descripcion' => 'Descripción',
                'formato' => 'preservación digital',
                'tipo_proyecto' => 'preservación digital',
                'advertencia' => 'Advertencia',
                'categoria_id' => $categoria->id,
                'plataformas' => [$plataforma->id],
                'imagenes' => [\Illuminate\Http\UploadedFile::fake()->image('test.png')],
            ])->assertRedirect();

        $videojuego = Videojuego::first();
        $this->assertSame('borrador', $videojuego->estado);

        $this->actingAs($admin)
            ->post(route('videojuegos.enviar-revision', $videojuego))
            ->assertSessionHas('success');

        $this->assertSame('en_revision', $videojuego->fresh()->estado);

        $this->actingAs($admin)
            ->patch(route('videojuegos.publicar', $videojuego))
            ->assertSessionHas('success');

        $this->assertSame('publicado', $videojuego->fresh()->estado);

        $this->actingAs($admin)
            ->patch(route('videojuegos.despublicar', $videojuego))
            ->assertSessionHas('success');

        $this->assertSame('borrador', $videojuego->fresh()->estado);

        $this->actingAs($admin)
            ->patch(route('videojuegos.retirar', $videojuego))
            ->assertSessionHas('success');

        $this->assertSame('retirado', $videojuego->fresh()->estado);
    }

    public function test_transiciones_invalidas_no_cambian_estado()
    {
        $admin = User::factory()->create(['rol' => 'administrador']);
        $colaborador = User::factory()->create(['rol' => 'colaborador']);

        // Borrador no puede publicar directamente
        $videojuego = Videojuego::factory()->create(['estado' => 'borrador']);
        $this->actingAs($admin)
            ->patch(route('videojuegos.publicar', $videojuego))
            ->assertForbidden();
        $this->assertSame('borrador', $videojuego->fresh()->estado);

        // Publicado no puede enviar a revisión
        $videojuego->update(['estado' => 'publicado']);
        $this->actingAs($admin)
            ->post(route('videojuegos.enviar-revision', $videojuego))
            ->assertForbidden();
        $this->assertSame('publicado', $videojuego->fresh()->estado);

        // En revisión no puede enviar a revisión de nuevo
        $videojuego->update(['estado' => 'en_revision']);
        $this->actingAs($admin)
            ->post(route('videojuegos.enviar-revision', $videojuego))
            ->assertForbidden();
        $this->assertSame('en_revision', $videojuego->fresh()->estado);

        // Retirado no puede hacer transiciones
        $videojuego->update(['estado' => 'retirado']);
        $this->actingAs($admin)
            ->post(route('videojuegos.enviar-revision', $videojuego))
            ->assertForbidden();
        $this->actingAs($admin)
            ->patch(route('videojuegos.publicar', $videojuego))
            ->assertForbidden();
        $this->actingAs($admin)
            ->patch(route('videojuegos.devolver', $videojuego))
            ->assertForbidden();
        $this->actingAs($admin)
            ->patch(route('videojuegos.despublicar', $videojuego))
            ->assertForbidden();
        $this->assertSame('retirado', $videojuego->fresh()->estado);

        // Colaborador no puede publicar
        $videojuego->update(['estado' => 'en_revision', 'user_id' => $colaborador->id]);
        $this->actingAs($colaborador)
            ->patch(route('videojuegos.publicar', $videojuego))
            ->assertForbidden();
        $this->assertSame('en_revision', $videojuego->fresh()->estado);

        // Colaborador no puede devolver
        $this->actingAs($colaborador)
            ->patch(route('videojuegos.devolver', $videojuego))
            ->assertForbidden();
        $this->assertSame('en_revision', $videojuego->fresh()->estado);
    }

    public function test_usuario_inactivo_no_inicia_sesion()
    {
        $usuario = User::factory()->create(['activo' => false]);

        $this->post(route('login'), [
            'email' => $usuario->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
