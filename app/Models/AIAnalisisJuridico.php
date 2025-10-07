<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AIAnalisisJuridico extends Model
{
    use HasFactory;

    protected $table = 'ai_analisis_juridico';

    protected $fillable = [
        'consulta_id',
        'user_id',
        'tipo_caso',
        'categoria_juridica',
        'texto_analizado',
        'clasificaciones',
        'resumen',
        'fundamentos_legales',
        'jurisprudencia',
        'normativa_aplicable',
        'recomendaciones',
        'evaluacion_riesgos',
        'confianza_promedio',
        'proveedores_utilizados',
        'nivel_analisis',
    ];

    protected $casts = [
        'clasificaciones' => 'array',
        'fundamentos_legales' => 'array',
        'jurisprudencia' => 'array',
        'normativa_aplicable' => 'array',
        'recomendaciones' => 'array',
        'evaluacion_riesgos' => 'array',
        'proveedores_utilizados' => 'array',
        'confianza_promedio' => 'decimal:2',
    ];

    public function consulta(): BelongsTo
    {
        return $this->belongsTo(AIConsulta::class, 'consulta_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function proyectos(): HasMany
    {
        return $this->hasMany(Proyecto::class, 'analisis_ia_id');
    }

    public function casosLegales(): HasMany
    {
        return $this->hasMany(CasoLegal::class, 'analisis_ia_id');
    }
}

