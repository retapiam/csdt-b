<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AlertaPAE;
use Illuminate\Http\Request;

class AlertasController extends Controller
{
    public function index(Request $request)
    {
        $query = AlertaPAE::query();

        if ($request->filled('estado')) {
            $query->where('estado', $request->get('estado'));
        }
        if ($request->filled('severidad')) {
            $query->where('severidad', $request->get('severidad'));
        }

        $alertas = $query->orderBy('created_at', 'desc')->paginate(20);

        return response()->json([
            'success' => true,
            'data' => $alertas
        ]);
    }

    public function show($id)
    {
        $alerta = AlertaPAE::findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $alerta
        ]);
    }

    public function update(Request $request, $id)
    {
        $alerta = AlertaPAE::findOrFail($id);

        $data = $request->only(['estado', 'severidad', 'asignado_a', 'sla_at', 'mensaje']);

        // Normalizar estado permitido
        if (isset($data['estado']) && !in_array($data['estado'], ['abierta', 'cerrada'])) {
            return response()->json([
                'success' => false,
                'message' => 'Estado inválido'
            ], 422);
        }

        $alerta->update($data);

        return response()->json([
            'success' => true,
            'data' => $alerta
        ]);
    }
}


