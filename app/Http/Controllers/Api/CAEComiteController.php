<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CAEComite;
use Illuminate\Http\Request;

class CAEComiteController extends Controller
{
    public function index(Request $request)
    {
        $q = CAEComite::query();
        if ($request->filled('institucion_id')) $q->where('institucion_id', $request->institucion_id);
        return response()->json($q->orderBy('id','desc')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'institucion_id' => 'required|integer',
            'nombre' => 'required|string',
            'miembros' => 'nullable|array',
        ]);
        $c = CAEComite::create($data);
        return response()->json($c, 201);
    }

    public function show($id)
    {
        return response()->json(CAEComite::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $c = CAEComite::findOrFail($id);
        $data = $request->validate([
            'nombre' => 'nullable|string',
            'miembros' => 'nullable|array',
        ]);
        $c->update($data);
        return response()->json($c);
    }

    public function destroy($id)
    {
        $c = CAEComite::findOrFail($id);
        $c->delete();
        return response()->json(['success' => true]);
    }
}


