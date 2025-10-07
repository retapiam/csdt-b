<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Actividad extends Model
{
    protected $table = 'actividades';

    protected $fillable = [
        'proyecto_id',
        'modulo_id',
        'codigo',
        'nombre',
        'nombre_modulo',
        'descripcion',
        'tipo_actividad',
        'categoria',
        'estado',
        'prioridad',
        'creada_por',
        'responsable_id',
        'fecha_inicio',
        'fecha_fin_planeada',
        'fecha_fin_real',
        'duracion_dias',
        'progreso',
        'horas_estimadas',
        'horas_reales',
        'costo_estimado',
        'costo_real',
        'actividad_padre_id',
        'nivel',
        'orden',
        'documentos_requeridos',
        'documentos_entregados',
        'pdfs_generados',
        'configuracion',
        'metadata',
        'notas',
        'color',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin_planeada' => 'date',
        'fecha_fin_real' => 'date',
        'progreso' => 'integer',
        'duracion_dias' => 'integer',
        'nivel' => 'integer',
        'orden' => 'integer',
        'horas_estimadas' => 'decimal:2',
        'horas_reales' => 'decimal:2',
        'costo_estimado' => 'decimal:2',
        'costo_real' => 'decimal:2',
        'documentos_requeridos' => 'array',
        'documentos_entregados' => 'array',
        'pdfs_generados' => 'array',
        'configuracion' => 'array',
        'metadata' => 'array',
    ];

    // Relaciones
    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creada_por');
    }

    public function responsable(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    public function tareas(): HasMany
    {
        return $this->hasMany(Tarea::class, 'actividad_id');
    }

    public function actividadPadre(): BelongsTo
    {
        return $this->belongsTo(Actividad::class, 'actividad_padre_id');
    }

    public function subActividades(): HasMany
    {
        return $this->hasMany(Actividad::class, 'actividad_padre_id');
    }

    // Métodos helper
    public function esActividadPrincipal(): bool
    {
        return $this->nivel === 1;
    }

    public function tieneSubActividades(): bool
    {
        // Optimizado: usar exists() en lugar de count()
        return $this->subActividades()->exists();
    }

    public function calcularProgresoReal(): int
    {
        // Optimizado: cargar tareas solo si no están ya cargadas
        if (!$this->relationLoaded('tareas')) {
            $this->load('tareas:id,actividad_id,progreso');
        }
        
        $tareas = $this->tareas;
        if ($tareas->count() === 0) {
            return $this->progreso;
        }

        $progresoTotal = $tareas->sum('progreso');
        return round($progresoTotal / $tareas->count());
    }

    public function actualizarProgreso(): void
    {
        $this->progreso = $this->calcularProgresoReal();
        $this->save();
    }

    public function agregarPDF(string $rutaPDF, string $tipo = 'general'): void
    {
        $pdfs = $this->pdfs_generados ?? [];
        $pdfs[] = [
            'ruta' => $rutaPDF,
            'tipo' => $tipo,
            'fecha' => now()->toDateTimeString(),
        ];
        $this->pdfs_generados = $pdfs;
        $this->save();
    }
}

