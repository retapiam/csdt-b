<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MenuPAE;
use Illuminate\Http\Request;

class MenuPAEController extends Controller
{
    public function index(Request $request)
    {
        $q = MenuPAE::query();
        if ($request->filled('institucion_id')) $q->where('institucion_id', $request->institucion_id);
        return response()->json($q->orderBy('id','desc')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'institucion_id' => 'required|integer',
            'nombre' => 'required|string',
            'componentes' => 'nullable|array',
            'calorias' => 'nullable|integer',
            'restricciones_culturales' => 'nullable|array',
        ]);
        $m = MenuPAE::create($data);
        return response()->json($m, 201);
    }

    public function show($id)
    {
        return response()->json(MenuPAE::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $m = MenuPAE::findOrFail($id);
        $data = $request->validate([
            'nombre' => 'sometimes|string',
            'componentes' => 'nullable|array',
            'calorias' => 'nullable|integer',
            'restricciones_culturales' => 'nullable|array',
        ]);
        $m->update($data);
        return response()->json($m);
    }

    public function destroy($id)
    {
        $m = MenuPAE::findOrFail($id);
        $m->delete();
        return response()->json(['success' => true]);
    }
}


