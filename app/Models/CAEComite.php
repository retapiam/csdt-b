<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CAEComite extends Model
{
    use HasFactory;

    protected $table = 'cae_comites';

    protected $fillable = [
        'institucion_id', 'nombre', 'miembros'
    ];

    protected $casts = [
        'miembros' => 'array'
    ];
}


