<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ComunidadAfro;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComunidadAfroController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = ComunidadAfro::query();

            // Filtros
            if ($request->has('departamento')) {
                $query->where('departamento', 'like', '%' . $request->departamento . '%');
            }

            if ($request->has('municipio')) {
                $query->where('municipio', 'like', '%' . $request->municipio . '%');
            }

            if ($request->has('tipo')) {
                $query->where('tipo', $request->tipo);
            }

            if ($request->has('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', '%' . $search . '%')
                        ->orWhere('territorio_colectivo', 'like', '%' . $search . '%')
                        ->orWhere('titulo_colectivo', 'like', '%' . $search . '%');
                });
            }

            // Ordenamiento
            $sortBy = $request->get('sort_by', 'nombre');
            $sortDirection = $request->get('sort_direction', 'asc');
            $query->orderBy($sortBy, $sortDirection);

            // Paginación
            $perPage = $request->get('per_page', 50);
            
            if ($request->has('paginate') && $request->paginate === 'false') {
                $comunidades = $query->get();
            } else {
                $comunidades = $query->paginate($perPage);
            }

            return response()->json([
                'success' => true,
                'data' => $comunidades
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener comunidades afro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|in:consejo_comunitario,comunidad_negra,palenque,raizal',
            'departamento' => 'required|string|max:100',
            'municipio' => 'required|string|max:100',
            'territorio_colectivo' => 'nullable|string|max:255',
            'titulo_colectivo' => 'nullable|string|max:100',
            'extension_hectareas' => 'nullable|numeric',
            'poblacion' => 'nullable|integer',
            'representante_legal' => 'nullable|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'estado' => 'nullable|in:activo,en_proceso_titulacion,inactivo',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $comunidad = ComunidadAfro::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Comunidad afro creada exitosamente',
                'data' => $comunidad
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear comunidad afro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $comunidad = ComunidadAfro::with(['consultasPrevias', 'planesEtnodesarrollo'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $comunidad
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Comunidad afro no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener comunidad afro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'sometimes|required|string|max:255',
            'tipo' => 'sometimes|required|in:consejo_comunitario,comunidad_negra,palenque,raizal',
            'departamento' => 'sometimes|required|string|max:100',
            'municipio' => 'sometimes|required|string|max:100',
            'territorio_colectivo' => 'nullable|string|max:255',
            'titulo_colectivo' => 'nullable|string|max:100',
            'extension_hectareas' => 'nullable|numeric',
            'poblacion' => 'nullable|integer',
            'representante_legal' => 'nullable|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'estado' => 'nullable|in:activo,en_proceso_titulacion,inactivo',
            'metadata' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Errores de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $comunidad = ComunidadAfro::findOrFail($id);
            $comunidad->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Comunidad afro actualizada exitosamente',
                'data' => $comunidad
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Comunidad afro no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar comunidad afro',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $comunidad = ComunidadAfro::findOrFail($id);
            $comunidad->delete();

            return response()->json([
                'success' => true,
                'message' => 'Comunidad afro eliminada exitosamente'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Comunidad afro no encontrada'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar comunidad afro',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
