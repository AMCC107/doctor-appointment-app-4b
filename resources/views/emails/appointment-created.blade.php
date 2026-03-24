<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Confirmación de cita</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.5; color: #333;">
    <p>Hola {{ $appointment->patient->user->name ?? 'Paciente' }},</p>
    <p>Su cita ha sido registrada correctamente. Adjuntamos un PDF con los detalles.</p>
    <p>
        <strong>Fecha:</strong> {{ $appointment->date->format('d/m/Y') }}<br>
        <strong>Horario:</strong> {{ substr($appointment->start_time, 0, 5) }} – {{ substr($appointment->end_time, 0, 5) }}<br>
        <strong>Médico:</strong> {{ $appointment->doctor->user->name ?? '—' }}
    </p>
    <p>Saludos cordiales,<br>Clínica</p>
</body>
</html>
