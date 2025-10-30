<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntregaPAE extends Model
{
    use HasFactory;

    protected $table = 'entregas_pae';

    protected $fillable = [
        'institucion_id', 'menu_id', 'fecha', 'jornada', 'planificado', 'entregado', 'calidad', 'evidencias'
    ];

    protected $casts = [
        'evidencias' => 'array',
        'fecha' => 'date',
    ];
}


