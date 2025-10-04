<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profesion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'profesiones';
    protected $primaryKey = 'id';

    protected $fillable = [
        'codigo',
        'nombre',
        'descripcion',
        'categoria',
        'nivel_minimo',
        'nivel_maximo',
        'habilidades_requeridas',
        'permisos_especiales',
        'requiere_matricula',
        'entidad_matricula',
        'estado'
    ];

    protected $casts = [
        'habilidades_requeridas' => 'array',
        'permisos_especiales' => 'array',
        'requiere_matricula' => 'boolean',
        'nivel_minimo' => 'integer',
        'nivel_maximo' => 'integer'
    ];

    // Relaciones
    public function usuarios()
    {
        return $this->hasMany(Usuario::class, 'profesion_id');
    }

    public function nivelesPermitidos()
    {
        return $this->hasMany(NivelUsuario::class, 'categoria', 'categoria')
                    ->where('numero_nivel', '>=', $this->nivel_minimo)
                    ->where('numero_nivel', '<=', $this->nivel_maximo);
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

    public function scopeQueRequierenMatricula($query)
    {
        return $query->where('requiere_matricula', true);
    }

    // Métodos de utilidad
    public function esValidaParaNivel($nivel)
    {
        return $nivel >= $this->nivel_minimo && $nivel <= $this->nivel_maximo;
    }

    public function obtenerHabilidades()
    {
        return $this->habilidades_requeridas ?? [];
    }

    public function obtenerPermisosEspeciales()
    {
        return $this->permisos_especiales ?? [];
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
        $uniqueCodigo = 'unique:profesiones,codigo';
        if ($id) {
            $uniqueCodigo .= ',' . $id;
        }

        return [
            'codigo' => 'required|string|max:20|' . $uniqueCodigo,
            'nombre' => 'required|string|max:100',
            'descripcion' => 'nullable|string',
            'categoria' => 'required|string|max:50',
            'nivel_minimo' => 'required|integer|min:1|max:5',
            'nivel_maximo' => 'required|integer|min:1|max:5|gte:nivel_minimo',
            'habilidades_requeridas' => 'nullable|array',
            'permisos_especiales' => 'nullable|array',
            'requiere_matricula' => 'boolean',
            'entidad_matricula' => 'nullable|string|max:100',
            'estado' => 'required|in:activo,inactivo,en_revision'
        ];
    }

    // Mensajes de validación
    public static function mensajes()
    {
        return [
            'codigo.required' => 'El código de la profesión es obligatorio',
            'codigo.unique' => 'Este código de profesión ya existe',
            'nombre.required' => 'El nombre de la profesión es obligatorio',
            'categoria.required' => 'La categoría es obligatoria',
            'nivel_minimo.required' => 'El nivel mínimo es obligatorio',
            'nivel_maximo.gte' => 'El nivel máximo debe ser mayor o igual al nivel mínimo',
            'estado.required' => 'El estado es obligatorio',
            'estado.in' => 'El estado debe ser activo, inactivo o en_revision'
        ];
    }
}
