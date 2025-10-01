<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\IADerechosEspecializados;
use Illuminate\Support\Facades\Validator;

class IADerechosEspecializadosController extends Controller
{
    protected $iaDerechos;

    public function __construct(IADerechosEspecializados $iaDerechos)
    {
        $this->iaDerechos = $iaDerechos;
    }

    /**
     * Análisis de Derechos Mineros
     */
    public function analizarDerechosMineros(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'datos_mineros' => 'required|string|min:10',
            'tipo_mineria' => 'nullable|string|in:general,oro,carbon,esmeraldas,petroleo,gas'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaDerechos->analizarDerechosMineros(
                $request->datos_mineros,
                $request->tipo_mineria ?? 'general'
            );

            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de derechos mineros',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Derechos Catastrales
     */
    public function analizarDerechosCatastrales(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'datos_catastrales' => 'required|string|min:10',
            'tipo_predio' => 'nullable|string|in:general,urbano,rural,comercial,residencial'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaDerechos->analizarDerechosCatastrales(
                $request->datos_catastrales,
                $request->tipo_predio ?? 'general'
            );

            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de derechos catastrales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Desarrollo Territorial
     */
    public function analizarDesarrolloTerritorial(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'datos_territoriales' => 'required|string|min:10',
            'nivel_gobierno' => 'nullable|string|in:municipal,departamental,nacional'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaDerechos->analizarDesarrolloTerritorial(
                $request->datos_territoriales,
                $request->nivel_gobierno ?? 'municipal'
            );

            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de desarrollo territorial',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Planes de Desarrollo y Gobierno
     */
    public function analizarPlanesDesarrolloGobierno(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'datos_planes' => 'required|string|min:10',
            'tipo_plan' => 'nullable|string|in:desarrollo,gobierno,ordenamiento,presupuesto'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaDerechos->analizarPlanesDesarrolloGobierno(
                $request->datos_planes,
                $request->tipo_plan ?? 'desarrollo'
            );

            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de planes de desarrollo y gobierno',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Derechos Internacionales
     */
    public function analizarDerechosInternacionales(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caso_internacional' => 'required|string|min:10',
            'area_derecho' => 'nullable|string|in:derechos_humanos,derecho_humanitario,derecho_ambiental,derecho_economico'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaDerechos->analizarDerechosInternacionales(
                $request->caso_internacional,
                $request->area_derecho ?? 'derechos_humanos'
            );

            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de derechos internacionales',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Derechos CAN e INCA
     */
    public function analizarDerechosCanInca(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'datos_can_inca' => 'required|string|min:10',
            'tipo_derecho' => 'nullable|string|in:can,inca'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaDerechos->analizarDerechosCanInca(
                $request->datos_can_inca,
                $request->tipo_derecho ?? 'can'
            );

            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de derechos CAN e INCA',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Derechos Latinoamericanos
     */
    public function analizarDerechosLatinoamericanos(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'caso_latinoamericano' => 'required|string|min:10',
            'pais' => 'nullable|string|in:colombia,venezuela,ecuador,peru,bolivia,chile,argentina,brasil,mexico'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaDerechos->analizarDerechosLatinoamericanos(
                $request->caso_latinoamericano,
                $request->pais ?? 'colombia'
            );

            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de derechos latinoamericanos',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Derechos de Propiedad
     */
    public function analizarDerechosPropiedad(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'datos_propiedad' => 'required|string|min:10',
            'tipo_propiedad' => 'nullable|string|in:raiz,mueble,inmueble,intelectual,industrial'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaDerechos->analizarDerechosPropiedad(
                $request->datos_propiedad,
                $request->tipo_propiedad ?? 'raiz'
            );

            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de derechos de propiedad',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Análisis de Derechos en Comunidades Étnicas
     */
    public function analizarDerechosComunidadesEtnicas(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'datos_etnicos' => 'required|string|min:10',
            'tipo_comunidad' => 'nullable|string|in:indigena,afrodescendiente,raizal,palenquero'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos de entrada inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $resultado = $this->iaDerechos->analizarDerechosComunidadesEtnicas(
                $request->datos_etnicos,
                $request->tipo_comunidad ?? 'indigena'
            );

            return response()->json($resultado);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error procesando análisis de derechos en comunidades étnicas',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Obtener todas las especialidades disponibles
     */
    public function obtenerEspecialidades()
    {
        return response()->json([
            'success' => true,
            'especialidades' => [
                'derechos_mineros' => [
                    'nombre' => 'Derechos Mineros',
                    'descripcion' => 'Análisis especializado de derechos mineros nacionales e internacionales',
                    'tipos' => ['general', 'oro', 'carbon', 'esmeraldas', 'petroleo', 'gas']
                ],
                'derechos_catastrales' => [
                    'nombre' => 'Derechos Catastrales',
                    'descripcion' => 'Análisis de derechos catastrales e inmobiliarios',
                    'tipos' => ['general', 'urbano', 'rural', 'comercial', 'residencial']
                ],
                'desarrollo_territorial' => [
                    'nombre' => 'Desarrollo Territorial',
                    'descripcion' => 'Análisis de desarrollo territorial y planificación',
                    'tipos' => ['municipal', 'departamental', 'nacional']
                ],
                'planes_desarrollo_gobierno' => [
                    'nombre' => 'Planes de Desarrollo y Gobierno',
                    'descripcion' => 'Análisis de planes de desarrollo y gobierno',
                    'tipos' => ['desarrollo', 'gobierno', 'ordenamiento', 'presupuesto']
                ],
                'derechos_internacionales' => [
                    'nombre' => 'Derechos Internacionales',
                    'descripcion' => 'Análisis de derechos internacionales y derecho internacional público',
                    'tipos' => ['derechos_humanos', 'derecho_humanitario', 'derecho_ambiental', 'derecho_economico']
                ],
                'derechos_can_inca' => [
                    'nombre' => 'Derechos CAN e INCA',
                    'descripcion' => 'Análisis de derechos de integración regional',
                    'tipos' => ['can', 'inca']
                ],
                'derechos_latinoamericanos' => [
                    'nombre' => 'Derechos Latinoamericanos',
                    'descripcion' => 'Análisis de derechos comparado latinoamericano',
                    'tipos' => ['colombia', 'venezuela', 'ecuador', 'peru', 'bolivia', 'chile', 'argentina', 'brasil', 'mexico']
                ],
                'derechos_propiedad' => [
                    'nombre' => 'Derechos de Propiedad',
                    'descripcion' => 'Análisis de derechos de propiedad en raíz y propiedad',
                    'tipos' => ['raiz', 'mueble', 'inmueble', 'intelectual', 'industrial']
                ],
                'derechos_comunidades_etnicas' => [
                    'nombre' => 'Derechos en Comunidades Étnicas',
                    'descripcion' => 'Análisis de derechos en comunidades étnicas',
                    'tipos' => ['indigena', 'afrodescendiente', 'raizal', 'palenquero']
                ]
            ]
        ]);
    }
}
