<?php

namespace App\Jobs;

use App\Services\AlertasService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerarAlertasTempranasJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $backoff = 5;

    public function __construct()
    {
    }

    public function handle(): void
    {
        try {
            $servicio = app(AlertasService::class);
            $resultado = $servicio->generarAlertasTempranas();

            Log::info('[CSDT] Alertas tempranas generadas', [
                'generadas' => $resultado['generadas'] ?? 0,
                'detalles' => $resultado['detalles'] ?? []
            ]);
        } catch (\Throwable $e) {
            Log::error('[CSDT] Error generando alertas tempranas', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }
}


