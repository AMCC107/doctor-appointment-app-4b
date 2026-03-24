<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nueva cita asignada</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.5; color: #333;">
    <p>Estimado/a <strong>Dr(a). {{ $appointment->doctor->user->name ?? 'Médico' }}</strong>,</p>
    <p>Se le ha asignado una nueva cita. A continuación los datos del paciente y el horario:</p>
    <p>
        <strong>Paciente:</strong> {{ $appointment->patient->user->name ?? '—' }}<br>
        <strong>Fecha:</strong> {{ $appointment->date->format('d/m/Y') }}<br>
        <strong>Horario:</strong> {{ substr($appointment->start_time, 0, 5) }} – {{ substr($appointment->end_time, 0, 5) }}<br>
        @if($appointment->reason)
        <strong>Motivo / nota:</strong> {{ $appointment->reason }}<br>
        @endif
    </p>
    <p>Adjuntamos el mismo comprobante en PDF que recibe el paciente para su archivo.</p>
    <p>Saludos cordiales,<br>Clínica</p>
</body>
</html>
