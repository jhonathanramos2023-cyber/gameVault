<?php

namespace Database\Seeders;

use App\Models\Categoria;
use App\Models\ImagenVideojuego;
use App\Models\Plataforma;
use App\Models\Referencia;
use App\Models\User;
use App\Models\Videojuego;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    protected function isProduction(): bool
    {
        return app()->environment('production');
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminPassword = env('ADMIN_PASSWORD');
        $adminEmail = env('ADMIN_EMAIL');

        if ($this->isProduction()) {
            if (blank($adminEmail)) {
                throw new RuntimeException('Define ADMIN_EMAIL en producción antes de sembrar el administrador.');
            }
            if (blank($adminPassword)) {
                throw new RuntimeException('Define ADMIN_PASSWORD en producción antes de sembrar el administrador.');
            }
        }

        $admin = User::updateOrCreate(
            ['email' => $adminEmail ?: 'admin@gamevault.local'],
            [
                'name' => 'Administración GameVault',
                'password' => $adminPassword ?: 'GameVaultAdmin2026!',
                'rol' => 'administrador',
                'activo' => true,
            ]
        );

        $seedDemo = env('GAMEVAULT_SEED_DEMO', ! app()->environment('production'));
        $colaboradores = collect();
        if ($seedDemo) {
            foreach ([
                ['nombre' => 'Colaborador Uno', 'correo' => 'colaborador1@gamevault.local'],
                ['nombre' => 'Colaborador Dos', 'correo' => 'colaborador2@gamevault.local'],
            ] as $datos) {
                $colaboradores->push(User::updateOrCreate(
                    ['email' => $datos['correo']],
                    ['name' => $datos['nombre'], 'password' => 'GameVaultLocal2026!', 'rol' => 'colaborador', 'activo' => true]
                ));
            }
        } else {
            $colaboradores = User::where('rol', 'colaborador')->limit(2)->get();
        }

        $categorias = [];
        foreach ([
            ['Emulación y compatibilidad', 'Emuladores y herramientas de compatibilidad documentadas.'],
            ['Motores y adaptación', 'Motores abiertos, ports y adaptaciones comunitarias.'],
            ['Preservación digital', 'Iniciativas que conservan código, documentación y contexto histórico.'],
            ['Estrategia y simulación', 'Proyectos abiertos de estrategia, simulación y gestión.'],
            ['Accesibilidad y comunidad', 'Proyectos con desarrollo comunitario abierto.'],
            ['Ingeniería inversa', 'Investigación pública y reconstrucción de software.'],
            ['Herramientas de desarrollo', 'Herramientas abiertas que apoyan proyectos de videojuegos.'],
            ['Otros proyectos abiertos', 'Otras iniciativas legales con código abierto.'],
        ] as [$nombre, $descripcion]) {
            $categoria = Categoria::firstOrCreate(['slug' => Str::slug($nombre)], ['nombre' => $nombre, 'descripcion' => $descripcion]);
            $categorias[$nombre] = $categoria;
        }

        $plataformas = [];
        foreach ([
            ['Linux', 'Escritorio'], ['Windows', 'Escritorio'], ['macOS', 'Escritorio'],
            ['BSD', 'Escritorio'], ['Android', 'Móvil'], ['Web / WASM', 'Web'],
            ['Raspberry Pi', 'Hardware abierto'], ['Multiplataforma', 'Varios'],
        ] as [$nombre, $tipo]) {
            $plataformas[$nombre] = Plataforma::firstOrCreate(['nombre' => $nombre], ['tipo' => $tipo]);
        }

        $proyectos = [
            ['ScummVM', 'https://www.scummvm.org/', 'Emulación y compatibilidad', 'emulación', 'emulación', ['Windows', 'Linux', 'macOS', 'Android'], 'Proyecto de código abierto que preserva y ejecuta motores de aventuras gráficas y juegos narrativos compatibles.'],
            ['OpenMW', 'https://openmw.org/', 'Motores y adaptación', 'port o adaptación', 'port o adaptación', ['Windows', 'Linux', 'macOS'], 'Motor de juego de rol de mundo abierto, reconstruido como proyecto de software libre.'],
            ['OpenRA', 'https://www.openra.net/', 'Estrategia y simulación', 'reconstrucción/ingeniería inversa', 'reconstrucción/ingeniería inversa', ['Windows', 'Linux', 'macOS'], 'Proyecto abierto de estrategia en tiempo real y reconstrucción de mecánicas clásicas.'],
            ['OpenTTD', 'https://www.openttd.org/', 'Estrategia y simulación', 'preservación digital', 'preservación digital', ['Windows', 'Linux', 'macOS', 'Android'], 'Simulador de transporte de código abierto con desarrollo comunitario y documentación pública.'],
            ['GZDoom', 'https://zdoom.org/', 'Motores y adaptación', 'port o adaptación', 'port o adaptación', ['Windows', 'Linux', 'macOS'], 'Motor abierto para proyectos compatibles con Doom; las fichas no enlazan contenido de juego.'],
            ['DOSBox', 'https://www.dosbox.com/', 'Emulación y compatibilidad', 'emulación', 'emulación', ['Windows', 'Linux', 'macOS'], 'Emulador de código abierto para documentar compatibilidad histórica de software DOS.'],
            ['0 A.D.', 'https://play0ad.com/', 'Estrategia y simulación', 'otro', 'otro', ['Windows', 'Linux', 'macOS'], 'Juego de estrategia histórica desarrollado como software libre y de código abierto.'],
            ['The Battle for Wesnoth', 'https://www.wesnoth.org/', 'Estrategia y simulación', 'otro', 'otro', ['Windows', 'Linux', 'macOS', 'Android'], 'Juego de estrategia por turnos abierto, con campañas y contenido comunitario.'],
            ['SuperTuxKart', 'https://supertuxkart.net/', 'Accesibilidad y comunidad', 'otro', 'otro', ['Windows', 'Linux', 'macOS', 'Android'], 'Juego de carreras de código abierto con recursos y desarrollo comunitarios.'],
            ['Hedgewars', 'https://www.hedgewars.org/', 'Accesibilidad y comunidad', 'otro', 'otro', ['Windows', 'Linux', 'macOS'], 'Juego de estrategia por turnos de código abierto, mantenido por su comunidad.'],
            ['Freeciv', 'https://www.freeciv.org/', 'Estrategia y simulación', 'preservación digital', 'preservación digital', ['Windows', 'Linux', 'macOS', 'Web / WASM'], 'Juego de estrategia de código abierto inspirado en la construcción de civilizaciones.'],
            ['Luanti', 'https://www.luanti.org/', 'Motores y adaptación', 'otro', 'otro', ['Windows', 'Linux', 'macOS', 'Android'], 'Motor abierto de juegos voxel y plataforma comunitaria de creación.'],
        ];

        if ($seedDemo) {
            foreach ($proyectos as [$titulo, $url, $categoriaNombre, $formato, $tipo, $nombresPlataformas, $descripcion]) {
                $slug = Str::slug($titulo);
                $videojuego = Videojuego::updateOrCreate(
                    ['slug' => $slug],
                    [
                        'titulo' => $titulo,
                        'descripcion' => $descripcion,
                        'informacion_tecnica' => 'Proyecto de software abierto; consulta su sitio oficial para información técnica actualizada.',
                        'requisitos_conocidos' => 'Los requisitos pueden cambiar según la versión y plataforma.',
                        'formato' => $formato,
                        'tipo_proyecto' => $tipo,
                        'estado' => 'publicado',
                        'advertencia' => config('gamevault.advertencia'),
                        'fecha_lanzamiento' => null,
                        'categoria_id' => $categorias[$categoriaNombre]->id,
                        'user_id' => $admin->id,
                    ]
                );
                $videojuego->plataformas()->sync(collect($nombresPlataformas)->map(fn ($nombre) => $plataformas[$nombre]->id)->all());

                $ruta = 'placeholders/'.$slug.'.svg';
                $texto = 'Ilustración tipográfica generada por código para '.$titulo;
                $svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 680"><defs><linearGradient id="f" x2="1" y2="1"><stop stop-color="#0b2232"/><stop offset="1" stop-color="#26331e"/></linearGradient></defs><rect width="1200" height="680" fill="url(#f)"/><circle cx="920" cy="150" r="260" fill="#00e5ff" opacity=".14"/><path d="M0 540 450 280l310 210 240-150 200 120v220H0Z" fill="#ffc83d" opacity=".12"/><text x="80" y="330" fill="#00e5ff" font-family="Arial,sans-serif" font-size="30" letter-spacing="12">GAMEVAULT · ARCHIVO ABIERTO</text><text x="80" y="430" fill="#eaf3fa" font-family="Arial,sans-serif" font-size="66" font-weight="700">'.htmlspecialchars($titulo, ENT_QUOTES | ENT_XML1, 'UTF-8').'</text></svg>';
                Storage::disk(config('gamevault.image_disk'))->put($ruta, $svg);
                ImagenVideojuego::updateOrCreate(
                    ['videojuego_id' => $videojuego->id, 'ruta' => $ruta],
                    ['texto_alternativo' => $texto, 'orden' => 0]
                );

                Referencia::updateOrCreate(
                    ['videojuego_id' => $videojuego->id, 'titulo' => 'Sitio oficial del proyecto'],
                    [
                        'url' => $url,
                        'tipo_fuente' => 'Sitio oficial',
                        'observaciones' => 'Referencia informativa oficial; GameVault no verifica archivos ni disponibilidad.',
                        'fecha_consulta' => now()->toDateString(),
                    ]
                );
            }
        }

        if ($seedDemo && $colaboradores->count() !== 2) {
            throw new RuntimeException('No se pudieron preparar las cuentas locales de colaboradores.');
        }
    }
}
