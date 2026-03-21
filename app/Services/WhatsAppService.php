<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    public function __construct(
        protected string $baseUrl,
        protected string $phoneNumberId,
        protected string $accessToken,
        protected string $apiVersion = 'v22.0'
    ) {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    /**
     * Envía un mensaje de texto vía WhatsApp Cloud API (Meta).
     * No lanza excepciones; registra errores en log.
     *
     * @param string $to Número con código de país, sin +
     * @param string $text Cuerpo del mensaje
     * @return bool true si se envió correctamente, false en caso contrario
     */
    public function sendTextMessage(string $to, string $text): bool
    {
        if (empty($this->phoneNumberId) || empty($this->accessToken)) {
            Log::warning('WhatsApp not configured: missing phone_number_id or access_token');

            return false;
        }

        $url = "{$this->baseUrl}/{$this->apiVersion}/{$this->phoneNumberId}/messages";

        $payload = $this->buildTextMessagePayload($to, $text);

        Log::info('WhatsApp sending text message', [
            'to_normalized' => $payload['to'],
            'url' => $url,
        ]);

        try {
            $client = Http::withToken($this->accessToken)->timeout(15);

            $verifySsl = filter_var(config('services.whatsapp.verify_ssl', true), FILTER_VALIDATE_BOOLEAN);
            if ($verifySsl === false) {
                $client = $client->withOptions(['verify' => false]);
            }

            $response = $client->post($url, $payload);

            if ($response->successful()) {
                return true;
            }

            $body = $response->json();
            $errorMsg = $body['error']['message'] ?? $response->body();

            Log::error('WhatsApp API error', [
                'status' => $response->status(),
                'error_message' => $errorMsg,
                'body' => $body,
                'to' => $to,
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsApp service exception', [
                'message' => $e->getMessage(),
                'to' => $to,
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Envía un mensaje tipo template (ej. hello_world) como en el ejemplo de Meta.
     * Útil cuando la API solo permite templates (ej. inicio de conversación).
     *
     * @param string $to Número con código de país (ej. 529993292237)
     * @param string $templateName Nombre del template (ej. hello_world)
     * @param string $languageCode Código de idioma (ej. en_US)
     * @return bool
     */
    public function sendTemplateMessage(string $to, string $templateName = 'hello_world', string $languageCode = 'en_US'): bool
    {
        if (empty($this->phoneNumberId) || empty($this->accessToken)) {
            Log::warning('WhatsApp not configured: missing phone_number_id or access_token');

            return false;
        }

        $url = "{$this->baseUrl}/{$this->apiVersion}/{$this->phoneNumberId}/messages";
        $to = $this->normalizePhoneForMexico($to);

        $payload = [
            'messaging_product' => 'whatsapp',
            'to' => $to,
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => [
                    'code' => $languageCode,
                ],
            ],
        ];

        Log::info('WhatsApp sending template message', [
            'to_normalized' => $to,
            'template' => $templateName,
            'url' => $url,
        ]);

        try {
            $client = Http::withToken($this->accessToken)->timeout(15);

            $verifySsl = filter_var(config('services.whatsapp.verify_ssl', true), FILTER_VALIDATE_BOOLEAN);
            if ($verifySsl === false) {
                $client = $client->withOptions(['verify' => false]);
            }

            $response = $client->post($url, $payload);

            if ($response->successful()) {
                return true;
            }

            $body = $response->json();
            $errorMsg = $body['error']['message'] ?? $response->body();

            Log::error('WhatsApp API error (template)', [
                'status' => $response->status(),
                'error_message' => $errorMsg,
                'body' => $body,
                'to' => $to,
            ]);

            return false;
        } catch (\Throwable $e) {
            Log::error('WhatsApp service exception (template)', [
                'message' => $e->getMessage(),
                'to' => $to,
                'trace' => $e->getTraceAsString(),
            ]);

            return false;
        }
    }

    /**
     * Payload JSON compatible con WhatsApp Cloud API (Meta) para mensaje de texto.
     * Normaliza el número: si son 10 dígitos (México) se antepone 52.
     *
     * @param string $to Número destinatario (con o sin +52, espacios, etc.)
     * @param string $text Cuerpo del mensaje
     * @return array<string, mixed>
     */
    protected function buildTextMessagePayload(string $to, string $text): array
    {
        $to = $this->normalizePhoneForMexico($to);

        return [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => [
                'body' => $text,
                'preview_url' => false,
            ],
        ];
    }

    /**
     * Deja solo dígitos y, si es número mexicano de 10 dígitos, antepone 52.
     * Ej: 5512345678 -> 5215512345678; +52 55 1234 5678 -> 5215512345678
     */
    protected function normalizePhoneForMexico(string $phone): string
    {
        $digits = preg_replace('/\D/', '', $phone);

        if (strlen($digits) === 10 && ! str_starts_with($digits, '52')) {
            $digits = '52' . $digits;
        }

        return $digits;
    }

    /**
     * Crea instancia desde configuración (config/whatsapp o .env).
     */
    public static function fromConfig(): self
    {
        return new self(
            baseUrl: config('services.whatsapp.base_url', 'https://graph.facebook.com'),
            phoneNumberId: config('services.whatsapp.phone_number_id', ''),
            accessToken: config('services.whatsapp.access_token', ''),
            apiVersion: config('services.whatsapp.api_version', 'v22.0')
        );
    }
}
