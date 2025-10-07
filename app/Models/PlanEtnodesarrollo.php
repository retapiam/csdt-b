<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanEtnodesarrollo extends Model
{
    protected $table = 'planes_etnodesarrollo';

    protected $fillable = [
        'comunidad_id',
        'tipo_comunidad',
        'nombre_plan',
        'descripcion',
        'vision',
        'objetivos',
        'programas',
        'proyectos',
        'presupuesto',
        'fuentes_financiacion',
        'periodo_inicio',
        'periodo_fin',
        'estado',
        'avance_porcentaje',
    ];

    protected $casts = [
        'objetivos' => 'array',
        'programas' => 'array',
        'proyectos' => 'array',
        'presupuesto' => 'decimal:2',
        'fuentes_financiacion' => 'array',
        'periodo_inicio' => 'date',
        'periodo_fin' => 'date',
        'avance_porcentaje' => 'integer',
    ];
}
