<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdfGenerado extends Model
{
    protected $table = 'pdfs_generados';

    protected $fillable = [
        'user_id',
        'tipo_pdf',
        'plantilla',
        'datos_entrada',
        'ruta_archivo',
        'tamano_bytes',
        'paginas',
        'calidad',
        'analisis_ia_id',
    ];

    protected $casts = [
        'datos_entrada' => 'array',
        'tamano_bytes' => 'integer',
        'paginas' => 'integer',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
