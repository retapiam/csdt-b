<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuPAE extends Model
{
    use HasFactory;

    protected $table = 'menus_pae';

    protected $fillable = [
        'institucion_id', 'nombre', 'componentes', 'calorias', 'restricciones_culturales'
    ];

    protected $casts = [
        'componentes' => 'array',
        'restricciones_culturales' => 'array',
    ];
}


