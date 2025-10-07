<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones';

    protected $fillable = [
        'proyecto_id',
        'codigo',
        'fecha_generacion',
        'validez_dias',
        'desglose',
        'subtotal',
        'iva',
        'total',
        'condiciones',
        'estado',
        'aprobada_por',
        'fecha_aprobacion',
        'observaciones',
    ];

    protected $casts = [
        'fecha_generacion' => 'datetime',
        'desglose' => 'array',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'total' => 'decimal:2',
        'condiciones' => 'array',
        'validez_dias' => 'integer',
        'fecha_aprobacion' => 'datetime',
    ];

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'aprobada_por');
    }
}
