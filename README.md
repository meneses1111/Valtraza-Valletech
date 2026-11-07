# Valtraza

Sistema web de ValleTech para gestionar servicios de mantenimiento y reparación de computadores. Usa Laravel 12, PHP 8.3, PostgreSQL y vistas Blade.

## Preparación

Instala PHP 8.3 y Composer. Ejecuta `composer install`, copia `.env.example` a `.env`, configura PostgreSQL y ejecuta `php artisan key:generate` y `php artisan migrate` sobre una base vacía llamada `valtraza`.

La aplicación se construye por módulos siguiendo la ERS 1.1. La documentación técnica estará en `docs/`.
