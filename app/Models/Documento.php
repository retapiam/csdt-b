<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Documento extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'entidad_tipo',
        'entidad_id',
        'nombre',
        'descripcion',
        'tipo_documento',
        'mime_type',
        'ruta_archivo',
        'tamano_bytes',
        'hash_archivo',
        'version',
        'estado',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entidad()
    {
        return $this->morphTo('entidad', 'entidad_tipo', 'entidad_id');
    }
}

