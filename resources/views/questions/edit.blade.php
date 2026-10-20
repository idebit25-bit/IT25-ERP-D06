<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Pregunta</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* --- ESTILOS GENERALES (BASE UNIFICADA) --- */
        body {
            margin: 0;
            padding: 0;
            background-color: #a0a0a0;
            font-family: Arial, sans-serif;
            display: flex;
            min-height: 100vh;
            color: #333;
        }

        /* --- CONFIGURACIÓN DEL SIDEBAR --- */
        .sidebar {
            width: 260px;
            height: 100vh;
            background-color: #2a2a2a;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            z-index: 1000;
            transition: transform 0.3s ease;
            box-shadow: 3px 0 10px rgba(0,0,0,0.2);
        }

        /* Logo superior */
        .sidebar-top {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #444;
        }
        .sidebar-top .logo-link img {
            width: 120px;
            height: auto;
        }

        /* Menú de Navegación */
        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 20px 10px;
            flex-grow: 1;
        }
        .sidebar-nav a {
            color: #b8b8b8;
            text-decoration: none;
            font-family: 'Archivo Black', sans-serif;
            font-size: 14px;
            padding: 14px 15px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
            text-align: left;
        }
        .sidebar-nav a i {
            font-size: 16px;
            width: 20px;
        }
        .sidebar-nav a:hover {
            color: white;
            background-color: #333;
        }
        .sidebar-nav a.active {
            color: white;
            background-color: #3498db;
        }

        /* Footer del Sidebar */
        .sidebar-footer {
            padding: 15px;
            border-top: 1px solid #444;
            background-color: #222;
            position: relative;
        }
        .sidebar-user {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            padding: 5px;
            border-radius: 6px;
            transition: background 0.2s;
        }
        .sidebar-user:hover {
            background-color: #2a2a2a;
        }
        .sidebar-user .gear-icon {
            width: 35px;
            height: 35px;
            transition: transform 0.3s;
        }
        .sidebar-user:hover .gear-icon {
            transform: rotate(45deg);
        }
        .user-info {
            display: flex;
            flex-direction: column;
            color: white;
            font-family: 'Archivo Black', sans-serif;
            text-align: left;
        }
        .user-name {
            font-size: 12px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 140px;
        }
        .user-role {
            font-size: 10px;
            color: #888;
            font-family: Arial, sans-serif;
            margin-top: 2px;
        }

        /* Dropdown de Salida */
        .gear-dropdown {
            display: none;
            position: absolute;
            bottom: 70px;
            left: 15px;
            right: 15px;
            background-color: #f0f0f0;
            border-radius: 8px;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.3);
            padding: 8px;
            z-index: 1100;
            text-align: left;
        }
        .logout-button {
            background-color: red;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-family: 'Archivo Black', sans-serif;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        /* Botón Hamburguesa Móvil */
        .menu-toggle {
            display: none;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1010;
            background: #2a2a2a;
            padding: 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .menu-toggle span {
            width: 25px;
            height: 3px;
            background-color: white;
            border-radius: 2px;
        }

        /* --- CONTENEDOR PRINCIPAL --- */
        .report-container {
            flex-grow: 1;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 30px;
            margin-left: 260px;
            transition: margin-left 0.3s ease;
            box-sizing: border-box;
            width: 100%;
        }

        .card {
            background: #c4c4c4;
            padding: 40px;
            border-radius: 15px;
            width: 100%;
            max-width: 500px;
            box-sizing: border-box;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            text-align: left;
        }

        h2 {
            font-family: 'Archivo Black', sans-serif;
            text-align: center;
            color: #222;
            margin-top: 0;
            font-size: 26px;
            margin-bottom: 25px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
            color: #333;
        }

        input[type="text"], input[type="number"], select {
            width: 100%;
            padding: 12px;
            margin-top: 8px;
            border: 1px solid #000;
            border-radius: 5px;
            background-color: #ddd;
            box-sizing: border-box;
        }

        /* Bloques dinámicos de Opciones */
        .options-box {
            background: #f8f8f8;
            padding: 15px;
            border-radius: 10px;
            margin-top: 15px;
            border: 1px solid #999;
        }

        .option-field {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            gap: 10px;
        }

        .btn-add-opt {
            background: #555;
            color: white;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
            font-size: 12px;
            font-weight: bold;
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: #000;
            color: white;
            border: none;
            border-radius: 5px;
            font-family: 'Archivo Black', sans-serif;
            font-size: 18px;
            margin-top: 25px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .btn-submit:hover {
            background: #222;
        }

        /* --- RESPONSIVIDAD --- */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .menu-toggle {
                display: flex;
            }
            .report-container {
                margin-left: 0;
                padding-top: 80px;
            }
        }
    </style>
</head>
<body>

    <div class="menu-toggle" onclick="toggleSidebar()">
        <span></span><span></span><span></span>
    </div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-top">
            <a href="/admin" class="logo-link">
                <img src="{{ asset('images/logob.png') }}" alt="Logo">
            </a>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('induction.start') }}" class="{{ request()->routeIs('induction.*') ? 'active' : '' }}">
                <i class="fas fa-graduation-cap"></i> INDUCCIÓN
            </a>
            <a href="/users" class="{{ request()->is('users*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> USUARIOS
            </a>
            <a href="/pages" class="active">
                <i class="fas fa-file-alt"></i> PÁGINAS
            </a>
            <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                <i class="fas fa-chart-bar"></i> REPORTES
            </a>
        </nav>

        <div class="sidebar-footer">
            @if(Auth::check())
            <div class="sidebar-user" onclick="toggleGearMenu(event)">
                <img src="{{ asset('images/gear.png') }}" alt="Ajustes" class="gear-icon">
                <div class="user-info">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <span class="user-role">Administrador</span>
                </div>
                
                <div class="gear-dropdown" id="gearDropdown">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-button">
                            <i class="fas fa-sign-out-alt"></i> SALIR
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </aside>

    <div class="report-container">
        <div class="card">
            <h2>Editar Pregunta</h2>

            <form action="{{ route('questions.update', $question->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <label>Texto de la pregunta:</label>
                <input type="text" name="question_text" value="{{ old('question_text', $question->question_text) }}" required>

                <label>Tipo de pregunta:</label>
                <select name="question_type" id="question_type" onchange="toggleOptions()">
                    <option value="abierta" {{ $question->question_type == 'abierta' ? 'selected' : '' }}>Abierta</option>
                    <option value="opcion_multiple" {{ $question->question_type == 'opcion_multiple' ? 'selected' : '' }}>Opción Múltiple</option>
                </select>

                <div id="optionsContainer" class="options-box">
                    <p style="font-weight:bold; margin-top:0; color:#111;">Opciones:</p>
                    <div id="optionsList"></div>
                    <button type="button" class="btn-add-opt" id="addOptionBtn">+ Agregar Opción</button>
                    
                    <label style="margin-top:15px;">Índice de la respuesta correcta:</label>
                    <input type="number" name="correct_option" value="{{ $question->correct_option ?? 0 }}" min="0">
                </div>

                <button type="submit" class="btn-submit">ACTUALIZAR PREGUNTA</button>
            </form>
        </div>
    </div>

    <script>
        const questionType = document.getElementById('question_type');
        const container = document.getElementById('optionsContainer');
        const list = document.getElementById('optionsList');

        // Decodificación segura de las opciones existentes de la BD
        @php
            $options = json_decode($question->options, true) ?? [];
        @endphp
        let optionsData = {!! json_encode($options) !!};

        // Mostrar u ocultar bloque de incisos según tipo de pregunta
        function toggleOptions() {
            container.style.display = questionType.value === 'opcion_multiple' ? 'block' : 'none';
        }

        // Crear dinámicamente un campo de texto para cada inciso
        function addOptionField(val = '') {
            const div = document.createElement('div');
            div.className = 'option-field';
            div.innerHTML = `
                <input type="text" name="options[]" value="${val}" required style="flex:1;">
                <span onclick="this.parentElement.remove()" style="color:red; cursor:pointer; font-weight:bold; margin-left:5px;">X</span>
            `;
            list.appendChild(div);
        }

        // Renderizar opciones precargadas al arrancar
        optionsData.forEach(opt => addOptionField(opt));
        document.getElementById('addOptionBtn').onclick = () => addOptionField();
        
        toggleOptions();

        // Control del Sidebar Móvil
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        // Control de menú de usuario (Tuerca)
        function toggleGearMenu(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('gearDropdown');
            dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
        }

        // Cierre general de los menús dinámicos al clickear fuera
        window.onclick = function(event) {
            const dropdown = document.getElementById('gearDropdown');
            if (dropdown && !event.target.closest('.sidebar-user')) {
                dropdown.style.display = 'none';
            }
            if (!event.target.closest('#sidebar') && !event.target.closest('.menu-toggle')) {
                document.getElementById('sidebar').classList.remove('active');
            }
        }
    </script>
</body>
</html>