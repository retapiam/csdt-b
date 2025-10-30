<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institucion extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'codigo_dane', 'municipio', 'departamento', 'etnia_predominante', 'direccion', 'telefono'
    ];
}


