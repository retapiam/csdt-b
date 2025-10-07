<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComunidadAfro extends Model
{
    protected $table = 'comunidades_afro';

    protected $fillable = [
        'nombre',
        'tipo',
        'departamento',
        'municipio',
        'territorio_colectivo',
        'titulo_colectivo',
        'extension_hectareas',
        'poblacion',
        'representante_legal',
        'contacto',
        'estado',
        'metadata',
    ];

    protected $casts = [
        'extension_hectareas' => 'decimal:2',
        'poblacion' => 'integer',
        'metadata' => 'array',
    ];

    public function consultasPrevias(): HasMany
    {
        return $this->hasMany(ConsultaPrevia::class, 'comunidad_id')
            ->whereIn('tipo_comunidad', ['afro', 'raizal']);
    }

    public function planesEtnodesarrollo(): HasMany
    {
        return $this->hasMany(PlanEtnodesarrollo::class, 'comunidad_id')
            ->whereIn('tipo_comunidad', ['afro', 'raizal']);
    }
}
