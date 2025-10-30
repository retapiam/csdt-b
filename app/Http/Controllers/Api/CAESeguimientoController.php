<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CAESeguimiento;
use Illuminate\Http\Request;

class CAESeguimientoController extends Controller
{
    public function index(Request $request)
    {
        $q = CAESeguimiento::query();
        if ($request->filled('comite_id')) $q->where('comite_id', $request->comite_id);
        return response()->json($q->orderBy('fecha','desc')->paginate(20));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'comite_id' => 'required|integer',
            'fecha' => 'required|date',
            'hallazgos' => 'nullable|string',
            'checklist' => 'nullable|array',
            'evidencias' => 'nullable|array',
        ]);
        $s = CAESeguimiento::create($data);
        return response()->json($s, 201);
    }

    public function show($id)
    {
        return response()->json(CAESeguimiento::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $s = CAESeguimiento::findOrFail($id);
        $data = $request->validate([
            'hallazgos' => 'nullable|string',
            'checklist' => 'nullable|array',
            'evidencias' => 'nullable|array',
        ]);
        $s->update($data);
        return response()->json($s);
    }

    public function destroy($id)
    {
        $s = CAESeguimiento::findOrFail($id);
        $s->delete();
        return response()->json(['success' => true]);
    }
}


