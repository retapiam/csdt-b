<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Models\User;

class AlertasService
{
    public function generarAlertasTempranas(): array
    {
        // Mock inicial con persistencia
        $detalles = [];

        $alerta = \App\Models\AlertaPAE::create([
            'tipo' => 'retraso_entrega',
            'severidad' => 'media',
            'estado' => 'abierta',
            'mensaje' => 'Retraso detectado en entrega PAE',
            'data' => [ 'fuente' => 'regla_mock' ]
        ]);

        $detalles[] = $alerta->toArray();

        // Notificación por correo si está habilitada y es crítica
        try {
            $alertsConfig = config('services.alerts');
            $emailEnabled = Cache::get('alerts_email_enabled', $alertsConfig['email_enabled'] ?? false);
            if ($emailEnabled && ($alerta->severidad === 'critica')) {
                $toGlobal = $alertsConfig['to'];
                $emails = [];
                if ($toGlobal) {
                    $emails[] = $toGlobal;
                }
                if ($alerta->asignado_a) {
                    $u = User::find($alerta->asignado_a);
                    if ($u && $u->email) {
                        $emails[] = $u->email;
                    }
                }
                $emails = array_values(array_unique(array_filter($emails)));
                foreach ($emails as $to) {
                    Mail::raw('Alerta Crítica: '.$alerta->mensaje, function ($message) use ($to) {
                        $message->to($to)->subject('CSDT - Alerta Crítica PAE');
                    });
                }
            }
        } catch (\Throwable $e) {
            Log::warning('[CSDT] No se pudo enviar correo de alerta', ['error' => $e->getMessage()]);
        }

        Log::info('[CSDT] Mock alertas tempranas ejecutado');
        return [
            'generadas' => count($detalles),
            'detalles' => $detalles,
        ];
    }
}


