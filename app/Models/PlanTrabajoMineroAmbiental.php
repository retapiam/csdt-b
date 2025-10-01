<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlanTrabajoMineroAmbiental extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'planes_trabajo_minero_ambiental';

    protected $fillable = [
        'user_id',
        'tipo_plan',
        'nombre_proyecto',
        'tipo_mineria',
        'ubicacion',
        'duracion',
        'descripcion',
        'datos_entrada',
        'plan_generado',
        'estado',
        'metadata',
        'observaciones',
        'aprobado_por',
        'fecha_aprobacion'
    ];

    protected $casts = [
        'datos_entrada' => 'array',
        'plan_generado' => 'array',
        'metadata' => 'array',
        'fecha_aprobacion' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    /**
     * Relación con Usuario
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relación con Usuario Aprobador
     */
    public function aprobador()
    {
        return $this->belongsTo(User::class, 'aprobado_por');
    }

    /**
     * Scope: Filtrar por tipo de plan
     */
    public function scopeTipoPlan($query, $tipo)
    {
        return $query->where('tipo_plan', $tipo);
    }

    /**
     * Scope: Filtrar por estado
     */
    public function scopeEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope: Filtrar por tipo de minería
     */
    public function scopeTipoMineria($query, $tipo)
    {
        return $query->where('tipo_mineria', $tipo);
    }

    /**
     * Scope: Planes recientes
     */
    public function scopeRecientes($query, $limit = 10)
    {
        return $query->orderBy('created_at', 'desc')->take($limit);
    }

    /**
     * Accessor: Obtener contenido del plan
     */
    public function getContenidoPlanAttribute()
    {
        return $this->plan_generado['contenido'] ?? null;
    }

    /**
     * Accessor: Obtener nivel del análisis
     */
    public function getNivelAnalisisAttribute()
    {
        return $this->plan_generado['nivel'] ?? 'POST-DOCTORADO';
    }

    /**
     * Accessor: Obtener tokens usados
     */
    public function getTokensUsadosAttribute()
    {
        return $this->plan_generado['tokens_usados'] ?? 0;
    }

    /**
     * Mutator: Asegurar formato correcto de datos de entrada
     */
    public function setDatosEntradaAttribute($value)
    {
        $this->attributes['datos_entrada'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Mutator: Asegurar formato correcto del plan generado
     */
    public function setPlanGeneradoAttribute($value)
    {
        $this->attributes['plan_generado'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Mutator: Asegurar formato correcto de metadata
     */
    public function setMetadataAttribute($value)
    {
        $this->attributes['metadata'] = is_array($value) ? json_encode($value) : $value;
    }

    /**
     * Verificar si el plan está aprobado
     */
    public function estaAprobado()
    {
        return $this->estado === 'aprobado';
    }

    /**
     * Verificar si el plan está en ejecución
     */
    public function estaEnEjecucion()
    {
        return $this->estado === 'en_ejecucion';
    }

    /**
     * Verificar si el plan está finalizado
     */
    public function estaFinalizado()
    {
        return $this->estado === 'finalizado';
    }

    /**
     * Marcar plan como aprobado
     */
    public function aprobar($aprobadorId, $observaciones = null)
    {
        $this->update([
            'estado' => 'aprobado',
            'aprobado_por' => $aprobadorId,
            'fecha_aprobacion' => now(),
            'observaciones' => $observaciones
        ]);
    }

    /**
     * Marcar plan como rechazado
     */
    public function rechazar($observaciones)
    {
        $this->update([
            'estado' => 'rechazado',
            'observaciones' => $observaciones
        ]);
    }

    /**
     * Iniciar ejecución del plan
     */
    public function iniciarEjecucion()
    {
        if ($this->estaAprobado()) {
            $this->update(['estado' => 'en_ejecucion']);
            return true;
        }
        return false;
    }

    /**
     * Finalizar plan
     */
    public function finalizar($observaciones = null)
    {
        $this->update([
            'estado' => 'finalizado',
            'observaciones' => $observaciones
        ]);
    }

    /**
     * Obtener resumen del plan
     */
    public function obtenerResumen()
    {
        return [
            'id' => $this->id,
            'tipo_plan' => $this->tipo_plan,
            'nombre_proyecto' => $this->nombre_proyecto,
            'tipo_mineria' => $this->tipo_mineria,
            'ubicacion' => $this->ubicacion,
            'duracion' => $this->duracion,
            'estado' => $this->estado,
            'nivel_analisis' => $this->nivel_analisis,
            'fecha_creacion' => $this->created_at->format('d/m/Y H:i'),
            'usuario' => $this->usuario->name ?? 'N/A'
        ];
    }
}

