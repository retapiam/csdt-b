<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AIAnalisisVeeduria extends Model
{
    use HasFactory;

    protected $table = 'ai_analisis_veeduria';

    protected $fillable = [
        'user_id',
        'entidad',
        'proyecto',
        'tipo_veeduria',
        'narracion',
        'analisis_transparencia',
        'analisis_contratacion',
        'analisis_participacion',
        'nivel_transparencia',
        'nivel_riesgo',
        'hallazgos',
        'recomendaciones',
        'alertas',
    ];

    protected $casts = [
        'analisis_transparencia' => 'array',
        'analisis_contratacion' => 'array',
        'analisis_participacion' => 'array',
        'hallazgos' => 'array',
        'recomendaciones' => 'array',
        'alertas' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function veedurias(): HasMany
    {
        return $this->hasMany(Veeduria::class, 'analisis_ia_id');
    }
}

