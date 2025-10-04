<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Profesion;
use App\Models\NivelUsuario;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class AuthControllerMejorado extends Controller
{
    /**
     * Iniciar sesión con validaciones mejoradas
     */
    public function login(Request $request): JsonResponse
    {
        try {
            // Rate limiting mejorado
            $key = 'login.' . $request->ip();
            
            if (RateLimiter::tooManyAttempts($key, 5)) {
                $seconds = RateLimiter::availableIn($key);
                return response()->json([
                    'success' => false,
                    'message' => "Demasiados intentos de login. Intente nuevamente en {$seconds} segundos.",
                    'retry_after' => $seconds
                ], 429);
            }

            // Validación mejorada
            $validator = Validator::make($request->all(), [
                'email' => 'required|email|max:150',
                'password' => 'required|string|min:6|max:255',
                'remember' => 'boolean'
            ], [
                'email.required' => 'El correo electrónico es obligatorio',
                'email.email' => 'El formato del correo electrónico no es válido',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña debe tener al menos 6 caracteres'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Buscar usuario por email
            $usuario = Usuario::where('cor', $request->email)->first();

            if (!$usuario || !Hash::check($request->password, $usuario->con)) {
                RateLimiter::hit($key, 300); // 5 minutos
                return response()->json([
                    'success' => false,
                    'message' => 'Credenciales inválidas'
                ], 401);
            }

            // Verificar estado del usuario
            if ($usuario->est !== 'act') {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario inactivo o suspendido'
                ], 401);
            }

            // Generar token y actualizar último acceso
            $token = $usuario->createToken('auth_token')->plainTextToken;
            $usuario->actualizarUltimoAcceso();

            // Limpiar rate limiting en login exitoso
            RateLimiter::clear($key);

            // Log de login
            Log::crear('login', 'usuarios', $usuario->id, 'Usuario inició sesión');

            // Obtener información completa del usuario
            $usuarioInfo = $this->obtenerInformacionCompletaUsuario($usuario);

            return response()->json([
                'success' => true,
                'message' => 'Login exitoso',
                'data' => [
                    'user' => $usuarioInfo,
                    'token' => $token,
                    'perfil_completo' => $usuario->perfilCompleto(),
                    'requiere_verificacion' => $usuario->estado_verificacion === 'pendiente'
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }

    /**
     * Registro mejorado con validaciones de profesión y nivel
     */
    public function register(Request $request): JsonResponse
    {
        try {
            // Rate limiting para registro
            $key = 'register.' . $request->ip();
            
            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);
                return response()->json([
                    'success' => false,
                    'message' => "Demasiados intentos de registro. Intente nuevamente en {$seconds} segundos.",
                    'retry_after' => $seconds
                ], 429);
            }

            // Validación completa
            $validator = Validator::make($request->all(), [
                'nombres' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                'apellidos' => 'required|string|max:100|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
                'email' => 'required|email|max:150|unique:usu,cor',
                'password' => 'required|string|min:8|max:255|confirmed',
                'telefono' => 'nullable|string|max:20|regex:/^[\+]?[0-9\-\s\(\)]{7,20}$/',
                'documento' => 'required|string|max:20|unique:usu,doc',
                'tipo_documento' => 'required|in:cc,ce,ti,pp,nit',
                'fecha_nacimiento' => 'nullable|date|before:today|after:1900-01-01',
                'direccion' => 'nullable|string|max:200',
                'ciudad' => 'nullable|string|max:100',
                'departamento' => 'nullable|string|max:100',
                'genero' => 'nullable|in:m,f,o,n',
                'rol' => 'required|in:cli,ope,adm',
                // Campos de profesión y nivel
                'profesion_id' => 'nullable|exists:profesiones,id',
                'nivel_id' => 'nullable|exists:niveles_usuario,id',
                'años_experiencia' => 'nullable|integer|min:0|max:50',
                'numero_matricula' => 'nullable|string|max:50',
                'entidad_matricula' => 'nullable|string|max:100',
                'especializaciones' => 'nullable|array',
                'certificaciones' => 'nullable|array',
                'perfil_profesional' => 'nullable|string'
            ], $this->obtenerMensajesValidacion());

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Errores de validación',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Validar compatibilidad de profesión y nivel
            if ($request->profesion_id && $request->nivel_id) {
                $profesion = Profesion::find($request->profesion_id);
                $nivel = NivelUsuario::find($request->nivel_id);
                
                if ($profesion && $nivel && !$profesion->esValidaParaNivel($nivel->numero_nivel)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'La profesión seleccionada no es compatible con el nivel elegido'
                    ], 422);
                }
            }

            // Crear usuario
            $usuario = Usuario::create([
                'nom' => $request->nombres,
                'ape' => $request->apellidos,
                'cor' => $request->email,
                'con' => Hash::make($request->password),
                'tel' => $request->telefono,
                'doc' => $request->documento,
                'tip_doc' => $request->tipo_documento,
                'fec_nac' => $request->fecha_nacimiento,
                'dir' => $request->direccion,
                'ciu' => $request->ciudad,
                'dep' => $request->departamento,
                'gen' => $request->genero ?? 'n',
                'rol' => $request->rol,
                'est' => 'act',
                'cor_ver' => false,
                // Campos de profesión y nivel
                'profesion_id' => $request->profesion_id,
                'nivel_id' => $request->nivel_id,
                'años_experiencia' => $request->años_experiencia ?? 0,
                'numero_matricula' => $request->numero_matricula,
                'entidad_matricula' => $request->entidad_matricula,
                'especializaciones' => $request->especializaciones,
                'certificaciones' => $request->certificaciones,
                'perfil_profesional' => $request->perfil_profesional,
                'estado_verificacion' => 'pendiente'
            ]);

            RateLimiter::clear($key);

            // Log de registro
            Log::crear('registro', 'usuarios', $usuario->id, 
                      "Usuario {$request->rol} registrado: {$usuario->nombre_completo}");

            return response()->json([
                'success' => true,
                'message' => 'Usuario registrado exitosamente',
                'data' => [
                    'user' => $this->obtenerInformacionCompletaUsuario($usuario),
                    'requiere_verificacion' => true
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar usuario',
                'error' => config('app.debug') ? $e->getMessage() : 'Error interno'
            ], 500);
        }
    }

    /**
     * Obtener información completa del usuario
     */
    private function obtenerInformacionCompletaUsuario($usuario): array
    {
        $usuario->load(['profesion', 'nivel', 'verificadoPor']);
        
        return [
            'id' => $usuario->id,
            'nombre' => $usuario->nombre_completo,
            'nombres' => $usuario->nom,
            'apellidos' => $usuario->ape,
            'email' => $usuario->cor,
            'telefono' => $usuario->tel,
            'documento' => $usuario->doc,
            'tipo_documento' => $usuario->tip_doc,
            'fecha_nacimiento' => $usuario->fec_nac,
            'direccion' => $usuario->dir,
            'ciudad' => $usuario->ciu,
            'departamento' => $usuario->dep,
            'genero' => $usuario->gen,
            'rol' => $usuario->rol,
            'estado' => $usuario->est,
            'iniciales' => $usuario->iniciales,
            'correo_verificado' => $usuario->cor_ver,
            'ultimo_acceso' => $usuario->ult_acc,
            // Información de profesión y nivel
            'profesion' => $usuario->profesion ? [
                'id' => $usuario->profesion->id,
                'nombre' => $usuario->profesion->nombre,
                'categoria' => $usuario->profesion->categoria,
                'codigo' => $usuario->profesion->codigo
            ] : null,
            'nivel' => $usuario->nivel ? [
                'id' => $usuario->nivel->id,
                'nombre' => $usuario->nivel->nombre,
                'numero_nivel' => $usuario->nivel->numero_nivel,
                'categoria' => $usuario->nivel->categoria,
                'codigo' => $usuario->nivel->codigo
            ] : null,
            'años_experiencia' => $usuario->años_experiencia,
            'numero_matricula' => $usuario->numero_matricula,
            'entidad_matricula' => $usuario->entidad_matricula,
            'especializaciones' => $usuario->obtenerEspecializaciones(),
            'certificaciones' => $usuario->obtenerCertificaciones(),
            'perfil_profesional' => $usuario->perfil_profesional,
            'perfil_verificado' => $usuario->perfil_verificado,
            'estado_verificacion' => $usuario->estado_verificacion,
            'perfil_completo' => $usuario->perfilCompleto()
        ];
    }

    /**
     * Obtener mensajes de validación personalizados
     */
    private function obtenerMensajesValidacion(): array
    {
        return [
            'nombres.required' => 'Los nombres son obligatorios',
            'nombres.regex' => 'Los nombres solo pueden contener letras y espacios',
            'apellidos.required' => 'Los apellidos son obligatorios',
            'apellidos.regex' => 'Los apellidos solo pueden contener letras y espacios',
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El formato del correo electrónico no es válido',
            'email.unique' => 'Este correo electrónico ya está registrado',
            'password.required' => 'La contraseña es obligatoria',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
            'documento.required' => 'El documento es obligatorio',
            'documento.unique' => 'Este documento ya está registrado',
            'tipo_documento.required' => 'El tipo de documento es obligatorio',
            'tipo_documento.in' => 'El tipo de documento no es válido',
            'telefono.regex' => 'El formato del teléfono no es válido',
            'fecha_nacimiento.date' => 'La fecha de nacimiento no es válida',
            'fecha_nacimiento.before' => 'La fecha de nacimiento debe ser anterior a hoy',
            'genero.in' => 'El género seleccionado no es válido',
            'rol.required' => 'El rol es obligatorio',
            'rol.in' => 'El rol seleccionado no es válido',
            'profesion_id.exists' => 'La profesión seleccionada no existe',
            'nivel_id.exists' => 'El nivel seleccionado no existe',
            'años_experiencia.integer' => 'Los años de experiencia deben ser un número',
            'años_experiencia.min' => 'Los años de experiencia no pueden ser negativos',
            'años_experiencia.max' => 'Los años de experiencia no pueden ser mayores a 50'
        ];
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request): JsonResponse
    {
        try {
            if ($request->user()) {
                $request->user()->currentAccessToken()->delete();
            }
            
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'success' => true,
                'message' => 'Sesión cerrada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al cerrar sesión'
            ], 500);
        }
    }

    /**
     * Obtener información del usuario autenticado
     */
    public function me(Request $request): JsonResponse
    {
        try {
            $usuario = $request->user();
            
            if (!$usuario) {
                return response()->json([
                    'success' => false,
                    'message' => 'Usuario no autenticado'
                ], 401);
            }

            return response()->json([
                'success' => true,
                'data' => $this->obtenerInformacionCompletaUsuario($usuario)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener información del usuario'
            ], 500);
        }
    }

    /**
     * Obtener profesiones disponibles
     */
    public function profesiones(): JsonResponse
    {
        try {
            $profesiones = Profesion::activos()
                ->orderBy('categoria')
                ->orderBy('nombre')
                ->get(['id', 'codigo', 'nombre', 'categoria', 'nivel_minimo', 'nivel_maximo', 'requiere_matricula']);

            return response()->json([
                'success' => true,
                'data' => $profesiones
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener profesiones'
            ], 500);
        }
    }

    /**
     * Obtener niveles disponibles
     */
    public function niveles(): JsonResponse
    {
        try {
            $niveles = NivelUsuario::activos()
                ->orderBy('categoria')
                ->orderBy('numero_nivel')
                ->get(['id', 'codigo', 'nombre', 'categoria', 'numero_nivel', 'experiencia_requerida', 'requiere_aprobacion']);

            return response()->json([
                'success' => true,
                'data' => $niveles
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al obtener niveles'
            ], 500);
        }
    }
}
