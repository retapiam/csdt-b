<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CasoLegal extends Model
{
    use HasFactory;

    protected $table = 'casos_legales';

    protected $fillable = [
        'user_id',
        'tipo_caso',
        'numero_radicado',
        'demandante',
        'demandado',
        'hechos',
        'pretensiones',
        'fundamentos_legales',
        'estado_caso',
        'fecha_presentacion',
        'fecha_admision',
        'fecha_fallo',
        'juzgado',
        'juez',
        'resultado',
        'archivos_adjuntos',
        'analisis_ia_id',
    ];

    protected $casts = [
        'fecha_presentacion' => 'date',
        'fecha_admision' => 'date',
        'fecha_fallo' => 'date',
        'archivos_adjuntos' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function analisisIA(): BelongsTo
    {
        return $this->belongsTo(AIAnalisisJuridico::class, 'analisis_ia_id');
    }
}

