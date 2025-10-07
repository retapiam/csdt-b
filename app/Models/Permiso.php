<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Permiso extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'modulo',
        'permiso',
        'puede_ver',
        'puede_crear',
        'puede_editar',
        'puede_eliminar',
    ];

    protected $casts = [
        'puede_ver' => 'boolean',
        'puede_crear' => 'boolean',
        'puede_editar' => 'boolean',
        'puede_eliminar' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tienePermiso(string $accion): bool
    {
        return match($accion) {
            'ver' => $this->puede_ver,
            'crear' => $this->puede_crear,
            'editar' => $this->puede_editar,
            'eliminar' => $this->puede_eliminar,
            default => false,
        };
    }
}

