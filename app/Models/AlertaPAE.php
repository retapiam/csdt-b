<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AlertaPAE extends Model
{
    use HasFactory;

    protected $table = 'alertas_pae';

    protected $fillable = [
        'proyecto_id',
        'actividad_id',
        'asignado_a',
        'tipo',
        'severidad',
        'estado',
        'mensaje',
        'data',
        'sla_at',
    ];

    protected $casts = [
        'data' => 'array',
        'sla_at' => 'datetime',
    ];
}


