<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class NivelUsuario extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'niveles_usuario';
    protected $primaryKey = 'id';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'numero_nivel',
        'categoria',
        'permisos_por_defecto',
        'restricciones',
        'experiencia_requerida',
        'requiere_aprobacion',
        'estado'
    ];

    protected $casts = [
        'permisos_por_defecto' => 'array',
        'restricciones' => 'array',
        'numero_nivel' => 'integer',
        'experiencia_requerida' => 'integer',
        'requiere_aprobacion' => 'boolean'
    ];

    // Relaciones
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'nivel_id');
    }

    public function profesionesCompatibles()
    {
        return $this->hasMany(Profesion::class, 'categoria', 'categoria')
                    ->where('nivel_minimo', '<=', $this->numero_nivel)
                    ->where('nivel_maximo', '>=', $this->numero_nivel);
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePorCategoria($query, $categoria)
    {
        return $query->where('categoria', $categoria);
    }

    public function scopePorNumero($query, $numero)
    {
        return $query->where('numero_nivel', $numero);
    }

    public function scopeQueNoRequierenAprobacion($query)
    {
        return $query->where('requiere_aprobacion', false);
    }

    // Métodos de utilidad
    public function obtenerPermisosPorDefecto()
    {
        return $this->permisos_por_defecto ?? [];
    }

    public function obtenerRestricciones()
    {
        return $this->restricciones ?? [];
    }

    public function esCompatibleConProfesion($profesion)
    {
        if (is_object($profesion)) {
            return $profesion->esValidaParaNivel($this->numero_nivel);
        }
        
        $profesionModel = Profesion::find($profesion);
        return $profesionModel ? $profesionModel->esValidaParaNivel($this->numero_nivel) : false;
    }

    public function puedeSerAsignadoA($usuario)
    {
        // Verificar experiencia requerida
        if ($this->experiencia_requerida > 0) {
            $experienciaUsuario = $usuario->años_experiencia ?? 0;
            if ($experienciaUsuario < $this->experiencia_requerida) {
                return false;
            }
        }

        return true;
    }

    public function activar()
    {
        $this->update(['estado' => 'activo']);
    }

    public function desactivar()
    {
        $this->update(['estado' => 'inactivo']);
    }

    // Reglas de validación
    public static function reglas($id = null)
    {
        $uniqueCodigo = 'unique:niveles_usuario,codigo';
        if ($id) {
            $uniqueCodigo .= ',' . $id;
        }

        return [
            'codigo' => 'required|string|max:20|' . $uniqueCodigo,
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'numero_nivel' => 'required|integer|min:1|max:5',
            'categoria' => 'required|string|max:50',
            'permisos_por_defecto' => 'nullable|array',
            'restricciones' => 'nullable|array',
            'experiencia_requerida' => 'integer|min:0|max:50',
            'requiere_aprobacion' => 'boolean',
            'estado' => 'required|in:activo,inactivo,beta'
        ];
    }

    // Mensajes de validación
    public static function mensajes()
    {
        return [
            'codigo.required' => 'El código del nivel es obligatorio',
            'codigo.unique' => 'Este código de nivel ya existe',
            'nombre.required' => 'El nombre del nivel es obligatorio',
            'numero_nivel.required' => 'El número del nivel es obligatorio',
            'numero_nivel.min' => 'El número del nivel debe ser al menos 1',
            'numero_nivel.max' => 'El número del nivel no puede ser mayor a 5',
            'categoria.required' => 'La categoría es obligatoria',
            'experiencia_requerida.min' => 'La experiencia requerida no puede ser negativa',
            'experiencia_requerida.max' => 'La experiencia requerida no puede ser mayor a 50 años',
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado debe ser activo, inactivo o beta'
        ];
    }
}
