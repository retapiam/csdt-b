<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CAEActa extends Model
{
    use HasFactory;

    protected $table = 'cae_actas';

    protected $fillable = [
        'comite_id', 'fecha', 'acuerdos', 'evidencias'
    ];

    protected $casts = [
        'evidencias' => 'array',
        'fecha' => 'date',
    ];
}


