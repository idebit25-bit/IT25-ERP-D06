<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pantalla Usuario</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #a0a0a0;
            text-align: center;
            font-family: Arial, sans-serif;
            position: relative;
        }
        .header {
            background-color: #2a2a2a;
            padding: 10px;
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }
        .header img {
            width: 100px;
        }
        .header nav {
            display: flex;
            margin-left: 20px;
        }
        .header nav a {
            color: white;
            margin: 0 20px;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
            transition: all 0.3s ease-in-out;
            padding-bottom: 5px;
        }
        .header nav a:hover {
            transform: scale(1.1);
            color: lightgray;
            border-bottom: 2px solid lightgray;
        }
        .header form {
            margin-left: auto;
            margin-right: 30px;
        }
        .logout {
            background-color: red;
            color: white;
            padding: 16px 22px;
            font-size: 18px;
            border: none;
            cursor: pointer;
            transition: transform 0.3s ease, background-color 0.3s ease;
        }
        .logout:hover {
            transform: scale(1.1);
            background-color: darkred;
        }
        .container {
            padding: 50px;
            margin-top: 5px;
        }
        .video-box {
            background-color: white;
            padding: 50px;
            display: inline-block;
            border: 3px solid gray;
            box-shadow: 5px 5px 10px rgba(0, 0, 0, 0.2);
            margin-bottom: 30px;
        }
        .nav-buttons {
            position: fixed;
            top: 150px;
            width: 100%;
            display: flex;
            justify-content: space-between;
            padding: 0 30px;
            z-index: 100;
        }
        .nav-buttons button {
            background-color: black;
            color: white;
            padding: 20px;
            border: 3px solid gray;
            cursor: pointer;
            font-size: 18px;
            width: 200px;
            transition: all 0.3s ease-in-out;
        }
        .nav-buttons button:hover {
            border-color: lightgray;
            transform: scale(1.1);
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.3);
        }
        .nav-buttons .next {
            position: relative;
            left: -70px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ asset('images/logob.png') }}" alt="Logo">
        <nav>
            <a href="/admin">INICIO</a>
            <a href="#">EJEMPLO</a>
            <a href="#">EJEMPLO</a>
        </nav>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="logout">SALIR</button>
        </form>
    </div>

    <div class="container">
        <div class="video-box">
            <h2>VIDEO DE BIENVENIDA</h2>
            <!-- Cargar video desde YouTube -->
            <iframe width="560" height="315" src="https://www.youtube.com/embed/TqRjQswjWCs" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>

    <div class="nav-buttons">
        <button onclick="window.history.back()">VOLVER</button>
        <button class="next" onclick="window.location.href='{{ route('video') }}'">SIGUIENTE</button>
    </div>
</body>
</html>
