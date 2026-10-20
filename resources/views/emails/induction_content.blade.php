<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f4f4f4; color: #333; padding: 20px; }
        .card { background: #121212; color: #fff; max-width: 600px; margin: 0 auto; padding: 40px; border-radius: 12px; }
        p { color: #a0a0a0; font-size: 16px; line-height: 1.6; }
        .btn { display: inline-block; background: #fff; color: #000; text-decoration: none; padding: 14px 30px; border-radius: 6px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Hola, {{ $user->name }}</h2>
        <p>Tienes contenido disponible para tu perfil en el proceso de inducción.</p>
        <h3>{{ $page->title }}</h3>
        <p>Ingresa para revisar el módulo y sus actividades. Si hay módulos anteriores pendientes, deberás completarlos primero.</p>
        <a class="btn" href="{{ url('/login') }}">Continuar inducción</a>
        <p style="font-size: 12px;">Sistema de Inducción I-DEB</p>
    </div>
</body>
</html>
