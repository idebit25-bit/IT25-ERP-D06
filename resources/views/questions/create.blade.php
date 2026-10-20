@php
    $pages = $pages ?? collect();
@endphp
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Preguntas</title>
    
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
            padding: 30px;
            border-radius: 15px;
            width: 100%;
            max-width: 750px;
            box-sizing: border-box;
            box-shadow: 0 8px 25px rgba(0,0,0,0.2);
            text-align: left;
        }

        h2 {
            font-family: 'Archivo Black', sans-serif;
            text-align: center;
            color: #222;
            margin-top: 0;
            font-size: 24px;
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-top: 15px;
            color: #333;
        }

        select, input[type="text"], input[type="number"] {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #000;
            border-radius: 5px;
            background: #eee;
            box-sizing: border-box;
        }

        /* Bloques Dinámicos */
        .question-block {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-top: 20px;
            border-left: 8px solid #000;
            position: relative;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .remove-question {
            position: absolute;
            top: 10px;
            right: 10px;
            color: white;
            background: #ff4444;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            text-align: center;
            line-height: 24px;
            cursor: pointer;
            font-weight: bold;
        }

        .options-container {
            background: #f1f1f1;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
        }

        .btn-add {
            background: #555;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
            font-weight: bold;
        }

        .btn-save {
            width: 100%;
            padding: 15px;
            background: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            font-family: 'Archivo Black', sans-serif;
            font-size: 18px;
            margin-top: 30px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .btn-save:hover { background: #218838; }

        /* Lista de Preguntas ya creadas */
        .created-item {
            background: #f8f8f8;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            border-left: 5px solid #444;
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

        .btn-volver {
            margin-top: 20px;
            display: inline-block;
            font-family: 'Archivo Black', sans-serif;
            color: #000;
            text-decoration: none;
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
            <h2>Crear Preguntas</h2>
            
            <form action="{{ route('questions.store') }}" method="POST">
                @csrf

                <label for="page_id">Seleccionar Página:</label>
                <select name="page_id" id="page_id" required>
                    @foreach($pages as $page)
                        <option value="{{ $page->id }}">{{ $page->title }}</option>
                    @endforeach
                </select>

                <div id="questionsContainer"></div>

                <button type="button" class="btn-add" onclick="addQuestion()">+ Agregar Pregunta</button>
                <button type="submit" class="btn-save">GUARDAR TODO</button>
                <br>
                <div style="text-align: center;">
                    <a href="{{ route('pages.index') }}" class="btn-volver">← Volver al listado</a>
                </div>
            </form>
        </div>

        <div class="card">
            <h2>Preguntas Creadas</h2>
            @php
                $createdQuestions = \App\Models\Question::orderBy('id', 'desc')->get();
            @endphp
            @forelse($createdQuestions as $q)
                <div class="created-item">
                    <strong>{{ $q->question_text }}</strong> 
                    <p style="margin:5px 0 0; font-size:12px; color:#666;">Tipo: {{ $q->question_type }}</p>
                </div>
            @empty
                <p style="text-align:center; color: #555;">No hay preguntas todavía.</p>
            @endforelse
        </div>
    </div>

    <script>
        // Agregar pregunta de forma dinámica
        function addQuestion() {
            const index = document.querySelectorAll('.question-block').length;
            const container = document.getElementById('questionsContainer');
            
            const div = document.createElement('div');
            div.className = 'question-block';
            div.innerHTML = `
                <span class="remove-question" onclick="this.parentElement.remove()">X</span>
                <label>Texto de la Pregunta:</label>
                <input type="text" name="questions[${index}][question_text]" placeholder="Ej: ¿Cuál es el protocolo de seguridad?" required>

                <label>Tipo de Pregunta:</label>
                <select name="questions[${index}][question_type]" onchange="toggleOptions(this, ${index})">
                    <option value="abierta">Abierta (Texto)</option>
                    <option value="opcion_multiple">Opción Múltiple</option>
                </select>

                <div id="optionsWrapper${index}" class="options-container" style="display:none;">
                    <label>Incisos:</label>
                    <div id="optionsList${index}"></div>
                    <button type="button" class="btn-add" style="background:#888; font-size:12px;" onclick="addOption(${index})">+ Añadir Opción</button>
                    
                    <div style="margin-top:15px; padding:10px; background:#fff; border-radius:5px;">
                        <label>Índice de la respuesta correcta (0, 1, 2...):</label>
                        <input type="number" name="questions[${index}][correct_option]" value="0" min="0" style="width:80px;">
                    </div>
                </div>
            `;
            container.appendChild(div);
        }

        // Mostrar u ocultar contenedor de opciones si es opción múltiple
        function toggleOptions(select, index) {
            const wrapper = document.getElementById(`optionsWrapper${index}`);
            wrapper.style.display = (select.value === 'opcion_multiple') ? 'block' : 'none';
        }

        // Añadir incisos dinámicamente a la pregunta
        function addOption(qIndex) {
            const list = document.getElementById(`optionsList${qIndex}`);
            const oIndex = list.children.length;
            const row = document.createElement('div');
            row.style.display = 'flex';
            row.style.gap = '10px';
            row.style.alignItems = 'center';
            row.style.marginTop = '5px';
            row.innerHTML = `
                <span>${oIndex}.</span>
                <input type="text" name="questions[${qIndex}][options][]" placeholder="Texto de la opción" required>
                <span onclick="this.parentElement.remove()" style="color:red; cursor:pointer; font-weight:bold; margin-left:5px;">X</span>
            `;
            list.appendChild(row);
        }

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

        // Cierre general de componentes al clickear por fuera
        window.onclick = function(event) {
            if (!event.target.closest('.sidebar-user')) {
                const dropdown = document.getElementById('gearDropdown');
                if (dropdown) dropdown.style.display = 'none';
            }
            if (!event.target.closest('#sidebar') && !event.target.closest('.menu-toggle')) {
                document.getElementById('sidebar').classList.remove('active');
            }
        }

        // Inicializar con la primera pregunta abierta por defecto
        addQuestion();
    </script>
</body>
</html>