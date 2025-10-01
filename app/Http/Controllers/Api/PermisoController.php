<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Permiso;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PermisoController extends Controller
{
    /**
     * Listar todos los permisos
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Permiso::query();

            // Filtros
            if ($request->has('mod')) {
                $query->where('mod', $request->mod);
            }

            if ($request->has('est')) {
                $query->where('est', $request->est);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('des', 'like', "%{$search}%")
                      ->orWhere('slug', 'like', "%{$search}%");
                });
            }

            $permisos = $query->orderBy('mod')->orderBy('nom')->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Permisos obtenidos exitosamente',
                'data' => $permisos->items(),
                'meta' => [
                    'pagination' => [
                        'current_page' => $permisos->currentPage(),
                        'per_page' => $permisos->perPage(),
                        'total' => $permisos->total(),
                        'last_page' => $permisos->lastPage(),
                        'from' => $permisos->firstItem(),
                        'to' => $permisos->lastItem()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener permisos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar un permiso específico
     */
    public function show($id): JsonResponse
    {
        try {
            $permiso = Permiso::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Permiso obtenido exitosamente',
                'data' => $permiso
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Permiso no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Crear nuevo permiso
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'nom' => 'required|string|max:100',
                'slug' => 'required|string|max:100|unique:perm,slug',
                'des' => 'nullable|string|max:255',
                'mod' => 'required|string|max:50',
                'rec' => 'required|string|max:50',
                'acc' => 'required|string|max:50',
                'est' => 'required|in:act,ina',
                'niv' => 'integer|min:1'
            ]);

            $permiso = Permiso::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Permiso creado exitosamente',
                'data' => $permiso
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear permiso',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Actualizar permiso
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $permiso = Permiso::findOrFail($id);

            $request->validate([
                'nom' => 'required|string|max:100',
                'slug' => 'required|string|max:100|unique:perm,slug,' . $id,
                'des' => 'nullable|string|max:255',
                'mod' => 'required|string|max:50',
                'rec' => 'required|string|max:50',
                'acc' => 'required|string|max:50',
                'est' => 'required|in:act,ina',
                'niv' => 'integer|min:1'
            ]);

            $permiso->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Permiso actualizado exitosamente',
                'data' => $permiso
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar permiso',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Eliminar permiso
     */
    public function destroy($id): JsonResponse
    {
        try {
            $permiso = Permiso::findOrFail($id);
            $permiso->delete();

            return response()->json([
                'success' => true,
                'message' => 'Permiso eliminado exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar permiso',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener permisos por módulo
     */
    public function porModulo($modulo): JsonResponse
    {
        try {
            $permisos = Permiso::where('mod', $modulo)
                ->where('est', 'act')
                ->orderBy('nom')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Permisos del módulo obtenidos exitosamente',
                'data' => $permisos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener permisos del módulo',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todos los módulos disponibles
     */
    public function modulos(): JsonResponse
    {
        try {
            $modulos = Permiso::select('mod')
                ->distinct()
                ->where('est', 'act')
                ->orderBy('mod')
                ->pluck('mod');

            return response()->json([
                'success' => true,
                'message' => 'Módulos obtenidos exitosamente',
                'data' => $modulos
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener módulos',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
