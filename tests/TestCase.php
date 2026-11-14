<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $dropTypes = true;

    public function createApplication()
    {
        $app = parent::createApplication();

        if (! $app->environment('testing') || $app['db']->connection()->getConfig('database') !== 'valtraza_test') {
            throw new \RuntimeException('Las pruebas solo pueden ejecutarse con APP_ENV=testing y la base exclusiva valtraza_test.');
        }

        return $app;
    }
}
