<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class PermisoUsuario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'permisos_usuarios';

    protected $fillable = [
        'user_id',
        'tipo_permiso',
        'estado',
        'fecha_inicio',
        'fecha_fin',
        'es_temporal',
        'restricciones',
        'motivo_veto',
        'otorgado_por',
        'modificado_por',
        'notas',
    ];

    protected $casts = [
        'restricciones' => 'array',
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'es_temporal' => 'boolean',
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function otorgador()
    {
        return $this->belongsTo(User::class, 'otorgado_por');
    }

    public function modificador()
    {
        return $this->belongsTo(User::class, 'modificado_por');
    }

    public function historial()
    {
        return $this->hasMany(HistorialPermiso::class, 'permiso_id');
    }

    // Métodos de utilidad

    /**
     * Verifica si el permiso está activo
     */
    public function estaActivo(): bool
    {
        // Verificar estado
        if ($this->estado !== 'activo' && $this->estado !== 'temporal') {
            return false;
        }

        // Si es temporal, verificar fechas
        if ($this->es_temporal) {
            $ahora = Carbon::now();
            
            if ($this->fecha_inicio && $ahora->lt($this->fecha_inicio)) {
                return false;
            }
            
            if ($this->fecha_fin && $ahora->gt($this->fecha_fin)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verifica si el permiso ha expirado
     */
    public function haExpirado(): bool
    {
        if (!$this->es_temporal || !$this->fecha_fin) {
            return false;
        }

        return Carbon::now()->gt($this->fecha_fin);
    }

    /**
     * Días restantes del permiso temporal
     */
    public function diasRestantes(): ?int
    {
        if (!$this->es_temporal || !$this->fecha_fin) {
            return null;
        }

        return Carbon::now()->diffInDays($this->fecha_fin, false);
    }

    /**
     * Vetar el permiso
     */
    public function vetar(string $motivo, User $modificador): bool
    {
        $estadoAnterior = $this->estado;

        $this->estado = 'vetado';
        $this->motivo_veto = $motivo;
        $this->modificado_por = $modificador->id;
        $this->save();

        // Registrar en historial
        HistorialPermiso::create([
            'permiso_id' => $this->id,
            'user_id' => $this->user_id,
            'modificado_por' => $modificador->id,
            'accion' => 'vetar',
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => 'vetado',
            'motivo' => $motivo,
        ]);

        return true;
    }

    /**
     * Activar el permiso
     */
    public function activar(User $modificador): bool
    {
        $estadoAnterior = $this->estado;

        $this->estado = $this->es_temporal ? 'temporal' : 'activo';
        $this->motivo_veto = null;
        $this->modificado_por = $modificador->id;
        $this->save();

        // Registrar en historial
        HistorialPermiso::create([
            'permiso_id' => $this->id,
            'user_id' => $this->user_id,
            'modificado_por' => $modificador->id,
            'accion' => 'activar',
            'estado_anterior' => $estadoAnterior,
            'estado_nuevo' => $this->estado,
            'motivo' => 'Permiso reactivado',
        ]);

        return true;
    }

    /**
     * Scopes
     */
    public function scopeActivos($query)
    {
        return $query->whereIn('estado', ['activo', 'temporal'])
                    ->where(function ($q) {
                        $q->where('es_temporal', false)
                          ->orWhere(function ($subQ) {
                              $subQ->where('es_temporal', true)
                                   ->where(function ($dateQ) {
                                       $dateQ->whereNull('fecha_inicio')
                                             ->orWhere('fecha_inicio', '<=', Carbon::now());
                                   })
                                   ->where(function ($dateQ) {
                                       $dateQ->whereNull('fecha_fin')
                                             ->orWhere('fecha_fin', '>=', Carbon::now());
                                   });
                          });
                    });
    }

    public function scopeVetados($query)
    {
        return $query->where('estado', 'vetado');
    }

    public function scopeTemporales($query)
    {
        return $query->where('es_temporal', true);
    }

    public function scopeExpirados($query)
    {
        return $query->where('es_temporal', true)
                    ->where('fecha_fin', '<', Carbon::now());
    }

    public function scopePorUsuario($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePorTipo($query, $tipoPermiso)
    {
        return $query->where('tipo_permiso', $tipoPermiso);
    }
}

