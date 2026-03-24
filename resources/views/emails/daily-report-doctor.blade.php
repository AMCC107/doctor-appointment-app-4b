<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sus citas de hoy</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; line-height: 1.5; color: #333;">
    <h1 style="font-size: 20px;">Sus citas de hoy</h1>
    <p>
        <strong>Médico:</strong> {{ $doctor->user->name ?? '—' }}<br>
        <strong>Fecha:</strong> {{ now()->format('d/m/Y') }}
    </p>
    <p>Citas asignadas: <strong>{{ $appointments->count() }}</strong></p>
    @if($appointments->isEmpty())
        <p>No tiene citas programadas para hoy.</p>
    @else
        <table cellpadding="8" cellspacing="0" border="1" style="border-collapse: collapse; width: 100%; max-width: 640px;">
            <thead>
                <tr style="background: #f1f5f9;">
                    <th>Hora</th>
                    <th>Paciente</th>
                    <th>Motivo</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $a)
                <tr>
                    <td>{{ substr($a->start_time, 0, 5) }} – {{ substr($a->end_time, 0, 5) }}</td>
                    <td>{{ $a->patient->user->name ?? '—' }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($a->reason ?? '—', 80) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
