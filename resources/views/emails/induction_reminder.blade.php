<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background-color: #f4f4f4; color: #333; padding: 20px; }
        .card { background-color: #121212; color: #ffffff; max-width: 600px; margin: 0 auto; padding: 40px; border-radius: 12px; }
        h1 { color: #ffffff; font-size: 24px; }
        p { color: #a0a0a0; font-size: 16px; line-height: 1.6; }
        .badge { background-color: #ffffff; color: #000000; padding: 6px 12px; font-weight: bold; border-radius: 4px; }
        .btn { display: inline-block; background-color: #ffffff; color: #000000 !important; text-decoration: none; padding: 14px 30px; font-weight: bold; border-radius: 6px; margin-top: 20px; text-transform: uppercase; font-size: 14px; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Hola, {{ $user->name }}</h2>
        <p>Te contactamos de la administración para recordarte que tienes un proceso de inducción pendiente por concluir.</p>
        
        <p>Tu avance actual registrado es del: <span class="badge">{{ $percentage }}%</span></p>
        
        <p>Completar los módulos corporativos y evaluaciones correspondientes a tu perfil de <strong>{{ $user->tipo_usuario }}</strong> es fundamental para tus actividades.</p>
        
        <a href="{{ url('/login') }}" class="btn">Continuar Capacitación</a>
        
        <hr style="border: 0; border-top: 1px solid #262626; margin-top: 30px;">
        <p style="font-size: 12px; color: #555;">Este es un mensaje automático de seguimiento del Sistema de Inducción I-DEB.</p>
    </div>
</body>
</html>