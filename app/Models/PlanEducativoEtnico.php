<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class PlanEducativoEtnico extends Model
{
    use HasFactory;

    protected $table = 'planes_educativos_etnicos';

    protected $fillable = [
        'comunidad_id',
        'nombre',
        'nivel',
        'area',
        'descripcion',
        'objetivos',
        'metodologia',
        'duracion',
        'participantes',
        'recursos',
        'evaluacion',
        'cosmovision',
        'saberes',
        'estado'
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
     * Scope por nivel educativo
     */
    public function scopePorNivel($query, $nivel)
    {
        return $query->where('nivel', $nivel);
    }

    /**
     * Scope por área de conocimiento
     */
    public function scopePorArea($query, $area)
    {
        return $query->where('area', $area);
    }

    /**
     * Scope por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para planes activos
     */
    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['en_desarrollo', 'activo']);
    }

    /**
     * Obtener el color del nivel educativo
     */
    public function getColorNivelAttribute()
    {
        return match($this->nivel) {
            'inicial' => 'pink',
            'basica' => 'blue',
            'media' => 'green',
            'superior' => 'purple',
            'adultos' => 'orange',
            'especializada' => 'indigo',
            default => 'gray'
        };
    }

    /**
     * Obtener el icono del nivel educativo
     */
    public function getIconoNivelAttribute()
    {
        return match($this->nivel) {
            'inicial' => '👶',
            'basica' => '📚',
            'media' => '🎓',
            'superior' => '🏛️',
            'adultos' => '👥',
            'especializada' => '🔬',
            default => '🎓'
        };
    }

    /**
     * Obtener el color del área de conocimiento
     */
    public function getColorAreaAttribute()
    {
        return match($this->area) {
            'saberes_tradicionales' => 'purple',
            'lenguas_nativas' => 'blue',
            'historia_ancestral' => 'yellow',
            'territorio' => 'green',
            'medicina_tradicional' => 'emerald',
            'agricultura' => 'lime',
            'artesania' => 'pink',
            'musica_danza' => 'indigo',
            'cosmovision' => 'violet',
            'derechos' => 'red',
            default => 'gray'
        };
    }

    /**
     * Obtener el icono del área de conocimiento
     */
    public function getIconoAreaAttribute()
    {
        return match($this->area) {
            'saberes_tradicionales' => '🧠',
            'lenguas_nativas' => '🗣️',
            'historia_ancestral' => '📜',
            'territorio' => '🗺️',
            'medicina_tradicional' => '🌿',
            'agricultura' => '🌾',
            'artesania' => '🎨',
            'musica_danza' => '🎵',
            'cosmovision' => '🌟',
            'derechos' => '⚖️',
            default => '📚'
        };
    }
}
