<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultaPrevia extends Model
{
    protected $table = 'consultas_previas';

    protected $fillable = [
        'proyecto_nombre',
        'entidad_solicitante',
        'tipo_proyecto',
        'comunidad_id',
        'tipo_comunidad',
        'ubicacion',
        'descripcion_proyecto',
        'impactos_identificados',
        'estado',
        'fecha_inicio',
        'fecha_finalizacion',
        'resultado',
        'acuerdos',
        'seguimiento',
        'responsable',
        'analisis_ia_id',
    ];

    protected $casts = [
        'impactos_identificados' => 'array',
        'acuerdos' => 'array',
        'seguimiento' => 'array',
        'fecha_inicio' => 'date',
        'fecha_finalizacion' => 'date',
    ];

    public function analisisEtnico(): BelongsTo
    {
        return $this->belongsTo(AIAnalisisEtnico::class, 'analisis_ia_id');
    }
}
