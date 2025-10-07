<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proyecto extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo_caso',
        'estado',
        'prioridad',
        'administrador_id',
        'operador_id',
        'cliente_id',
        'fecha_inicio',
        'fecha_limite',
        'fecha_completado',
        'presupuesto_estimado',
        'presupuesto_ejecutado',
        'progreso',
        'tareas_completadas',
        'tareas_totales',
        'analisis_ia_id',
        'configuracion',
        'metadata',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_limite' => 'date',
        'fecha_completado' => 'datetime',
        'presupuesto_estimado' => 'decimal:2',
        'presupuesto_ejecutado' => 'decimal:2',
        'configuracion' => 'array',
        'metadata' => 'array',
    ];

    public function administrador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administrador_id');
    }

    public function operador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operador_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cliente_id');
    }

    public function analisisIA(): BelongsTo
    {
        return $this->belongsTo(AIAnalisisJuridico::class, 'analisis_ia_id');
    }

    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class);
    }

    public function cotizaciones(): HasMany
    {
        return $this->hasMany(Cotizacion::class);
    }

    public function calcularProgreso(): void
    {
        // Optimizado: una sola consulta con agregación
        $stats = $this->tareas()
            ->selectRaw('COUNT(*) as total, SUM(CASE WHEN estado = "completada" THEN 1 ELSE 0 END) as completadas')
            ->first();
        
        $this->tareas_totales = $stats->total ?? 0;
        $this->tareas_completadas = $stats->completadas ?? 0;
        $this->progreso = $this->tareas_totales > 0 
            ? round(($this->tareas_completadas / $this->tareas_totales) * 100)
            : 0;
        $this->save();
    }
}

