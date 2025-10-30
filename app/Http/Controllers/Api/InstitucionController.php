<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Institucion;
use Illuminate\Http\Request;

class InstitucionController extends Controller
{
    public function index(Request $request)
    {
        $q = Institucion::query();
        if ($request->filled('municipio')) $q->where('municipio', $request->municipio);
        if ($request->filled('departamento')) $q->where('departamento', $request->departamento);
        return response()->json($q->orderBy('nombre')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre' => 'required|string',
            'codigo_dane' => 'nullable|string',
            'municipio' => 'nullable|string',
            'departamento' => 'nullable|string',
            'etnia_predominante' => 'nullable|string',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string',
        ]);
        $inst = Institucion::create($data);
        return response()->json($inst, 201);
    }

    public function show($id)
    {
        return response()->json(Institucion::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $inst = Institucion::findOrFail($id);
        $data = $request->validate([
            'nombre' => 'sometimes|string',
            'codigo_dane' => 'nullable|string',
            'municipio' => 'nullable|string',
            'departamento' => 'nullable|string',
            'etnia_predominante' => 'nullable|string',
            'direccion' => 'nullable|string',
            'telefono' => 'nullable|string',
        ]);
        $inst->update($data);
        return response()->json($inst);
    }

    public function destroy($id)
    {
        $inst = Institucion::findOrFail($id);
        $inst->delete();
        return response()->json(['success' => true]);
    }
}


