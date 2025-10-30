<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IncidenciaPAE;
use Illuminate\Http\Request;

class IncidenciaPAEController extends Controller
{
    public function index(Request $request)
    {
        $q = IncidenciaPAE::query();
        if ($request->filled('institucion_id')) $q->where('institucion_id', $request->institucion_id);
        if ($request->filled('severidad')) $q->where('severidad', $request->severidad);
        return response()->json($q->orderBy('fecha','desc')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'institucion_id' => 'required|integer',
            'fecha' => 'required|date',
            'tipo' => 'required|string',
            'severidad' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'evidencias' => 'nullable|array',
        ]);
        $i = IncidenciaPAE::create($data);
        return response()->json($i, 201);
    }

    public function show($id)
    {
        return response()->json(IncidenciaPAE::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $i = IncidenciaPAE::findOrFail($id);
        $data = $request->validate([
            'tipo' => 'nullable|string',
            'severidad' => 'nullable|string',
            'descripcion' => 'nullable|string',
            'evidencias' => 'nullable|array',
        ]);
        $i->update($data);
        return response()->json($i);
    }

    public function destroy($id)
    {
        $i = IncidenciaPAE::findOrFail($id);
        $i->delete();
        return response()->json(['success' => true]);
    }
}


