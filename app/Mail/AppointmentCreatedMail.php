<?php

namespace App\Mail;

use App\Models\Appointment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AppointmentCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Appointment $appointment,
        public bool $forDoctor = false,
    ) {
        $this->appointment->loadMissing(['patient.user', 'doctor.user', 'doctor.specialty']);
    }

    public function envelope(): Envelope
    {
        if ($this->forDoctor) {
            $patientName = $this->appointment->patient->user->name ?? 'Paciente';

            return new Envelope(
                subject: 'Nueva cita: '.$patientName.' — '.$this->appointment->date->format('d/m/Y'),
            );
        }

        return new Envelope(
            subject: 'Confirmación de su cita',
        );
    }

    public function content(): Content
    {
        return new Content(
            html: $this->forDoctor
                ? 'emails.appointment-created-doctor'
                : 'emails.appointment-created',
        );
    }

    /**
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(
                fn () => Pdf::loadView('emails.appointment-pdf', ['appointment' => $this->appointment])->output(),
                'cita-'.$this->appointment->id.'.pdf',
            )->withMime('application/pdf'),
        ];
    }
}
