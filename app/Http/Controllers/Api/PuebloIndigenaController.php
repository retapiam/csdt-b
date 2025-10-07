<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PuebloIndigena;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PuebloIndigenaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $query = PuebloIndigena::query();

            // Filtros
            if ($request->has('departamento')) {
                $query->where('departamento', 'like', '%' . $request->departamento . '%');
            }

            if ($request->has('municipio')) {
                $query->where('municipio', 'like', '%' . $request->municipio . '%');
            }

            if ($request->has('pueblo')) {
                $query->where('pueblo', 'like', '%' . $request->pueblo . '%');
            }

            if ($request->has('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nombre', 'like', '%' . $search . '%')
                        ->orWhere('pueblo', 'like', '%' . $search . '%')
                        ->orWhere('territorio_ancestral', 'like', '%' . $search . '%')
                        ->orWhere('idioma', 'like', '%' . $search . '%');
                });
            }

            // Ordenamiento
            $sortBy = $request->get('sort_by', 'nombre');
            $sortDirection = $request->get('sort_direction', 'asc');
            $query->orderBy($sortBy, $sortDirection);

            // Paginación
            $perPage = $request->get('per_page', 50);
            
            if ($request->has('paginate') && $request->paginate === 'false') {
                $pueblos = $query->get();
            } else {
                $pueblos = $query->paginate($perPage);
            }

            return response()->json([
                'success' => true,
                'data' => $pueblos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pueblos indígenas',
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
            'pueblo' => 'required|string|max:255',
            'departamento' => 'required|string|max:100',
            'municipio' => 'required|string|max:100',
            'resguardo' => 'nullable|string|max:255',
            'territorio_ancestral' => 'nullable|string|max:255',
            'extension_hectareas' => 'nullable|numeric',
            'poblacion' => 'nullable|integer',
            'idioma' => 'nullable|string|max:100',
            'autoridades_tradicionales' => 'nullable|array',
            'representante_legal' => 'nullable|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'estado' => 'nullable|in:activo,en_proceso_reconocimiento,inactivo',
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
            $pueblo = PuebloIndigena::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Pueblo indígena creado exitosamente',
                'data' => $pueblo
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear pueblo indígena',
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
            $pueblo = PuebloIndigena::with(['consultasPrevias', 'planesEtnodesarrollo'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $pueblo
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pueblo indígena no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener pueblo indígena',
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
            'pueblo' => 'sometimes|required|string|max:255',
            'departamento' => 'sometimes|required|string|max:100',
            'municipio' => 'sometimes|required|string|max:100',
            'resguardo' => 'nullable|string|max:255',
            'territorio_ancestral' => 'nullable|string|max:255',
            'extension_hectareas' => 'nullable|numeric',
            'poblacion' => 'nullable|integer',
            'idioma' => 'nullable|string|max:100',
            'autoridades_tradicionales' => 'nullable|array',
            'representante_legal' => 'nullable|string|max:255',
            'contacto' => 'nullable|string|max:255',
            'estado' => 'nullable|in:activo,en_proceso_reconocimiento,inactivo',
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
            $pueblo = PuebloIndigena::findOrFail($id);
            $pueblo->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Pueblo indígena actualizado exitosamente',
                'data' => $pueblo
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pueblo indígena no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar pueblo indígena',
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
            $pueblo = PuebloIndigena::findOrFail($id);
            $pueblo->delete();

            return response()->json([
                'success' => true,
                'message' => 'Pueblo indígena eliminado exitosamente'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Pueblo indígena no encontrado'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar pueblo indígena',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
