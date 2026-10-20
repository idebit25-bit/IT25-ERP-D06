<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        /* --- CONFIGURACIÓN DE COLORES --- */
        :root {
            --bg-body: #a0a0a0;           
            --bg-sidebar: #222222;        
            --active-blue: #3498db;       
            --button-black: #1a1a1a;      
            --text-light: #ffffff;
            --text-muted: #9e9e9e;
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
        }

        body {
            background-color: var(--bg-body);
            display: flex;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* --- SIDEBAR --- */
        .sidebar {
            width: var(--sidebar-width);
            background-color: var(--bg-sidebar);
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            left: 0;
            top: 0;
            z-index: 1000;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
            transition: transform 0.3s ease;
        }

        .sidebar-brand {
            padding: 40px 20px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .sidebar-brand img {
            width: 130px; 
            height: auto;
        }

        .sidebar-menu {
            list-style: none;
            padding: 10px 16px;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .sidebar-item a {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 14px 20px;
            color: var(--text-muted);
            text-decoration: none;
            font-weight: 700;
            font-size: 15px;
            letter-spacing: 0.8px;
            border-radius: 12px; 
            transition: all 0.25s ease;
        }

        .sidebar-item a i {
            font-size: 18px;
            width: 24px;
            text-align: center;
        }

        /* Estado dinámico activo en azul */
        .sidebar-item.active a {
            background-color: var(--active-blue);
            color: var(--text-light);
        }

        /* Hover para los botones no seleccionados */
        .sidebar-item a:hover {
            color: var(--text-light);
            background-color: rgba(255, 255, 255, 0.05);
        }

        /* Sección de Perfil Inferior */
        .sidebar-footer {
            margin-top: auto;
            padding: 24px 20px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            position: relative;
        }

        .sidebar-footer:hover {
            background-color: rgba(255, 255, 255, 0.02);
        }

        .footer-gear-icon {
            font-size: 20px;
            color: #5b7590; 
            transition: transform 0.3s ease;
        }

        .sidebar-footer:hover .footer-gear-icon {
            transform: rotate(45deg);
        }

        .user-info {
            display: flex;
            flex-direction: column;
            text-align: left;
        }

        .user-info .username {
            color: var(--text-light);
            font-size: 15px;
            font-weight: 700;
            line-height: 1.2;
        }

        .user-info .role {
            color: var(--text-muted);
            font-size: 12px;
            font-weight: 500;
        }

        .gear-dropdown {
            display: none;
            position: absolute;
            bottom: 80px;
            left: 16px;
            right: 16px;
            background-color: #f5f5f5;
            border-radius: 8px;
            box-shadow: 0 -5px 20px rgba(0,0,0,0.3);
            padding: 8px;
            z-index: 1001;
        }

        .logout-button {
            width: 100%;
            background-color: #df4747;
            color: white;
            padding: 10px;
            font-size: 13px;
            font-weight: bold;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.2s;
        }

        .logout-button:hover {
            background-color: #b82d2d;
        }

        /* --- CONTENEDOR DE CONTENIDO --- */
        .main-content {
            margin-left: var(--sidebar-width);
            flex-grow: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            min-width: 0;
        }

        .dashboard-header {
            background-color: rgba(255, 255, 255, 0.25);
            border: 2px solid rgba(255, 255, 255, 0.4);
            border-radius: 20px;
            padding: 35px 40px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .dashboard-header h1 {
            font-size: 32px;
            font-weight: 700;
            color: #111111;
            margin-bottom: 8px;
        }

        .dashboard-header p {
            font-size: 16px;
            color: #333333;
            font-weight: 500;
        }

        /* --- CUADRÍCULA DE BOTONES --- */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
            width: 100%;
            max-width: 900px;
            margin: auto 0;
        }

        .action-card {
            background-color: var(--button-black);
            color: var(--text-light);
            border: 3px solid #666666;
            border-radius: 20px;
            padding: 35px;
            cursor: pointer;
            height: 160px;
            display: flex;
            align-items: center;
            gap: 25px;
            text-align: left;
            position: relative;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .card-icon {
            font-size: 36px;
            color: var(--bg-body);
            transition: all 0.3s ease;
        }

        .card-text h3 {
            font-size: 20px;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .card-text p {
            font-size: 13px;
            color: var(--text-muted);
            font-weight: 500;
        }

        .action-card:hover {
            border-color: #f5f5f5;
            transform: translateY(-6px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        .action-card:hover .card-icon {
            color: var(--text-light);
            transform: scale(1.1);
        }

        .action-card::after {
            content: '\f061';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            right: 30px;
            font-size: 18px;
            color: var(--text-muted);
            opacity: 0;
            transform: translateX(-10px);
            transition: all 0.3s ease;
        }

        .action-card:hover::after {
            opacity: 1;
            transform: translateX(0);
            color: var(--text-light);
        }

        /* --- SOPORTE MÓVIL TOTAL --- */
        .mobile-header {
            display: none;
            background-color: var(--bg-sidebar);
            padding: 0 20px;
            align-items: center;
            justify-content: space-between;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1002;
            height: 60px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        .menu-toggle {
            cursor: pointer;
            display: flex;
            flex-direction: column;
            gap: 5px;
        }

        .menu-toggle span {
            width: 24px;
            height: 3px;
            background: white;
            border-radius: 2px;
        }

        @media screen and (max-width: 1024px) {
            .mobile-header { display: flex; }
            .sidebar {
                transform: translateX(-100%);
                top: 60px;
                height: calc(100vh - 60px);
            }
            .sidebar.active { transform: translateX(0); }
            .main-content {
                margin-left: 0;
                padding: 30px 20px;
                margin-top: 70px;
            }
            .dashboard-grid { grid-template-columns: 1fr; gap: 20px; }
            .action-card { height: 120px; padding: 25px; }
        }

        #mainSidebar {
        display: flex;
        flex-direction: column;
        height: 100vh;
        height: 100dvh; /* Alto real bloqueado en celulares */
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        overflow: hidden;
    }

    .sidebar-menu {
        flex: 1;
        overflow-y: auto; /* Zona elástica de scroll */
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-footer {
        margin-top: auto; /* Empuja el engranaje firmemente abajo */
        position: relative;
        cursor: pointer;
    }

    .gear-dropdown {
        display: none; /* Controlado dinámicamente por JS */
        position: absolute;
        bottom: 100%; /* Despliega el botón hacia ARRIBA */
        left: 10px;
        right: 10px;
        margin-bottom: 8px;
        z-index: 1050;
    }
    </style>
</head>
<body>

    <div class="mobile-header">
        <div class="menu-toggle" onclick="toggleSidebar(event)">
            <span></span>
            <span></span>
            <span></span>
        </div>
        <img src="{{ asset('images/logob.png') }}" alt="Logo" style="height: 35px;">
        <div style="width: 24px;"></div>
    </div>

    <aside class="sidebar" id="mainSidebar">
        <div class="sidebar-brand">
            <img src="{{ asset('images/logob.png') }}" alt="Logo Corporativo">
        </div>
        
        <ul class="sidebar-menu">
            <li class="sidebar-item {{ request()->routeIs('induction.*') ? 'active' : '' }}">
                <a href="#" onclick="startInduction()"><i class="fas fa-graduation-cap"></i> INDUCCIÓN</a>
            </li>
            <li class="sidebar-item {{ request()->is('users*') ? 'active' : '' }}"> 
                <a href="/users"><i class="fas fa-users"></i> USUARIOS</a>
            </li>
            <li class="sidebar-item {{ request()->is('pages*') ? 'active' : '' }}">
                <a href="/pages"><i class="fas fa-file-alt"></i> PÁGINAS</a>
            </li>
            <li class="sidebar-item {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                <a href="{{ route('admin.reports') }}"><i class="fas fa-chart-bar"></i> REPORTES</a>
            </li>
        </ul>

        @if(Auth::check())
            <div class="sidebar-footer" onclick="toggleGearMenu(event)">
                <i class="fas fa-cog footer-gear-icon"></i>
                <div class="user-info">
                    <span class="username">{{ Auth::user()->name }}</span>
                    <span class="role">Administrador</span>
                </div>

                <div class="gear-dropdown" id="gearDropdown">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-button">
                            <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </aside>

    <main class="main-content">
        
        <div class="dashboard-header">
            <h1>Panel de Administración Central</h1>
            <p>Bienvenido de vuelta. Utiliza las tarjetas de acceso inmediato o el menú lateral para gestionar la plataforma.</p>
        </div>

        <div class="dashboard-grid">
            
            <div class="action-card" onclick="startInduction()">
                <i class="fas fa-play-circle card-icon"></i>
                <div class="card-text">
                    <h3>VER INDUCCIÓN</h3>
                    <p>Visualizar el flujo del sistema de aprendizaje</p>
                </div>
            </div>

            <div class="action-card" onclick="window.location.href='/users'">
                <i class="fas fa-user-plus card-icon"></i>
                <div class="card-text">
                    <h3>REGISTRAR USUARIO</h3>
                    <p>Administrar cuentas, roles y accesos del personal</p>
                </div>
            </div>

            <div class="action-card" onclick="window.location.href='/pages'">
                <i class="fas fa-sliders-h card-icon"></i>
                <div class="card-text">
                    <h3>EDITAR PÁGINAS</h3>
                    <p>Modificar, agregar o reordenar diapositivas</p>
                </div>
            </div>

            <div class="action-card" onclick="window.location.href='{{ route('admin.reports') }}'">
                <i class="fas fa-file-invoice card-icon"></i>
                <div class="card-text">
                    <h3>VER REPORTES</h3>
                    <p>Monitorear avances y descargar archivos CSV</p>
                </div>
            </div>

        </div>

    </main>

    <script>
    function startInduction() {
        window.location.href = "{{ route('induction.start') }}";
    }

    function toggleGearMenu(event) {
        event.preventDefault();
        event.stopPropagation(); // Evita que el evento escale al objeto window y lo cierre
        
        const dropdown = document.getElementById('gearDropdown');
        if (!dropdown) return;

        // Comprobamos el estilo computado real para que no falle en el primer clic
        const currentDisplay = dropdown.style.display || window.getComputedStyle(dropdown).display;
        
        if (currentDisplay === 'none') {
            dropdown.style.display = 'block';
        } else {
            dropdown.style.display = 'none';
        }
    }

    function toggleSidebar(event) {
        if (event) {
            event.preventDefault();
            event.stopPropagation();
        }
        const sidebar = document.getElementById('mainSidebar');
        if (sidebar) {
            sidebar.classList.toggle('active');
        }
    }

    // Escuchador global unificado y optimizado para clics externos
    document.addEventListener('click', function(e) {
        // 1. Cerrar el menú del engranaje si se hace clic fuera del footer
        const dropdown = document.getElementById('gearDropdown');
        const footer = document.querySelector('.sidebar-footer');
        if (dropdown && footer && !footer.contains(e.target)) {
            dropdown.style.display = 'none';
        }

        // 2. Cerrar el sidebar si está activo en móvil y se hace clic fuera de él
        const sidebar = document.getElementById('mainSidebar');
        const toggleBtn = document.querySelector('.menu-toggle');
        if (sidebar && sidebar.classList.contains('active')) {
            // Si no se hizo clic dentro del sidebar ni en el botón de hamburguesa, lo cerramos
            const clickedInsideSidebar = sidebar.contains(e.target);
            const clickedToggleBtn = toggleBtn && toggleBtn.contains(e.target);
            
            if (!clickedInsideSidebar && !clickedToggleBtn) {
                sidebar.classList.remove('active');
            }
        }
    });
</script>
</body>
</html>