<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialPermiso extends Model
{
    use HasFactory;

    protected $table = 'historial_permisos';

    protected $fillable = [
        'permiso_id',
        'user_id',
        'modificado_por',
        'accion',
        'estado_anterior',
        'estado_nuevo',
        'datos_anteriores',
        'datos_nuevos',
        'motivo',
    ];

    protected $casts = [
        'datos_anteriores' => 'array',
        'datos_nuevos' => 'array',
    ];

    // Relaciones
    public function permiso()
    {
        return $this->belongsTo(PermisoUsuario::class, 'permiso_id');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function modificador()
    {
        return $this->belongsTo(User::class, 'modificado_por');
    }
}

