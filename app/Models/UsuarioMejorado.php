<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class UsuarioMejorado extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'usu';
    protected $primaryKey = 'id';

    protected $fillable = [
        'cod', 'nom', 'ape', 'cor', 'con', 'tel', 'doc', 'tip_doc',
        'fec_nac', 'dir', 'ciu', 'dep', 'gen', 'rol', 'est', 'cor_ver',
        'cor_ver_at', 'ult_ing', 'fot', 'met'
    ];

    protected $hidden = [
        'con', 'remember_token',
    ];

    protected $casts = [
        'cor_ver' => 'boolean',
        'cor_ver_at' => 'datetime',
        'ult_ing' => 'datetime',
        'fec_nac' => 'date',
        'met' => 'array',
    ];

    // ========================================
    // SCOPES
    // ========================================

    public function scopeActivos($query)
    {
        return $query->where('est', 'act');
    }

    public function scopeInactivos($query)
    {
        return $query->where('est', 'ina');
    }

    public function scopePendientes($query)
    {
        return $query->where('est', 'pen');
    }

    public function scopeSuspendidos($query)
    {
        return $query->where('est', 'sus');
    }

    public function scopePorRol($query, $rol)
    {
        return $query->where('rol', $rol);
    }

    public function scopePorCiudad($query, $ciudad)
    {
        return $query->where('ciu', 'like', "%{$ciudad}%");
    }

    public function scopePorDepartamento($query, $departamento)
    {
        return $query->where('dep', 'like', "%{$departamento}%");
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where(function($q) use ($termino) {
            $q->where('nom', 'like', "%{$termino}%")
              ->orWhere('ape', 'like', "%{$termino}%")
              ->orWhere('cor', 'like', "%{$termino}%")
              ->orWhere('doc', 'like', "%{$termino}%");
        });
    }

    // ========================================
    // RELACIONES
    // ========================================

    public function veedurias()
    {
        return $this->hasMany(VeeduriaMejorada::class, 'usu_id');
    }

    public function veeduriasAsignadas()
    {
        return $this->hasMany(VeeduriaMejorada::class, 'ope_id');
    }

    public function donaciones()
    {
        return $this->hasMany(DonacionMejorada::class, 'usu_id');
    }

    public function tareas()
    {
        return $this->hasMany(TareaMejorada::class, 'usu_id');
    }

    public function archivos()
    {
        return $this->hasMany(ArchivoMejorado::class, 'usu_id');
    }

    public function logs()
    {
        return $this->hasMany(LogMejorado::class, 'usu_id');
    }

    // ========================================
    // MÉTODOS DE UTILIDAD
    // ========================================

    public function getNombreCompletoAttribute()
    {
        return "{$this->nom} {$this->ape}";
    }

    public function esAdministradorGeneral()
    {
        return $this->rol === 'adm_gen';
    }

    public function esAdministrador()
    {
        return $this->rol === 'adm';
    }

    public function esOperador()
    {
        return $this->rol === 'ope';
    }

    public function esCliente()
    {
        return $this->rol === 'cli';
    }

    public function estaActivo()
    {
        return $this->est === 'act';
    }

    public function estaPendiente()
    {
        return $this->est === 'pen';
    }

    public function estaSuspendido()
    {
        return $this->est === 'sus';
    }

    public function tieneCorreoVerificado()
    {
        return $this->cor_ver;
    }

    // ========================================
    // MÉTODOS DE VALIDACIÓN
    // ========================================

    public function validarDocumento($documento)
    {
        return $this->doc === $documento;
    }

    public function validarCorreo($correo)
    {
        return $this->cor === $correo;
    }

    public function validarTelefono($telefono)
    {
        return $this->tel === $telefono;
    }

    // ========================================
    // MÉTODOS DE ESTADÍSTICAS
    // ========================================

    public function estadisticas()
    {
        return [
            'veedurias_creadas' => $this->veedurias()->count(),
            'veedurias_asignadas' => $this->veeduriasAsignadas()->count(),
            'donaciones_realizadas' => $this->donaciones()->count(),
            'tareas_asignadas' => $this->tareas()->count(),
            'archivos_subidos' => $this->archivos()->count(),
            'ultima_actividad' => $this->ult_ing,
            'dias_registrado' => $this->created_at->diffInDays(now()),
        ];
    }

    // ========================================
    // MÉTODOS DE ACTUALIZACIÓN
    // ========================================

    public function actualizarUltimoIngreso()
    {
        $this->update(['ult_ing' => now()]);
    }

    public function verificarCorreo()
    {
        $this->update([
            'cor_ver' => true,
            'cor_ver_at' => now()
        ]);
    }

    public function cambiarEstado($nuevoEstado)
    {
        $this->update(['est' => $nuevoEstado]);
    }

    public function cambiarRol($nuevoRol)
    {
        $this->update(['rol' => $nuevoRol]);
    }

    public function cambiarContrasena($nuevaContrasena)
    {
        $this->update(['con' => Hash::make($nuevaContrasena)]);
    }

    // ========================================
    // MÉTODOS DE BÚSQUEDA AVANZADA
    // ========================================

    public static function buscarAvanzado($filtros = [])
    {
        $query = self::query();

        if (isset($filtros['buscar'])) {
            $query->buscar($filtros['buscar']);
        }

        if (isset($filtros['rol'])) {
            $query->porRol($filtros['rol']);
        }

        if (isset($filtros['est'])) {
            $query->where('est', $filtros['est']);
        }

        if (isset($filtros['ciu'])) {
            $query->porCiudad($filtros['ciu']);
        }

        if (isset($filtros['dep'])) {
            $query->porDepartamento($filtros['dep']);
        }

        if (isset($filtros['cor_ver'])) {
            $query->where('cor_ver', $filtros['cor_ver']);
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
            'nombre_completo' => $this->nombre_completo,
            'correo' => $this->cor,
            'telefono' => $this->tel,
            'documento' => $this->doc,
            'tipo_documento' => $this->tip_doc,
            'rol' => $this->rol,
            'estado' => $this->est,
            'ciudad' => $this->ciu,
            'departamento' => $this->dep,
            'correo_verificado' => $this->cor_ver ? 'Sí' : 'No',
            'fecha_registro' => $this->created_at->format('d/m/Y H:i:s'),
            'ultimo_ingreso' => $this->ult_ing ? $this->ult_ing->format('d/m/Y H:i:s') : 'Nunca',
        ];
    }
}
