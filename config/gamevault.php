<?php

return [
    'image_disk' => env('GAMEVAULT_IMAGE_DISK', 'local'),
    'advertencia' => 'Las referencias externas pertenecen a terceros. GameVault no analiza ni certifica la seguridad, legalidad ni disponibilidad de sus sitios y archivos, y no ofrece asesoría legal.',
    'estados' => ['borrador', 'en_revision', 'publicado', 'retirado'],
    'formatos' => ['crack/modificación', 'ROM', 'imagen ISO', 'repack', 'port o adaptación', 'emulación', 'reconstrucción/ingeniería inversa', 'preservación digital', 'otro'],
    'fuentes' => ['Sitio oficial', 'Código fuente', 'Documentación', 'Publicación académica', 'Otro'],
];
