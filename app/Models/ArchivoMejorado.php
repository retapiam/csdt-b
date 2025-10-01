<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

/**
 * Modelo mejorado para archivos
 * Implementa nomenclatura estándar según CONTROL.md
 */
class ArchivoMejorado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'arc';
    protected $primaryKey = 'id';

    protected $fillable = [
        'usu_id',        // Usuario propietario
        'vee_id',        // Veeduría relacionada (opcional)
        'tar_id',        // Tarea relacionada (opcional)
        'nom_ori',       // Nombre original del archivo
        'nom_arc',       // Nombre del archivo en el sistema
        'rut',           // Ruta del archivo
        'tip',           // Tipo MIME del archivo
        'tam',           // Tamaño en bytes
        'est',           // Estado del archivo
        'des',           // Descripción del archivo
        'hash_archivo'   // Hash del archivo
    ];

    protected $casts = [
        'tam' => 'integer',
    ];

    protected $attributes = [
        'est' => 'act',
    ];

    // Relaciones
    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usu_id');
    }

    public function veeduria()
    {
        return $this->belongsTo(Veeduria::class, 'vee_id');
    }

    public function tarea()
    {
        return $this->belongsTo(Tarea::class, 'tar_id');
    }

    // Scopes
    public function scopeActivos($query)
    {
        return $query->where('est', 'act');
    }

    public function scopeInactivos($query)
    {
        return $query->where('est', 'ina');
    }

    public function scopeEliminados($query)
    {
        return $query->where('est', 'eli');
    }

    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('usu_id', $usuarioId);
    }

    public function scopePorVeeduria($query, $veeduriaId)
    {
        return $query->where('vee_id', $veeduriaId);
    }

    public function scopePorTarea($query, $tareaId)
    {
        return $query->where('tar_id', $tareaId);
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tip', $tipo);
    }

    public function scopePorExtension($query, $extension)
    {
        return $query->where('nom_ori', 'like', '%.' . $extension);
    }

    public function scopeImagenes($query)
    {
        return $query->whereIn('tip', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);
    }

    public function scopeDocumentos($query)
    {
        return $query->whereIn('tip', [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ]);
    }

    public function scopePorTamaño($query, $tamañoMin, $tamañoMax = null)
    {
        $query->where('tam', '>=', $tamañoMin);
        if ($tamañoMax) {
            $query->where('tam', '<=', $tamañoMax);
        }
        return $query;
    }

    public function scopeGrandes($query, $limite = 10485760) // 10MB por defecto
    {
        return $query->where('tam', '>', $limite);
    }

    public function scopePequeños($query, $limite = 1048576) // 1MB por defecto
    {
        return $query->where('tam', '<', $limite);
    }

    public function scopePorFecha($query, $fechaInicio, $fechaFin = null)
    {
        $query->whereDate('created_at', '>=', $fechaInicio);
        if ($fechaFin) {
            $query->whereDate('created_at', '<=', $fechaFin);
        }
        return $query;
    }

    // Accessors
    public function getTamañoFormateadoAttribute()
    {
        $bytes = $this->tam;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }

    public function getExtensionAttribute()
    {
        return pathinfo($this->nom_ori, PATHINFO_EXTENSION);
    }

    public function getNombreSinExtensionAttribute()
    {
        return pathinfo($this->nom_ori, PATHINFO_FILENAME);
    }

    public function getEsImagenAttribute()
    {
        return str_starts_with($this->tip, 'image/');
    }

    public function getEsDocumentoAttribute()
    {
        $tiposDocumento = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain'
        ];
        
        return in_array($this->tip, $tiposDocumento);
    }

    public function getEsVideoAttribute()
    {
        return str_starts_with($this->tip, 'video/');
    }

    public function getEsAudioAttribute()
    {
        return str_starts_with($this->tip, 'audio/');
    }

    public function getUrlAttribute()
    {
        return Storage::url($this->rut);
    }

    public function getRutaCompletaAttribute()
    {
        return Storage::path($this->rut);
    }

    public function getExisteAttribute()
    {
        return Storage::exists($this->rut);
    }

    public function getTipoDescripcionAttribute()
    {
        $tipos = [
            'image/jpeg' => 'Imagen JPEG',
            'image/png' => 'Imagen PNG',
            'image/gif' => 'Imagen GIF',
            'image/webp' => 'Imagen WebP',
            'application/pdf' => 'Documento PDF',
            'application/msword' => 'Documento Word',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Documento Word (DOCX)',
            'application/vnd.ms-excel' => 'Hoja de Cálculo Excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'Hoja de Cálculo Excel (XLSX)',
            'text/plain' => 'Archivo de Texto',
            'application/zip' => 'Archivo ZIP',
            'video/mp4' => 'Video MP4',
            'video/avi' => 'Video AVI',
            'audio/mp3' => 'Audio MP3',
            'audio/wav' => 'Audio WAV'
        ];
        
        return $tipos[$this->tip] ?? $this->tip;
    }

    public function getEstadoDescripcionAttribute()
    {
        $estados = [
            'act' => 'Activo',
            'ina' => 'Inactivo',
            'eli' => 'Eliminado'
        ];
        return $estados[$this->est] ?? 'Desconocido';
    }

    public function getEstadoColorAttribute()
    {
        return match ($this->est) {
            'act' => 'success',
            'ina' => 'warning',
            'eli' => 'danger',
            default => 'secondary'
        };
    }

    public function getDiasTranscurridosAttribute()
    {
        return $this->created_at->diffInDays(now());
    }

    public function getEsRecienteAttribute()
    {
        return $this->created_at->isAfter(now()->subDays(7));
    }

    // Mutators
    public function setNomOriAttribute($value)
    {
        $this->attributes['nom_ori'] = trim($value);
    }

    public function setNomArcAttribute($value)
    {
        $this->attributes['nom_arc'] = trim($value);
    }

    public function setRutAttribute($value)
    {
        $this->attributes['rut'] = trim($value);
    }

    public function setDesAttribute($value)
    {
        $this->attributes['des'] = $value ? trim($value) : null;
    }

    // Métodos de utilidad
    public function activar()
    {
        $this->update(['est' => 'act']);
    }

    public function desactivar()
    {
        $this->update(['est' => 'ina']);
    }

    public function marcarComoEliminado()
    {
        $this->update(['est' => 'eli']);
    }

    public function esActivo()
    {
        return $this->est === 'act';
    }

    public function esInactivo()
    {
        return $this->est === 'ina';
    }

    public function estaEliminado()
    {
        return $this->est === 'eli';
    }

    public function esGrande($limite = 10485760) // 10MB por defecto
    {
        return $this->tam > $limite;
    }

    public function esPequeño($limite = 1048576) // 1MB por defecto
    {
        return $this->tam < $limite;
    }

    public function eliminarFisicamente()
    {
        if ($this->existe) {
            Storage::delete($this->rut);
        }
        $this->delete();
    }

    public function mover($nuevaRuta)
    {
        if ($this->existe) {
            Storage::move($this->rut, $nuevaRuta);
            $this->update(['rut' => $nuevaRuta]);
        }
    }

    public function copiar($nuevaRuta)
    {
        if ($this->existe) {
            return Storage::copy($this->rut, $nuevaRuta);
        }
        return false;
    }

    public function obtenerContenido()
    {
        if ($this->existe) {
            return Storage::get($this->rut);
        }
        return null;
    }

    public function obtenerStream()
    {
        if ($this->existe) {
            return Storage::readStream($this->rut);
        }
        return null;
    }

    public function verificarIntegridad()
    {
        if (!$this->existe) {
            return false;
        }
        
        if (!$this->hash_archivo) {
            return true; // No hay hash para verificar
        }
        
        $hashActual = hash_file('sha256', $this->ruta_completa);
        return $hashActual === $this->hash_archivo;
    }

    public function generarHash()
    {
        if ($this->existe) {
            $hash = hash_file('sha256', $this->ruta_completa);
            $this->update(['hash_archivo' => $hash]);
            return $hash;
        }
        return null;
    }

    // Métodos estáticos
    public static function obtenerPorUsuario($usuarioId, $estado = 'act')
    {
        return self::where('usu_id', $usuarioId)
            ->where('est', $estado)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function obtenerPorVeeduria($veeduriaId, $estado = 'act')
    {
        return self::where('vee_id', $veeduriaId)
            ->where('est', $estado)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function obtenerPorTarea($tareaId, $estado = 'act')
    {
        return self::where('tar_id', $tareaId)
            ->where('est', $estado)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public static function obtenerEstadisticas($fechaInicio = null, $fechaFin = null)
    {
        $query = self::query();
        
        if ($fechaInicio) {
            $query->whereDate('created_at', '>=', $fechaInicio);
        }
        
        if ($fechaFin) {
            $query->whereDate('created_at', '<=', $fechaFin);
        }

        return [
            'total_archivos' => $query->count(),
            'tamaño_total' => $query->sum('tam'),
            'tamaño_promedio' => $query->avg('tam'),
            'activos' => $query->where('est', 'act')->count(),
            'inactivos' => $query->where('est', 'ina')->count(),
            'eliminados' => $query->where('est', 'eli')->count(),
            'por_tipo' => $query->selectRaw('tip, COUNT(*) as total, SUM(tam) as tamaño_total')
                ->groupBy('tip')
                ->get(),
            'por_estado' => $query->selectRaw('est, COUNT(*) as total, SUM(tam) as tamaño_total')
                ->groupBy('est')
                ->get()
        ];
    }

    public static function limpiarArchivosEliminados()
    {
        $archivosEliminados = self::where('est', 'eli')
            ->where('created_at', '<', now()->subDays(30))
            ->get();

        foreach ($archivosEliminados as $archivo) {
            $archivo->eliminarFisicamente();
        }

        return $archivosEliminados->count();
    }

    public static function obtenerArchivosSinHash()
    {
        return self::whereNull('hash_archivo')
            ->where('est', 'act')
            ->where('created_at', '<', now()->subDays(1)) // Solo archivos con más de 1 día
            ->get();
    }

    public static function verificarIntegridadArchivos()
    {
        $archivosConHash = self::whereNotNull('hash_archivo')
            ->where('est', 'act')
            ->get();

        $archivosCorruptos = [];
        foreach ($archivosConHash as $archivo) {
            if (!$archivo->verificarIntegridad()) {
                $archivosCorruptos[] = $archivo;
            }
        }

        return $archivosCorruptos;
    }

    public static function crearArchivo($datos)
    {
        return self::create([
            'usu_id' => $datos['usu_id'],
            'vee_id' => $datos['vee_id'] ?? null,
            'tar_id' => $datos['tar_id'] ?? null,
            'nom_ori' => $datos['nom_ori'],
            'nom_arc' => $datos['nom_arc'],
            'rut' => $datos['rut'],
            'tip' => $datos['tip'],
            'tam' => $datos['tam'],
            'est' => $datos['est'] ?? 'act',
            'des' => $datos['des'] ?? null,
            'hash_archivo' => $datos['hash_archivo'] ?? null
        ]);
    }

    // Reglas de validación
    public static function reglas($id = null)
    {
        return [
            'usu_id' => 'required|exists:usu,id',
            'vee_id' => 'nullable|exists:vee,id',
            'tar_id' => 'nullable|exists:tar,id',
            'nom_ori' => 'required|string|max:255',
            'nom_arc' => 'required|string|max:255',
            'rut' => 'required|string|max:500',
            'tip' => 'required|string|max:100',
            'tam' => 'required|integer|min:0',
            'est' => 'nullable|in:act,ina,eli',
            'des' => 'nullable|string|max:500',
            'hash_archivo' => 'nullable|string|max:64'
        ];
    }

    public static function mensajes()
    {
        return [
            'usu_id.required' => 'El usuario es obligatorio.',
            'usu_id.exists' => 'El usuario seleccionado no existe.',
            'vee_id.exists' => 'La veeduría seleccionada no existe.',
            'tar_id.exists' => 'La tarea seleccionada no existe.',
            'nom_ori.required' => 'El nombre original es obligatorio.',
            'nom_ori.max' => 'El nombre original no puede exceder 255 caracteres.',
            'nom_arc.required' => 'El nombre del archivo es obligatorio.',
            'nom_arc.max' => 'El nombre del archivo no puede exceder 255 caracteres.',
            'rut.required' => 'La ruta es obligatoria.',
            'rut.max' => 'La ruta no puede exceder 500 caracteres.',
            'tip.required' => 'El tipo MIME es obligatorio.',
            'tip.max' => 'El tipo MIME no puede exceder 100 caracteres.',
            'tam.required' => 'El tamaño es obligatorio.',
            'tam.integer' => 'El tamaño debe ser un número entero.',
            'tam.min' => 'El tamaño debe ser mayor o igual a 0.',
            'est.in' => 'El estado no es válido.',
            'des.max' => 'La descripción no puede exceder 500 caracteres.',
            'hash_archivo.max' => 'El hash no puede exceder 64 caracteres.'
        ];
    }
}
