<?php

namespace App\Support;

use Illuminate\View\FileViewFinder;

class BuscadorVistas extends FileViewFinder
{
    protected function findInPaths($name, $paths)
    {
        $path = parent::findInPaths($name, $paths);

        // view:cache compila con realpath; las peticiones deben usar la misma clave.
        return realpath($path) ?: $path;
    }
}
