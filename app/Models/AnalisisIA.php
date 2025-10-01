<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnalisisIA extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'analisis_ia';
    
    protected $fillable = [
        'vee_id',
        'contexto_adicional',
        'analisis_generado',
        'prioridad_sugerida',
        'categoria_sugerida',
        'confianza',
        'recomendaciones',
        'metadatos',
        'est'
    ];

    protected $casts = [
        'recomendaciones' => 'array',
        'metadatos' => 'array',
        'confianza' => 'integer',
        'vee_id' => 'integer'
    ];

    // Relaciones
    public function veeduria()
    {
        return $this->belongsTo(Veeduria::class, 'vee_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('est', 'act');
    }

    public function scopePorPrioridad($query, $prioridad)
    {
        return $query->where('prioridad_sugerida', $prioridad);
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria_sugerida', $categoria);
    }

    // Métodos de utilidad
    public function esAltaConfianza()
    {
        return $this->confianza >= 80;
    }

    public function esMediaConfianza()
    {
        return $this->confianza >= 50 && $this->confianza < 80;
    }

    public function esBajaConfianza()
    {
        return $this->confianza < 50;
    }

    public function obtenerRecomendaciones()
    {
        return $this->recomendaciones ?? [];
    }

    public function obtenerMetadatos()
    {
        return $this->metadatos ?? [];
    }
}