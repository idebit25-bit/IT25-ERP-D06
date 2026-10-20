<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #a0a0a0;
            font-family: Arial, sans-serif;
        }

        .reset-container {
            width: 350px;
            padding: 40px;
            background: #c4c4c4;
            border-radius: 15px;
            text-align: center;
        }

        .reset-container h2 {
            font-family: 'Archivo Black', sans-serif;
            font-size: 24px;
        }

        .reset-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #000;
            border-radius: 5px;
            background-color: #ddd;
            text-align: center;
        }

        .reset-container a {
            display: inline-block;
            width: 50%;
            padding: 10px;
            background: #000;
            color: white;
            text-decoration: none;
            text-align: center;
            border-radius: 5px;
            font-family: 'Archivo Black', sans-serif;
            margin-top: 20px;
        }

        .reset-container a:hover {
            background: #333;
        }
    </style>
</head>
<body>
    <div class="reset-container">
        <h2>Restablecer Contraseña</h2>
        <form method="POST" action="{{ route('password.update') }}" id="reset-password-form">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <input type="email" name="email" placeholder="Correo Electrónico" value="{{ old('email') }}" required>
            <input type="password" name="password" placeholder="Nueva Contraseña" required>
            <input type="password" name="password_confirmation" placeholder="Confirmar Contraseña" required>
            <!-- Enlace en lugar de botón -->
            <a href="javascript:void(0);" onclick="document.getElementById('reset-password-form').submit();">Restablecer</a>
        </form>

        <!-- Mensaje de éxito -->
        @if(session('status'))
            <div style="margin-top: 20px; color: green;">
                {{ session('status') }}
                <br>
                <a href="{{ route('/login') }}" style="color: blue;">Ir a login</a>
            </div>
        @endif
    </div>
</body>
</html>
