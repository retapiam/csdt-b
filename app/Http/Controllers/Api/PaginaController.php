<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pagina;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class PaginaController extends Controller
{
    /**
     * Obtener páginas activas del sistema
     */
    public function paginasActivas(): JsonResponse
    {
        try {
            $paginas = Pagina::where('est', 'act')
                ->orderBy('ord')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Páginas activas obtenidas exitosamente',
                'data' => $paginas
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener páginas activas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar todas las páginas
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Pagina::query();

            // Filtros
            if ($request->has('est')) {
                $query->where('est', $request->est);
            }

            if ($request->has('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nom', 'like', "%{$search}%")
                      ->orWhere('des', 'like', "%{$search}%")
                      ->orWhere('rut', 'like', "%{$search}%");
                });
            }

            $paginas = $query->orderBy('ord')->paginate(15);

            return response()->json([
                'success' => true,
                'message' => 'Páginas obtenidas exitosamente',
                'data' => $paginas->items(),
                'meta' => [
                    'pagination' => [
                        'current_page' => $paginas->currentPage(),
                        'per_page' => $paginas->perPage(),
                        'total' => $paginas->total(),
                        'last_page' => $paginas->lastPage(),
                        'from' => $paginas->firstItem(),
                        'to' => $paginas->lastItem()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener páginas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Mostrar una página específica
     */
    public function show($id): JsonResponse
    {
        try {
            $pagina = Pagina::findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Página obtenida exitosamente',
                'data' => $pagina
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Página no encontrada',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Crear nueva página
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'nom' => 'required|string|max:100',
                'rut' => 'required|string|max:200|unique:pag,rut',
                'des' => 'nullable|string|max:255',
                'est' => 'required|in:act,ina',
                'ord' => 'integer|min:0'
            ]);

            $pagina = Pagina::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Página creada exitosamente',
                'data' => $pagina
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear página',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Actualizar página
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $pagina = Pagina::findOrFail($id);

            $request->validate([
                'nom' => 'required|string|max:100',
                'rut' => 'required|string|max:200|unique:pag,rut,' . $id,
                'des' => 'nullable|string|max:255',
                'est' => 'required|in:act,ina',
                'ord' => 'integer|min:0'
            ]);

            $pagina->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Página actualizada exitosamente',
                'data' => $pagina
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar página',
                'error' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * Eliminar página
     */
    public function destroy($id): JsonResponse
    {
        try {
            $pagina = Pagina::findOrFail($id);
            $pagina->delete();

            return response()->json([
                'success' => true,
                'message' => 'Página eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar página',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
