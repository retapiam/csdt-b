<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class APU extends Model
{
    protected $table = 'apus';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'tipo_proyecto',
        'categoria',
        'unidad_medida',
        'costo_por_hora',
        'costo_materiales',
        'costo_equipos',
        'costo_total',
        'tiempo_ejecucion',
        'rendimiento',
        'vigencia_desde',
        'vigencia_hasta',
        'estado',
    ];

    protected $casts = [
        'costo_por_hora' => 'decimal:2',
        'costo_materiales' => 'decimal:2',
        'costo_equipos' => 'decimal:2',
        'costo_total' => 'decimal:2',
        'tiempo_ejecucion' => 'decimal:2',
        'vigencia_desde' => 'date',
        'vigencia_hasta' => 'date',
    ];
}
