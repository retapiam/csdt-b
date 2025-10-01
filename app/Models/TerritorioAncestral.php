<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class TerritorioAncestral extends Model
{
    use HasFactory;

    protected $table = 'territorios_ancestrales';

    protected $fillable = [
        'comunidad_id',
        'nombre',
        'tipo',
        'ubicacion',
        'extension',
        'limites',
        'descripcion',
        'importancia',
        'amenazas',
        'estado_proteccion',
        'sitios_sagrados',
        'recursos',
        'historia',
        'cosmovision',
        'coordenadas',
        'archivos'
    ];

    protected $casts = [
        'coordenadas' => 'array',
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
     * Scope por tipo de territorio
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope por estado de protección
     */
    public function scopePorEstadoProteccion($query, $estado)
    {
        return $query->where('estado_proteccion', $estado);
    }

    /**
     * Scope para territorios en riesgo
     */
    public function scopeEnRiesgo($query)
    {
        return $query->whereIn('estado_proteccion', ['amenazado', 'critico']);
    }

    /**
     * Scope para territorios protegidos
     */
    public function scopeProtegidos($query)
    {
        return $query->where('estado_proteccion', 'protegido');
    }

    /**
     * Obtener el color del tipo de territorio
     */
    public function getColorTipoAttribute()
    {
        return match($this->tipo) {
            'resguardo' => 'green',
            'consejo_comunitario' => 'blue',
            'territorio_tradicional' => 'emerald',
            'sitio_sagrado' => 'purple',
            'zona_reserva' => 'yellow',
            'territorio_ancestral' => 'orange',
            default => 'gray'
        };
    }

    /**
     * Obtener el icono del tipo de territorio
     */
    public function getIconoTipoAttribute()
    {
        return match($this->tipo) {
            'resguardo' => '🏞️',
            'consejo_comunitario' => '🏘️',
            'territorio_tradicional' => '🌲',
            'sitio_sagrado' => '⛰️',
            'zona_reserva' => '🛡️',
            'territorio_ancestral' => '🏛️',
            default => '🗺️'
        };
    }

    /**
     * Obtener el color del estado de protección
     */
    public function getColorEstadoProteccionAttribute()
    {
        return match($this->estado_proteccion) {
            'protegido' => 'green',
            'vulnerable' => 'yellow',
            'amenazado' => 'orange',
            'critico' => 'red',
            default => 'gray'
        };
    }

    /**
     * Obtener el icono del estado de protección
     */
    public function getIconoEstadoProteccionAttribute()
    {
        return match($this->estado_proteccion) {
            'protegido' => '🛡️',
            'vulnerable' => '⚠️',
            'amenazado' => '🚨',
            'critico' => '🔴',
            default => '❓'
        };
    }
}
