<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Modelo mejorado para permisos
 * Implementa nomenclatura estándar según CONTROL.md
 */
class PermisoMejorado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'perm';
    protected $primaryKey = 'id';

    protected $fillable = [
        'nom',        // Nombre del permiso
        'slug',       // Slug único del permiso
        'des',        // Descripción del permiso
        'mod',        // Módulo al que pertenece
        'rec',        // Recurso
        'acc',        // Acción
        'est',        // Estado del permiso
        'niv'         // Nivel requerido
    ];

    protected $casts = [
        'niv' => 'integer',
    ];

    protected $attributes = [
        'est' => 'act',
        'niv' => 1,
    ];

    // Relaciones
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'rol_perm', 'perm_id', 'rol_id')
                    ->withPivot(['otorgado', 'asig_en', 'asig_por'])
                    ->withTimestamps();
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('est', 'act');
    }

    public function scopeInactivos($query)
    {
        return $query->where('est', 'ina');
    }

    public function scopePorModulo($query, $modulo)
    {
        return $query->where('mod', $modulo);
    }

    public function scopePorRecurso($query, $recurso)
    {
        return $query->where('rec', $recurso);
    }

    public function scopePorAccion($query, $accion)
    {
        return $query->where('acc', $accion);
    }

    public function scopePorNivel($query, $nivel)
    {
        return $query->where('niv', $nivel);
    }

    public function scopePorSlug($query, $slug)
    {
        return $query->where('slug', $slug);
    }

    // Accessors
    public function getModuloDescripcionAttribute()
    {
        $modulos = [
            'usuarios' => 'Gestión de Usuarios',
            'roles' => 'Gestión de Roles',
            'permisos' => 'Gestión de Permisos',
            'veedurias' => 'Gestión de Veedurías',
            'donaciones' => 'Gestión de Donaciones',
            'tareas' => 'Gestión de Tareas',
            'archivos' => 'Gestión de Archivos',
            'configuracion' => 'Configuración del Sistema',
            'logs' => 'Logs y Auditoría',
            'estadisticas' => 'Estadísticas',
            'ia' => 'Inteligencia Artificial',
            'pqrsfd' => 'PQRSFD'
        ];
        return $modulos[$this->mod] ?? ucfirst($this->mod);
    }

    public function getRecursoDescripcionAttribute()
    {
        $recursos = [
            'usuario' => 'Usuario',
            'rol' => 'Rol',
            'permiso' => 'Permiso',
            'veeduria' => 'Veeduría',
            'donacion' => 'Donación',
            'tarea' => 'Tarea',
            'archivo' => 'Archivo',
            'configuracion' => 'Configuración',
            'log' => 'Log',
            'estadistica' => 'Estadística',
            'analisis' => 'Análisis',
            'narracion' => 'Narración',
            'pqrsfd' => 'PQRSFD'
        ];
        return $recursos[$this->rec] ?? ucfirst($this->rec);
    }

    public function getAccionDescripcionAttribute()
    {
        $acciones = [
            'crear' => 'Crear',
            'leer' => 'Leer/Ver',
            'actualizar' => 'Actualizar/Editar',
            'eliminar' => 'Eliminar',
            'buscar' => 'Buscar',
            'validar' => 'Validar',
            'asignar' => 'Asignar',
            'activar' => 'Activar',
            'desactivar' => 'Desactivar',
            'radicar' => 'Radicar',
            'cerrar' => 'Cerrar',
            'cancelar' => 'Cancelar',
            'confirmar' => 'Confirmar',
            'restaurar' => 'Restaurar',
            'exportar' => 'Exportar',
            'importar' => 'Importar',
            'analizar' => 'Analizar',
            'generar' => 'Generar'
        ];
        return $acciones[$this->acc] ?? ucfirst($this->acc);
    }

    public function getNombreCompletoAttribute()
    {
        return $this->accion_descripcion . ' ' . $this->recurso_descripcion;
    }

    // Mutators
    public function setNomAttribute($value)
    {
        $this->attributes['nom'] = ucfirst(trim($value));
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = strtolower(trim($value));
    }

    public function setModAttribute($value)
    {
        $this->attributes['mod'] = strtolower(trim($value));
    }

    public function setRecAttribute($value)
    {
        $this->attributes['rec'] = strtolower(trim($value));
    }

    public function setAccAttribute($value)
    {
        $this->attributes['acc'] = strtolower(trim($value));
    }

    // Métodos de utilidad
    public function activar()
    {
        $this->update(['est' => 'act']);
    }

    public function desactivar()
    {
        $this->update(['est' => 'ina']);
    }

    public function esActivo()
    {
        return $this->est === 'act';
    }

    public function esInactivo()
    {
        return $this->est === 'ina';
    }

    public function tieneNivel($nivel)
    {
        return $this->niv <= $nivel;
    }

    public function esDelModulo($modulo)
    {
        return $this->mod === $modulo;
    }

    public function esDelRecurso($recurso)
    {
        return $this->rec === $recurso;
    }

    public function esDeLaAccion($accion)
    {
        return $this->acc === $accion;
    }

    // Métodos estáticos
    public static function obtenerPorModulo($modulo)
    {
        return self::activos()
            ->porModulo($modulo)
            ->orderBy('rec')
            ->orderBy('acc')
            ->get();
    }

    public static function obtenerPorRecurso($recurso)
    {
        return self::activos()
            ->porRecurso($recurso)
            ->orderBy('acc')
            ->get();
    }

    public static function obtenerPorSlug($slug)
    {
        return self::activos()
            ->porSlug($slug)
            ->first();
    }

    public static function crearPermiso($datos)
    {
        return self::create([
            'nom' => $datos['nom'],
            'slug' => $datos['slug'],
            'des' => $datos['des'] ?? null,
            'mod' => $datos['mod'],
            'rec' => $datos['rec'],
            'acc' => $datos['acc'],
            'est' => $datos['est'] ?? 'act',
            'niv' => $datos['niv'] ?? 1
        ]);
    }

    public static function obtenerTodosActivos()
    {
        return self::activos()
            ->orderBy('mod')
            ->orderBy('rec')
            ->orderBy('acc')
            ->get();
    }

    public static function obtenerPorNivel($nivel)
    {
        return self::activos()
            ->porNivel($nivel)
            ->get();
    }

    // Reglas de validación
    public static function reglas($id = null)
    {
        return [
            'nom' => 'required|string|max:100',
            'slug' => 'required|string|max:100|unique:perm,slug' . ($id ? ',' . $id : ''),
            'des' => 'nullable|string|max:255',
            'mod' => 'required|string|max:50',
            'rec' => 'required|string|max:50',
            'acc' => 'required|string|max:50',
            'est' => 'in:act,ina',
            'niv' => 'integer|min:1|max:5'
        ];
    }

    public static function mensajes()
    {
        return [
            'nom.required' => 'El nombre del permiso es obligatorio.',
            'nom.max' => 'El nombre no puede exceder 100 caracteres.',
            'slug.required' => 'El slug es obligatorio.',
            'slug.unique' => 'Este slug ya está en uso.',
            'slug.max' => 'El slug no puede exceder 100 caracteres.',
            'des.max' => 'La descripción no puede exceder 255 caracteres.',
            'mod.required' => 'El módulo es obligatorio.',
            'mod.max' => 'El módulo no puede exceder 50 caracteres.',
            'rec.required' => 'El recurso es obligatorio.',
            'rec.max' => 'El recurso no puede exceder 50 caracteres.',
            'acc.required' => 'La acción es obligatoria.',
            'acc.max' => 'La acción no puede exceder 50 caracteres.',
            'est.in' => 'El estado debe ser activo o inactivo.',
            'niv.integer' => 'El nivel debe ser un número entero.',
            'niv.min' => 'El nivel debe ser al menos 1.',
            'niv.max' => 'El nivel no puede ser mayor a 5.'
        ];
    }

    // Métodos para generar permisos automáticamente
    public static function generarPermisosCRUD($modulo, $recurso, $descripcion = null)
    {
        $permisos = [
            [
                'nom' => 'Crear ' . ucfirst($recurso),
                'slug' => $modulo . '_' . $recurso . '_crear',
                'des' => 'Crear nuevos ' . ($descripcion ?? $recurso),
                'mod' => $modulo,
                'rec' => $recurso,
                'acc' => 'crear',
                'niv' => 2
            ],
            [
                'nom' => 'Leer ' . ucfirst($recurso),
                'slug' => $modulo . '_' . $recurso . '_leer',
                'des' => 'Ver información de ' . ($descripcion ?? $recurso),
                'mod' => $modulo,
                'rec' => $recurso,
                'acc' => 'leer',
                'niv' => 1
            ],
            [
                'nom' => 'Actualizar ' . ucfirst($recurso),
                'slug' => $modulo . '_' . $recurso . '_actualizar',
                'des' => 'Modificar ' . ($descripcion ?? $recurso) . ' existentes',
                'mod' => $modulo,
                'rec' => $recurso,
                'acc' => 'actualizar',
                'niv' => 2
            ],
            [
                'nom' => 'Eliminar ' . ucfirst($recurso),
                'slug' => $modulo . '_' . $recurso . '_eliminar',
                'des' => 'Eliminar ' . ($descripcion ?? $recurso),
                'mod' => $modulo,
                'rec' => $recurso,
                'acc' => 'eliminar',
                'niv' => 3
            ],
            [
                'nom' => 'Buscar ' . ucfirst($recurso),
                'slug' => $modulo . '_' . $recurso . '_buscar',
                'des' => 'Buscar ' . ($descripcion ?? $recurso),
                'mod' => $modulo,
                'rec' => $recurso,
                'acc' => 'buscar',
                'niv' => 1
            ]
        ];

        $permisosCreados = [];
        foreach ($permisos as $permiso) {
            $permisosCreados[] = self::create($permiso);
        }

        return $permisosCreados;
    }

    public static function generarPermisosEspeciales($modulo, $recurso, $acciones, $descripcion = null)
    {
        $permisosCreados = [];
        foreach ($acciones as $accion) {
            $permiso = [
                'nom' => ucfirst($accion) . ' ' . ucfirst($recurso),
                'slug' => $modulo . '_' . $recurso . '_' . $accion,
                'des' => $descripcion ? $descripcion : ucfirst($accion) . ' ' . ($descripcion ?? $recurso),
                'mod' => $modulo,
                'rec' => $recurso,
                'acc' => $accion,
                'niv' => 2
            ];
            $permisosCreados[] = self::create($permiso);
        }
        return $permisosCreados;
    }
}