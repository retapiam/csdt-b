<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetricaSistema extends Model
{
    protected $table = 'metricas_sistema';

    protected $fillable = [
        'fecha',
        'usuarios_activos',
        'proyectos_creados',
        'proyectos_completados',
        'tareas_creadas',
        'tareas_completadas',
        'consultas_ia',
        'analisis_generados',
        'pdfs_generados',
        'donaciones_recibidas',
        'monto_donaciones',
    ];

    protected $casts = [
        'fecha' => 'date',
        'usuarios_activos' => 'integer',
        'proyectos_creados' => 'integer',
        'proyectos_completados' => 'integer',
        'tareas_creadas' => 'integer',
        'tareas_completadas' => 'integer',
        'consultas_ia' => 'integer',
        'analisis_generados' => 'integer',
        'pdfs_generados' => 'integer',
        'donaciones_recibidas' => 'integer',
        'monto_donaciones' => 'decimal:2',
    ];
}
