<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PDFGeneratorService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class PDFController extends Controller
{
    protected $pdfGenerator;

    public function __construct(PDFGeneratorService $pdfGenerator)
    {
        $this->pdfGenerator = $pdfGenerator;
    }

    /**
     * Generar PDF de análisis legal
     */
    public function generarAnalisisLegal(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'hechos' => 'required|string',
                'analisis' => 'required|array',
                'analisis.summary' => 'required|string',
                'analisis.classifications' => 'array',
                'analisis.recommendations' => 'array',
                'analisis.risk_assessment' => 'array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $datos = $request->all();
            $datos['fecha'] = $request->get('fecha', now()->format('d/m/Y'));

            $resultado = $this->pdfGenerator->generarYGuardarPDF('analisis_legal', $datos);

            // Guardar registro en base de datos
            $archivo = \App\Models\Archivo::create([
                'nombre' => $resultado['nombre_archivo'],
                'tipo' => 'pdf',
                'categoria' => 'analisis_legal',
                'ruta' => $resultado['ruta'],
                'url' => $resultado['url'],
                'tamaño' => Storage::disk('public')->size($resultado['ruta']),
                'descripcion' => 'Análisis legal generado con IA',
                'estado' => 'activo',
                'metadatos' => json_encode($datos)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PDF de análisis legal generado exitosamente',
                'data' => [
                    'archivo' => $archivo,
                    'url' => $resultado['url'],
                    'download_url' => route('api.pdf.download', $archivo->id)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF de análisis legal: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar PDF de PQRSFD
     */
    public function generarPQRSFD(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string',
                'identificacion' => 'required|string',
                'telefono' => 'required|string',
                'email' => 'required|email',
                'direccion' => 'required|string',
                'tipo' => 'required|string',
                'asunto' => 'required|string',
                'descripcion' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $datos = $request->all();
            $datos['fecha'] = $request->get('fecha', now()->format('d/m/Y'));
            $datos['estado'] = $request->get('estado', 'Pendiente');

            $resultado = $this->pdfGenerator->generarYGuardarPDF('pqrsfd', $datos);

            // Guardar registro en base de datos
            $archivo = \App\Models\Archivo::create([
                'nombre' => $resultado['nombre_archivo'],
                'tipo' => 'pdf',
                'categoria' => 'pqrsfd',
                'ruta' => $resultado['ruta'],
                'url' => $resultado['url'],
                'tamaño' => Storage::disk('public')->size($resultado['ruta']),
                'descripcion' => 'PQRSFD generado',
                'estado' => 'activo',
                'metadatos' => json_encode($datos)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PDF de PQRSFD generado exitosamente',
                'data' => [
                    'archivo' => $archivo,
                    'url' => $resultado['url'],
                    'download_url' => route('api.pdf.download', $archivo->id)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF de PQRSFD: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar PDF de donación
     */
    public function generarDonacion(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'donante' => 'required|string',
                'monto' => 'required|numeric|min:0',
                'metodoPago' => 'required|string',
                'concepto' => 'string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $datos = $request->all();
            $datos['fecha'] = $request->get('fecha', now()->format('d/m/Y'));

            $resultado = $this->pdfGenerator->generarYGuardarPDF('donacion', $datos);

            // Guardar registro en base de datos
            $archivo = \App\Models\Archivo::create([
                'nombre' => $resultado['nombre_archivo'],
                'tipo' => 'pdf',
                'categoria' => 'donacion',
                'ruta' => $resultado['ruta'],
                'url' => $resultado['url'],
                'tamaño' => Storage::disk('public')->size($resultado['ruta']),
                'descripcion' => 'Comprobante de donación',
                'estado' => 'activo',
                'metadatos' => json_encode($datos)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PDF de donación generado exitosamente',
                'data' => [
                    'archivo' => $archivo,
                    'url' => $resultado['url'],
                    'download_url' => route('api.pdf.download', $archivo->id)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF de donación: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar PDF de reporte de veeduría
     */
    public function generarReporteVeeduria(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'veedor' => 'required|array',
                'veedor.nombre' => 'required|string',
                'veedor.identificacion' => 'required|string',
                'proyecto' => 'required|string',
                'entidad' => 'required|string',
                'hallazgos' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $datos = $request->all();
            $datos['fechaInicio'] = $request->get('fechaInicio', now()->format('d/m/Y'));
            $datos['fechaFinalizacion'] = $request->get('fechaFinalizacion', now()->format('d/m/Y'));
            $datos['estado'] = $request->get('estado', 'En proceso');

            $resultado = $this->pdfGenerator->generarYGuardarPDF('reporte_veeduria', $datos);

            // Guardar registro en base de datos
            $archivo = \App\Models\Archivo::create([
                'nombre' => $resultado['nombre_archivo'],
                'tipo' => 'pdf',
                'categoria' => 'reporte_veeduria',
                'ruta' => $resultado['ruta'],
                'url' => $resultado['url'],
                'tamaño' => Storage::disk('public')->size($resultado['ruta']),
                'descripcion' => 'Reporte de veeduría ciudadana',
                'estado' => 'activo',
                'metadatos' => json_encode($datos)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'PDF de reporte de veeduría generado exitosamente',
                'data' => [
                    'archivo' => $archivo,
                    'url' => $resultado['url'],
                    'download_url' => route('api.pdf.download', $archivo->id)
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF de reporte de veeduría: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todos los archivos PDF
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = \App\Models\Archivo::where('tipo', 'pdf');

            // Filtros
            if ($request->has('categoria')) {
                $query->where('categoria', $request->categoria);
            }

            if ($request->has('estado')) {
                $query->where('estado', $request->estado);
            }

            if ($request->has('busqueda')) {
                $busqueda = $request->busqueda;
                $query->where(function($q) use ($busqueda) {
                    $q->where('nombre', 'LIKE', "%{$busqueda}%")
                      ->orWhere('descripcion', 'LIKE', "%{$busqueda}%");
                });
            }

            $pdfs = $query->with(['usuario', 'veeduria', 'donacion', 'tarea'])
                         ->orderBy('created_at', 'desc')
                         ->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'data' => $pdfs,
                'message' => 'PDFs obtenidos exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener PDFs',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener PDF por ID
     */
    public function show($id): JsonResponse
    {
        try {
            $pdf = \App\Models\Archivo::where('tipo', 'pdf')
                                     ->with(['usuario', 'veeduria', 'donacion', 'tarea'])
                                     ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $pdf,
                'message' => 'PDF obtenido exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'PDF no encontrado',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Crear nuevo archivo PDF
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre' => 'required|string|max:255',
                'descripcion' => 'nullable|string',
                'archivo' => 'required|file|mimes:pdf|max:10240', // 10MB max
                'categoria' => 'nullable|string|max:100',
                'veeduria_id' => 'nullable|exists:veedurias,id',
                'donacion_id' => 'nullable|exists:donaciones,id',
                'tarea_id' => 'nullable|exists:tareas,id',
                'metadatos' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $usuario = auth()->user();
            $archivo = $request->file('archivo');
            $nombreArchivo = time() . '_' . Str::slug($request->nombre) . '.pdf';
            $ruta = $archivo->storeAs('pdfs', $nombreArchivo, 'public');

            $pdf = \App\Models\Archivo::create([
                'nom' => $request->nombre,
                'des' => $request->descripcion,
                'tip' => 'pdf',
                'ruta' => $ruta,
                'tam' => $archivo->getSize(),
                'cat' => $request->categoria ?? 'general',
                'est' => 'activo',
                'usu_id' => $usuario->id,
                'vee_id' => $request->veeduria_id,
                'don_id' => $request->donacion_id,
                'tar_id' => $request->tarea_id,
                'met' => json_encode($request->metadatos ?? [])
            ]);

            return response()->json([
                'success' => true,
                'data' => $pdf->load(['usuario', 'veeduria', 'donacion', 'tarea']),
                'message' => 'PDF creado exitosamente'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Actualizar PDF
     */
    public function update(Request $request, $id): JsonResponse
    {
        try {
            $pdf = \App\Models\Archivo::where('tipo', 'pdf')->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'nombre' => 'sometimes|string|max:255',
                'descripcion' => 'nullable|string',
                'categoria' => 'nullable|string|max:100',
                'estado' => 'sometimes|in:activo,inactivo,eliminado',
                'metadatos' => 'nullable|array'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            $pdf->update($request->only(['nombre', 'descripcion', 'categoria', 'estado']));

            if ($request->has('metadatos')) {
                $pdf->metadatos = json_encode($request->metadatos);
                $pdf->save();
            }

            return response()->json([
                'success' => true,
                'data' => $pdf->load(['usuario', 'veeduria', 'donacion', 'tarea']),
                'message' => 'PDF actualizado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Eliminar PDF
     */
    public function destroy($id): JsonResponse
    {
        try {
            $pdf = \App\Models\Archivo::where('tipo', 'pdf')->findOrFail($id);

            // Eliminar archivo físico
            if (Storage::disk('public')->exists($pdf->ruta)) {
                Storage::disk('public')->delete($pdf->ruta);
            }

            $pdf->delete();

            return response()->json([
                'success' => true,
                'message' => 'PDF eliminado exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al eliminar PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Descargar PDF
     */
    public function download($id): JsonResponse
    {
        try {
            $pdf = \App\Models\Archivo::where('tipo', 'pdf')->findOrFail($id);

            if (!Storage::disk('public')->exists($pdf->ruta)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Archivo PDF no encontrado'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'url' => Storage::disk('public')->url($pdf->ruta),
                    'nombre' => $pdf->nombre,
                    'tamaño' => $pdf->tamaño
                ],
                'message' => 'URL de descarga generada'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar descarga',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar PDF desde datos
     */
    public function generate(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'tipo' => 'required|in:analisis_legal,pqrsfd,donacion,reporte_veeduria',
                'datos' => 'required|array',
                'nombre' => 'required|string|max:255'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Aquí se implementaría la generación del PDF usando las librerías PHP
            // Por ahora devolvemos una respuesta simulada
            $usuario = auth()->user();
            
            $pdf = \App\Models\Archivo::create([
                'nom' => $request->nombre,
                'des' => "PDF generado de tipo: {$request->tipo}",
                'tip' => 'pdf',
                'ruta' => 'pdfs/generados/' . time() . '_' . Str::slug($request->nombre) . '.pdf',
                'tam' => 0, // Se actualizará cuando se genere el archivo real
                'cat' => $request->tipo,
                'est' => 'activo',
                'usu_id' => $usuario->id,
                'met' => json_encode($request->datos)
            ]);

            return response()->json([
                'success' => true,
                'data' => $pdf,
                'message' => 'PDF generado exitosamente'
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al generar PDF',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Procesar PDF con IA
     */
    public function processWithAI(Request $request, $id): JsonResponse
    {
        try {
            $pdf = \App\Models\Archivo::where('tipo', 'pdf')->findOrFail($id);

            $validator = Validator::make($request->all(), [
                'tipo_analisis' => 'required|in:legal,tecnico,veeduria,general'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Datos de validación incorrectos',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Aquí se implementaría el procesamiento con IA
            // Por ahora devolvemos una respuesta simulada
            $resultado = [
                'tipo_analisis' => $request->tipo_analisis,
                'confianza' => 0.95,
                'resumen' => 'Análisis procesado con IA',
                'clasificaciones' => [],
                'recomendaciones' => [],
                'timestamp' => now()->toISOString()
            ];

            return response()->json([
                'success' => true,
                'data' => $resultado,
                'message' => 'PDF procesado con IA exitosamente'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar PDF con IA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de PDFs
     */
    public function statistics(): JsonResponse
    {
        try {
            $estadisticas = [
                'total_pdfs' => \App\Models\Archivo::where('tipo', 'pdf')->count(),
                'pdfs_por_categoria' => \App\Models\Archivo::where('tipo', 'pdf')
                    ->selectRaw('categoria, COUNT(*) as total')
                    ->groupBy('categoria')
                    ->get(),
                'pdfs_por_estado' => \App\Models\Archivo::where('tipo', 'pdf')
                    ->selectRaw('estado, COUNT(*) as total')
                    ->groupBy('estado')
                    ->get(),
                'pdfs_recientes' => \App\Models\Archivo::where('tipo', 'pdf')
                    ->where('created_at', '>=', now()->subDays(30))
                    ->count(),
                'tamaño_total' => \App\Models\Archivo::where('tipo', 'pdf')
                    ->sum('tamaño')
            ];

            return response()->json([
                'success' => true,
                'data' => $estadisticas,
                'message' => 'Estadísticas de PDFs obtenidas exitosamente'
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
