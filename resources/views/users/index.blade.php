<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

    <style>
        /* --- ESTILOS BASE Y RESPONSIVOS --- */
        body {
            margin: 0;
            padding: 0;
            background-color: #a0a0a0;
            font-family: Arial, sans-serif;
            display: flex;
            min-height: 100vh;
            box-sizing: border-box;
        }

        /* --- CONFIGURACIÓN DEL SIDEBAR (REPARADO PARA MÓVILES) --- */
        .sidebar {
            width: 260px;
            height: 100vh;          /* Fallback navegadores viejos */
            height: 100dvh;         /* Alto dinámico real y exacto en móviles */
            background-color: #2a2a2a;
            position: fixed;
            top: 0;
            left: 0;
            display: flex;
            flex-direction: column;
            z-index: 1050;
            transition: transform 0.3s ease;
            box-shadow: 3px 0 10px rgba(0,0,0,0.2);
            overflow: hidden;       /* Crucial: evita que el sidebar crezca hacia abajo */
        }

        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1040;
            transition: opacity 0.3s ease;
        }
        .sidebar-overlay.active { display: block; }

        .sidebar-top {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #444;
            flex-shrink: 0;         /* Evita que el logo se aplaste en pantallas enanas */
        }
        .sidebar-top .logo-link img { width: 120px; height: auto; }

        /* NAVEGACIÓN CON SCROLL INTERNO INTELIGENTE */
        .sidebar-nav {
            display: flex;
            flex-direction: column;
            gap: 5px;
            padding: 20px 10px;
            flex-grow: 1;
            overflow-y: auto;       /* Si los botones no caben, esta zona lleva el scroll */
        }
        
        /* Estilizar barra de scroll interna del menú para que se vea sutil */
        .sidebar-nav::-webkit-scrollbar { width: 5px; }
        .sidebar-nav::-webkit-scrollbar-thumb { background: #444; border-radius: 4px; }

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
        .sidebar-nav a i { font-size: 16px; width: 20px; }
        .sidebar-nav a:hover { color: white; background-color: #3d3d3d; }
        .sidebar-nav a.active { color: white; background-color: #3498db; }

        /* FOOTER ANCLADO AL RAS DEL SUELO */
        .sidebar-footer {
            padding: 15px;
            border-top: 1px solid #444;
            background-color: #222;
            position: relative;
            flex-shrink: 0;         /* Bloquea por completo que el footer se mueva o encoja */
            margin-top: auto;       /* Lo empuja hacia abajo con firmeza */
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
        .sidebar-user:hover { background-color: #2a2a2a; }
        .sidebar-user .gear-icon { width: 35px; height: 35px; transition: transform 0.3s; }
        .sidebar-user:hover .gear-icon { transform: rotate(45deg); }
        
        .user-info { display: flex; flex-direction: column; color: white; font-family: 'Archivo Black', sans-serif; text-align: left; }
        .user-name { font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 140px; }
        .user-role { font-size: 10px; color: #888; font-family: Arial, sans-serif; margin-top: 2px; }

        /* DROPDOWN INTERACTIVO DESPLEGADO HACIA ARRIBA */
        .gear-dropdown {
            display: none;
            position: absolute;
            bottom: 100%;           /* Cambiado de 70px a 100% para abrir hacia ARRIBA del footer */
            left: 15px;
            right: 15px;
            background-color: #f0f0f0;
            border-radius: 8px;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.3);
            padding: 8px;
            margin-bottom: 10px;    /* Separación estética del engranaje */
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

        /* --- NAVBAR SUPERIOR MÓVIL --- */
        .mobile-header {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 65px;
            background-color: #2a2a2a;
            z-index: 1020;
            align-items: center;
            padding: 0 24px;
            box-sizing: border-box;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
        }
        .menu-toggle { display: flex; flex-direction: column; gap: 5px; cursor: pointer; padding: 5px; }
        .menu-toggle span { width: 26px; height: 3px; background-color: white; border-radius: 2px; transition: 0.3s; }
        .mobile-title { color: white; font-family: 'Archivo Black', sans-serif; font-size: 18px; margin-left: 20px; letter-spacing: 0.5px; }

        /* --- CONTENEDOR PRINCIPAL --- */
        .main-content {
            flex-grow: 1;
            padding: 40px;
            box-sizing: border-box;
            margin-left: 260px;
            transition: margin-left 0.3s ease;
            max-width: calc(100% - 260px);
        }

        h2 { font-family: 'Archivo Black', sans-serif; font-size: 28px; color: #333; text-align: center; margin-bottom: 30px; }

        .print-header { display: none; }

        .top-actions { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 25px; 
            gap: 15px; 
        }

        .btn {
            padding: 11px 22px;
            background-color: black;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-family: 'Archivo Black', sans-serif;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            font-size: 13px;
            transition: background-color 0.2s, transform 0.1s;
        }
        .btn:hover { background-color: #222; }
        .btn:active { transform: scale(0.98); }
        
        .btn-export-container { display: flex; gap: 10px; }
        .btn-excel { background-color: #27ae60; }
        .btn-excel:hover { background-color: #219150; }
        .btn-pdf { background-color: #e74c3c; }
        .btn-pdf:hover { background-color: #c0392b; }

        /* --- DISEÑO DE LA TABLA --- */
        .table-responsive { 
            width: 100%; 
            overflow-x: auto; 
            background: white; 
            border-radius: 12px; 
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15); 
        }
        
        table { 
            width: 100%; 
            border-collapse: collapse; 
            min-width: 950px; 
            font-family: Arial, sans-serif;
        }
        
        th, td { 
            padding: 16px 18px; 
            text-align: left; 
            font-size: 14px; 
            border-bottom: 1px solid #eef2f5;
        }
        
        th { 
            background-color: #f8f9fa; 
            color: #2a2a2a;
            font-family: 'Archivo Black', sans-serif;
            font-size: 12px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border-bottom: 2px solid #ddd;
        }

        tbody tr { transition: background-color 0.2s ease; }
        tbody tr:hover { background-color: #fcfdfe; }
        tbody tr:last-child td { border-bottom: none; }

        .trashed-row { 
            background-color: #fde8e8 !important; 
            border-left: 5px solid #e74c3c; 
        }
        .trashed-row:hover { background-color: #fbd5d5 !important; }
        .trashed-row td { color: #9b1c1c !important; }

        /* Etiquetas para Roles y Tipos */
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }
        .badge-admin { 
            background-color: #2a2a2a !important; 
            color: #ffffff !important; 
        }
        .badge-user { 
            background-color: #e9ecef !important; 
            color: #495057 !important; 
            border: 1px solid #ddd;
        }
        .badge-tipo {
            background-color: #e8f4fd;
            color: #1d82c4;
            border: 1px solid #d4ebf9;
        }

        .pdf-only-row { display: none; } 

        /* Barra de progreso */
        .progress-container { 
            width: 100%; 
            min-width: 130px; 
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .progress-track { 
            background-color: #e9ecef; 
            border-radius: 10px; 
            height: 10px; 
            flex-grow: 1;
            overflow: hidden; 
            border: 1px solid #dcdcdc;
        }
        .progress-bar { 
            background-color: #3498db; 
            height: 100%; 
            border-radius: 10px;
        }
        .progress-text {
            font-size: 12px;
            font-weight: bold;
            color: #495057;
            min-width: 35px;
            text-align: right;
        }

        /* Botones de acción */
        .actions-container { display: flex; flex-wrap: wrap; gap: 6px; }
        .actions-container button, .actions-container a { 
            padding: 6px 12px; 
            font-size: 12px; 
            border-radius: 4px; 
            text-decoration: none; 
            color: white; 
            border: none; 
            cursor: pointer; 
            font-weight: bold;
            transition: opacity 0.2s, transform 0.1s;
        }
        .actions-container button:hover, .actions-container a:hover { opacity: 0.85; }
        
        .btn-editar { background-color: #27ae60; }
        .btn-baja { background-color: #2980b9; }
        .btn-eliminar { background-color: #c0392b; }
        .btn-restore { background-color: #2ecc71; }

        /* Paginación */
        .pagination-container { margin-top: 25px; display: flex; justify-content: center; align-items: center; gap: 15px; }
        .pagination-btn { background-color: #2a2a2a; color: white; padding: 10px 18px; border: none; border-radius: 6px; font-family: 'Archivo Black', sans-serif; font-size: 13px; cursor: pointer; display: flex; align-items: center; gap: 8px; }
        .pagination-btn:disabled { background-color: #ccc; color: #777; cursor: not-allowed; }
        .pagination-info { font-family: 'Archivo Black', sans-serif; font-size: 14px; color: #2a2a2a; background: white; padding: 8px 16px; border-radius: 6px; border: 1px solid #ddd; }

        /* --- CORRECCIÓN EXCLUSIVA PARA IMPRESIÓN Y PDF --- */
        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color-adjust: exact !important;
            }

            body { background-color: white; color: black; display: block; }
            
            .sidebar, .mobile-header, .sidebar-overlay, .top-actions, .actions-container, .pagination-container, th:last-child, td:last-child { 
                display: none !important; 
            }
            
            .main-content { width: 100%; margin: 0; padding: 0; max-width: 100%; }
            .table-responsive { box-shadow: none; overflow: visible; border-radius: 0; }
            .pdf-only-row { display: table-row !important; }

            .print-header {
                display: flex !important;
                justify-content: space-between;
                align-items: center;
                border-bottom: 3px solid #2a2a2a;
                padding-bottom: 12px;
                margin-bottom: 25px;
            }
            .print-header img { height: 65px; width: auto; }
            .print-header h1 { font-family: 'Archivo Black', sans-serif; font-size: 20px; margin: 0; color: #2a2a2a; }

            table { min-width: 100%; }
            tr { page-break-inside: avoid; }
            
            th { 
                background-color: #f2f2f2 !important; 
                border-bottom: 2px solid #000; 
                color: #2a2a2a !important;
            }
            td { border-bottom: 1px solid #ddd; padding: 12px 14px; }
            
            .badge-admin { 
                background-color: #2a2a2a !important; 
                color: #ffffff !important; 
                padding: 4px 8px !important;
            }
            .badge-user {
                background-color: #e9ecef !important;
                color: #495057 !important;
                border: 1px solid #ccc !important;
            }
            .badge-tipo {
                background-color: #e8f4fd !important;
                color: #1d82c4 !important;
                border: 1px solid #d4ebf9 !important;
            }

            .progress-track { 
                background-color: #e9ecef !important; 
                border: 1px solid #b0b0b0 !important;
                display: block !important;
                height: 10px !important;
            }
            .progress-bar { 
                background-color: #3498db !important; 
                display: block !important;
                height: 100% !important;
            }

            .trashed-row { background-color: #fde8e8 !important; }
        }

        /* --- MEDIA QUERIES RESPONSIVAS --- */
        @media screen and (max-width: 1200px) {
            .main-content { padding: 30px 20px; }
        }

        @media screen and (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .mobile-header { display: flex; }
            .main-content { margin-left: 0; padding-top: 95px; max-width: 100%; }
        }

        @media screen and (max-width: 768px) {
            table, thead, tbody, th, td, tr { display: block; }
            table { min-width: auto; }
            thead { display: none; } 
            
            .table-responsive { box-shadow: none; background: transparent; }
            
            tr { 
                margin-bottom: 20px; 
                background: white; 
                border-radius: 12px; 
                box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
                padding: 12px 0; 
                overflow: hidden;
                border-left: 5px solid transparent;
            }

            tr.trashed-row { border-left: 5px solid #e74c3c; }
            
            td { 
                border: none; 
                border-bottom: 1px solid #f1f3f5; 
                position: relative; 
                padding: 12px 15px 12px 45% !important; 
                text-align: left !important; 
                display: flex; 
                align-items: center; 
                justify-content: flex-start;
                min-height: 35px;
            }
            td:last-child { border-bottom: none; }
            
            td:before { 
                content: attr(data-label); 
                position: absolute; 
                left: 15px; 
                font-weight: bold; 
                text-transform: uppercase; 
                font-size: 11px; 
                color: #777; 
                width: 35%; 
                text-align: left; 
            }

            .trashed-row td:before { color: #9b1c1c; }
            
            .actions-container { width: 100%; padding: 5px 0 0 0; gap: 8px; }
            .actions-container form, .actions-container a, .actions-container button { width: 100%; }
            .actions-container * { text-align: center; box-sizing: border-box; }
            
            .top-actions { flex-direction: column; align-items: stretch; gap: 12px; }
            .btn-export-container { flex-direction: column; gap: 8px; }
            
            .pagination-container { flex-direction: row; justify-content: space-between; width: 100%; }
            .pagination-btn { flex: 1; justify-content: center; padding: 12px; font-size: 12px; }
            .pagination-info { padding: 10px; font-size: 12px; white-space: nowrap; }
        }

        @media screen and (max-width: 480px) {
            .mobile-title { font-size: 16px; }
            td { padding-left: 40% !important; }
            td:before { width: 32%; font-size: 10px; }
        }
    </style>
</head>
<body>

    <div class="mobile-header">
        <div class="menu-toggle" onclick="toggleSidebar()">
            <span></span><span></span><span></span>
        </div>
        <div class="mobile-title">Usuarios Admin</div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

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
                    <form method="POST" action="{{ route('logout') }}" onclick="event.stopPropagation();">
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

    <div class="main-content">
        
        <div class="print-header">
            <img src="{{ asset('images/I-DEBLogo.jpg') }}" alt="Logo Corporativo">
            <div class="report-title">
                <h1>REPORTE GLOBAL DE USUARIOS</h1>
                <span>Fecha de Emisión: {{ date('d/m/Y H:i') }}</span>
            </div>
        </div>

        <h2>Lista de Usuarios</h2>

        <div class="top-actions">
            <a href="/users/create" class="btn">Crear Usuario</a>
            
            <div class="btn-export-container">
                <button onclick="exportToExcel()" class="btn btn-excel">
                    <i class="fas fa-file-excel"></i> Excel
                </button>
                <button onclick="exportToPDF()" class="btn btn-pdf">
                    <i class="fas fa-file-pdf"></i> PDF / Imprimir
                </button>
            </div>
        </div>
   
        <div class="table-responsive">
            <table id="usersTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Tipo</th>
                        <th>Progreso</th>
                        <th>F. Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($allUsers as $user)
                        @php
                            $isInCurrentPage = $users->getCollection()->contains('id', $user->id);
                        @endphp
                    <tr class="{{ $user->trashed() ? 'trashed-row' : '' }} {{ !$isInCurrentPage ? 'pdf-only-row' : '' }}">
                        <td data-label="ID" style="font-weight: bold;">{{ $user->id }}</td>
                        <td data-label="Nombre" style="font-weight: 600;">{{ $user->name }}</td>
                        <td data-label="Email">{{ $user->email }}</td>
                        <td data-label="Rol">
                            <span class="badge {{ $user->role === 'admin' ? 'badge-admin' : 'badge-user' }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td data-label="Tipo">
                            @if($user->role === 'user')
                                <span class="badge badge-tipo">
                                    {{ $user->tipo_usuario }} 
                                    @if($user->area)
                                        <small style="display: block; font-size: 10px; margin-top: 3px; color: #333; font-weight: bold;">
                                            ({{ $user->area }})
                                        </small>
                                    @endif
                                </span>
                            @else
                                <span style="color: #bbb;">---</span>
                            @endif
                        </td>
                        <td data-label="Progreso">
                            @if($user->role === 'user')
                                <div class="progress-container">
                                    <div class="progress-track">
                                        <div class="progress-bar" style="width: {{ $user->inductionViewedPercentage }}%;"></div>
                                    </div>
                                    <span class="progress-text">{{ $user->inductionViewedPercentage }}%</span>
                                </div>
                            @else
                                <span style="color: #bbb;">---</span>
                            @endif
                        </td>
                        <td data-label="F. Registro" style="white-space: nowrap; font-size: 13px;">{{ $user->fecha_registro }}</td>
                        <td data-label="Acciones">
                            <div class="actions-container">
                                @if($user->trashed())
                                    <form action="{{ route('users.restore', $user->id) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="btn-restore">Reactivar</button>
                                    </form>
                                @else
                                    <a href="{{ route('users.edit', $user->id) }}" class="btn-editar">Editar</a>
                                    
                                    <form action="{{ route('users.destroy', $user->id) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas dar de BAJA a este usuario?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-baja">Baja</button>
                                    </form>
                                @endif

                                <form action="{{ route('users.forceDestroy', $user->id) }}" method="POST" onsubmit="return confirm('⚠️ ¡ATENCIÓN! ¿Estás seguro de que deseas ELIMINAR permanentemente este registro?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-eliminar">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="pagination-container">
            <button id="btnPrev" class="pagination-btn" onclick="changePage(-1)"><i class="fas fa-chevron-left"></i> Anterior</button>
            <span id="pageInfo" class="pagination-info"></span>
            <button id="btnNext" class="pagination-btn" onclick="changePage(1)">Siguiente <i class="fas fa-chevron-right"></i></button>
        </div>
    </div>

    <script>
        const COMPANY_LOGO_BASE64 = "data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('images/I-DEBLogo.jpg'))) }}";

        const currentPage = {{ $users->currentPage() }};
        const lastPage = {{ $users->lastPage() }};

        function initPagination() {
            const btnPrev = document.getElementById('btnPrev');
            const btnNext = document.getElementById('btnNext');
            const pageInfo = document.getElementById('pageInfo');

            if (pageInfo) pageInfo.innerText = `Pág. ${currentPage} de ${lastPage}`;
            if (btnPrev) btnPrev.disabled = (currentPage <= 1);
            if (btnNext) btnNext.disabled = (currentPage >= lastPage);
        }

        function changePage(direction) {
            const targetPage = currentPage + direction;
            if (targetPage >= 1 && targetPage <= lastPage) {
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('page', targetPage);
                window.location.search = urlParams.toString();
            }
        }

        document.addEventListener("DOMContentLoaded", initPagination);

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            if (sidebar) sidebar.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
        }

        // FUNCIÓN REPARADA: Manejo del engranaje en móviles leyendo el estado real de la pantalla
        function toggleGearMenu(event) {
            event.preventDefault();
            event.stopPropagation(); // Detiene la propagación para que window.onclick no lo cierre inmediatamente
            
            const dropdown = document.getElementById('gearDropdown');
            if (!dropdown) return;

            // Comprobamos el display real (calculado) en lugar de depender únicamente de la propiedad inline
            const currentDisplay = dropdown.style.display || window.getComputedStyle(dropdown).display;
            
            if (currentDisplay === 'none') {
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        }

        // Manejador global unificado para clics externos en móviles y computadoras
        window.onclick = function(e) {
            // 1. Cerrar dropdown de ajustes si se cliquea fuera de la sección del usuario
            if (!e.target.closest('.sidebar-user')) {
                const dropdown = document.getElementById('gearDropdown');
                if (dropdown) dropdown.style.display = 'none';
            }
            
            // 2. Cerrar sidebar y remover overlay si se cliquea en el fondo oscuro
            if (!e.target.closest('#sidebar') && !e.target.closest('.menu-toggle')) {
                const sidebar = document.getElementById('sidebar');
                const overlay = document.getElementById('sidebarOverlay');
                if (sidebar) sidebar.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
            }
        }

        function exportToExcel() {
            const table = document.getElementById("usersTable");
            if (!table) return;
            
            let data = [];
            const rows = table.querySelectorAll("tbody tr");
            rows.forEach(row => {
                const cells = row.querySelectorAll("td");
                if (cells.length >= 7) {
                    let progressText = cells[5].innerText.trim();
                    if (progressText.includes("%")) {
                        progressText = progressText.split("\n")[0] || progressText;
                    }

                    data.push({
                        'ID': cells[0].innerText.trim(),
                        'Nombre': cells[1].innerText.trim(),
                        'Email': cells[2].innerText.trim(),
                        'Rol': cells[3].innerText.trim(),
                        'Tipo': cells[4].innerText.trim(), 
                        'Progreso': progressText,
                        'Fecha Registro': cells[6].innerText.trim()
                    });
                }
            });

            if (data.length === 0) return;

            const workbook = XLSX.utils.book_new();
            const worksheet = XLSX.utils.json_to_sheet(data, { origin: "A5" });

            XLSX.utils.sheet_add_aoa(workbook, [["CONTROL DE USUARIOS ADMINISTRATIVOS Y AVANCES"]], { origin: "C2" });
            XLSX.utils.sheet_add_aoa(workbook, [[`Generado el: ${new Date().toLocaleString()}`]], { origin: "C3" });

            if(!worksheet['!images']) worksheet['!images'] = [];
            worksheet['!images'].push({
                name: 'I-DEBLogo.jpg',
                txt: COMPANY_LOGO_BASE64.split(',')[1],
                type: 'jpeg',
                range: 'A1:B4'
            });

            const maxProps = Object.keys(data[0]);
            worksheet['!cols'] = maxProps.map(prop => ({
                wch: Math.max(...data.map(obj => (obj[prop] ? obj[prop].toString().length : 0)), prop.length) + 4
            }));

            XLSX.utils.book_append_sheet(workbook, worksheet, "Lista Usuarios");
            const filename = `Lista_Usuarios_${new Date().toISOString().slice(0,10)}.xlsx`;
            XLSX.writeFile(workbook, filename);
        }

        function exportToPDF() {
            window.print();
        }
    </script>
</body>
</html>