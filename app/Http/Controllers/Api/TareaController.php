<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Tarea;
use App\Models\Actividad;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class TareaController extends Controller
{
    /**
     * Listar todas las tareas con filtros
     */
    public function index(Request $request)
    {
        $query = Tarea::with([
            'proyecto:id,nombre',
            'actividad:id,nombre,codigo',
            'asignadoA:id,name,email',
            'creadoPor:id,name,email',
            'tareaPadre:id,nombre'
        ]);

        // Filtros
        if ($request->has('proyecto_id')) {
            $query->where('proyecto_id', $request->proyecto_id);
        }

        if ($request->has('actividad_id')) {
            $query->where('actividad_id', $request->actividad_id);
        }

        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('asignado_a')) {
            $query->where('asignado_a', $request->asignado_a);
        }

        if ($request->has('nivel_tarea')) {
            $query->where('nivel_tarea', $request->nivel_tarea);
        }

        // Ordenar
        $query->orderBy('fecha_limite');

        // Paginación
        $perPage = min($request->get('per_page', 20), 100);
        $tareas = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $tareas
        ]);
    }

    /**
     * Crear nueva tarea (Con permisos por rol)
     * Admin puede crear tareas para clientes y operadores
     */
    public function store(Request $request)
    {
        $usuario = $request->user();
        
        // Validar permisos según rol
        if (!in_array($usuario->rol, ['superadmin', 'administrador', 'operador'])) {
            return response()->json([
                'success' => false,
                'message' => 'No tienes permisos para crear tareas. Solo administradores y operadores.'
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'proyecto_id' => 'required|exists:proyectos,id',
            'actividad_id' => 'nullable|exists:actividades,id',
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'tipo' => 'required|string|in:tarea,subtarea,hito',
            'estado' => 'nullable|in:pendiente,en_progreso,completada,bloqueada,cancelada',
            'prioridad' => 'required|in:baja,media,alta,critica',
            'nivel_tarea' => 'required|in:admin,operador,cliente',
            'asignado_a' => 'required|exists:users,id',
            'fecha_limite' => 'required|date',
            'tiempo_estimado' => 'nullable|numeric',
            'dependencias' => 'nullable|array',
            'dependencias.*.tarea_id' => 'exists:tareas,id',
            'dependencias.*.tipo' => 'in:fin_inicio,inicio_inicio,fin_fin,inicio_fin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Verificar que el usuario asignado existe
            $usuarioAsignado = User::findOrFail($request->asignado_a);

            // Validar que el nivel de tarea corresponda con el rol del asignado
            $nivelCorrectoSegunRol = match($usuarioAsignado->rol) {
                'superadmin', 'administrador' => 'admin',
                'operador' => 'operador',
                'cliente' => 'cliente',
                default => null,
            };

            if ($nivelCorrectoSegunRol && $request->nivel_tarea !== $nivelCorrectoSegunRol) {
                return response()->json([
                    'success' => false,
                    'message' => "El nivel de tarea '{$request->nivel_tarea}' no corresponde con el rol del usuario asignado '{$usuarioAsignado->rol}'. Debe ser '{$nivelCorrectoSegunRol}'."
                ], 422);
            }

            // Determinar color según nivel si no se especifica
            $color = $request->color ?? match($request->nivel_tarea) {
                'admin' => '#8B5CF6',
                'operador' => '#10B981',
                'cliente' => '#F59E0B',
                default => '#3B82F6'
            };

            $tarea = Tarea::create([
                'proyecto_id' => $request->proyecto_id,
                'actividad_id' => $request->actividad_id,
                'tarea_padre_id' => $request->tarea_padre_id,
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'tipo' => $request->tipo,
                'estado' => $request->estado ?? 'pendiente',
                'prioridad' => $request->prioridad,
                'nivel_tarea' => $request->nivel_tarea,
                'asignado_a' => $request->asignado_a,
                'creado_por' => $usuario->id,
                'fecha_asignacion' => now(),
                'fecha_limite' => $request->fecha_limite,
                'tiempo_estimado' => $request->tiempo_estimado ?? 0,
                'costo_estimado' => $request->costo_estimado ?? 0,
                'progreso' => 0,
                'color' => $color,
                'documentos_requeridos' => $request->documentos_requeridos ?? [],
            ]);

            // Agregar dependencias si existen
            if ($request->has('dependencias')) {
                foreach ($request->dependencias as $dependencia) {
                    DB::table('dependencias_tareas')->insert([
                        'tarea_origen_id' => $dependencia['tarea_id'],
                        'tarea_destino_id' => $tarea->id,
                        'tipo_dependencia' => $dependencia['tipo'] ?? 'fin_inicio',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Notificar al usuario asignado
            $this->notificarTareaAsignada($tarea, $usuarioAsignado, $usuario);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Tarea creada y asignada exitosamente',
                'data' => $tarea->load(['proyecto', 'actividad', 'asignadoA', 'creadoPor']),
                'notificacion' => [
                    'asignado_a' => $usuarioAsignado->name,
                    'rol' => $usuarioAsignado->rol,
                    'nivel_tarea' => $request->nivel_tarea,
                ]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al crear tarea',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Notificar al usuario cuando se le asigna una tarea
     */
    private function notificarTareaAsignada($tarea, $usuarioAsignado, $creador)
    {
        try {
            DB::table('notificaciones')->insert([
                'user_id' => $usuarioAsignado->id,
                'tipo' => 'tarea_asignada',
                'titulo' => 'Nueva tarea asignada',
                'mensaje' => "{$creador->name} te ha asignado una nueva tarea: {$tarea->nombre}",
                'datos' => json_encode([
                    'tarea_id' => $tarea->id,
                    'actividad_id' => $tarea->actividad_id,
                    'proyecto_id' => $tarea->proyecto_id,
                    'creador_id' => $creador->id,
                    'creador_nombre' => $creador->name,
                    'prioridad' => $tarea->prioridad,
                    'fecha_limite' => $tarea->fecha_limite,
                    'nivel_tarea' => $tarea->nivel_tarea,
                ]),
                'leido' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Error al notificar tarea asignada: ' . $e->getMessage());
        }
    }

    /**
     * Obtener una tarea específica con dependencias
     */
    public function show($id)
    {
        $tarea = Tarea::with([
            'proyecto',
            'actividad',
            'asignadoA',
            'creadoPor',
            'tareaPadre',
            'subTareas',
            'dependenciasOrigen',
            'dependenciasDestino'
        ])->find($id);

        if (!$tarea) {
            return response()->json([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tarea
        ]);
    }

    /**
     * Actualizar tarea
     */
    public function update(Request $request, $id)
    {
        $tarea = Tarea::find($id);

        if (!$tarea) {
            return response()->json([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ], 404);
        }

        try {
            $tarea->update($request->all());

            // Si se completa la tarea, registrar fecha
            if ($request->estado === 'completada' && !$tarea->fecha_completada) {
                $tarea->fecha_completada = now();
                $tarea->progreso = 100;
                $tarea->save();

                // Actualizar progreso de la actividad
                if ($tarea->actividad_id) {
                    $actividad = Actividad::find($tarea->actividad_id);
                    if ($actividad) {
                        $actividad->actualizarProgreso();
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Tarea actualizada exitosamente',
                'data' => $tarea->load(['proyecto', 'actividad', 'asignadoA'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar tarea',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar tarea
     */
    public function destroy($id)
    {
        $tarea = Tarea::find($id);

        if (!$tarea) {
            return response()->json([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ], 404);
        }

        try {
            // Eliminar dependencias
            DB::table('dependencias_tareas')
                ->where('tarea_origen_id', $id)
                ->orWhere('tarea_destino_id', $id)
                ->delete();

            $tarea->delete();

            return response()->json([
                'success' => true,
                'message' => 'Tarea eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar tarea',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener tareas por proyecto
     */
    public function tareasPorProyecto($proyectoId)
    {
        $tareas = Tarea::where('proyecto_id', $proyectoId)
            ->with(['actividad', 'asignadoA', 'creadoPor', 'subTareas'])
            ->orderBy('fecha_limite')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tareas
        ]);
    }

    /**
     * Obtener tareas por actividad
     */
    public function tareasPorActividad($actividadId)
    {
        $tareas = Tarea::where('actividad_id', $actividadId)
            ->with(['asignadoA', 'creadoPor', 'subTareas', 'dependenciasOrigen'])
            ->whereNull('tarea_padre_id') // Solo tareas principales
            ->orderBy('fecha_limite')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $tareas
        ]);
    }

    /**
     * Agregar PDF a una tarea
     */
    public function agregarPDF(Request $request, $id)
    {
        $tarea = Tarea::find($id);

        if (!$tarea) {
            return response()->json([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'ruta_pdf' => 'required|string',
            'nombre' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tarea->agregarPDF($request->ruta_pdf, $request->nombre ?? 'Documento');

            return response()->json([
                'success' => true,
                'message' => 'PDF agregado exitosamente',
                'data' => $tarea
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
     * Agregar soporte documental a una tarea
     */
    public function agregarSoporte(Request $request, $id)
    {
        $tarea = Tarea::find($id);

        if (!$tarea) {
            return response()->json([
                'success' => false,
                'message' => 'Tarea no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'ruta_soporte' => 'required|string',
            'tipo' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $tarea->agregarSoporte($request->ruta_soporte, $request->tipo ?? 'documento');

            return response()->json([
                'success' => true,
                'message' => 'Soporte agregado exitosamente',
                'data' => $tarea
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agregar soporte',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
