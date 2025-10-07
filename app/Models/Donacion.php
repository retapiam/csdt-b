<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donacion extends Model
{
    protected $table = 'donaciones';

    protected $fillable = [
        'donante_nombre',
        'donante_email',
        'donante_telefono',
        'donante_documento',
        'monto',
        'moneda',
        'metodo_pago',
        'estado',
        'referencia_pago',
        'comprobante',
        'mensaje',
        'es_recurrente',
        'frecuencia_recurrente',
        'destino',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'es_recurrente' => 'boolean',
    ];
}
