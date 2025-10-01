<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ComunidadEtnica;
use App\Models\PatrimonioCulturalEtnico;
use App\Models\PlanEducativoEtnico;
use App\Models\CasoJusticiaIndigena;
use App\Models\TerritorioAncestral;
use App\Models\AnalisisIAEtnico;
use App\Models\NotificacionEtnica;
use App\Models\EstadisticaEtnica;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class EtnicosController extends Controller
{
    /**
     * Obtener todas las comunidades étnicas
     */
    public function getComunidades(Request $request): JsonResponse
    {
        try {
            $query = ComunidadEtnica::with(['patrimonioCultural', 'planesEducativos', 'casosJusticia', 'territoriosAncestrales']);

            // Filtros
            if ($request->has('tipo')) {
                $query->where('tipo', $request->tipo);
            }

            if ($request->has('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->has('busqueda')) {
                $busqueda = $request->busqueda;
                $query->where(function($q) use ($busqueda) {
                    $q->where('nombre', 'like', "%{$busqueda}%")
                      ->orWhere('ubicacion', 'like', "%{$busqueda}%")
                      ->orWhere('descripcion', 'like', "%{$busqueda}%");
                });
            }

            $comunidades = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $comunidades,
                'message' => 'Comunidades étnicas obtenidas exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener comunidades étnicas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener patrimonio cultural étnico
     */
    public function getPatrimonioCultural(Request $request): JsonResponse
    {
        try {
            $query = PatrimonioCulturalEtnico::with('comunidad');

            // Filtros
            if ($request->has('tipo')) {
                $query->where('tipo', $request->tipo);
            }

            if ($request->has('estado_conservacion')) {
                $query->where('estado_conservacion', $request->estado_conservacion);
            }

            if ($request->has('comunidad_id')) {
                $query->where('comunidad_id', $request->comunidad_id);
            }

            if ($request->has('busqueda')) {
                $busqueda = $request->busqueda;
                $query->where(function($q) use ($busqueda) {
                    $q->where('nombre', 'like', "%{$busqueda}%")
                      ->orWhere('descripcion', 'like', "%{$busqueda}%");
                });
            }

            $patrimonio = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $patrimonio,
                'message' => 'Patrimonio cultural obtenido exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener patrimonio cultural',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener planes educativos étnicos
     */
    public function getPlanesEducativos(Request $request): JsonResponse
    {
        try {
            $query = PlanEducativoEtnico::with('comunidad');

            // Filtros
            if ($request->has('nivel')) {
                $query->where('nivel', $request->nivel);
            }

            if ($request->has('area')) {
                $query->where('area', $request->area);
            }

            if ($request->has('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->has('comunidad_id')) {
                $query->where('comunidad_id', $request->comunidad_id);
            }

            if ($request->has('busqueda')) {
                $busqueda = $request->busqueda;
                $query->where(function($q) use ($busqueda) {
                    $q->where('nombre', 'like', "%{$busqueda}%")
                      ->orWhere('descripcion', 'like', "%{$busqueda}%");
                });
            }

            $planes = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $planes,
                'message' => 'Planes educativos obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener planes educativos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener casos de justicia indígena
     */
    public function getCasosJusticia(Request $request): JsonResponse
    {
        try {
            $query = CasoJusticiaIndigena::with('comunidad');

            // Filtros
            if ($request->has('tipo_conflicto')) {
                $query->where('tipo_conflicto', $request->tipo_conflicto);
            }

            if ($request->has('gravedad')) {
                $query->where('gravedad', $request->gravedad);
            }

            if ($request->has('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->has('comunidad_id')) {
                $query->where('comunidad_id', $request->comunidad_id);
            }

            if ($request->has('busqueda')) {
                $busqueda = $request->busqueda;
                $query->where(function($q) use ($busqueda) {
                    $q->where('descripcion', 'like', "%{$busqueda}%")
                      ->orWhere('partes_involucradas', 'like', "%{$busqueda}%");
                });
            }

            $casos = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $casos,
                'message' => 'Casos de justicia obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener casos de justicia',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener territorios ancestrales
     */
    public function getTerritoriosAncestrales(Request $request): JsonResponse
    {
        try {
            $query = TerritorioAncestral::with('comunidad');

            // Filtros
            if ($request->has('tipo')) {
                $query->where('tipo', $request->tipo);
            }

            if ($request->has('estado_proteccion')) {
                $query->where('estado_proteccion', $request->estado_proteccion);
            }

            if ($request->has('comunidad_id')) {
                $query->where('comunidad_id', $request->comunidad_id);
            }

            if ($request->has('busqueda')) {
                $busqueda = $request->busqueda;
                $query->where(function($q) use ($busqueda) {
                    $q->where('nombre', 'like', "%{$busqueda}%")
                      ->orWhere('ubicacion', 'like', "%{$busqueda}%")
                      ->orWhere('descripcion', 'like', "%{$busqueda}%");
                });
            }

            $territorios = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $territorios,
                'message' => 'Territorios ancestrales obtenidos exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener territorios ancestrales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas étnicas
     */
    public function getEstadisticas(): JsonResponse
    {
        try {
            $estadisticas = [
                'comunidades' => [
                    'total' => ComunidadEtnica::count(),
                    'activas' => ComunidadEtnica::where('estado', 'activo')->count(),
                    'por_tipo' => ComunidadEtnica::select('tipo', DB::raw('count(*) as total'))
                        ->groupBy('tipo')
                        ->get()
                ],
                'patrimonio' => [
                    'total' => PatrimonioCulturalEtnico::count(),
                    'en_riesgo' => PatrimonioCulturalEtnico::whereIn('estado_conservacion', ['malo', 'critico'])->count(),
                    'por_tipo' => PatrimonioCulturalEtnico::select('tipo', DB::raw('count(*) as total'))
                        ->groupBy('tipo')
                        ->get()
                ],
                'planes_educativos' => [
                    'total' => PlanEducativoEtnico::count(),
                    'activos' => PlanEducativoEtnico::whereIn('estado', ['en_desarrollo', 'activo'])->count(),
                    'por_nivel' => PlanEducativoEtnico::select('nivel', DB::raw('count(*) as total'))
                        ->groupBy('nivel')
                        ->get()
                ],
                'casos_justicia' => [
                    'total' => CasoJusticiaIndigena::count(),
                    'pendientes' => CasoJusticiaIndigena::whereIn('estado', ['pendiente', 'en_proceso'])->count(),
                    'resueltos' => CasoJusticiaIndigena::where('estado', 'resuelto')->count(),
                    'por_gravedad' => CasoJusticiaIndigena::select('gravedad', DB::raw('count(*) as total'))
                        ->groupBy('gravedad')
                        ->get()
                ],
                'territorios' => [
                    'total' => TerritorioAncestral::count(),
                    'protegidos' => TerritorioAncestral::where('estado_proteccion', 'protegido')->count(),
                    'en_riesgo' => TerritorioAncestral::whereIn('estado_proteccion', ['amenazado', 'critico'])->count(),
                    'por_tipo' => TerritorioAncestral::select('tipo', DB::raw('count(*) as total'))
                        ->groupBy('tipo')
                        ->get()
                ]
            ];

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
                'message' => 'Estadísticas étnicas obtenidas exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas étnicas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Crear análisis de IA étnico
     */
    public function crearAnalisisIA(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'analizable_type' => 'required|string',
                'analizable_id' => 'required|integer',
                'tipo_ia' => 'required|string',
                'clasificacion' => 'required|string',
                'respuesta' => 'required|string',
                'confianza' => 'required|integer|min:0|max:100',
                'recomendaciones' => 'nullable|array',
                'analisis_general' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $analisis = AnalisisIAEtnico::create($request->all());

            return response()->json([
                'success' => true,
                'data' => $analisis,
                'message' => 'Análisis de IA creado exitosamente'
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear análisis de IA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener notificaciones étnicas
     */
    public function getNotificaciones(Request $request): JsonResponse
    {
        try {
            $query = NotificacionEtnica::with('comunidad');

            // Filtros
            if ($request->has('tipo')) {
                $query->where('tipo', $request->tipo);
            }

            if ($request->has('prioridad')) {
                $query->where('prioridad', $request->prioridad);
            }

            if ($request->has('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->has('comunidad_id')) {
                $query->where('comunidad_id', $request->comunidad_id);
            }

            if ($request->has('busqueda')) {
                $busqueda = $request->busqueda;
                $query->where(function($q) use ($busqueda) {
                    $q->where('titulo', 'like', "%{$busqueda}%")
                      ->orWhere('descripcion', 'like', "%{$busqueda}%");
                });
            }

            $notificaciones = $query->orderBy('created_at', 'desc')
                ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $notificaciones,
                'message' => 'Notificaciones étnicas obtenidas exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener notificaciones étnicas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
