<?php

namespace Tests\Unit;

use App\Support\BuscadorVistas;
use Illuminate\Filesystem\Filesystem;
use Illuminate\View\Compilers\BladeCompiler;
use PHPUnit\Framework\TestCase;

class CacheVistasTest extends TestCase
{
    public function test_la_vista_y_la_precompilacion_utilizan_la_misma_clave(): void
    {
        $files = new Filesystem;
        $root = dirname(__DIR__, 2);
        $finder = new BuscadorVistas($files, [$root.'/resources/views']);
        $compiler = new BladeCompiler($files, $root.'/storage/framework/views');

        foreach (['acceso.panel', 'layouts.administrador', 'layouts.tecnico', 'layouts.base'] as $name) {
            $path = $finder->find($name);
            $this->assertSame(realpath($path), $path);
            $this->assertSame($compiler->getCompiledPath(realpath($path)), $compiler->getCompiledPath($path));
        }
    }
}
