<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AnalisisIAEtnico extends Model
{
    use HasFactory;

    protected $table = 'analisis_ia_etnicos';

    protected $fillable = [
        'analizable_type',
        'analizable_id',
        'tipo_ia',
        'clasificacion',
        'respuesta',
        'confianza',
        'recomendaciones',
        'analisis_general'
    ];

    protected $casts = [
        'recomendaciones' => 'array'
    ];

    /**
     * Relación polimórfica con el modelo analizable
     */
    public function analizable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Scope por tipo de IA
     */
    public function scopePorTipoIA($query, $tipo)
    {
        return $query->where('tipo_ia', $tipo);
    }

    /**
     * Scope por clasificación
     */
    public function scopePorClasificacion($query, $clasificacion)
    {
        return $query->where('clasificacion', $clasificacion);
    }

    /**
     * Scope por nivel de confianza
     */
    public function scopePorConfianza($query, $minimo = 80)
    {
        return $query->where('confianza', '>=', $minimo);
    }

    /**
     * Scope para análisis recientes
     */
    public function scopeRecientes($query, $dias = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    /**
     * Obtener el color del tipo de IA
     */
    public function getColorTipoIAAttribute()
    {
        return match($this->tipo_ia) {
            'etnica' => 'green',
            'juridica' => 'blue',
            'territorial' => 'purple',
            'cultural' => 'yellow',
            'educativa' => 'indigo',
            'justicia' => 'red',
            'desarrollo' => 'orange',
            'ambiental' => 'emerald',
            default => 'gray'
        };
    }

    /**
     * Obtener el icono del tipo de IA
     */
    public function getIconoTipoIAAttribute()
    {
        return match($this->tipo_ia) {
            'etnica' => '🌿',
            'juridica' => '⚖️',
            'territorial' => '🗺️',
            'cultural' => '📚',
            'educativa' => '🎓',
            'justicia' => '⚖️',
            'desarrollo' => '🚀',
            'ambiental' => '🌱',
            default => '🤖'
        };
    }

    /**
     * Obtener el color del nivel de confianza
     */
    public function getColorConfianzaAttribute()
    {
        if ($this->confianza >= 90) return 'green';
        if ($this->confianza >= 80) return 'blue';
        if ($this->confianza >= 70) return 'yellow';
        if ($this->confianza >= 60) return 'orange';
        return 'red';
    }

    /**
     * Obtener el nivel de confianza como texto
     */
    public function getNivelConfianzaAttribute()
    {
        if ($this->confianza >= 90) return 'Excelente';
        if ($this->confianza >= 80) return 'Muy Bueno';
        if ($this->confianza >= 70) return 'Bueno';
        if ($this->confianza >= 60) return 'Regular';
        return 'Bajo';
    }
}
