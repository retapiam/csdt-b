<?php

namespace App\Http\Controllers;

use App\Services\IAPlanesTrabajoMineroAmbiental;
use App\Models\PlanTrabajoMineroAmbiental;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class PlanesTrabajoMineroAmbientalController extends Controller
{
    protected $iaService;

    public function __construct(IAPlanesTrabajoMineroAmbiental $iaService)
    {
        $this->middleware('auth:sanctum');
        $this->iaService = $iaService;
    }

    /**
     * Generar Plan de Trabajo Minero
     */
    public function generarPlanMinero(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_proyecto' => 'required|string|max:500',
            'tipo_mineria' => 'required|string|in:oro,carbon,esmeraldas,petroleo,gas,cobre,niquel,hierro,caliza,arcilla,general',
            'ubicacion' => 'required|string|max:500',
            'duracion' => 'required|string',
            'descripcion' => 'required|string',
            'area_hectareas' => 'nullable|numeric',
            'inversion_estimada' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $datos = $request->all();
            $resultado = $this->iaService->generarPlanTrabajoMinero($datos);

            // Guardar en base de datos
            $plan = PlanTrabajoMineroAmbiental::create([
                'user_id' => Auth::id(),
                'tipo_plan' => 'minero',
                'nombre_proyecto' => $datos['nombre_proyecto'],
                'tipo_mineria' => $datos['tipo_mineria'],
                'ubicacion' => $datos['ubicacion'],
                'duracion' => $datos['duracion'],
                'descripcion' => $datos['descripcion'],
                'datos_entrada' => $datos,
                'plan_generado' => $resultado['planCompleto'] ?? null,
                'estado' => 'generado',
                'metadata' => $resultado['planCompleto']['metadata'] ?? []
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plan de trabajo minero generado exitosamente',
                'plan' => $plan,
                'resultado' => $resultado
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar plan de trabajo minero',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar Plan de Trabajo Ambiental
     */
    public function generarPlanAmbiental(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_proyecto' => 'required|string|max:500',
            'tipo_ecosistema' => 'required|string|in:bosque,paramo,humedal,manglar,marino,amazonico,andino,caribe,general',
            'ubicacion' => 'required|string|max:500',
            'duracion' => 'required|string',
            'descripcion' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $datos = $request->all();
            $resultado = $this->iaService->generarPlanTrabajoAmbiental($datos);

            // Guardar en base de datos
            $plan = PlanTrabajoMineroAmbiental::create([
                'user_id' => Auth::id(),
                'tipo_plan' => 'ambiental',
                'nombre_proyecto' => $datos['nombre_proyecto'],
                'tipo_mineria' => null,
                'ubicacion' => $datos['ubicacion'],
                'duracion' => $datos['duracion'],
                'descripcion' => $datos['descripcion'],
                'datos_entrada' => $datos,
                'plan_generado' => $resultado['planCompleto'] ?? null,
                'estado' => 'generado',
                'metadata' => $resultado['planCompleto']['metadata'] ?? []
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plan de trabajo ambiental generado exitosamente',
                'plan' => $plan,
                'resultado' => $resultado
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar plan de trabajo ambiental',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar Plan Integrado Minero-Ambiental
     */
    public function generarPlanIntegrado(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_proyecto' => 'required|string|max:500',
            'tipo_mineria' => 'required|string',
            'ubicacion' => 'required|string|max:500',
            'duracion' => 'required|string',
            'descripcion' => 'required|string',
            'comunidad_etnica' => 'nullable|string',
            'tipo_ecosistema' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $datos = $request->all();
            $resultado = $this->iaService->generarPlanIntegradoMineroAmbiental($datos);

            // Guardar en base de datos
            $plan = PlanTrabajoMineroAmbiental::create([
                'user_id' => Auth::id(),
                'tipo_plan' => 'integrado',
                'nombre_proyecto' => $datos['nombre_proyecto'],
                'tipo_mineria' => $datos['tipo_mineria'],
                'ubicacion' => $datos['ubicacion'],
                'duracion' => $datos['duracion'],
                'descripcion' => $datos['descripcion'],
                'datos_entrada' => $datos,
                'plan_generado' => $resultado['planCompleto'] ?? null,
                'estado' => 'generado',
                'metadata' => $resultado['planCompleto']['metadata'] ?? []
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Plan integrado minero-ambiental generado exitosamente',
                'plan' => $plan,
                'resultado' => $resultado
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar plan integrado',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analizar Impacto Ambiental Minero
     */
    public function analizarImpactoAmbiental(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_proyecto' => 'required|string',
            'tipo_mineria' => 'required|string',
            'ubicacion' => 'required|string',
            'descripcion_impactos' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $datos = $request->all();
            $resultado = $this->iaService->analizarImpactoAmbientalMinero($datos);

            return response()->json([
                'success' => true,
                'message' => 'Análisis de impacto ambiental completado',
                'resultado' => $resultado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al analizar impacto ambiental',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analizar Derechos Étnicos en Minería
     */
    public function analizarDerechosEtnicos(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_proyecto' => 'required|string',
            'comunidad_etnica' => 'required|string',
            'tipo_comunidad' => 'required|string|in:indigena,afrodescendiente,raizal,palenquero,rom',
            'descripcion_afectacion' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $datos = $request->all();
            $resultado = $this->iaService->analizarDerechosEtnicosMineria($datos);

            return response()->json([
                'success' => true,
                'message' => 'Análisis de derechos étnicos completado',
                'resultado' => $resultado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al analizar derechos étnicos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar Cronograma de Trabajo Minero
     */
    public function generarCronograma(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_proyecto' => 'required|string',
            'duracion_meses' => 'required|integer|min:1',
            'fases' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $datos = $request->all();
            $resultado = $this->iaService->generarCronogramaMinero($datos);

            return response()->json([
                'success' => true,
                'message' => 'Cronograma generado exitosamente',
                'resultado' => $resultado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar cronograma',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Analizar Riesgos Minero-Ambientales
     */
    public function analizarRiesgos(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_proyecto' => 'required|string',
            'tipo_mineria' => 'required|string',
            'descripcion_riesgos' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $datos = $request->all();
            $resultado = $this->iaService->analizarRiesgosMineroAmbientales($datos);

            return response()->json([
                'success' => true,
                'message' => 'Análisis de riesgos completado',
                'resultado' => $resultado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al analizar riesgos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar Medidas de Mitigación
     */
    public function generarMedidasMitigacion(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre_proyecto' => 'required|string',
            'impactos_identificados' => 'required|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $datos = $request->all();
            $resultado = $this->iaService->generarMedidasMitigacion($datos);

            return response()->json([
                'success' => true,
                'message' => 'Medidas de mitigación generadas exitosamente',
                'resultado' => $resultado
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar medidas de mitigación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listar Planes del Usuario
     */
    public function listarPlanes(Request $request)
    {
        try {
            $planes = PlanTrabajoMineroAmbiental::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'planes' => $planes
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al listar planes',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener Plan por ID
     */
    public function obtenerPlan($id)
    {
        try {
            $plan = PlanTrabajoMineroAmbiental::where('user_id', Auth::id())
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'plan' => $plan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Plan no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Actualizar Estado del Plan
     */
    public function actualizarEstado(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'estado' => 'required|string|in:generado,en_revision,aprobado,rechazado,en_ejecucion,finalizado',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $plan = PlanTrabajoMineroAmbiental::where('user_id', Auth::id())
                ->findOrFail($id);

            $plan->update([
                'estado' => $request->estado
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Estado del plan actualizado exitosamente',
                'plan' => $plan
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar estado del plan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar Plan
     */
    public function eliminarPlan($id)
    {
        try {
            $plan = PlanTrabajoMineroAmbiental::where('user_id', Auth::id())
                ->findOrFail($id);

            $plan->delete();

            return response()->json([
                'success' => true,
                'message' => 'Plan eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar plan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener Estadísticas de Planes
     */
    public function obtenerEstadisticas()
    {
        try {
            $userId = Auth::id();

            $estadisticas = [
                'total_planes' => PlanTrabajoMineroAmbiental::where('user_id', $userId)->count(),
                'planes_mineros' => PlanTrabajoMineroAmbiental::where('user_id', $userId)->where('tipo_plan', 'minero')->count(),
                'planes_ambientales' => PlanTrabajoMineroAmbiental::where('user_id', $userId)->where('tipo_plan', 'ambiental')->count(),
                'planes_integrados' => PlanTrabajoMineroAmbiental::where('user_id', $userId)->where('tipo_plan', 'integrado')->count(),
                'por_estado' => PlanTrabajoMineroAmbiental::where('user_id', $userId)
                    ->selectRaw('estado, COUNT(*) as total')
                    ->groupBy('estado')
                    ->get(),
                'por_tipo_mineria' => PlanTrabajoMineroAmbiental::where('user_id', $userId)
                    ->whereNotNull('tipo_mineria')
                    ->selectRaw('tipo_mineria, COUNT(*) as total')
                    ->groupBy('tipo_mineria')
                    ->get(),
                'planes_recientes' => PlanTrabajoMineroAmbiental::where('user_id', $userId)
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get()
            ];

            return response()->json([
                'success' => true,
                'estadisticas' => $estadisticas
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener estadísticas',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

