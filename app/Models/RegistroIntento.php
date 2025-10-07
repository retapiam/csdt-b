<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroIntento extends Model
{
    use HasFactory;

    protected $table = 'registro_intentos';

    protected $fillable = [
        'email',
        'documento',
        'tipo_documento',
        'nombre',
        'telefono',
        'rol',
        'tipo_intento',
        'estado',
        'mensaje',
        'errores',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'errores' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Registrar un intento de registro
     */
    public static function registrarIntento(array $datos, string $tipoIntento, string $estado, ?string $mensaje = null, ?array $errores = null)
    {
        return self::create([
            'email' => $datos['email'] ?? null,
            'documento' => $datos['documento'] ?? null,
            'tipo_documento' => $datos['tipo_documento'] ?? null,
            'nombre' => $datos['name'] ?? $datos['nombre'] ?? null,
            'telefono' => $datos['telefono'] ?? null,
            'rol' => $datos['rol'] ?? null,
            'tipo_intento' => $tipoIntento,
            'estado' => $estado,
            'mensaje' => $mensaje,
            'errores' => $errores,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Verificar si hay intentos duplicados recientes
     */
    public static function tieneIntentosDuplicadosRecientes(string $email, int $minutos = 5)
    {
        return self::where('email', $email)
            ->where('estado', 'duplicado')
            ->where('created_at', '>=', now()->subMinutes($minutos))
            ->exists();
    }

    /**
     * Obtener estadísticas de intentos
     */
    public static function estadisticas(int $dias = 30)
    {
        return [
            'total_intentos' => self::where('created_at', '>=', now()->subDays($dias))->count(),
            'exitosos' => self::where('estado', 'exitoso')
                ->where('created_at', '>=', now()->subDays($dias))->count(),
            'duplicados' => self::where('estado', 'duplicado')
                ->where('created_at', '>=', now()->subDays($dias))->count(),
            'fallidos' => self::where('estado', 'fallido')
                ->where('created_at', '>=', now()->subDays($dias))->count(),
        ];
    }

    /**
     * Limpiar registros antiguos
     */
    public static function limpiarAntiguos(int $dias = 90)
    {
        return self::where('created_at', '<', now()->subDays($dias))->delete();
    }
}

