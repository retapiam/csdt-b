<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EntregaPAE;
use Illuminate\Http\Request;

class EntregaPAEController extends Controller
{
    public function index(Request $request)
    {
        $q = EntregaPAE::query();
        if ($request->filled('institucion_id')) $q->where('institucion_id', $request->institucion_id);
        if ($request->filled('desde') && $request->filled('hasta')) $q->whereBetween('fecha', [$request->desde, $request->hasta]);
        return response()->json($q->orderBy('fecha','desc')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'institucion_id' => 'required|integer',
            'menu_id' => 'nullable|integer',
            'fecha' => 'required|date',
            'jornada' => 'nullable|string',
            'planificado' => 'required|integer',
            'entregado' => 'required|integer',
            'calidad' => 'nullable|string',
            'evidencias' => 'nullable|array',
        ]);
        $e = EntregaPAE::create($data);
        return response()->json($e, 201);
    }

    public function show($id)
    {
        return response()->json(EntregaPAE::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $e = EntregaPAE::findOrFail($id);
        $data = $request->validate([
            'jornada' => 'nullable|string',
            'planificado' => 'nullable|integer',
            'entregado' => 'nullable|integer',
            'calidad' => 'nullable|string',
            'evidencias' => 'nullable|array',
        ]);
        $e->update($data);
        return response()->json($e);
    }

    public function destroy($id)
    {
        $e = EntregaPAE::findOrFail($id);
        $e->delete();
        return response()->json(['success' => true]);
    }
}


