<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CAEActa;
use Illuminate\Http\Request;

class CAEActaController extends Controller
{
    public function index(Request $request)
    {
        $q = CAEActa::query();
        if ($request->filled('comite_id')) $q->where('comite_id', $request->comite_id);
        return response()->json($q->orderBy('fecha','desc')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'comite_id' => 'required|integer',
            'fecha' => 'required|date',
            'acuerdos' => 'nullable|string',
            'evidencias' => 'nullable|array',
        ]);
        $a = CAEActa::create($data);
        return response()->json($a, 201);
    }

    public function show($id)
    {
        return response()->json(CAEActa::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $a = CAEActa::findOrFail($id);
        $data = $request->validate([
            'acuerdos' => 'nullable|string',
            'evidencias' => 'nullable|array',
        ]);
        $a->update($data);
        return response()->json($a);
    }

    public function destroy($id)
    {
        $a = CAEActa::findOrFail($id);
        $a->delete();
        return response()->json(['success' => true]);
    }
}


