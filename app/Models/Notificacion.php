<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    use HasFactory;

    protected $table = 'notificaciones';

    protected $fillable = [
        'user_id',
        'tipo',
        'titulo',
        'mensaje',
        'url',
        'icono',
        'leida',
        'importante',
        'fecha_leida',
        'metadata',
    ];

    protected $casts = [
        'leida' => 'boolean',
        'importante' => 'boolean',
        'fecha_leida' => 'datetime',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function marcarComoLeida(): void
    {
        $this->update([
            'leida' => true,
            'fecha_leida' => now(),
        ]);
    }
}

