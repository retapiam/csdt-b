<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class AIConsulta extends Model
{
    use HasFactory;

    protected $table = 'ai_consultas';

    protected $fillable = [
        'user_id',
        'tipo_consulta',
        'texto_consulta',
        'proveedor_ia',
        'modelo_utilizado',
        'respuesta',
        'confianza',
        'tiempo_procesamiento',
        'tokens_utilizados',
        'costo_tokens',
        'metadata',
    ];

    protected $casts = [
        'confianza' => 'decimal:2',
        'costo_tokens' => 'decimal:6',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function analisisJuridico(): HasOne
    {
        return $this->hasOne(AIAnalisisJuridico::class, 'consulta_id');
    }
}

