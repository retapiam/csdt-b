<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProyectoController extends Controller
{
    /**
     * Listar todos los proyectos
     * OPTIMIZADO: Eager loading selectivo, paginación eficiente
     */
    public function index(Request $request)
    {
        // Optimizado: seleccionar solo campos necesarios
        $query = Proyecto::select([
                'id', 'nombre', 'descripcion', 'tipo_caso', 'estado', 'prioridad',
                'administrador_id', 'operador_id', 'cliente_id',
                'fecha_inicio', 'fecha_limite', 'progreso',
                'presupuesto_estimado', 'presupuesto_ejecutado',
                'created_at', 'updated_at'
            ])
            ->with([
                'administrador:id,name,email',
                'operador:id,name,email',
                'cliente:id,name,email'
            ]);

        // Filtros
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('tipo_caso')) {
            $query->where('tipo_caso', $request->tipo_caso);
        }

        if ($request->has('prioridad')) {
            $query->where('prioridad', $request->prioridad);
        }

        // Para operadores y clientes, solo mostrar sus proyectos
        $user = $request->user();
        if ($user->esOperador()) {
            $query->where('operador_id', $user->id);
        } elseif ($user->esCliente()) {
            $query->where('cliente_id', $user->id);
        }

        // Paginación ajustable (por defecto 15, máximo 50)
        $perPage = min($request->get('per_page', 15), 50);
        $proyectos = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $proyectos
        ]);
    }

    /**
     * Crear nuevo proyecto
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'descripcion' => 'required|string',
            'tipo_caso' => 'required|string|max:100',
            'prioridad' => 'required|in:baja,media,alta,urgente',
            'cliente_id' => 'required|exists:users,id',
            'operador_id' => 'nullable|exists:users,id',
            'fecha_limite' => 'required|date',
            'presupuesto_estimado' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $proyecto = Proyecto::create([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
                'tipo_caso' => $request->tipo_caso,
                'estado' => 'pendiente',
                'prioridad' => $request->prioridad,
                'administrador_id' => $request->user()->id,
                'operador_id' => $request->operador_id,
                'cliente_id' => $request->cliente_id,
                'fecha_limite' => $request->fecha_limite,
                'presupuesto_estimado' => $request->presupuesto_estimado ?? 0,
                'configuracion' => $request->configuracion ?? [],
                'metadata' => $request->metadata ?? [],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Proyecto creado exitosamente',
                'data' => $proyecto->load(['administrador', 'operador', 'cliente'])
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener un proyecto específico
     */
    public function show($id)
    {
        $proyecto = Proyecto::with([
            'administrador',
            'operador',
            'cliente',
            'tareas',
            'cotizaciones',
            'analisisJuridico'
        ])->find($id);

        if (!$proyecto) {
            return response()->json([
                'success' => false,
                'message' => 'Proyecto no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $proyecto
        ]);
    }

    /**
     * Actualizar un proyecto
     */
    public function update(Request $request, $id)
    {
        $proyecto = Proyecto::find($id);

        if (!$proyecto) {
            return response()->json([
                'success' => false,
                'message' => 'Proyecto no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|string|max:255',
            'descripcion' => 'sometimes|string',
            'estado' => 'sometimes|in:pendiente,en_progreso,completado,cancelado,pausado',
            'prioridad' => 'sometimes|in:baja,media,alta,urgente',
            'operador_id' => 'sometimes|nullable|exists:users,id',
            'fecha_limite' => 'sometimes|date',
            'progreso' => 'sometimes|integer|min:0|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $proyecto->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Proyecto actualizado exitosamente',
                'data' => $proyecto->load(['administrador', 'operador', 'cliente'])
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar un proyecto
     */
    public function destroy($id)
    {
        $proyecto = Proyecto::find($id);

        if (!$proyecto) {
            return response()->json([
                'success' => false,
                'message' => 'Proyecto no encontrado'
            ], 404);
        }

        try {
            $proyecto->delete();

            return response()->json([
                'success' => true,
                'message' => 'Proyecto eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar proyecto',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
