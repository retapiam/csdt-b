<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AnalisisIADerechosEspecializados extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'analisis_ia_derechos_especializados';

    protected $fillable = [
        'usuario_id',
        'area_derecho',
        'tipo_analisis',
        'datos_entrada',
        'resultado_ia',
        'metadata',
        'tokens_usados',
        'modelo_ia',
        'tiempo_procesamiento',
        'estado',
        'observaciones'
    ];

    protected $casts = [
        'datos_entrada' => 'array',
        'resultado_ia' => 'array',
        'metadata' => 'array',
        'tiempo_procesamiento' => 'decimal:3'
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Relación con el usuario que realizó el análisis
     */
    public function usuario()
    {
        return $this->belongsTo(Usuario::class);
    }

    /**
     * Scope para filtrar por área de derecho
     */
    public function scopePorAreaDerecho($query, $area)
    {
        return $query->where('area_derecho', $area);
    }

    /**
     * Scope para filtrar por tipo de análisis
     */
    public function scopePorTipoAnalisis($query, $tipo)
    {
        return $query->where('tipo_analisis', $tipo);
    }

    /**
     * Scope para filtrar por estado
     */
    public function scopePorEstado($query, $estado)
    {
        return $query->where('estado', $estado);
    }

    /**
     * Scope para análisis recientes
     */
    public function scopeRecientes($query, $dias = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($dias));
    }

    /**
     * Scope para análisis por usuario
     */
    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usuario_id', $usuarioId);
    }

    /**
     * Obtener estadísticas de análisis
     */
    public static function obtenerEstadisticas()
    {
        return [
            'total_analisis' => self::count(),
            'analisis_por_area' => self::selectRaw('area_derecho, COUNT(*) as total')
                ->groupBy('area_derecho')
                ->get(),
            'analisis_por_estado' => self::selectRaw('estado, COUNT(*) as total')
                ->groupBy('estado')
                ->get(),
            'analisis_por_usuario' => self::selectRaw('usuario_id, COUNT(*) as total')
                ->groupBy('usuario_id')
                ->with('usuario')
                ->get(),
            'tokens_totales' => self::sum('tokens_usados'),
            'tiempo_promedio' => self::avg('tiempo_procesamiento'),
            'analisis_recientes' => self::recientes()->count()
        ];
    }

    /**
     * Obtener análisis más frecuentes
     */
    public static function obtenerAnalisisFrecuentes($limite = 10)
    {
        return self::selectRaw('area_derecho, tipo_analisis, COUNT(*) as frecuencia')
            ->groupBy('area_derecho', 'tipo_analisis')
            ->orderBy('frecuencia', 'desc')
            ->limit($limite)
            ->get();
    }

    /**
     * Obtener análisis por rango de fechas
     */
    public static function obtenerAnalisisPorFechas($fechaInicio, $fechaFin)
    {
        return self::whereBetween('created_at', [$fechaInicio, $fechaFin])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Obtener análisis por usuario y área
     */
    public static function obtenerAnalisisPorUsuarioArea($usuarioId, $area)
    {
        return self::where('usuario_id', $usuarioId)
            ->where('area_derecho', $area)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Crear nuevo análisis
     */
    public static function crearAnalisis($datos)
    {
        return self::create([
            'usuario_id' => $datos['usuario_id'],
            'area_derecho' => $datos['area_derecho'],
            'tipo_analisis' => $datos['tipo_analisis'],
            'datos_entrada' => $datos['datos_entrada'],
            'resultado_ia' => $datos['resultado_ia'],
            'metadata' => $datos['metadata'],
            'tokens_usados' => $datos['tokens_usados'] ?? 0,
            'modelo_ia' => $datos['modelo_ia'] ?? 'gpt-4',
            'tiempo_procesamiento' => $datos['tiempo_procesamiento'] ?? 0,
            'estado' => $datos['estado'] ?? 'completado',
            'observaciones' => $datos['observaciones'] ?? null
        ]);
    }

    /**
     * Actualizar análisis
     */
    public function actualizarAnalisis($datos)
    {
        $this->update($datos);
        return $this;
    }

    /**
     * Marcar como completado
     */
    public function marcarCompletado()
    {
        $this->update(['estado' => 'completado']);
        return $this;
    }

    /**
     * Marcar como fallido
     */
    public function marcarFallido($observaciones = null)
    {
        $this->update([
            'estado' => 'fallido',
            'observaciones' => $observaciones
        ]);
        return $this;
    }

    /**
     * Obtener resumen del análisis
     */
    public function obtenerResumen()
    {
        return [
            'id' => $this->id,
            'area_derecho' => $this->area_derecho,
            'tipo_analisis' => $this->tipo_analisis,
            'estado' => $this->estado,
            'tokens_usados' => $this->tokens_usados,
            'tiempo_procesamiento' => $this->tiempo_procesamiento,
            'fecha_creacion' => $this->created_at->format('d/m/Y H:i:s'),
            'usuario' => $this->usuario ? $this->usuario->nom . ' ' . $this->usuario->ape : 'Usuario no encontrado'
        ];
    }
}
