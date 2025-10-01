<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pagina extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'paginas';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nombre',
        'ruta',
        'carpeta',
        'descripcion',
        'tipo',
        'estado',
        'orden',
        'icono',
        'es_publica',
        'requiere_autenticacion',
        'permisos_requeridos'
    ];

    protected $casts = [
        'es_publica' => 'boolean',
        'requiere_autenticacion' => 'boolean',
        'permisos_requeridos' => 'array',
        'orden' => 'integer',
    ];

    // Constantes para tipos de página
    const TIPO_LIBRE = 'libre';
    const TIPO_COMPARTIDA = 'compartida';
    const TIPO_PRIVADA = 'privada';
    const TIPO_ADMINISTRATIVA = 'administrativa';

    // Constantes para estados
    const ESTADO_ACTIVA = 'activa';
    const ESTADO_INACTIVA = 'inactiva';
    const ESTADO_BLOQUEADA = 'bloqueada';

    // Constantes para carpetas
    const CARPETA_01 = '01';
    const CARPETA_02 = '02';
    const CARPETA_03 = '03';
    const CARPETA_04 = '04';
    const CARPETA_05 = '05';
    const CARPETA_06 = '06';
    const CARPETA_07 = '07';
    const CARPETA_08 = '08';
    const CARPETA_09 = '09';
    const CARPETA_10 = '10';
    const CARPETA_CLIENTE = '11-cliente';
    const CARPETA_OPERADOR = '12-operador';
    const CARPETA_ADMINISTRADOR = '13-administrador';
    const CARPETA_ADMINISTRADOR_GENERAL = '14-administrador-general';

    // Relaciones
    public function permisos()
    {
        return $this->belongsToMany(PermisoMejorado::class, 'pagina_permisos', 'pagina_id', 'permiso_id')
                    ->withTimestamps();
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'pagina_roles', 'pagina_id', 'rol_id')
                    ->withPivot(['activo', 'asignado_por', 'asignado_en'])
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('estado', self::ESTADO_ACTIVA);
    }

    public function scopePublicas($query)
    {
        return $query->where('es_publica', true);
    }

    public function scopePorCarpeta($query, $carpeta)
    {
        return $query->where('carpeta', $carpeta);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopeOrdenadas($query)
    {
        return $query->orderBy('orden');
    }

    // Métodos estáticos para crear páginas del sistema
    public static function crearPaginasSistema()
    {
        $paginas = [
            // Páginas libres (carpetas 01-10)
            ['nombre' => 'Inicio', 'ruta' => '/', 'carpeta' => self::CARPETA_01, 'tipo' => self::TIPO_LIBRE, 'es_publica' => true, 'orden' => 1, 'icono' => 'home'],
            ['nombre' => 'Acerca de', 'ruta' => '/acerca', 'carpeta' => self::CARPETA_02, 'tipo' => self::TIPO_LIBRE, 'es_publica' => true, 'orden' => 2, 'icono' => 'info'],
            ['nombre' => 'Servicios', 'ruta' => '/servicios', 'carpeta' => self::CARPETA_03, 'tipo' => self::TIPO_LIBRE, 'es_publica' => true, 'orden' => 3, 'icono' => 'services'],
            ['nombre' => 'Contacto', 'ruta' => '/contacto', 'carpeta' => self::CARPETA_04, 'tipo' => self::TIPO_LIBRE, 'es_publica' => true, 'orden' => 4, 'icono' => 'contact'],
            ['nombre' => 'Noticias', 'ruta' => '/noticias', 'carpeta' => self::CARPETA_05, 'tipo' => self::TIPO_LIBRE, 'es_publica' => true, 'orden' => 5, 'icono' => 'news'],
            ['nombre' => 'Eventos', 'ruta' => '/eventos', 'carpeta' => self::CARPETA_06, 'tipo' => self::TIPO_LIBRE, 'es_publica' => true, 'orden' => 6, 'icono' => 'events'],
            ['nombre' => 'Galería', 'ruta' => '/galeria', 'carpeta' => self::CARPETA_07, 'tipo' => self::TIPO_LIBRE, 'es_publica' => true, 'orden' => 7, 'icono' => 'gallery'],
            ['nombre' => 'Documentos', 'ruta' => '/documentos', 'carpeta' => self::CARPETA_08, 'tipo' => self::TIPO_LIBRE, 'es_publica' => true, 'orden' => 8, 'icono' => 'documents'],
            ['nombre' => 'FAQ', 'ruta' => '/faq', 'carpeta' => self::CARPETA_09, 'tipo' => self::TIPO_LIBRE, 'es_publica' => true, 'orden' => 9, 'icono' => 'help'],
            ['nombre' => 'Políticas', 'ruta' => '/politicas', 'carpeta' => self::CARPETA_10, 'tipo' => self::TIPO_LIBRE, 'es_publica' => true, 'orden' => 10, 'icono' => 'policy'],

            // Páginas compartidas (carpeta 11-cliente)
            ['nombre' => 'Mi Perfil', 'ruta' => '/perfil', 'carpeta' => self::CARPETA_CLIENTE, 'tipo' => self::TIPO_COMPARTIDA, 'es_publica' => false, 'requiere_autenticacion' => true, 'orden' => 11, 'icono' => 'user'],
            ['nombre' => 'Mis Veedurías', 'ruta' => '/mis-veedurias', 'carpeta' => self::CARPETA_CLIENTE, 'tipo' => self::TIPO_COMPARTIDA, 'es_publica' => false, 'requiere_autenticacion' => true, 'orden' => 12, 'icono' => 'veeduria'],
            ['nombre' => 'Mis Donaciones', 'ruta' => '/mis-donaciones', 'carpeta' => self::CARPETA_CLIENTE, 'tipo' => self::TIPO_COMPARTIDA, 'es_publica' => false, 'requiere_autenticacion' => true, 'orden' => 13, 'icono' => 'donation'],

            // Páginas de operador (carpeta 12-operador)
            ['nombre' => 'Panel Operador', 'ruta' => '/operador', 'carpeta' => self::CARPETA_OPERADOR, 'tipo' => self::TIPO_PRIVADA, 'es_publica' => false, 'requiere_autenticacion' => true, 'orden' => 14, 'icono' => 'dashboard'],
            ['nombre' => 'Veedurías Asignadas', 'ruta' => '/operador/veedurias', 'carpeta' => self::CARPETA_OPERADOR, 'tipo' => self::TIPO_PRIVADA, 'es_publica' => false, 'requiere_autenticacion' => true, 'orden' => 15, 'icono' => 'veeduria-assigned'],

            // Páginas de administrador (carpeta 13-administrador)
            ['nombre' => 'Panel Administrador', 'ruta' => '/admin', 'carpeta' => self::CARPETA_ADMINISTRADOR, 'tipo' => self::TIPO_ADMINISTRATIVA, 'es_publica' => false, 'requiere_autenticacion' => true, 'orden' => 16, 'icono' => 'admin'],
            ['nombre' => 'Gestión de Usuarios', 'ruta' => '/admin/usuarios', 'carpeta' => self::CARPETA_ADMINISTRADOR, 'tipo' => self::TIPO_ADMINISTRATIVA, 'es_publica' => false, 'requiere_autenticacion' => true, 'orden' => 17, 'icono' => 'users'],
            ['nombre' => 'Gestión de Veedurías', 'ruta' => '/admin/veedurias', 'carpeta' => self::CARPETA_ADMINISTRADOR, 'tipo' => self::TIPO_ADMINISTRATIVA, 'es_publica' => false, 'requiere_autenticacion' => true, 'orden' => 18, 'icono' => 'veeduria-manage'],
            ['nombre' => 'Panel de Vista', 'ruta' => '/admin/panel-vista', 'carpeta' => self::CARPETA_ADMINISTRADOR, 'tipo' => self::TIPO_ADMINISTRATIVA, 'es_publica' => false, 'requiere_autenticacion' => true, 'orden' => 19, 'icono' => 'view-panel'],

            // Páginas de administrador general (carpeta 14-administrador-general)
            ['nombre' => 'Panel Administrador General', 'ruta' => '/admin-general', 'carpeta' => self::CARPETA_ADMINISTRADOR_GENERAL, 'tipo' => self::TIPO_ADMINISTRATIVA, 'es_publica' => false, 'requiere_autenticacion' => true, 'orden' => 20, 'icono' => 'admin-general'],
            ['nombre' => 'Gestión de Roles', 'ruta' => '/admin-general/roles', 'carpeta' => self::CARPETA_ADMINISTRADOR_GENERAL, 'tipo' => self::TIPO_ADMINISTRATIVA, 'es_publica' => false, 'requiere_autenticacion' => true, 'orden' => 21, 'icono' => 'roles'],
            ['nombre' => 'Gestión de Permisos', 'ruta' => '/admin-general/permisos', 'carpeta' => self::CARPETA_ADMINISTRADOR_GENERAL, 'tipo' => self::TIPO_ADMINISTRATIVA, 'es_publica' => false, 'requiere_autenticacion' => true, 'orden' => 22, 'icono' => 'permissions'],
            ['nombre' => 'Configuración del Sistema', 'ruta' => '/admin-general/configuracion', 'carpeta' => self::CARPETA_ADMINISTRADOR_GENERAL, 'tipo' => self::TIPO_ADMINISTRATIVA, 'es_publica' => false, 'requiere_autenticacion' => true, 'orden' => 23, 'icono' => 'settings'],
        ];

        foreach ($paginas as $pagina) {
            self::firstOrCreate(
                ['ruta' => $pagina['ruta']],
                array_merge($pagina, [
                    'estado' => self::ESTADO_ACTIVA,
                    'descripcion' => $pagina['descripcion'] ?? '',
                    'permisos_requeridos' => []
                ])
            );
        }
    }

    // Métodos de utilidad
    public function activar()
    {
        $this->update(['estado' => self::ESTADO_ACTIVA]);
    }

    public function desactivar()
    {
        $this->update(['estado' => self::ESTADO_INACTIVA]);
    }

    public function bloquear()
    {
        $this->update(['estado' => self::ESTADO_BLOQUEADA]);
    }

    public function estaActiva()
    {
        return $this->estado === self::ESTADO_ACTIVA;
    }

    public function esPublica()
    {
        return $this->es_publica;
    }

    public function requiereAutenticacion()
    {
        return $this->requiere_autenticacion;
    }

    // Verificar si un usuario puede acceder a esta página
    public function puedeAcceder(Usuario $usuario)
    {
        // Si es pública y no requiere autenticación, todos pueden acceder
        if ($this->es_publica && !$this->requiere_autenticacion) {
            return true;
        }

        // Si requiere autenticación, el usuario debe estar autenticado
        if ($this->requiere_autenticacion && !$usuario) {
            return false;
        }

        // Verificar permisos específicos
        if (!empty($this->permisos_requeridos)) {
            foreach ($this->permisos_requeridos as $permiso) {
                if (!$usuario->tienePermiso($permiso)) {
                    return false;
                }
            }
        }

        // Verificar roles específicos
        $rolesPermitidos = $this->roles()->wherePivot('activo', true)->pluck('nom')->toArray();
        if (!empty($rolesPermitidos)) {
            $tieneRolPermitido = false;
            foreach ($rolesPermitidos as $rol) {
                if ($usuario->tieneRol($rol)) {
                    $tieneRolPermitido = true;
                    break;
                }
            }
            if (!$tieneRolPermitido) {
                return false;
            }
        }

        return true;
    }

    // Obtener páginas accesibles para un usuario
    public static function obtenerPaginasAccesibles(Usuario $usuario)
    {
        return self::activas()
            ->where(function($query) use ($usuario) {
                $query->where('es_publica', true)
                      ->orWhereHas('roles', function($q) use ($usuario) {
                          $q->where('nom', $usuario->rol)
                            ->wherePivot('activo', true);
                      });
            })
            ->ordenadas()
            ->get()
            ->filter(function($pagina) use ($usuario) {
                return $pagina->puedeAcceder($usuario);
            });
    }

    // Reglas de validación
    public static function reglas($id = null)
    {
        return [
            'nombre' => 'required|string|max:100',
            'ruta' => 'required|string|max:255|unique:paginas,ruta,' . $id,
            'carpeta' => 'required|string|max:50',
            'descripcion' => 'nullable|string|max:500',
            'tipo' => 'required|string|in:' . implode(',', [self::TIPO_LIBRE, self::TIPO_COMPARTIDA, self::TIPO_PRIVADA, self::TIPO_ADMINISTRATIVA]),
            'estado' => 'required|string|in:' . implode(',', [self::ESTADO_ACTIVA, self::ESTADO_INACTIVA, self::ESTADO_BLOQUEADA]),
            'orden' => 'nullable|integer|min:0',
            'icono' => 'nullable|string|max:50',
            'es_publica' => 'boolean',
            'requiere_autenticacion' => 'boolean',
            'permisos_requeridos' => 'nullable|array',
        ];
    }

    public static function mensajes()
    {
        return [
            'nombre.required' => 'El nombre de la página es obligatorio',
            'ruta.required' => 'La ruta de la página es obligatoria',
            'ruta.unique' => 'Esta ruta ya existe',
            'carpeta.required' => 'La carpeta es obligatoria',
            'tipo.required' => 'El tipo de página es obligatorio',
            'tipo.in' => 'El tipo de página no es válido',
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado no es válido',
        ];
    }
}
