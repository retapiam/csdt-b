<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Veeduria extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'tipo',
        'entidad_vigilada',
        'proyecto_vigilado',
        'descripcion',
        'objetivos',
        'integrantes',
        'representante_legal',
        'contacto',
        'departamento',
        'municipio',
        'fecha_constitucion',
        'estado',
        'hallazgos',
        'recomendaciones',
        'analisis_ia_id',
    ];

    protected $casts = [
        'fecha_constitucion' => 'date',
        'objetivos' => 'array',
        'integrantes' => 'array',
        'hallazgos' => 'array',
        'recomendaciones' => 'array',
    ];

    public function analisisIA(): BelongsTo
    {
        return $this->belongsTo(AIAnalisisVeeduria::class, 'analisis_ia_id');
    }

    public function seguimientos(): HasMany
    {
        return $this->hasMany(SeguimientoVeeduria::class);
    }
}

