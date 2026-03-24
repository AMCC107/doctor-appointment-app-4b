<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #222; }
        h1 { font-size: 18px; border-bottom: 2px solid #2563eb; padding-bottom: 6px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { text-align: left; padding: 8px; border: 1px solid #ccc; }
        th { background: #f1f5f9; }
    </style>
</head>
<body>
    <h1>Comprobante de cita</h1>
    <table>
        <tr><th>Nº cita</th><td>{{ $appointment->id }}</td></tr>
        <tr><th>Paciente</th><td>{{ $appointment->patient->user->name ?? '—' }}</td></tr>
        <tr><th>Médico</th><td>{{ $appointment->doctor->user->name ?? '—' }}</td></tr>
        @if($appointment->doctor->specialty)
        <tr><th>Especialidad</th><td>{{ $appointment->doctor->specialty->name ?? '—' }}</td></tr>
        @endif
        <tr><th>Fecha</th><td>{{ $appointment->date->format('d/m/Y') }}</td></tr>
        <tr><th>Inicio</th><td>{{ substr($appointment->start_time, 0, 5) }}</td></tr>
        <tr><th>Fin</th><td>{{ substr($appointment->end_time, 0, 5) }}</td></tr>
        <tr><th>Motivo</th><td>{{ $appointment->reason ?: '—' }}</td></tr>
    </table>
</body>
</html>
