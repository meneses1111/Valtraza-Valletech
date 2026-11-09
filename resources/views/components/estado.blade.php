@props(['valor'])

<span @class(['badge', 'text-bg-secondary' => $valor === 'pendiente', 'text-bg-primary' => $valor === 'en_curso', 'text-bg-success' => $valor === 'completado'])>{{ \App\Enums\EstadoServicio::from($valor)->etiqueta() }}</span>
