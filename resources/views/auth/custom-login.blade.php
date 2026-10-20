<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    
    <style>
        @keyframes logoMove {
            0% { opacity: 0; transform: scale(0.5) translateY(-50px); }
            100% { opacity: 1; transform: scale(1) translateY(0); }
        }

        @keyframes fadeIn {
            0% { opacity: 0; }
            100% { opacity: 1; }
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #a0a0a0;
            font-family: Arial, sans-serif;
            overflow: hidden;
        }

        .login-container {
            width: 360px;
            padding: 50px 40px;
            background: #c4c4c4;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
            opacity: 0;
            animation: fadeIn 0.8s ease-out forwards, logoMove 0.8s ease-out forwards;
            box-sizing: border-box;
        }

        .login-container img {
            width: 180px;
            height: auto;
            margin-bottom: 25px;
            animation: logoMove 0.8s ease-out forwards;
        }

        .login-container h2, 
        .login-container p {
            font-family: 'Archivo Black', sans-serif;
            margin: 0;
            opacity: 0;
            animation: fadeIn 1s ease-out forwards;
            animation-delay: 1s;
        }

        .login-container h2 { 
            font-size: 26px; 
            color: #000;
            letter-spacing: 1px;
        }
        
        .login-container p { 
            font-size: 13px; 
            color: #333;
            margin-top: 5px;
            margin-bottom: 30px;
            letter-spacing: 0.5px;
        }

        form {
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .input-container {
            position: relative;
            width: 100%;
        }

        .login-container input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #000;
            border-radius: 6px;
            background-color: #ddd;
            font-size: 14px;
            color: #000;
            box-sizing: border-box;
            outline: none;
            transition: background-color 0.2s ease, border-color 0.2s ease;
            opacity: 0;
            animation: fadeIn 1s ease-out forwards;
            animation-delay: 1.3s;
        }

        .login-container input::placeholder {
            color: #666;
        }

        .login-container input:focus {
            background-color: #e5e5e5;
            border-color: #333;
        }

        .input-container .login-container input {
            padding-right: 45px; /* Espacio para que el texto de la clave no toque el ojo */
        }

        .input-container .eye-icon {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            width: 22px;
            height: 22px;
            opacity: 0;
            animation: fadeIn 1s ease-out forwards;
            animation-delay: 1.5s;
            user-select: none;
        }

        .login-container button {
            width: 100%;
            padding: 14px;
            background: #000;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 6px;
            font-family: 'Archivo Black', sans-serif;
            font-size: 14px;
            letter-spacing: 0.5px;
            margin-top: 10px;
            transition: background-color 0.2s ease, transform 0.1s ease;
            opacity: 0;
            animation: fadeIn 1s ease-out forwards;
            animation-delay: 1.5s;
        }

        .login-container button:hover { 
            background: #222; 
        }

        .login-container button:active {
            transform: scale(0.98);
        }

        .login-error {
            width: 100%;
            margin-bottom: 15px;
            padding: 12px 15px;
            background: #ffe6e6;
            color: #900;
            border: 1px solid #f5c2c2;
            border-radius: 6px;
            text-align: left;
            box-sizing: border-box;
            font-size: 13px;
            opacity: 0;
            animation: fadeIn 1s ease-out forwards;
            animation-delay: 1.3s;
        }

        .login-error ul {
            margin: 0;
            padding-left: 18px;
        }

        .login-error li {
            margin-bottom: 4px;
        }

        .login-error li:last-child {
            margin-bottom: 0;
        }

        .forgot-password {
            display: inline-block;
            margin-top: 20px;
            font-size: 13px;
            text-decoration: none;
            color: #000;
            font-weight: bold;
            transition: color 0.2s ease;
            opacity: 0;
            animation: fadeIn 1s ease-out forwards;
            animation-delay: 1.7s;
        }

        .forgot-password:hover { 
            text-decoration: underline; 
            color: #222;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <p>MÓDULO DE INDUCCIÓN Y CAPACITACIÓN</p>
        <img src="{{ asset('images/ideb.png') }}" alt="Logo">
        <h2>LOGIN</h2>
        <p>PROCESO DE INDUCCIÓN</p>

        @if ($errors->any())
            <div class="login-error">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="email" name="email" placeholder="CORREO" required>

            <div class="input-container">
                <input type="password" id="password" name="password" placeholder="CONTRASEÑA" required>
                <img src="{{ asset('images/ojocerrado.png') }}" id="eyeIcon" class="eye-icon" onclick="togglePassword()" alt="Mostrar/Ocultar">
            </div>

            <button type="submit">INICIAR SESIÓN</button>
        </form>

    </div>

    <script>
        function togglePassword() {
            var passwordInput = document.getElementById("password");
            var eyeIcon = document.getElementById("eyeIcon");
            
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeIcon.src = "{{ asset('images/ojoabierto.png') }}";
            } else {
                passwordInput.type = "password";
                eyeIcon.src = "{{ asset('images/ojocerrado.png') }}";
            }
        }
    </script>
</body>
</html>