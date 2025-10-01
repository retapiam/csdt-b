<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ComunidadEtnica extends Model
{
    use HasFactory;

    protected $table = 'comunidades_etnicas';

    protected $fillable = [
        'nombre',
        'tipo',
        'ubicacion',
        'poblacion',
        'territorio',
        'representante',
        'telefono',
        'email',
        'idioma',
        'tradicion',
        'descripcion',
        'coordenadas',
        'estado'
    ];

    protected $casts = [
        'coordenadas' => 'array',
        'poblacion' => 'integer'
    ];

    /**
     * Relación con patrimonio cultural
     */
    public function patrimonioCultural(): HasMany
    {
        return $this->hasMany(PatrimonioCulturalEtnico::class, 'comunidad_id');
    }

    /**
     * Relación con planes educativos
     */
    public function planesEducativos(): HasMany
    {
        return $this->hasMany(PlanEducativoEtnico::class, 'comunidad_id');
    }

    /**
     * Relación con casos de justicia
     */
    public function casosJusticia(): HasMany
    {
        return $this->hasMany(CasoJusticiaIndigena::class, 'comunidad_id');
    }

    /**
     * Relación con territorios ancestrales
     */
    public function territoriosAncestrales(): HasMany
    {
        return $this->hasMany(TerritorioAncestral::class, 'comunidad_id');
    }

    /**
     * Relación con consultas étnicas
     */
    public function consultasEtnicas(): HasMany
    {
        return $this->hasMany(ConsultaEtnica::class, 'comunidad_id');
    }

    /**
     * Relación con notificaciones
     */
    public function notificaciones(): HasMany
    {
        return $this->hasMany(NotificacionEtnica::class, 'comunidad_id');
    }

    /**
     * Scope para comunidades activas
     */
    public function scopeActivas($query)
    {
        return $query->where('estado', 'activo');
    }

    /**
     * Scope por tipo de comunidad
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Obtener estadísticas de la comunidad
     */
    public function getEstadisticasAttribute()
    {
        return [
            'patrimonio_count' => $this->patrimonioCultural()->count(),
            'planes_count' => $this->planesEducativos()->count(),
            'casos_count' => $this->casosJusticia()->count(),
            'territorios_count' => $this->territoriosAncestrales()->count(),
            'consultas_count' => $this->consultasEtnicas()->count(),
        ];
    }
}
