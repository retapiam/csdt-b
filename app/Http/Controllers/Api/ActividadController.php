<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Actividad;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ActividadController extends Controller
{
    /**
     * Listar todas las actividades (con filtros)
     * OPTIMIZADO: Eager loading solo campos necesarios, paginación ajustable
     */
    public function index(Request $request)
    {
        // Optimizado: seleccionar solo campos necesarios en las relaciones
        $query = Actividad::select([
                'id', 'proyecto_id', 'codigo', 'nombre', 'descripcion', 
                'tipo_actividad', 'categoria', 'estado', 'prioridad',
                'creada_por', 'responsable_id', 'fecha_inicio', 'fecha_fin_planeada',
                'progreso', 'horas_estimadas', 'costo_estimado', 'nivel', 'orden',
                'created_at', 'updated_at'
            ])
            ->with([
                'proyecto:id,nombre,estado',
                'creador:id,name,email',
                'responsable:id,name,email',
                'tareas:id,actividad_id,nombre,estado,progreso'
            ]);

        // Filtros
        if ($request->has('proyecto_id')) {
            $query->where('proyecto_id', $request->proyecto_id);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('categoria')) {
            $query->where('categoria', $request->categoria);
        }

        if ($request->has('modulo')) {
            $query->where('nombre_modulo', $request->modulo);
        }

        // Ordenar por proyecto y orden
        $query->orderBy('proyecto_id')->orderBy('orden');

        // Paginación ajustable (por defecto 20, máximo 100)
        $perPage = min($request->get('per_page', 20), 100);
        $actividades = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $actividades
        ]);
    }

    /**
     * Crear nueva actividad
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'proyecto_id' => 'required|exists:proyectos,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'tipo_actividad' => 'required|string|max:100',
            'categoria' => 'required|in:juridica,etnica,veeduria,administrativa,tecnica,financiera',
            'prioridad' => 'required|in:baja,media,alta,critica',
            'fecha_fin_planeada' => 'required|date',
            'horas_estimadas' => 'nullable|numeric',
            'costo_estimado' => 'nullable|numeric',
            'color' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Generar código único
            $proyecto = Proyecto::find($request->proyecto_id);
            $codigo = 'ACT-' . strtoupper(Str::random(8));

            // Determinar orden
            $ultimaActividad = Actividad::where('proyecto_id', $request->proyecto_id)
                ->orderBy('orden', 'desc')
                ->first();
            $orden = $ultimaActividad ? $ultimaActividad->orden + 1 : 1;

            $actividad = Actividad::create([
                'proyecto_id' => $request->proyecto_id,
                'modulo_id' => $request->modulo_id,
                'codigo' => $codigo,
                'nombre' => $request->nombre,
                'nombre_modulo' => $request->nombre_modulo,
                'descripcion' => $request->descripcion,
                'tipo_actividad' => $request->tipo_actividad,
                'categoria' => $request->categoria,
                'prioridad' => $request->prioridad,
                'creada_por' => $request->user()->id,
                'responsable_id' => $request->responsable_id,
                'fecha_inicio' => $request->fecha_inicio ?? now(),
                'fecha_fin_planeada' => $request->fecha_fin_planeada,
                'horas_estimadas' => $request->horas_estimadas ?? 0,
                'costo_estimado' => $request->costo_estimado ?? 0,
                'actividad_padre_id' => $request->actividad_padre_id,
                'nivel' => $request->nivel ?? 1,
                'orden' => $orden,
                'color' => $request->color ?? '#3B82F6',
                'configuracion' => $request->configuracion ?? [],
                'metadata' => $request->metadata ?? [],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Actividad creada exitosamente',
                'data' => $actividad->load(['proyecto', 'creador', 'responsable'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear actividad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener una actividad específica con sus tareas
     */
    public function show($id)
    {
        $actividad = Actividad::with([
            'proyecto',
            'creador',
            'responsable',
            'tareas.asignadoA',
            'tareas.creadoPor',
            'subActividades',
            'actividadPadre'
        ])->find($id);

        if (!$actividad) {
            return response()->json([
                'success' => false,
                'message' => 'Actividad no encontrada'
            ], 404);
        }

        // Agregar información calculada
        $actividadData = $actividad->toArray();
        $actividadData['progreso_real'] = $actividad->calcularProgresoReal();

        return response()->json([
            'success' => true,
            'data' => $actividadData
        ]);
    }

    /**
     * Actualizar actividad
     */
    public function update(Request $request, $id)
    {
        $actividad = Actividad::find($id);

        if (!$actividad) {
            return response()->json([
                'success' => false,
                'message' => 'Actividad no encontrada'
            ], 404);
        }

        try {
            $actividad->update($request->all());

            // Si se actualiza el progreso, actualizar el proyecto
            if ($request->has('progreso')) {
                $this->actualizarProgresoProyecto($actividad->proyecto_id);
            }

            return response()->json([
                'success' => true,
                'message' => 'Actividad actualizada exitosamente',
                'data' => $actividad->load(['proyecto', 'creador', 'responsable'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar actividad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar actividad
     */
    public function destroy($id)
    {
        $actividad = Actividad::find($id);

        if (!$actividad) {
            return response()->json([
                'success' => false,
                'message' => 'Actividad no encontrada'
            ], 404);
        }

        try {
            $proyectoId = $actividad->proyecto_id;
            $actividad->delete();

            // Actualizar progreso del proyecto
            $this->actualizarProgresoProyecto($proyectoId);

            return response()->json([
                'success' => true,
                'message' => 'Actividad eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar actividad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener actividades por proyecto con jerarquía tipo MS Project
     */
    public function porProyecto($proyectoId)
    {
        $actividades = Actividad::where('proyecto_id', $proyectoId)
            ->with([
                'tareas' => function($query) {
                    $query->orderBy('nivel_tarea')->orderBy('created_at');
                },
                'tareas.asignadoA',
                'tareas.creadoPor',
                'subActividades'
            ])
            ->whereNull('actividad_padre_id') // Solo actividades principales
            ->orderBy('orden')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $actividades
        ]);
    }

    /**
     * Agregar PDF a una actividad
     */
    public function agregarPDF(Request $request, $id)
    {
        $actividad = Actividad::find($id);

        if (!$actividad) {
            return response()->json([
                'success' => false,
                'message' => 'Actividad no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'ruta_pdf' => 'required|string',
            'tipo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $actividad->agregarPDF($request->ruta_pdf, $request->tipo ?? 'general');

            return response()->json([
                'success' => true,
                'message' => 'PDF agregado exitosamente',
                'data' => $actividad
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar progreso del proyecto basado en sus actividades
     */
    private function actualizarProgresoProyecto($proyectoId)
    {
        $actividades = Actividad::where('proyecto_id', $proyectoId)->get();
        
        if ($actividades->count() === 0) {
            return;
        }

        $progresoTotal = $actividades->sum('progreso');
        $progresoPromedio = round($progresoTotal / $actividades->count());

        $proyecto = Proyecto::find($proyectoId);
        if ($proyecto) {
            $proyecto->progreso = $progresoPromedio;
            $proyecto->save();
        }
    }
}

