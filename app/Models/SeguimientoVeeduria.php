<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeguimientoVeeduria extends Model
{
    use HasFactory;

    protected $table = 'seguimiento_veeduria';

    protected $fillable = [
        'veeduria_id',
        'fecha_seguimiento',
        'tipo_actividad',
        'descripcion',
        'hallazgos',
        'evidencias',
        'nivel_riesgo',
        'acciones_recomendadas',
        'responsable',
        'estado',
    ];

    protected $casts = [
        'fecha_seguimiento' => 'date',
        'hallazgos' => 'array',
        'evidencias' => 'array',
        'acciones_recomendadas' => 'array',
    ];

    public function veeduria(): BelongsTo
    {
        return $this->belongsTo(Veeduria::class);
    }
}

