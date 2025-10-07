<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\RegistroIntento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * Registro de nuevo usuario con validación avanzada y logging
     */
    public function register(Request $request)
    {
        // Normalizar campos (compatibilidad con frontend - inglés y español)
        $nombres = $request->input('nombres') ?? $request->input('name') ?? $request->input('nombreCompleto') ?? '';
        $apellidos = $request->input('apellidos') ?? '';
        $name = trim($nombres . ' ' . $apellidos) ?: ($request->input('nom') . ' ' . $request->input('ape'));
        $email = strtolower(trim($request->input('email') ?? $request->input('cor') ?? ''));
        $password = $request->input('password') ?? $request->input('con') ?? $request->input('contrasena') ?? '';
        $telefono = $request->input('telefono') ?? $request->input('tel') ?? '';
        $documento = trim($request->input('documento') ?? $request->input('doc') ?? $request->input('numeroDocumento') ?? '');
        $tipo_documento = $request->input('tipo_documento') ?? $request->input('tip_doc') ?? $request->input('tipoDocumento') ?? '';
        $rol = $request->input('rol') ?? 'cliente';

        // Preparar datos para logging
        $datosRegistro = compact('name', 'email', 'documento', 'tipo_documento', 'telefono', 'rol');

        // Validación con reglas personalizadas (soporte inglés y español)
        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'rol' => $rol,
            'telefono' => $telefono,
            'documento' => $documento,
            'tipo_documento' => $tipo_documento
        ], [
            'name' => 'required|string|max:255|regex:/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/',
            'rol' => 'nullable|in:administrador,operador,cliente,superadmin,cli,adm,ope,sup',
            'telefono' => 'nullable|string|max:20|regex:/^[0-9+\-\s()]+$/',
            'documento' => 'required|string|max:50|unique:users,documento',
            'tipo_documento' => 'nullable|in:CC,CE,TI,NIT,Pasaporte,cc,ce,ti,nit,pasaporte',
        ], [
            'name.regex' => 'El nombre solo puede contener letras y espacios.',
            'email.unique' => 'Este correo electrónico ya está registrado. Por favor, use otro o inicie sesión.',
            'documento.unique' => 'Este número de documento ya está registrado. Por favor, verifique los datos.',
            'documento.required' => 'El número de documento es obligatorio.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula, una minúscula y un número.',
            'name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'telefono.regex' => 'El teléfono solo puede contener números y caracteres válidos.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $message = 'Error de validación';
            
            // Mensaje personalizado si es email o documento duplicado
            if ($errors->has('email')) {
                $message = 'El usuario ya se encuentra registrado con este correo electrónico. Por favor, use otro email o inicie sesión.';
                
                // Registrar intento duplicado por email
                RegistroIntento::registrarIntento(
                    $datosRegistro,
                    'duplicado_email',
                    'duplicado',
                    $message,
                    $errors->toArray()
                );
            } elseif ($errors->has('documento')) {
                $message = 'El usuario ya se encuentra registrado con este número de documento. Por favor, verifique los datos o inicie sesión.';
                
                // Registrar intento duplicado por documento
                RegistroIntento::registrarIntento(
                    $datosRegistro,
                    'duplicado_documento',
                    'duplicado',
                    $message,
                    $errors->toArray()
                );
            } else {
                // Registrar intento fallido por validación
                $message = 'Por favor, corrija los errores en el formulario e intente nuevamente.';
                RegistroIntento::registrarIntento(
                    $datosRegistro,
                    'registro',
                    'fallido',
                    $message,
                    $errors->toArray()
                );
            }
            
            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => $errors
            ], 422);
        }

        try {
            // Usar transacción para garantizar consistencia
            DB::beginTransaction();

            // Verificación adicional de usuarios duplicados
            $usuarioExistenteEmail = User::where('email', $email)->first();
            if ($usuarioExistenteEmail) {
                DB::rollBack();
                
                // Registrar intento duplicado
                RegistroIntento::registrarIntento(
                    $datosRegistro,
                    'duplicado_email',
                    'duplicado',
                    'Ya existe un usuario con este correo electrónico',
                    ['email' => ['El correo electrónico ya está registrado']]
                );

                return response()->json([
                    'success' => false,
                    'message' => 'El usuario ya se encuentra registrado con este correo electrónico. Fecha de registro: ' . $usuarioExistenteEmail->created_at->format('d/m/Y') . '. Por favor, use otro email o inicie sesión.',
                    'errors' => ['email' => ['El correo electrónico ya está registrado']],
                    'usuario_existente' => [
                        'email' => $usuarioExistenteEmail->email,
                        'fecha_registro' => $usuarioExistenteEmail->created_at->format('d/m/Y'),
                        'tipo_registro' => 'email'
                    ]
                ], 422);
            }
            
            $usuarioExistenteDoc = User::where('documento', $documento)->first();
            if ($usuarioExistenteDoc) {
                DB::rollBack();
                
                // Registrar intento duplicado
                RegistroIntento::registrarIntento(
                    $datosRegistro,
                    'duplicado_documento',
                    'duplicado',
                    'Ya existe un usuario con este número de documento',
                    ['documento' => ['El número de documento ya está registrado']]
                );

                return response()->json([
                    'success' => false,
                    'message' => 'El usuario ya se encuentra registrado con este número de documento (' . $usuarioExistenteDoc->tipo_documento . ': ' . $usuarioExistenteDoc->documento . '). Fecha de registro: ' . $usuarioExistenteDoc->created_at->format('d/m/Y') . '. Por favor, verifique los datos o inicie sesión.',
                    'errors' => ['documento' => ['El número de documento ya está registrado']],
                    'usuario_existente' => [
                        'documento' => $usuarioExistenteDoc->documento,
                        'tipo_documento' => $usuarioExistenteDoc->tipo_documento,
                        'fecha_registro' => $usuarioExistenteDoc->created_at->format('d/m/Y'),
                        'tipo_registro' => 'documento'
                    ]
                ], 422);
            }

            // Mapear roles abreviados a nombres completos
            $rolMap = [
                'cli' => 'cliente',
                'adm' => 'administrador',
                'ope' => 'operador',
                'sup' => 'superadmin'
            ];
            $rolFinal = $rolMap[$rol] ?? $rol;

            // Normalizar tipo_documento a mayúsculas
            $tipo_documento_normalizado = $tipo_documento ? strtoupper($tipo_documento) : 'CC';

            // Crear usuario
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => Hash::make($password),
                'rol' => $rolFinal,
                'estado' => 'activo',
                'telefono' => $telefono,
                'documento' => $documento,
                'tipo_documento' => $tipo_documento_normalizado,
            ]);

            // Registrar intento exitoso
            RegistroIntento::registrarIntento(
                $datosRegistro,
                'registro',
                'exitoso',
                'Usuario registrado exitosamente'
            );

            // Crear token de autenticación
            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => '¡Registro exitoso! El usuario ha sido registrado correctamente en la base de datos. Bienvenido a CSDT.',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                ],
                'registro_info' => [
                    'fecha_registro' => $user->created_at->format('d/m/Y H:i:s'),
                    'estado' => 'activo',
                    'conexion_db' => 'conectado'
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            
            // Registrar intento fallido por error del sistema
            RegistroIntento::registrarIntento(
                $datosRegistro,
                'registro',
                'fallido',
                'Error del sistema: ' . $e->getMessage(),
                ['error' => $e->getMessage()]
            );

            // Detectar si es error de conexión a la base de datos
            $isDbError = str_contains($e->getMessage(), 'Connection') || 
                        str_contains($e->getMessage(), 'database') || 
                        str_contains($e->getMessage(), 'MySQL') ||
                        str_contains($e->getMessage(), 'MariaDB');

            $message = $isDbError 
                ? 'Error de conexión con la base de datos. El servidor no puede procesar el registro en este momento. Por favor, intente nuevamente más tarde.'
                : 'Error interno del servidor al procesar el registro. Por favor, intente nuevamente.';

            return response()->json([
                'success' => false,
                'message' => $message,
                'error' => config('app.debug') ? $e->getMessage() : null,
                'conexion_db' => $isDbError ? 'desconectado' : 'conectado'
            ], 500);
        }
    }

    /**
     * Login de usuario
     */
    public function login(Request $request)
    {
        // Normalizar campos (compatibilidad con frontend)
        $email = $request->input('email') ?? $request->input('cor');
        $password = $request->input('password') ?? $request->input('con');

        $validator = Validator::make(['email' => $email, 'password' => $password], [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        if (!Auth::attempt(['email' => $email, 'password' => $password])) {
            return response()->json([
                'success' => false,
                'message' => 'Credenciales inválidas'
            ], 401);
        }

        $user = User::where('email', $email)->firstOrFail();

        if ($user->estado !== 'activo') {
            return response()->json([
                'success' => false,
                'message' => 'Usuario inactivo o suspendido'
            ], 403);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Login exitoso',
            'data' => [
                'user' => $user,
                'token' => $token,
            ]
        ]);
    }

    /**
     * Logout de usuario
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout exitoso'
        ]);
    }

    /**
     * Obtener usuario autenticado
     */
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user()
        ]);
    }

    /**
     * Actualizar perfil del usuario
     */
    public function updateProfile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'telefono' => 'sometimes|nullable|string|max:20',
            'avatar' => 'sometimes|nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $user->update($request->only(['name', 'telefono', 'avatar']));

            return response()->json([
                'success' => true,
                'message' => 'Perfil actualizado exitosamente',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar perfil',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Cambiar contraseña
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|string|min:8|different:current_password',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'La contraseña actual es incorrecta'
            ], 401);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Contraseña actualizada exitosamente'
        ]);
    }

    /**
     * Verificar si un email o documento ya existe antes de registrar
     */
    public function verificarDisponibilidad(Request $request)
    {
        try {
            $email = $request->input('email');
            $documento = $request->input('documento');

            $disponibilidad = [
                'email_disponible' => true,
                'documento_disponible' => true,
                'mensajes' => [],
                'conexion_db' => 'conectado'
            ];

            if ($email) {
                $usuarioEmail = User::where('email', strtolower(trim($email)))->first();
                if ($usuarioEmail) {
                    $disponibilidad['email_disponible'] = false;
                    $disponibilidad['mensajes'][] = 'El usuario ya se encuentra registrado con este correo electrónico. Fecha de registro: ' . $usuarioEmail->created_at->format('d/m/Y');
                    $disponibilidad['usuario_existente_email'] = [
                        'fecha_registro' => $usuarioEmail->created_at->format('d/m/Y'),
                        'estado' => $usuarioEmail->estado
                    ];
                }
            }

            if ($documento) {
                $usuarioDoc = User::where('documento', trim($documento))->first();
                if ($usuarioDoc) {
                    $disponibilidad['documento_disponible'] = false;
                    $disponibilidad['mensajes'][] = 'El usuario ya se encuentra registrado con este número de documento (' . $usuarioDoc->tipo_documento . ': ' . $usuarioDoc->documento . '). Fecha de registro: ' . $usuarioDoc->created_at->format('d/m/Y');
                    $disponibilidad['usuario_existente_documento'] = [
                        'tipo_documento' => $usuarioDoc->tipo_documento,
                        'fecha_registro' => $usuarioDoc->created_at->format('d/m/Y'),
                        'estado' => $usuarioDoc->estado
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'data' => $disponibilidad
            ]);
        } catch (\Exception $e) {
            // Detectar si es error de conexión a la base de datos
            $isDbError = str_contains($e->getMessage(), 'Connection') || 
                        str_contains($e->getMessage(), 'database') || 
                        str_contains($e->getMessage(), 'MySQL') ||
                        str_contains($e->getMessage(), 'MariaDB');

            return response()->json([
                'success' => false,
                'message' => $isDbError 
                    ? 'Error de conexión con la base de datos. No se puede verificar la disponibilidad en este momento.'
                    : 'Error al verificar disponibilidad. Por favor, intente nuevamente.',
                'conexion_db' => $isDbError ? 'desconectado' : 'conectado'
            ], 500);
        }
    }

    /**
     * Obtener estadísticas de intentos de registro
     */
    public function estadisticasRegistros(Request $request)
    {
        // Solo administradores pueden ver estas estadísticas
        if (!$request->user() || !in_array($request->user()->rol, ['administrador', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permisos para acceder a esta información'
            ], 403);
        }

        $dias = $request->input('dias', 30);
        $estadisticas = RegistroIntento::estadisticas($dias);

        return response()->json([
            'success' => true,
            'data' => $estadisticas
        ]);
    }

    /**
     * Limpiar registros antiguos de intentos
     */
    public function limpiarRegistrosAntiguos(Request $request)
    {
        // Solo administradores pueden limpiar registros
        if (!$request->user() || !in_array($request->user()->rol, ['administrador', 'superadmin'])) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permisos para realizar esta acción'
            ], 403);
        }

        $dias = $request->input('dias', 90);
        $registrosEliminados = RegistroIntento::limpiarAntiguos($dias);

        return response()->json([
            'success' => true,
            'message' => "Se eliminaron {$registrosEliminados} registros antiguos",
            'data' => [
                'registros_eliminados' => $registrosEliminados
            ]
        ]);
    }

    /**
     * Recuperar contraseña usando email y documento
     */
    public function recuperarContrasena(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'documento' => 'required|string',
        ], [
            'email.required' => 'El correo electrónico es obligatorio',
            'email.email' => 'El correo electrónico no es válido',
            'documento.required' => 'El número de documento es obligatorio',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Buscar usuario por email y documento
            $user = User::where('email', $request->email)
                        ->where('documento', $request->documento)
                        ->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se encontró ningún usuario con estos datos. Verifica tu correo y número de documento.'
                ], 404);
            }

            // Verificar que el usuario esté activo
            if ($user->estado !== 'activo') {
                return response()->json([
                    'success' => false,
                    'message' => 'Tu cuenta está inactiva o suspendida. Contacta al administrador.'
                ], 403);
            }

            // Generar nueva contraseña usando el número de documento
            $nuevaContrasena = $request->documento;
            $user->password = Hash::make($nuevaContrasena);
            $user->save();

            // En producción, aquí se enviaría un email con la nueva contraseña
            // Por ahora, retornamos un mensaje de éxito

            return response()->json([
                'success' => true,
                'message' => 'Contraseña recuperada exitosamente. Tu nueva contraseña es tu número de cédula.',
                'data' => [
                    'email' => $user->email,
                    'mensaje' => 'Puedes iniciar sesión usando tu número de cédula como contraseña. Te recomendamos cambiarla desde tu perfil.'
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al procesar la recuperación de contraseña',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
