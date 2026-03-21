<?php

namespace App\Services;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Confirmación de cita por WhatsApp Cloud API (Meta Graph), vía HTTP de Laravel.
 * Mantiene el arreglo ['sent','reason','detail'] esperado por Admin\AppointmentController.
 */
class AppointmentWhatsAppNotifier
{
    public function __construct(
        protected MetaWhatsAppCloudService $metaWhatsApp
    ) {
    }

    /**
     * @return array{sent: bool, reason: string|null, detail: string|null}
     */
    public function notifyAppointmentCreated(Appointment $appointment): array
    {
        try {
            $appointment->loadMissing('patient.user', 'doctor.user');

            $rawPhone = $this->resolvePhoneForNotification($appointment);

            if ($rawPhone === null || $rawPhone === '') {
                Log::warning('AppointmentWhatsAppNotifier: sin teléfono (user.phone ni emergency_contact_phone)', [
                    'appointment_id' => $appointment->id,
                    'patient_id' => $appointment->patient_id,
                ]);

                return [
                    'sent' => false,
                    'reason' => 'no_phone',
                    'detail' => null,
                ];
            }

            $cleanPhone = MetaWhatsAppCloudService::sanitizeRecipientForMeta($rawPhone);

            Log::info('Intentando enviar Meta WhatsApp (solo dígitos E.164 sin +): '.$cleanPhone, [
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
                'raw_original' => $rawPhone,
            ]);

            $mensajePersonalizado = $this->buildConfirmationBody($appointment);

            $result = $this->metaWhatsApp->sendTextMessage($cleanPhone, $mensajePersonalizado);

            if ($result['ok']) {
                return [
                    'sent' => true,
                    'reason' => null,
                    'detail' => null,
                ];
            }

            return [
                'sent' => false,
                // Misma clave que usa el controlador para mostrar `detail` en el aviso ámbar
                'reason' => 'exception',
                'detail' => $result['error'],
            ];
        } catch (\Throwable $e) {
            Log::error('AppointmentWhatsAppNotifier: excepción', [
                'appointment_id' => $appointment->id ?? null,
                'message' => $e->getMessage(),
                'exception' => $e::class,
            ]);

            return [
                'sent' => false,
                'reason' => 'exception',
                'detail' => $e->getMessage(),
            ];
        }
    }

    protected function buildConfirmationBody(Appointment $appointment): string
    {
        $patientName = trim((string) ($appointment->patient?->user?->name ?? 'Paciente'));
        $doctorName = trim((string) ($appointment->doctor?->user?->name ?? 'su médico'));

        $date = $appointment->date instanceof \Carbon\CarbonInterface
            ? $appointment->date->copy()
            : Carbon::parse($appointment->date);

        $dateStr = $date->format('d/m/Y');
        $timeStr = Carbon::parse($appointment->start_time)->format('H:i');

        return "Hola {$patientName}, tu cita ha sido confirmada.\n"
            ."Fecha: {$dateStr}\n"
            ."Hora: {$timeStr}\n"
            ."Doctor(a): {$doctorName}\n"
            .'Si necesitas reprogramar, contáctanos.';
    }

    /**
     * Prioridad: teléfono del usuario del paciente; si falta, contacto de emergencia del paciente.
     */
    protected function resolvePhoneForNotification(Appointment $appointment): ?string
    {
        $userPhone = trim((string) ($appointment->patient?->user?->phone ?? ''));
        if ($userPhone !== '') {
            return $userPhone;
        }

        $emergency = trim((string) ($appointment->patient?->emergency_contact_phone ?? ''));

        return $emergency !== '' ? $emergency : null;
    }
}
