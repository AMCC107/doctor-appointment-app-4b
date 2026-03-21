<?php

/**
 * Prueba rápida Meta WhatsApp (sin Tinker / PowerShell).
 * Uso: php scripts/test-meta-whatsapp.php [10 dígitos o +52...]
 */

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$raw = $argv[1] ?? '9993292237';

$s = app(App\Services\MetaWhatsAppCloudService::class);
$n = App\Services\MetaWhatsAppCloudService::sanitizeRecipientForMeta($raw);

echo "Original: {$raw}\n";
echo "Sanitizado (to): {$n}\n";

$r = $s->sendTextMessage($n, 'Prueba Meta WhatsApp '.date('Y-m-d H:i:s'));
echo "\n";
print_r($r);
