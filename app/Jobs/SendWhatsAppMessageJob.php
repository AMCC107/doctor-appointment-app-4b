<?php

namespace App\Jobs;

use App\Models\Appointment;
use App\Services\MetaWhatsAppCloudService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SendWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 60;

    public function __construct(
        protected string $phone,
        protected string $message = '',
    ) {
        $this->connection = 'sync';
        $this->onQueue(config('services.whatsapp.queue', 'default'));
    }

    public function handle(MetaWhatsAppCloudService $meta): void
    {
        if (empty(trim($this->phone))) {
            Log::warning('SendWhatsAppMessageJob skipped: phone empty');

            return;
        }

        if (empty(trim($this->message))) {
            Log::warning('SendWhatsAppMessageJob skipped: message empty');

            return;
        }

        $clean = MetaWhatsAppCloudService::sanitizeRecipientForMeta($this->phone);
        $result = $meta->sendTextMessage($clean, $this->message);

        if (! $result['ok']) {
            Log::error('SendWhatsAppMessageJob: Meta no envió el mensaje', [
                'phone_last4' => strlen($clean) >= 4 ? substr($clean, -4) : $clean,
                'error' => $result['error'],
            ]);

            if ($this->attempts() < $this->tries) {
                $this->release($this->backoff);
            }
        }
    }

    /**
     * Recordatorio de cita (comando programado).
     */
    public static function dispatchForAppointmentReminder(Appointment $appointment): void
    {
        $appointment->loadMissing('patient.user', 'doctor.user');
        $userPhone = trim((string) ($appointment->patient?->user?->phone ?? ''));
        $emergency = trim((string) ($appointment->patient?->emergency_contact_phone ?? ''));
        $phone = $userPhone !== '' ? $userPhone : ($emergency !== '' ? $emergency : null);

        if (empty($phone)) {
            Log::info('SendWhatsAppMessageJob: no phone for appointment reminder', [
                'appointment_id' => $appointment->id,
            ]);

            return;
        }

        $date = $appointment->date instanceof \Carbon\CarbonInterface
            ? $appointment->date->copy()
            : Carbon::parse($appointment->date);
        $dateStr = $date->format('d/m/Y');
        $timeStr = Carbon::parse($appointment->start_time)->format('H:i');

        $message = "Recordatorio: tienes una cita el {$dateStr} a las {$timeStr}.";

        self::dispatch($phone, $message);
    }
}
