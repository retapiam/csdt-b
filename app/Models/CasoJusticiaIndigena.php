<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CasoJusticiaIndigena extends Model
{
    use HasFactory;

    protected $table = 'casos_justicia_indigena';

    protected $fillable = [
        'comunidad_id',
        'descripcion',
        'pueblo_indigena',
        'tipo_conflicto',
        'gravedad',
        'partes_involucradas',
        'ubicacion',
        'fecha_ocurrencia',
        'testigos',
        'antecedentes',
        'contexto',
        'solicitud',
        'evidencia',
        'procedimiento',
        'resolucion',
        'archivos',
        'estado'
    ];

    protected $casts = [
        'archivos' => 'array',
        'fecha_ocurrencia' => 'date'
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
     * Scope por tipo de conflicto
     */
    public function scopePorTipoConflicto($query, $tipo)
    {
        return $query->where('tipo_conflicto', $tipo);
    }

    /**
     * Scope por gravedad
     */
    public function scopePorGravedad($query, $gravedad)
    {
        return $query->where('gravedad', $gravedad);
    }

    /**
     * Scope por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para casos pendientes
     */
    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['pendiente', 'en_proceso']);
    }

    /**
     * Scope para casos resueltos
     */
    public function scopeResueltos($query)
    {
        return $query->where('estado', 'resuelto');
    }

    /**
     * Obtener el color del tipo de conflicto
     */
    public function getColorTipoConflictoAttribute()
    {
        return match($this->tipo_conflicto) {
            'territorial' => 'green',
            'recursos' => 'emerald',
            'cultural' => 'purple',
            'familiar' => 'pink',
            'comercial' => 'yellow',
            'sucesion' => 'blue',
            'violencia' => 'red',
            'administrativo' => 'gray',
            default => 'gray'
        };
    }

    /**
     * Obtener el icono del tipo de conflicto
     */
    public function getIconoTipoConflictoAttribute()
    {
        return match($this->tipo_conflicto) {
            'territorial' => '🗺️',
            'recursos' => '🌿',
            'cultural' => '🎭',
            'familiar' => '👨‍👩‍👧‍👦',
            'comercial' => '💰',
            'sucesion' => '📜',
            'violencia' => '⚠️',
            'administrativo' => '📋',
            default => '⚖️'
        };
    }

    /**
     * Obtener el color de la gravedad
     */
    public function getColorGravedadAttribute()
    {
        return match($this->gravedad) {
            'leve' => 'green',
            'moderado' => 'yellow',
            'grave' => 'orange',
            'muy_grave' => 'red',
            default => 'gray'
        };
    }

    /**
     * Obtener el color del estado
     */
    public function getColorEstadoAttribute()
    {
        return match($this->estado) {
            'pendiente' => 'yellow',
            'en_proceso' => 'blue',
            'resuelto' => 'green',
            'cerrado' => 'gray',
            'suspendido' => 'red',
            default => 'gray'
        };
    }
}
