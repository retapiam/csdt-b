<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CAESeguimiento extends Model
{
    use HasFactory;

    protected $table = 'cae_seguimientos';

    protected $fillable = [
        'comite_id', 'fecha', 'hallazgos', 'checklist', 'evidencias'
    ];

    protected $casts = [
        'checklist' => 'array',
        'evidencias' => 'array',
        'fecha' => 'date',
    ];
}


