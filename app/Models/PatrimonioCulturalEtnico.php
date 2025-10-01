<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PatrimonioCulturalEtnico extends Model
{
    use HasFactory;

    protected $table = 'patrimonio_cultural_etnico';

    protected $fillable = [
        'comunidad_id',
        'tipo',
        'nombre',
        'descripcion',
        'ubicacion',
        'estado_conservacion',
        'importancia',
        'transmision',
        'portadores',
        'amenazas',
        'archivos'
    ];

    protected $casts = [
        'archivos' => 'array'
    ];

    /**
     * Relación con la comunidad
     */
    public function comunidad(): BelongsTo
    {
        return $this->belongsTo(ComunidadEtnica::class, 'comunidad_id');
    }

    /**
     * Relación polimórfica con análisis de IA
     */
    public function analisisIA(): MorphMany
    {
        return $this->morphMany(AnalisisIAEtnico::class, 'analizable');
    }

    /**
     * Scope por tipo de patrimonio
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope por estado de conservación
     */
    public function scopePorEstadoConservacion($query, $estado)
    {
        return $query->where('estado_conservacion', $estado);
    }

    /**
     * Scope para patrimonio en riesgo
     */
    public function scopeEnRiesgo($query)
    {
        return $query->whereIn('estado_conservacion', ['malo', 'critico']);
    }

    /**
     * Obtener el color del estado de conservación
     */
    public function getColorEstadoAttribute()
    {
        return match($this->estado_conservacion) {
            'excelente' => 'green',
            'bueno' => 'blue',
            'regular' => 'yellow',
            'malo' => 'orange',
            'critico' => 'red',
            default => 'gray'
        };
    }

    /**
     * Obtener el icono del tipo de patrimonio
     */
    public function getIconoTipoAttribute()
    {
        return match($this->tipo) {
            'saberes_tradicionales' => '🧠',
            'lenguas_nativas' => '🗣️',
            'rituales_ceremonias' => '🕯️',
            'medicina_tradicional' => '🌿',
            'artesania' => '🎨',
            'musica_danza' => '🎵',
            'gastronomia' => '🍽️',
            'vestimenta' => '👗',
            'arquitectura' => '🏠',
            'agricultura' => '🌾',
            'caza_pesca' => '🎣',
            'navegacion' => '⛵',
            'astronomia' => '⭐',
            'matematicas' => '🔢',
            'calendarios' => '📅',
            default => '📚'
        };
    }
}
