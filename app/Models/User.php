<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'estado',
        'avatar',
        'telefono',
        'documento',
        'tipo_documento',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relaciones
    public function permisos(): HasMany
    {
        return $this->hasMany(Permiso::class);
    }

    public function consultasIA(): HasMany
    {
        return $this->hasMany(AIConsulta::class);
    }

    public function analisisJuridicos(): HasMany
    {
        return $this->hasMany(AIAnalisisJuridico::class);
    }

    public function analisisEtnicos(): HasMany
    {
        return $this->hasMany(AIAnalisisEtnico::class);
    }

    public function analisisVeeduria(): HasMany
    {
        return $this->hasMany(AIAnalisisVeeduria::class);
    }

    public function proyectosAdministrados(): HasMany
    {
        return $this->hasMany(Proyecto::class, 'administrador_id');
    }

    public function proyectosOperados(): HasMany
    {
        return $this->hasMany(Proyecto::class, 'operador_id');
    }

    public function proyectosCliente(): HasMany
    {
        return $this->hasMany(Proyecto::class, 'cliente_id');
    }

    public function tareasAsignadas(): HasMany
    {
        return $this->hasMany(Tarea::class, 'asignado_a');
    }

    public function tareasCreadas(): HasMany
    {
        return $this->hasMany(Tarea::class, 'creado_por');
    }

    public function casosLegales(): HasMany
    {
        return $this->hasMany(CasoLegal::class);
    }

    public function notificaciones(): HasMany
    {
        return $this->hasMany(Notificacion::class);
    }

    public function documentos(): HasMany
    {
        return $this->hasMany(Documento::class);
    }

    // Métodos de ayuda
    public function esAdministrador(): bool
    {
        return $this->rol === 'administrador' || $this->rol === 'superadmin';
    }

    public function esOperador(): bool
    {
        return $this->rol === 'operador';
    }

    public function esCliente(): bool
    {
        return $this->rol === 'cliente';
    }

    public function esSuperAdmin(): bool
    {
        return $this->rol === 'superadmin';
    }

    public function tienePermiso(string $modulo, string $accion): bool
    {
        if ($this->esSuperAdmin()) {
            return true;
        }

        $permiso = $this->permisos()
            ->where('modulo', $modulo)
            ->first();

        return $permiso?->tienePermiso($accion) ?? false;
    }
}
