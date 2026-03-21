<?php

namespace App\Console\Commands;

use App\Services\MetaWhatsAppCloudService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendWhatsAppReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-whatsapp-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía recordatorios de citas por WhatsApp';

    public function handle(): int
    {
        Log::info('Ejecutando recordatorios de WhatsApp...');

        $servicio = app(MetaWhatsAppCloudService::class);

        // Mismo destino y plantilla hello_world que en pruebas (evita 131030 al no usar pacientes reales)
        $resultado = $servicio->sendHelloWorldTemplate('529993292237');

        if (! $resultado['ok']) {
            Log::error('Recordatorio WhatsApp falló', ['error' => $resultado['error']]);
            $this->error('No se pudo enviar: '.$resultado['error']);

            return self::FAILURE;
        }

        $this->info('Recordatorios enviados exitosamente a los pacientes de mañana.');

        return self::SUCCESS;
    }
}
