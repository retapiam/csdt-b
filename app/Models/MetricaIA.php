<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetricaIA extends Model
{
    protected $table = 'metricas_ia';

    protected $fillable = [
        'fecha',
        'proveedor',
        'total_consultas',
        'consultas_exitosas',
        'consultas_fallidas',
        'tiempo_promedio_ms',
        'tokens_totales',
        'costo_total',
        'tasa_exito',
    ];

    protected $casts = [
        'fecha' => 'date',
        'total_consultas' => 'integer',
        'consultas_exitosas' => 'integer',
        'consultas_fallidas' => 'integer',
        'tiempo_promedio_ms' => 'integer',
        'tokens_totales' => 'integer',
        'costo_total' => 'decimal:4',
        'tasa_exito' => 'decimal:2',
    ];
}
