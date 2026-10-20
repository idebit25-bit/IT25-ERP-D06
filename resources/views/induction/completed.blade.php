<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inducción Finalizada</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-body: #434343;
            --card-black: #1a1a1a;
            --text-light: #ffffff;
            --border-gray: #666666;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-body);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .completed-container {
            background-color: var(--card-black);
            color: var(--text-light);
            max-width: 550px;
            width: 100%;
            border: 3px solid var(--border-gray);
            border-radius: 24px;
            padding: 50px 40px;
            text-align: center;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        }

        .success-checkmark {
            width: 100px;
            height: 100px;
            background: rgba(46, 204, 113, 0.15);
            border: 2px solid #2ecc71;
            color: #2ecc71;
            font-size: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 30px auto;
        }

        h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        p {
            color: #cdcdcd;
            font-size: 15px;
            line-height: 1.6;
            margin-bottom: 35px;
        }

        .btn-finish {
            background-color: #2ecc71;
            color: white;
            font-size: 15px;
            font-weight: 700;
            padding: 15px 30px;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.2s ease;
        }

        .btn-finish:hover {
            background-color: #27ae60;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <div class="completed-container">
        <div class="success-checkmark">
            <i class="fas fa-check"></i>
        </div>
        
        <h1>¡Inducción Completada con Éxito!</h1>
        <p>Has concluido satisfactoriamente la revisión de diapositivas y las actividades programadas para tu perfil. 
            Tus respuestas y avances han sido almacenados de forma segura en el servidor central.</p>

        
        <button class="btn-finish" onclick="window.location.href='{{ route('lobby') }}'" style="background-color: #3498db;">
            <i class="fas fa-home"></i> IR AL INICIO
        </button>
        <br>
        <button class="btn-finish" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt"></i> CONCLUIR Y SALIR
        </button>
        

        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

</body>
</html>
