<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $table = 'activity_logs';

    public $timestamps = false; // Solo tiene created_at

    protected $fillable = [
        'user_id',
        'accion',
        'entidad_tipo',
        'entidad_id',
        'descripcion',
        'ip_address',
        'user_agent',
        'datos_antes',
        'datos_despues',
    ];

    protected $casts = [
        'datos_antes' => 'array',
        'datos_despues' => 'array',
        'created_at' => 'datetime',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
