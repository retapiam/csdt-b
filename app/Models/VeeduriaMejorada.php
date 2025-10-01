<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VeeduriaMejorada extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vee';
    protected $primaryKey = 'id';

    protected $fillable = [
        'cod', 'usu_id', 'ope_id', 'tit', 'des', 'tip', 'est', 'pri',
        'cat', 'ciu', 'dep', 'dir', 'lat', 'lng', 'pre', 'fec_ini',
        'fec_fin', 'fec_ven', 'obs', 'met'
    ];

    protected $casts = [
        'fec_ini' => 'date',
        'fec_fin' => 'date',
        'fec_ven' => 'date',
        'pre' => 'decimal:2',
        'lat' => 'decimal:8',
        'lng' => 'decimal:8',
        'met' => 'array',
    ];

    // ========================================
    // SCOPES
    // ========================================

    public function scopePendientes($query)
    {
        return $query->where('est', 'pen');
    }

    public function scopeEnProceso($query)
    {
        return $query->where('est', 'pro');
    }

    public function scopeRadicadas($query)
    {
        return $query->where('est', 'rad');
    }

    public function scopeCerradas($query)
    {
        return $query->where('est', 'cer');
    }

    public function scopeCanceladas($query)
    {
        return $query->where('est', 'can');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tip', $tipo);
    }

    public function scopePorPrioridad($query, $prioridad)
    {
        return $query->where('pri', $prioridad);
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('cat', $categoria);
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usu_id', $usuarioId);
    }

    public function scopePorOperador($query, $operadorId)
    {
        return $query->where('ope_id', $operadorId);
    }

    public function scopePorCiudad($query, $ciudad)
    {
        return $query->where('ciu', 'like', "%{$ciudad}%");
    }

    public function scopePorDepartamento($query, $departamento)
    {
        return $query->where('dep', 'like', "%{$departamento}%");
    }

    public function scopeVencidas($query)
    {
        return $query->where('fec_ven', '<', now());
    }

    public function scopePorVencer($query, $dias = 7)
    {
        return $query->whereBetween('fec_ven', [now(), now()->addDays($dias)]);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('tit', 'like', "%{$termino}%")
              ->orWhere('des', 'like', "%{$termino}%")
              ->orWhere('cod', 'like', "%{$termino}%");
        });
    }

    public function scopeConPresupuesto($query)
    {
        return $query->whereNotNull('pre')->where('pre', '>', 0);
    }

    public function scopeConUbicacion($query)
    {
        return $query->whereNotNull('lat')->whereNotNull('lng');
    }

    // ========================================
    // RELACIONES
    // ========================================

    public function usuario()
    {
        return $this->belongsTo(UsuarioMejorado::class, 'usu_id');
    }

    public function operador()
    {
        return $this->belongsTo(UsuarioMejorado::class, 'ope_id');
    }

    public function tareas()
    {
        return $this->hasMany(TareaMejorada::class, 'vee_id');
    }

    public function donaciones()
    {
        return $this->hasMany(DonacionMejorada::class, 'vee_id');
    }

    public function archivos()
    {
        return $this->hasMany(ArchivoMejorado::class, 'vee_id');
    }

    public function logs()
    {
        return $this->hasMany(LogMejorado::class, 'reg_id')->where('tab', 'vee');
    }

    // ========================================
    // MÉTODOS DE UTILIDAD
    // ========================================

    public function getEstadoTextoAttribute()
    {
        $estados = [
            'pen' => 'Pendiente',
            'pro' => 'En Proceso',
            'rad' => 'Radicada',
            'cer' => 'Cerrada',
            'can' => 'Cancelada'
        ];
        return $estados[$this->est] ?? 'Desconocido';
    }

    public function getTipoTextoAttribute()
    {
        $tipos = [
            'pet' => 'Petición',
            'que' => 'Queja',
            'rec' => 'Reclamo',
            'sug' => 'Sugerencia',
            'fel' => 'Felicitación',
            'den' => 'Denuncia'
        ];
        return $tipos[$this->tip] ?? 'Desconocido';
    }

    public function getPrioridadTextoAttribute()
    {
        $prioridades = [
            'baj' => 'Baja',
            'med' => 'Media',
            'alt' => 'Alta',
            'urg' => 'Urgente'
        ];
        return $prioridades[$this->pri] ?? 'Desconocida';
    }

    public function getCategoriaTextoAttribute()
    {
        $categorias = [
            'inf' => 'Infraestructura',
            'ser' => 'Servicios Públicos',
            'seg' => 'Seguridad',
            'edu' => 'Educación',
            'sal' => 'Salud',
            'tra' => 'Transporte',
            'amb' => 'Medio Ambiente',
            'otr' => 'Otros'
        ];
        return $categorias[$this->cat] ?? 'Desconocida';
    }

    public function estaVencida()
    {
        return $this->fec_ven && $this->fec_ven < now();
    }

    public function estaPorVencer($dias = 7)
    {
        return $this->fec_ven && $this->fec_ven <= now()->addDays($dias) && $this->fec_ven > now();
    }

    public function tieneOperador()
    {
        return !is_null($this->ope_id);
    }

    public function tienePresupuesto()
    {
        return !is_null($this->pre) && $this->pre > 0;
    }

    public function tieneUbicacion()
    {
        return !is_null($this->lat) && !is_null($this->lng);
    }

    // ========================================
    // MÉTODOS DE ACCIONES
    // ========================================

    public function radicar()
    {
        $this->update(['est' => 'rad']);
    }

    public function cerrar()
    {
        $this->update(['est' => 'cer']);
    }

    public function cancelar()
    {
        $this->update(['est' => 'can']);
    }

    public function asignarOperador($operadorId)
    {
        $this->update(['ope_id' => $operadorId]);
    }

    public function cambiarPrioridad($nuevaPrioridad)
    {
        $this->update(['pri' => $nuevaPrioridad]);
    }

    public function actualizarPresupuesto($nuevoPresupuesto)
    {
        $this->update(['pre' => $nuevoPresupuesto]);
    }

    // ========================================
    // MÉTODOS DE ESTADÍSTICAS
    // ========================================

    public function estadisticas()
    {
        return [
            'tareas_totales' => $this->tareas()->count(),
            'tareas_completadas' => $this->tareas()->where('est', 'com')->count(),
            'tareas_pendientes' => $this->tareas()->where('est', 'pen')->count(),
            'donaciones_totales' => $this->donaciones()->count(),
            'donaciones_confirmadas' => $this->donaciones()->where('est', 'con')->count(),
            'archivos_totales' => $this->archivos()->count(),
            'dias_creada' => $this->created_at->diffInDays(now()),
            'dias_vencimiento' => $this->fec_ven ? $this->fec_ven->diffInDays(now()) : null,
        ];
    }

    // ========================================
    // MÉTODOS DE BÚSQUEDA AVANZADA
    // ========================================

    public static function buscarAvanzado($filtros = [])
    {
        $query = self::with(['usuario', 'operador']);

        if (isset($filtros['buscar'])) {
            $query->buscar($filtros['buscar']);
        }

        if (isset($filtros['est'])) {
            $query->where('est', $filtros['est']);
        }

        if (isset($filtros['tip'])) {
            $query->porTipo($filtros['tip']);
        }

        if (isset($filtros['pri'])) {
            $query->porPrioridad($filtros['pri']);
        }

        if (isset($filtros['cat'])) {
            $query->porCategoria($filtros['cat']);
        }

        if (isset($filtros['usu_id'])) {
            $query->porUsuario($filtros['usu_id']);
        }

        if (isset($filtros['ope_id'])) {
            $query->porOperador($filtros['ope_id']);
        }

        if (isset($filtros['ciu'])) {
            $query->porCiudad($filtros['ciu']);
        }

        if (isset($filtros['dep'])) {
            $query->porDepartamento($filtros['dep']);
        }

        if (isset($filtros['con_presupuesto'])) {
            $query->conPresupuesto();
        }

        if (isset($filtros['con_ubicacion'])) {
            $query->conUbicacion();
        }

        if (isset($filtros['vencidas'])) {
            $query->vencidas();
        }

        if (isset($filtros['por_vencer'])) {
            $query->porVencer($filtros['dias_vencimiento'] ?? 7);
        }

        if (isset($filtros['fec_ini'])) {
            $query->whereDate('created_at', '>=', $filtros['fec_ini']);
        }

        if (isset($filtros['fec_fin'])) {
            $query->whereDate('created_at', '<=', $filtros['fec_fin']);
        }

        return $query;
    }

    // ========================================
    // MÉTODOS DE EXPORTACIÓN
    // ========================================

    public function toArrayExport()
    {
        return [
            'codigo' => $this->cod,
            'titulo' => $this->tit,
            'tipo' => $this->tipo_texto,
            'estado' => $this->estado_texto,
            'prioridad' => $this->prioridad_texto,
            'categoria' => $this->categoria_texto,
            'usuario' => $this->usuario->nombre_completo ?? 'N/A',
            'operador' => $this->operador->nombre_completo ?? 'Sin asignar',
            'ciudad' => $this->ciu,
            'departamento' => $this->dep,
            'presupuesto' => $this->pre ? '$' . number_format($this->pre, 2) : 'Sin presupuesto',
            'fecha_creacion' => $this->created_at->format('d/m/Y H:i:s'),
            'fecha_vencimiento' => $this->fec_ven ? $this->fec_ven->format('d/m/Y') : 'Sin fecha',
            'vencida' => $this->estaVencida() ? 'Sí' : 'No',
        ];
    }
}
