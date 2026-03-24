<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('mail:test', function () {
    $to = env('MAIL_TEST_TO', 'mauriciocc11@hotmail.com');

    Mail::raw('¡Funciona! Mi configuración de Google SMTP está perfecta.', function ($message) use ($to) {
        $message->to($to)
            ->subject('Prueba de conexión Laravel');
    });

    $this->info('Correo de prueba enviado a '.$to.' (revisa bandeja y spam).');
})->purpose('Prueba la configuración SMTP (Google u otro)');

Schedule::command('app:send-whatsapp-reminders')->dailyAt('08:00');
Schedule::command('app:send-daily-reports')->dailyAt('08:00');
