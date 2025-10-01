<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Pagina;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Mostrar formulario de login
     */
    public function mostrarLogin()
    {
        if (auth()->check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Procesar login
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'correo' => 'required|email',
            'password' => 'required|string',
            'remember' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        $key = 'login.' . $request->ip();
        
        // Rate limiting
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return response()->json([
                'success' => false,
                'message' => "Demasiados intentos de login. Intente nuevamente en {$seconds} segundos."
            ], 429);
        }

        $credentials = [
            'cor' => $request->correo,
            'password' => $request->password
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($key);
            
            $usuario = Auth::user();
            $usuario->actualizarUltimoAcceso();
            
            // Obtener páginas accesibles para el usuario
            $paginasAccesibles = Pagina::obtenerPaginasAccesibles($usuario);
            
            $token = $usuario->createToken('auth-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'message' => 'Login exitoso',
                'data' => [
                    'usuario' => [
                        'id' => $usuario->id,
                        'nombre' => $usuario->nombre_completo,
                        'email' => $usuario->cor,
                        'rol' => $usuario->rol,
                        'iniciales' => $usuario->iniciales
                    ],
                    'token' => $token,
                    'paginas_accesibles' => $paginasAccesibles,
                    'redirect_to' => $this->obtenerRutaRedireccion($usuario)
                ]
            ]);
        }

        RateLimiter::hit($key, 300); // 5 minutos

        return response()->json([
            'success' => false,
            'message' => 'Credenciales inválidas'
        ], 401);
    }

    /**
     * Cerrar sesión
     */
    public function logout(Request $request): JsonResponse
    {
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
    }

    /**
     * Obtener información del usuario autenticado
     */
    public function usuario(Request $request): JsonResponse
    {
        $usuario = $request->user();
        
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        $paginasAccesibles = Pagina::obtenerPaginasAccesibles($usuario);

        return response()->json([
            'success' => true,
            'data' => [
                'usuario' => [
                    'id' => $usuario->id,
                    'nombre' => $usuario->nombre_completo,
                    'email' => $usuario->cor,
                    'rol' => $usuario->rol,
                    'iniciales' => $usuario->iniciales,
                    'ultimo_acceso' => $usuario->ult_acc
                ],
                'paginas_accesibles' => $paginasAccesibles,
                'permisos' => $usuario->obtenerPermisos()
            ]
        ]);
    }

    /**
     * Verificar si el usuario puede acceder a una ruta específica
     */
    public function verificarAcceso(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'ruta' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Ruta requerida',
                'errors' => $validator->errors()
            ], 400);
        }

        $usuario = $request->user();
        
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado',
                'puede_acceder' => false
            ], 401);
        }

        $ruta = $request->ruta;
        $pagina = Pagina::where('ruta', $ruta)->first();
        
        if (!$pagina) {
            return response()->json([
                'success' => true,
                'puede_acceder' => true,
                'message' => 'Página no registrada, acceso permitido'
            ]);
        }

        $puedeAcceder = $pagina->puedeAcceder($usuario);

        return response()->json([
            'success' => true,
            'puede_acceder' => $puedeAcceder,
            'pagina' => $pagina,
            'message' => $puedeAcceder ? 'Acceso permitido' : 'Acceso denegado'
        ]);
    }

    /**
     * Obtener páginas accesibles para el usuario autenticado
     */
    public function paginasAccesibles(Request $request): JsonResponse
    {
        $usuario = $request->user();
        
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no autenticado'
            ], 401);
        }

        $paginas = Pagina::obtenerPaginasAccesibles($usuario);

        return response()->json([
            'success' => true,
            'data' => $paginas
        ]);
    }

    /**
     * Obtener la ruta de redirección según el rol del usuario
     */
    private function obtenerRutaRedireccion(Usuario $usuario): string
    {
        switch ($usuario->rol) {
            case 'adm_gen':
                return '/admin-general';
            case 'adm':
                return '/admin';
            case 'ope':
                return '/operador';
            case 'cli':
            default:
                return '/';
        }
    }

    /**
     * Cambiar contraseña
     */
    public function cambiarPassword(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password_actual' => 'required|string',
            'password_nuevo' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        $usuario = $request->user();
        
        if (!Hash::check($request->password_actual, $usuario->con)) {
            return response()->json([
                'success' => false,
                'message' => 'La contraseña actual es incorrecta'
            ], 400);
        }

        $usuario->update([
            'con' => $request->password_nuevo
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente'
        ]);
    }

    /**
     * Actualizar perfil
     */
    public function actualizarPerfil(Request $request): JsonResponse
    {
        $usuario = $request->user();
        
        $validator = Validator::make($request->all(), [
            'nom' => 'sometimes|string|max:100',
            'ape' => 'sometimes|string|max:100',
            'tel' => 'nullable|string|max:20',
            'dir' => 'nullable|string|max:255',
            'ciu' => 'nullable|string|max:100',
            'dep' => 'nullable|string|max:100',
            'gen' => 'nullable|string|in:m,f,o,n'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Datos inválidos',
                'errors' => $validator->errors()
            ], 400);
        }

        $usuario->update($request->only([
            'nom', 'ape', 'tel', 'dir', 'ciu', 'dep', 'gen'
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Perfil actualizado correctamente',
            'data' => [
                'usuario' => [
                    'id' => $usuario->id,
                    'nombre' => $usuario->nombre_completo,
                    'email' => $usuario->cor,
                    'rol' => $usuario->rol,
                    'iniciales' => $usuario->iniciales
                ]
            ]
        ]);
    }
}
