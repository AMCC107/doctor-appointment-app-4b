<?php

namespace App\Console\Commands;

use App\Mail\DailyReportMail;
use App\Models\Appointment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyReports extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-daily-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía el reporte diario de citas al administrador y a cada médico';

    public function handle(): int
    {
        $appointments = Appointment::with(['patient.user', 'doctor.user', 'doctor.specialty'])
            ->whereDate('date', today())
            ->orderBy('start_time')
            ->get();

        Mail::to(config('mail.daily_report_admin'))->send(new DailyReportMail($appointments));

        foreach ($appointments->groupBy('doctor_id') as $group) {
            $first = $group->first();
            if ($first === null) {
                continue;
            }
            $doctor = $first->doctor;
            if ($doctor === null) {
                continue;
            }
            $doctor->loadMissing(['user', 'specialty']);
            $email = $doctor->user?->email;
            if (! $email) {
                continue;
            }
            Mail::to($email)->send(new DailyReportMail($group->values(), $doctor));
        }

        $this->info('Reportes diarios enviados.');

        return self::SUCCESS;
    }
}
