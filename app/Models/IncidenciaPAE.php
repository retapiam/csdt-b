<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IncidenciaPAE extends Model
{
    use HasFactory;

    protected $table = 'incidencias_pae';

    protected $fillable = [
        'institucion_id', 'fecha', 'tipo', 'severidad', 'descripcion', 'evidencias'
    ];

    protected $casts = [
        'evidencias' => 'array',
        'fecha' => 'date',
    ];
}


