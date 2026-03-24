<?php

namespace App\Mail;

use App\Models\Doctor;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DailyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Collection $appointments,
        public ?Doctor $doctor = null,
    ) {}

    public function envelope(): Envelope
    {
        $date = now()->format('d/m/Y');
        if ($this->doctor === null) {
            return new Envelope(subject: "Reporte diario de citas — {$date}");
        }

        $name = $this->doctor->user->name ?? 'Doctor';

        return new Envelope(subject: "Sus citas de hoy ({$date}) — {$name}");
    }

    public function content(): Content
    {
        if ($this->doctor === null) {
            return new Content(html: 'emails.daily-report-master');
        }

        return new Content(html: 'emails.daily-report-doctor');
    }
}
