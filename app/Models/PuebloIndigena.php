<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PuebloIndigena extends Model
{
    protected $table = 'pueblos_indigenas';

    protected $fillable = [
        'nombre',
        'pueblo',
        'departamento',
        'municipio',
        'resguardo',
        'territorio_ancestral',
        'extension_hectareas',
        'poblacion',
        'idioma',
        'autoridades_tradicionales',
        'representante_legal',
        'contacto',
        'estado',
        'metadata',
    ];

    protected $casts = [
        'extension_hectareas' => 'decimal:2',
        'poblacion' => 'integer',
        'autoridades_tradicionales' => 'array',
        'metadata' => 'array',
    ];

    public function consultasPrevias(): HasMany
    {
        return $this->hasMany(ConsultaPrevia::class, 'comunidad_id')
            ->where('tipo_comunidad', 'indigena');
    }

    public function planesEtnodesarrollo(): HasMany
    {
        return $this->hasMany(PlanEtnodesarrollo::class, 'comunidad_id')
            ->where('tipo_comunidad', 'indigena');
    }
}
