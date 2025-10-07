<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Donacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class DonacionController extends Controller
{
    /**
     * Listar todas las donaciones
     */
    public function index(Request $request)
    {
        $query = Donacion::query();

        // Filtros
        if ($request->has('estado')) {
            $query->where('estado', $request->estado);
        }

        if ($request->has('metodo_pago')) {
            $query->where('metodo_pago', $request->metodo_pago);
        }

        if ($request->has('destino')) {
            $query->where('destino', $request->destino);
        }

        if ($request->has('fecha_desde')) {
            $query->whereDate('created_at', '>=', $request->fecha_desde);
        }

        if ($request->has('fecha_hasta')) {
            $query->whereDate('created_at', '<=', $request->fecha_hasta);
        }

        $donaciones = $query->orderBy('created_at', 'desc')->paginate(15);

        return response()->json([
            'success' => true,
            'data' => $donaciones
        ]);
    }

    /**
     * Crear nueva donación
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'donante_nombre' => 'required|string|max:255',
            'donante_email' => 'required|email|max:255',
            'donante_telefono' => 'nullable|string|max:20',
            'donante_documento' => 'nullable|string|max:50',
            'monto' => 'required|numeric|min:1000',
            'moneda' => 'nullable|string|max:3|default:COP',
            'metodo_pago' => 'required|in:efectivo,transferencia,pse,nequi,daviplata,tarjeta_credito,tarjeta_debito',
            'destino' => 'nullable|string|max:255',
            'mensaje' => 'nullable|string',
            'es_recurrente' => 'nullable|boolean',
            'frecuencia_recurrente' => 'nullable|in:mensual,trimestral,semestral,anual',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $donacion = Donacion::create([
                'donante_nombre' => $request->donante_nombre,
                'donante_email' => $request->donante_email,
                'donante_telefono' => $request->donante_telefono,
                'donante_documento' => $request->donante_documento,
                'monto' => $request->monto,
                'moneda' => $request->moneda ?? 'COP',
                'metodo_pago' => $request->metodo_pago,
                'estado' => 'pendiente',
                'destino' => $request->destino,
                'mensaje' => $request->mensaje,
                'es_recurrente' => $request->es_recurrente ?? false,
                'frecuencia_recurrente' => $request->frecuencia_recurrente,
                'referencia_pago' => 'DON-' . strtoupper(uniqid()),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Donación registrada exitosamente',
                'data' => $donacion
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar donación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener una donación específica
     */
    public function show($id)
    {
        $donacion = Donacion::find($id);

        if (!$donacion) {
            return response()->json([
                'success' => false,
                'message' => 'Donación no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $donacion
        ]);
    }

    /**
     * Actualizar una donación
     */
    public function update(Request $request, $id)
    {
        $donacion = Donacion::find($id);

        if (!$donacion) {
            return response()->json([
                'success' => false,
                'message' => 'Donación no encontrada'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'estado' => 'sometimes|in:pendiente,completada,cancelada,fallida',
            'comprobante' => 'sometimes|string',
            'referencia_pago' => 'sometimes|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $donacion->update($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Donación actualizada exitosamente',
                'data' => $donacion
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar donación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar una donación
     */
    public function destroy($id)
    {
        $donacion = Donacion::find($id);

        if (!$donacion) {
            return response()->json([
                'success' => false,
                'message' => 'Donación no encontrada'
            ], 404);
        }

        try {
            $donacion->delete();

            return response()->json([
                'success' => true,
                'message' => 'Donación eliminada exitosamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar donación',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de donaciones
     */
    public function estadisticas(Request $request)
    {
        try {
            $estadisticas = [
                'total_donaciones' => Donacion::where('estado', 'completada')->count(),
                'monto_total' => Donacion::where('estado', 'completada')->sum('monto'),
                'monto_pendiente' => Donacion::where('estado', 'pendiente')->sum('monto'),
                'donaciones_mes_actual' => Donacion::where('estado', 'completada')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'monto_mes_actual' => Donacion::where('estado', 'completada')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->sum('monto'),
                'donaciones_recurrentes' => Donacion::where('es_recurrente', true)
                    ->where('estado', 'completada')
                    ->count(),
                'por_metodo_pago' => Donacion::where('estado', 'completada')
                    ->select('metodo_pago', DB::raw('count(*) as cantidad'), DB::raw('sum(monto) as monto_total'))
                    ->groupBy('metodo_pago')
                    ->get(),
                'por_destino' => Donacion::where('estado', 'completada')
                    ->select('destino', DB::raw('count(*) as cantidad'), DB::raw('sum(monto) as monto_total'))
                    ->groupBy('destino')
                    ->get(),
                'ultimas_donaciones' => Donacion::where('estado', 'completada')
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get(),
                'tendencia_mensual' => Donacion::where('estado', 'completada')
                    ->select(
                        DB::raw('DATE_FORMAT(created_at, "%Y-%m") as mes'),
                        DB::raw('count(*) as cantidad'),
                        DB::raw('sum(monto) as monto_total')
                    )
                    ->groupBy('mes')
                    ->orderBy('mes', 'desc')
                    ->take(12)
                    ->get(),
            ];

            return response()->json([
                'success' => true,
                'data' => $estadisticas
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
