<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AIAnalisisEtnico extends Model
{
    use HasFactory;

    protected $table = 'ai_analisis_etnico';

    protected $fillable = [
        'user_id',
        'grupo_etnico',
        'comunidad',
        'ubicacion',
        'narracion',
        'tipo_etnico_detectado',
        'confianza_tipo',
        'derechos_afectados',
        'requiere_consulta_previa',
        'nivel_urgencia',
        'impacto_territorial',
        'impacto_cultural',
        'impacto_autonomia',
        'recomendaciones',
        'procedimientos_sugeridos',
        'normativas_aplicables',
    ];

    protected $casts = [
        'confianza_tipo' => 'decimal:2',
        'derechos_afectados' => 'array',
        'requiere_consulta_previa' => 'boolean',
        'impacto_territorial' => 'array',
        'impacto_cultural' => 'array',
        'impacto_autonomia' => 'array',
        'recomendaciones' => 'array',
        'procedimientos_sugeridos' => 'array',
        'normativas_aplicables' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function consultasPrevias(): HasMany
    {
        return $this->hasMany(ConsultaPrevia::class, 'analisis_ia_id');
    }
}

