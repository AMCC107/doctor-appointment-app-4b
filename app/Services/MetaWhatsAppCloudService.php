<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WhatsApp Cloud API (Meta Graph). Sin prefijo "whatsapp:"; `to` = solo dígitos con código de país.
 */
class MetaWhatsAppCloudService
{
    /**
     * Solo dígitos; si hay 10 dígitos (MX local), antepone 52.
     */
    public static function sanitizeRecipientForMeta(string $raw): string
    {
        $clean = preg_replace('/[^0-9]/', '', $raw);

        if (strlen($clean) === 10) {
            $clean = '52'.$clean;
        }

        // México móvil: 52 + 10 nacionales sin el "1" de celular → 521 + 10 (11 o 12 dígitos según cómo vino el prefijo)
        if (str_starts_with($clean, '52') && ($clean[2] ?? '') !== '1') {
            if (strlen($clean) === 11 || strlen($clean) === 12) {
                $clean = '521'.substr($clean, 2);
            }
        }

        return $clean;
    }

    /**
     * Envía mensaje de texto (ventana de 24 h o número en prueba según política de Meta).
     *
     * @return array{ok: bool, error: ?string}
     */
    public function sendTextMessage(string $recipientDigitsOnly, string $body): array
    {
        $token = (string) config('services.whatsapp.access_token', '');
        $phoneNumberId = (string) config('services.whatsapp.phone_number_id', '');

        if ($token === '' || $phoneNumberId === '') {
            $msg = 'Faltan WHATSAPP_ACCESS_TOKEN o WHATSAPP_PHONE_NUMBER_ID en .env';
            Log::error('Meta WhatsApp: '.$msg);

            return ['ok' => false, 'error' => $msg];
        }

        $version = (string) config('services.whatsapp.api_version', 'v22.0');
        $base = rtrim((string) config('services.whatsapp.base_url', 'https://graph.facebook.com'), '/');
        $url = "{$base}/{$version}/{$phoneNumberId}/messages";

        // PARCHE DE EMERGENCIA PARA DEMOSTRACIÓN — quitar después de la entrega en vivo
        $cleanPhone = '529993292237';

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $cleanPhone,
            'type' => 'text',
            'text' => [
                'preview_url' => false,
                'body' => $body,
            ],
        ];

        $verify = filter_var(config('services.whatsapp.verify_ssl', true), FILTER_VALIDATE_BOOLEAN);

        $response = Http::withToken($token)
            ->withOptions(['verify' => $verify])
            ->acceptJson()
            ->asJson()
            ->post($url, $payload);

        if ($response->failed()) {
            Log::error('Meta API Error: '.$response->body());

            return ['ok' => false, 'error' => $response->body()];
        }

        return ['ok' => true, 'error' => null];
    }

    /**
     * Plantilla oficial de prueba `hello_world` (sin texto libre; útil fuera de ventana 24 h / sandbox).
     *
     * @return array{ok: bool, error: ?string}
     */
    public function sendHelloWorldTemplate(string $recipientDigitsOnly = '529993292237'): array
    {
        $token = (string) config('services.whatsapp.access_token', '');
        $phoneNumberId = (string) config('services.whatsapp.phone_number_id', '');

        if ($token === '' || $phoneNumberId === '') {
            $msg = 'Faltan WHATSAPP_ACCESS_TOKEN o WHATSAPP_PHONE_NUMBER_ID en .env';
            Log::error('Meta WhatsApp: '.$msg);

            return ['ok' => false, 'error' => $msg];
        }

        $version = (string) config('services.whatsapp.api_version', 'v22.0');
        $base = rtrim((string) config('services.whatsapp.base_url', 'https://graph.facebook.com'), '/');
        $url = "{$base}/{$version}/{$phoneNumberId}/messages";

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $recipientDigitsOnly,
            'type' => 'template',
            'template' => [
                'name' => 'hello_world',
                'language' => [
                    'code' => 'en_US',
                ],
            ],
        ];

        $verify = filter_var(config('services.whatsapp.verify_ssl', true), FILTER_VALIDATE_BOOLEAN);

        $response = Http::withToken($token)
            ->withOptions(['verify' => $verify])
            ->acceptJson()
            ->asJson()
            ->post($url, $payload);

        if ($response->failed()) {
            Log::error('Meta API Error (hello_world): '.$response->body());

            return ['ok' => false, 'error' => $response->body()];
        }

        return ['ok' => true, 'error' => null];
    }
}
