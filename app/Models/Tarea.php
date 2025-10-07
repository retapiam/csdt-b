<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarea extends Model
{
    use HasFactory;

    protected $fillable = [
        'proyecto_id',
        'actividad_id',          // NUEVO: Relación con actividad
        'tarea_padre_id',        // NUEVO: Para sub-tareas
        'nombre',
        'descripcion',
        'tipo',
        'nivel_tarea',           // NUEVO: admin/operador/cliente
        'color',                 // NUEVO: Color de visualización
        'estado',
        'prioridad',
        'asignado_a',
        'creado_por',
        'tiempo_estimado',
        'tiempo_invertido',
        'costo_estimado',
        'costo_real',
        'fecha_asignacion',
        'fecha_limite',
        'fecha_completada',
        'progreso',
        'motivo_bloqueo',
        'documentos_requeridos',
        'documentos_entregados',
        'pdfs_adjuntos',         // NUEVO: PDFs de la tarea
        'soportes',              // NUEVO: Soportes documentales
    ];

    protected $casts = [
        'fecha_asignacion' => 'datetime',
        'fecha_limite' => 'date',
        'fecha_completada' => 'datetime',
        'tiempo_estimado' => 'decimal:2',
        'tiempo_invertido' => 'decimal:2',
        'costo_estimado' => 'decimal:2',
        'costo_real' => 'decimal:2',
        'documentos_requeridos' => 'array',
        'documentos_entregados' => 'array',
        'pdfs_adjuntos' => 'array',
        'soportes' => 'array',
    ];

    public function proyecto(): BelongsTo
    {
        return $this->belongsTo(Proyecto::class);
    }

    public function asignadoA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'asignado_a');
    }

    public function creadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function dependenciasOrigen(): BelongsToMany
    {
        return $this->belongsToMany(
            Tarea::class,
            'dependencias_tareas',
            'tarea_destino_id',
            'tarea_origen_id'
        )->withPivot('tipo_dependencia', 'condicion')->withTimestamps();
    }

    public function dependenciasDestino(): BelongsToMany
    {
        return $this->belongsToMany(
            Tarea::class,
            'dependencias_tareas',
            'tarea_origen_id',
            'tarea_destino_id'
        )->withPivot('tipo_dependencia', 'condicion')->withTimestamps();
    }

    // NUEVAS RELACIONES para jerarquía

    public function actividad(): BelongsTo
    {
        return $this->belongsTo(Actividad::class);
    }

    public function tareaPadre(): BelongsTo
    {
        return $this->belongsTo(Tarea::class, 'tarea_padre_id');
    }

    public function subTareas(): HasMany
    {
        return $this->hasMany(Tarea::class, 'tarea_padre_id');
    }

    // MÉTODOS HELPER para niveles

    public function esTareaAdmin(): bool
    {
        return $this->nivel_tarea === 'admin';
    }

    public function esTareaOperador(): bool
    {
        return $this->nivel_tarea === 'operador';
    }

    public function esTareaCliente(): bool
    {
        return $this->nivel_tarea === 'cliente';
    }

    public function getColorPorNivel(): string
    {
        if ($this->color) {
            return $this->color;
        }

        // Colores por defecto según nivel
        return match($this->nivel_tarea) {
            'admin' => '#8B5CF6',      // Púrpura
            'operador' => '#10B981',    // Verde
            'cliente' => '#F59E0B',     // Naranja/Amarillo
            default => '#3B82F6'        // Azul
        };
    }

    public function agregarPDF(string $rutaPDF, string $nombre = 'Documento'): void
    {
        $pdfs = $this->pdfs_adjuntos ?? [];
        $pdfs[] = [
            'ruta' => $rutaPDF,
            'nombre' => $nombre,
            'fecha' => now()->toDateTimeString(),
        ];
        $this->pdfs_adjuntos = $pdfs;
        $this->save();
    }

    public function agregarSoporte(string $rutaSoporte, string $tipo = 'documento'): void
    {
        $soportes = $this->soportes ?? [];
        $soportes[] = [
            'ruta' => $rutaSoporte,
            'tipo' => $tipo,
            'fecha' => now()->toDateTimeString(),
        ];
        $this->soportes = $soportes;
        $this->save();
    }
}


