@php
    // Si usas la lógica de order=1, puedes incluirla aquí, aunque no es esencial en esta vista
    // use App\Models\Page;
    // $firstPage = Page::where('order', 1)->first();
    // $firstPageSlug = $firstPage ? $firstPage->slug : null;
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #a0a0a0;
            font-family: Arial, sans-serif;
            display: flex;
            min-height: 100vh;
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

        /* Logo */
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
            background-color: #3d3d3d;
        }
        .sidebar-nav a.active {
            color: white;
            background-color: #3498db;
        }

        /* Footer del Sidebar (Usuario y Tuerca) */
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

        /* --- CONTAINER PRINCIPAL --- */
        .container {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 40px 15px;
            box-sizing: border-box;
            margin-left: 260px;
            transition: margin-left 0.3s ease;
        }

        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            border: 3px solid gray;
            width: 100%;
            max-width: 450px;
            box-sizing: border-box;
            text-align: left;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        h2 {
            margin-top: 0;
            font-size: 24px;
            font-family: 'Archivo Black', sans-serif;
            text-align: center;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, select {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #000;
            border-radius: 5px;
            background-color: #eee;
            font-size: 16px;
            box-sizing: border-box;
            font-family: inherit; 
        }

        /* Contenedores dinámicos */
        .dynamic-field {
            transition: all 0.3s ease-in-out;
            opacity: 1;
            visibility: visible;
            display: block;
        }

        .hidden {
            display: none !important;
            opacity: 0;
            visibility: hidden;
        }

        .btn {
            background-color: black;
            color: white;
            border: 3px solid gray;
            cursor: pointer;
            font-size: 18px;
            padding: 12px;
            margin-top: 25px;
            width: 100%;
            font-weight: bold;
            transition: 0.3s;
            border-radius: 5px;
            text-align: center;
        }

        .btn-return {
            margin-top: 20px;
            background-color: #444;
            text-decoration: none;
            display: inline-block;
            width: 100%;
            max-width: 150px;
            text-align: center;
        }

        /* Mensajes de error */
        .error {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 20px;
        }
        
        .error ul {
            margin: 0;
            padding-left: 20px;
            color: #721c24;
            text-align: left;
            font-size: 14px;
        }

        /* --- RESPONSIVO PARA MÓVILES --- */
        @media screen and (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .menu-toggle {
                display: flex;
            }
            .container {
                margin-left: 0;
                padding-top: 80px;
            }
        }

        @media screen and (max-width: 768px) {
            .container {
                padding: 20px 10px;
            }

            .form-container {
                padding: 20px 15px;
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
            <a href="/pages" class="{{ request()->is('pages*') ? 'active' : '' }}">
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
                            <i class="fas fa-sign-out-alt"></i> Salir
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </aside>

    <div class="container">
        <div class="form-container">
            <h2>Crear Usuario</h2>

            @if ($errors->any())
                <div class="error">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('users.store') }}" method="POST">
                @csrf
                <label for="name">Nombre:</label>
                <input type="text" name="name" id="name" value="{{ old('name') }}" required>

                <label for="email">Correo:</label>
                <input type="email" name="email" id="email" value="{{ old('email') }}" required>

                <label for="password">Contraseña:</label>
                <input type="password" name="password" id="password" required>

                <label for="role">Rol:</label>
                <select name="role" id="role" required>
                    <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Usuario</option>
                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                </select>

                <div id="tipoUsuarioGroup" class="dynamic-field">
                    <label for="tipo_usuario">Tipo de Usuario:</label>
                    <select name="tipo_usuario" id="tipo_usuario">
                        <option value="Colaborador" {{ old('tipo_usuario') === 'Colaborador' ? 'selected' : '' }}>Colaborador (Contratación Interna)</option>
                        <option value="Estadía" {{ old('tipo_usuario') === 'Estadía' ? 'selected' : '' }}>Personal en Estadía</option>
                    </select>
                </div>

                <div id="tipoColaboradorGroup" class="dynamic-field">
                    <label for="area">Perfil de Colaborador / Área:</label>
                    <select name="area" id="area">
                        <option value="" disabled selected>-- Selecciona un perfil --</option>
                        
                        <optgroup label="Administrativos">
                            <option value="Capital Humano" {{ old('area') === 'Capital Humano' ? 'selected' : '' }}>Capital Humano</option>
                            <option value="Gestión de pedidos" {{ old('area') === 'Gestión de pedidos' ? 'selected' : '' }}>Gestión de pedidos</option>
                            <option value="Ingeniero de recursos materiales" {{ old('area') === 'Ingeniero de recursos materiales' ? 'selected' : '' }}>Ingeniero de recursos materiales</option>
                            <option value="Coordinadora General" {{ old('area') === 'Coordinadora General' ? 'selected' : '' }}>Coordinadora General</option>
                            <option value="Gerente General" {{ old('area') === 'Gerente General' ? 'selected' : '' }}>Gerente General</option>
                            <option value="Auxiliar administrativo" {{ old('area') === 'Auxiliar administrativo' ? 'selected' : '' }}>Auxiliar administrativo (Futuro)</option>
                        </optgroup>

                        <optgroup label="Operativos">
                            <option value="Coordinador técnico" {{ old('area') === 'Coordinador técnico' ? 'selected' : '' }}>Coordinador técnico</option>
                            <option value="Ingeniero de control y Soporte técnico" {{ old('area') === 'Ingeniero de control y Soporte técnico' ? 'selected' : '' }}>Ingeniero de control y Soporte técnico</option>
                            <option value="Ingeniería y proyectos" {{ old('area') === 'Ingeniería y proyectos' ? 'selected' : '' }}>Ingeniería y proyectos</option>
                            <option value="Ingeniero Junior" {{ old('area') === 'Ingeniero Junior' ? 'selected' : '' }}>Ingeniero Junior</option>
                            <option value="Ingeniero en mantenimiento y recurso materiales" {{ old('area') === 'Ingeniero en mantenimiento y recurso materiales' ? 'selected' : '' }}>Ingeniero en mantenimiento y recurso materiales</option>
                        </optgroup>
                    </select>
                </div>

                <button type="submit" class="btn">Crear</button>
            </form>
        </div>

        <a href="/users" class="btn btn-return">Volver</a>
    </div>

    <script>
        // Abrir/Cerrar el Sidebar (Móvil)
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        // Desplegar menú de la tuerca
        function toggleGearMenu(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('gearDropdown');
            dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
        }

        // --- CONTROL DE CAMPOS DINÁMICOS EN CASCADA ---
        const roleSelect = document.getElementById('role');
        const tipoUsuarioGroup = document.getElementById('tipoUsuarioGroup');
        const tipoUsuarioSelect = document.getElementById('tipo_usuario');
        
        const tipoColaboradorGroup = document.getElementById('tipoColaboradorGroup');
        const areaSelect = document.getElementById('area');

        function updateFormFields() {
            // Regla 1: Si es Admin, ocultar Tipo de Usuario y Perfil de Colaborador / Área
            if (roleSelect.value === 'admin') {
                tipoUsuarioGroup.classList.add('hidden');
                tipoUsuarioSelect.removeAttribute('required');

                tipoColaboradorGroup.classList.add('hidden');
                areaSelect.removeAttribute('required');
            } else {
                // Si es un Usuario normal, mostramos "Tipo de Usuario"
                tipoUsuarioGroup.classList.remove('hidden');
                tipoUsuarioSelect.setAttribute('required', 'required');

                // Regla 2: Si dentro de Usuario es 'Colaborador', mostramos Perfil / Área
                if (tipoUsuarioSelect.value === 'Colaborador') {
                    tipoColaboradorGroup.classList.remove('hidden');
                    areaSelect.setAttribute('required', 'required');
                } else {
                    // Si es Estadía, ocultamos Perfil / Área
                    tipoColaboradorGroup.classList.add('hidden');
                    areaSelect.removeAttribute('required');
                }
            }
        }

        // Escuchadores de eventos interactivos
        roleSelect.addEventListener('change', updateFormFields);
        tipoUsuarioSelect.addEventListener('change', updateFormFields);

        // Inicializar al cargar el DOM
        document.addEventListener('DOMContentLoaded', updateFormFields);

        // Eventos de clic globales para cerrar los menús abiertos
        window.addEventListener('click', function(e) {
            if (!e.target.closest('.sidebar-user')) {
                const dropdown = document.getElementById('gearDropdown');
                if (dropdown) dropdown.style.display = 'none';
            }
            
            if (!e.target.closest('#sidebar') && !e.target.closest('.menu-toggle')) {
                document.getElementById('sidebar').classList.remove('active');
            }
        });
    </script>
</body>
</html>