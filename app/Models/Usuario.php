<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class Usuario extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'usu';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nom',
        'ape', 
        'cor',
        'con',
        'tel',
        'doc',
        'tip_doc',
        'fec_nac',
        'dir',
        'ciu',
        'dep',
        'gen',
        'rol',
        'est',
        'cor_ver',
        'cor_ver_en',
        'ult_acc',
        'not',
        // Campos de profesión y nivel
        'profesion_id',
        'nivel_id',
        'años_experiencia',
        'numero_matricula',
        'entidad_matricula',
        'fecha_matricula',
        'especializaciones',
        'certificaciones',
        'perfil_profesional',
        'perfil_verificado',
        'perfil_verificado_en',
        'verificado_por',
        'estado_verificacion',
        'motivo_rechazo',
        'documentos_adjuntos'
    ];

    protected $hidden = [
        'con',
        'remember_token',
    ];

    protected $casts = [
        'cor_ver' => 'boolean',
        'cor_ver_en' => 'datetime',
        'ult_acc' => 'datetime',
        'fec_nac' => 'date',
        // Campos de profesión y nivel
        'especializaciones' => 'array',
        'certificaciones' => 'array',
        'documentos_adjuntos' => 'array',
        'perfil_verificado' => 'boolean',
        'perfil_verificado_en' => 'datetime',
        'fecha_matricula' => 'date',
        'años_experiencia' => 'integer'
    ];

    // Relaciones
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'usu_rol', 'usu_id', 'rol_id')
                    ->withPivot(['act', 'asig_por', 'asig_en', 'not'])
                    ->withTimestamps();
    }

    public function veedurias()
    {
        return $this->hasMany(Veeduria::class, 'usu_id');
    }

    public function veeduriasAsignadas()
    {
        return $this->hasMany(Veeduria::class, 'ope_id');
    }

    public function donaciones()
    {
        return $this->hasMany(Donacion::class, 'usu_id');
    }

    public function tareasAsignadas()
    {
        return $this->hasMany(Tarea::class, 'asig_a');
    }

    public function tareasCreadas()
    {
        return $this->hasMany(Tarea::class, 'asig_por');
    }

    public function archivos()
    {
        return $this->hasMany(Archivo::class, 'usu_id');
    }

    public function analisisIA()
    {
        return $this->hasMany(AnalisisIA::class, 'usu_id');
    }

    public function narracionesIA()
    {
        return $this->hasMany(NarracionIA::class, 'usu_id');
    }

    public function logs()
    {
        return $this->hasMany(Log::class, 'usu_id');
    }

    // Relaciones de profesión y nivel
    public function profesion()
    {
        return $this->belongsTo(Profesion::class, 'profesion_id');
    }

    public function nivel()
    {
        return $this->belongsTo(NivelUsuario::class, 'nivel_id');
    }

    public function verificadoPor()
    {
        return $this->belongsTo(Usuario::class, 'verificado_por');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('est', 'act');
    }

    public function scopePorRol($query, $rol)
    {
        return $query->where('rol', $rol);
    }

    public function scopeClientes($query)
    {
        return $query->where('rol', 'cli');
    }

    public function scopeOperadores($query)
    {
        return $query->where('rol', 'ope');
    }

    public function scopeAdministradores($query)
    {
        return $query->where('rol', 'adm');
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        return trim($this->nom . ' ' . $this->ape);
    }

    public function getInicialesAttribute()
    {
        $iniciales = '';
        if ($this->nom) $iniciales .= strtoupper(substr($this->nom, 0, 1));
        if ($this->ape) $iniciales .= strtoupper(substr($this->ape, 0, 1));
        return $iniciales;
    }

    // Mutators
    public function setCorAttribute($value)
    {
        $this->attributes['cor'] = strtolower(trim($value));
    }

    public function setNomAttribute($value)
    {
        $this->attributes['nom'] = ucwords(strtolower(trim($value)));
    }

    public function setApeAttribute($value)
    {
        $this->attributes['ape'] = ucwords(strtolower(trim($value)));
    }

    public function setConAttribute($value)
    {
        // Solo hashear si no está ya hasheado
        if (!password_get_info($value)['algo']) {
            $this->attributes['con'] = Hash::make($value);
        } else {
            $this->attributes['con'] = $value;
        }
    }

    // Métodos de utilidad


    public function esCliente()
    {
        return $this->rol === 'cli';
    }

    public function esOperador()
    {
        return $this->rol === 'ope';
    }

    public function esAdministrador()
    {
        return $this->rol === 'adm';
    }

    public function esAdministradorGeneral()
    {
        return $this->rol === 'adm_gen';
    }

    // Métodos simples de verificación
    public function puedeGestionarUsuarios()
    {
        return $this->esAdministradorGeneral() || $this->esAdministrador();
    }

    public function puedeGestionarVeedurias()
    {
        return true; // Todos pueden gestionar veedurías
    }

    public function puedeGestionarDonaciones()
    {
        return true; // Todos pueden gestionar donaciones
    }

    public function puedeGestionarTareas()
    {
        return $this->esAdministradorGeneral() || $this->esAdministrador() || $this->esOperador();
    }

    public function actualizarUltimoAcceso()
    {
        $this->update(['ult_acc' => now()]);
    }

    public function verificarCorreo()
    {
        $this->update([
            'cor_ver' => true,
            'cor_ver_en' => now()
        ]);
    }

    public function activarCuenta()
    {
        $this->update(['est' => 'act']);
    }

    // Reglas de validación
    public static function reglas()
    {
        return [
            'nom' => 'required|string|max:100',
            'ape' => 'required|string|max:100',
            'cor' => 'required|email|unique:usu,cor',
            'con' => 'required|string|min:8',
            'con_confirmation' => 'required_with:con|same:con',
            'tel' => 'nullable|string|max:20',
            'doc' => 'required|string|max:20|unique:usu,doc',
            'tip_doc' => 'required|string|in:cc,ce,ti,pp,nit',
            'fec_nac' => 'nullable|date|before:today',
            'dir' => 'nullable|string|max:255',
            'ciu' => 'nullable|string|max:100',
            'dep' => 'nullable|string|max:100',
            'gen' => 'nullable|string|in:m,f,o,n',
            'rol' => 'required|string|in:cli,ope,adm',
            'est' => 'nullable|string|in:act,ina,sus,pen',
        ];
    }

    // Mensajes de validación
    public static function mensajes()
    {
        return [
            'nom.required' => 'El nombre es obligatorio',
            'nom.max' => 'El nombre no puede tener más de 100 caracteres',
            'ape.required' => 'El apellido es obligatorio',
            'ape.max' => 'El apellido no puede tener más de 100 caracteres',
            'cor.required' => 'El correo electrónico es obligatorio',
            'cor.email' => 'El correo electrónico debe tener un formato válido',
            'cor.unique' => 'Este correo electrónico ya está registrado',
            'con.required' => 'La contraseña es obligatoria',
            'con.min' => 'La contraseña debe tener al menos 8 caracteres',
            'con_confirmation.required_with' => 'La confirmación de contraseña es obligatoria',
            'con_confirmation.same' => 'Las contraseñas no coinciden',
            'doc.required' => 'El documento es obligatorio',
            'doc.unique' => 'Este documento ya está registrado',
            'tip_doc.required' => 'El tipo de documento es obligatorio',
            'tip_doc.in' => 'El tipo de documento no es válido',
            'rol.required' => 'El rol es obligatorio',
            'rol.in' => 'El rol no es válido',
        ];
    }

    /**
     * Verificar si el usuario tiene un permiso específico - SIMPLE
     */
    public function tienePermiso($permiso)
    {
        // Solo verificar por rol - súper simple
        if ($this->esAdministradorGeneral()) {
            return true;
        }

        // Lógica simple por rol
        switch ($this->rol) {
            case 'adm':
                return in_array($permiso, ['usuarios', 'veedurias', 'donaciones', 'tareas', 'archivos']);
            case 'ope':
                return in_array($permiso, ['veedurias', 'tareas', 'archivos']);
            case 'cli':
                return in_array($permiso, ['veedurias', 'donaciones']);
            default:
                return false;
        }
    }

    /**
     * Verificar si el usuario puede acceder a un recurso específico
     */
    public function puedeAccederA($recurso)
    {
        // Lógica simple basada en el tipo de usuario
        switch ($this->rol) {
            case 'adm_gen':
                return true; // Acceso total
            case 'adm':
                return in_array($recurso, ['usuarios', 'veedurias', 'donaciones', 'tareas', 'archivos', 'configuraciones', 'logs']);
            case 'ope':
                return in_array($recurso, ['veedurias', 'tareas', 'archivos']);
            case 'cli':
                return in_array($recurso, ['veedurias', 'donaciones']);
            default:
                return false;
        }
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function tieneRol($rol)
    {
        return $this->rol === $rol || $this->esAdministradorGeneral();
    }

    // Métodos de profesión y nivel
    public function tieneProfesion()
    {
        return !is_null($this->profesion_id);
    }

    public function tieneNivel()
    {
        return !is_null($this->nivel_id);
    }

    public function perfilCompleto()
    {
        return $this->tieneProfesion() && $this->tieneNivel() && $this->perfil_verificado;
    }

    public function puedeAsignarNivel($nivel)
    {
        if (is_object($nivel)) {
            return $nivel->puedeSerAsignadoA($this);
        }
        
        $nivelModel = NivelUsuario::find($nivel);
        return $nivelModel ? $nivelModel->puedeSerAsignadoA($this) : false;
    }

    public function obtenerEspecializaciones()
    {
        return $this->especializaciones ?? [];
    }

    public function obtenerCertificaciones()
    {
        return $this->certificaciones ?? [];
    }

    public function obtenerDocumentosAdjuntos()
    {
        return $this->documentos_adjuntos ?? [];
    }

    public function verificarPerfil($verificadoPor = null)
    {
        $this->update([
            'perfil_verificado' => true,
            'perfil_verificado_en' => now(),
            'verificado_por' => $verificadoPor ?? auth()->id(),
            'estado_verificacion' => 'verificado'
        ]);
    }

    public function rechazarPerfil($motivo, $verificadoPor = null)
    {
        $this->update([
            'perfil_verificado' => false,
            'perfil_verificado_en' => null,
            'verificado_por' => $verificadoPor ?? auth()->id(),
            'estado_verificacion' => 'rechazado',
            'motivo_rechazo' => $motivo
        ]);
    }

    public function solicitarVerificacion()
    {
        $this->update(['estado_verificacion' => 'pendiente']);
    }
}