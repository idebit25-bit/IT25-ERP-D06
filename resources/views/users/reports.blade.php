<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Progreso</title>
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.29/jspdf.plugin.autotable.min.js"></script>

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
            z-index: 1050;
            transition: transform 0.3s ease;
            box-shadow: 3px 0 10px rgba(0,0,0,0.2);
        }

        /* Capa oscura de fondo al abrir menú en móviles */
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
        .sidebar-overlay.active {
            display: block;
        }

        .sidebar-top {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #444;
        }
        .sidebar-top .logo-link img {
            width: 120px;
            height: auto;
        }

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

        /* Navbar Superior Móvil */
        .mobile-header {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background-color: #2a2a2a;
            z-index: 1020;
            align-items: center;
            padding: 0 20px;
            box-sizing: border-box;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
        }
        .menu-toggle {
            display: flex;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
        }
        .menu-toggle span {
            width: 25px;
            height: 3px;
            background-color: white;
            border-radius: 2px;
        }
        .mobile-title {
            color: white;
            font-family: 'Archivo Black', sans-serif;
            font-size: 16px;
            margin-left: 20px;
        }

        /* --- CONTENIDO PRINCIPAL --- */
        .report-container { 
            flex-grow: 1;
            padding: 40px 20px; 
            display: flex; 
            justify-content: center; 
            align-items: flex-start;
            margin-left: 260px;
            transition: margin-left 0.3s ease;
            box-sizing: border-box;
            width: calc(100% - 260px);
        }

        .card { 
            background: #c4c4c4; 
            padding: 25px; 
            border-radius: 15px; 
            width: 100%; 
            max-width: 1200px; 
            box-sizing: border-box;
        }

        h1 { font-family: 'Archivo Black', sans-serif; font-size: 26px; margin-top: 0; margin-bottom: 20px; color: #222; }

        /* --- DISEÑO DE PESTAÑAS (TABS) --- */
        .tabs-header {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 3px solid #2a2a2a;
            padding-bottom: 2px;
        }

        .tab-button {
            padding: 12px 25px;
            background: #e0e0e0;
            border: none;
            border-radius: 10px 10px 0 0;
            cursor: pointer;
            font-family: 'Archivo Black', sans-serif;
            font-size: 14px;
            color: #555;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .tab-button:hover {
            background: #d0d0d0;
            color: #000;
        }

        .tab-button.active {
            background: #2a2a2a;
            color: white;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* --- FILTRO DE BÚSQUEDA Y EXPORTACIÓN --- */
        .search-container {
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        .search-wrapper {
            position: relative;
            width: 100%;
            max-width: 400px;
            flex-grow: 1;
        }
        .search-wrapper i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }
        .search-input {
            width: 100%;
            padding: 12px 12px 12px 40px;
            border-radius: 25px;
            border: 2px solid #888;
            font-size: 14px;
            outline: none;
            transition: 0.3s;
            box-sizing: border-box;
        }
        .search-input:focus {
            border-color: #3498db;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.3);
        }

        .export-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .export-btn {
            font-family: 'Archivo Black', sans-serif;
            font-size: 12px;
            padding: 10px 16px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            color: white;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.15);
            transition: background-color 0.2s, transform 0.1s;
            white-space: nowrap;
        }
        .export-btn:active {
            transform: scale(0.98);
        }
        .btn-excel { background-color: #27ae60; }
        .btn-excel:hover { background-color: #219653; }
        .btn-pdf { background-color: #c0392b; }
        .btn-pdf:hover { background-color: #a93226; }

        /* --- CONTENIDO DE TABLAS --- */
        .table-wrapper { 
            background: white; 
            border-radius: 10px; 
            overflow-x: auto; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.05); 
            width: 100%;
            -webkit-overflow-scrolling: touch; 
        }
        table { width: 100%; border-collapse: collapse; min-width: 750px; }
        th { background: #222; color: white; font-family: 'Archivo Black', sans-serif; padding: 12px; font-size: 12px; text-align: left; white-space: nowrap; }
        td { padding: 12px; border-bottom: 1px solid #eee; font-size: 13px; color: #333; }

        .status-badge { padding: 5px 10px; border-radius: 15px; font-size: 10px; font-weight: bold; color: white; white-space: nowrap; }
        .correct { background: #27ae60; }
        .incorrect { background: #c0392b; }
        .open-q { background: #2980b9; }

        /* --- SISTEMA ACORDEÓN --- */
        .user-report-card {
            background: white;
            border-radius: 8px;
            margin-bottom: 12px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid #ddd;
            width: 100%;
            box-sizing: border-box;
        }

        .user-report-header {
            background: #f9f9f9;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: 0.2s;
            user-select: none;
            gap: 15px;
        }
        .user-report-header:hover { background: #f1f1f1; }

        .user-info-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            min-width: 0; 
        }
        .user-info-text {
            min-width: 0;
        }
        .user-name-title {
            font-family: 'Archivo Black', sans-serif;
            font-size: 16px;
            color: #2a2a2a;
            margin: 0;
            text-align: left;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .user-email-subtitle {
            color: #666;
            font-size: 13px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: block;
        }

        .user-header-right {
            display: flex; 
            align-items: center; 
            gap: 15px;
            flex-shrink: 0;
        }
        .user-badge-count {
            background: #3498db;
            color: white;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
            white-space: nowrap;
        }
        .toggle-arrow {
            font-size: 18px;
            color: #666;
            transition: transform 0.3s ease;
        }
        .user-report-card.open .toggle-arrow { transform: rotate(180deg); }

        .user-report-body {
            display: none;
            padding: 20px;
            border-top: 1px solid #eee;
            background: #fafafa;
        }

        .hidden-row { display: none !important; }
        .no-data-msg { text-align: center; color: #777; padding: 20px; font-style: italic; }

        /* --- ESTILOS DE PAGINACIÓN --- */
        .pagination-container {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .pagination-btn {
            background-color: #2a2a2a;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 16px;
            font-family: 'Archivo Black', sans-serif;
            font-size: 12px;
            cursor: pointer;
            transition: 0.3s;
        }
        .pagination-btn:hover:not(:disabled) { background-color: #3498db; }
        .pagination-btn:disabled {
            background-color: #888;
            cursor: not-allowed;
            opacity: 0.5;
        }
        .pagination-info {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #2a2a2a;
            font-weight: bold;
        }

        /* --- MEDIA QUERIES (RESPONSIVIDAD COMPUTADA CORREGIDA) --- */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                /* Habilitamos comportamiento responsivo móvil con scroll */
                height: 100vh;
                height: -webkit-fill-available;
                display: flex;
                flex-direction: column;
                overflow-y: auto; 
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .mobile-header {
                display: flex;
            }
            
            /* Forzamos al footer a quedarse pegado en la base de la pantalla táctil */
            .sidebar-footer {
                position: sticky;
                bottom: 0;
                left: 0;
                width: 100%;
                background-color: #222222 !important; 
                z-index: 1060;
                box-sizing: border-box;
                margin-top: auto; 
                padding: 15px 15px 35px 15px; /* Espacio extra para barras de gestos */
                border-top: 1px solid #444;
            }

            .report-container {
                margin-left: 0;
                padding-top: 85px; 
                width: 100%;
            }
            .tabs-header { 
                flex-direction: column; 
                gap: 5px; 
                border-bottom: none;
            }
            .tab-button { 
                border-radius: 8px; 
                width: 100%;
                justify-content: center;
            }
            .search-container { 
                flex-direction: column; 
                align-items: stretch; 
                gap: 12px;
            }
            .search-wrapper { 
                max-width: 100%; 
            }
            .export-actions { 
                width: 100%; 
                justify-content: center; 
            }
            .export-btn {
                flex-grow: 1;
                justify-content: center;
            }
            .user-report-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
            }
            .user-info-meta {
                width: 100%;
            }
            .user-header-right {
                width: 100%;
                justify-content: space-between;
                border-top: 1px dotted #ccc;
                padding-top: 8px;
            }
        }

        @media (max-width: 480px) {
            .report-container {
                padding-left: 10px;
                padding-right: 10px;
            }
            .card {
                padding: 15px;
            }
            h1 {
                font-size: 20px;
                text-align: center;
            }
            .user-report-body {
                padding: 10px;
            }
        }
    </style>
</head>
<body>

    <div class="mobile-header">
        <div class="menu-toggle" onclick="toggleSidebar()">
            <span></span><span></span><span></span>
        </div>
        <div class="mobile-title">Reportes Admin</div>
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
            <h1>Reporte de Evaluaciones</h1>

            @if(session('success'))
                <div style="background-color: #27ae60; color: white; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-family: Arial, sans-serif; font-size: 14px; display: flex; align-items: center; gap: 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
                    <i class="fas fa-check-circle"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <div class="tabs-header">
                <button class="tab-button active" onclick="switchTab(event, 'general-tab')">
                    <i class="fas fa-list-alt"></i> Historial General
                </button>
                <button class="tab-button" onclick="switchTab(event, 'users-tab')">
                    <i class="fas fa-user-friends"></i> Por Usuario Separado
                </button>
            </div>

            <div class="search-container">
                <div class="search-wrapper">
                    <i class="fas fa-search"></i>
                    <input type="text" id="tableSearch" class="search-input" placeholder="Buscar usuario, página, pregunta o respuesta...">
                </div>
                <div class="export-actions">
                    <button class="export-btn btn-excel" onclick="exportToExcel()">
                        <i class="fas fa-file-excel"></i> Descargar Excel
                    </button>
                    <button class="export-btn btn-pdf" onclick="exportToPDF()">
                        <i class="fas fa-file-pdf"></i> Descargar PDF
                    </button>
                </div>
            </div>
            
            <div id="general-tab" class="tab-content active">
                <div class="table-wrapper">
                    <table id="reportsTable">
                        <thead>
                            <tr>
                                <th>USUARIO</th>
                                <th>TIPO DE USUARIO</th> <th>FECHA REGISTRO</th> 
                                <th>PÁGINA</th>
                                <th>PREGUNTA</th>
                                <th>RESPUESTA</th>
                                <th>RESULTADO</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)   
                                @foreach($user->responses as $response)
                                    <tr class="general-row-item">
                                        <td><strong>{{ $user->name }}</strong></td>
                                        <td>
                                            <span class="status-badge open-q" style="background-color: #f3f4f6; color: #1f2937; border: 1px solid #e5e7eb;">
                                                {{ $user->tipo_usuario ?? 'N/A' }}
                                                @if($user->area)
                                                    <small style="display: block; font-size: 10px; color: #6b7280;">({{ $user->area }})</small>
                                                @endif
                                            </span>
                                        </td>
                                        <td>{{ $user->created_at ? $user->created_at->setTimezone('America/Mexico_City')->format('d/m/Y H:i') : 'N/A' }}</td>
                                        <td>{{ $response->question->page->title ?? 'N/A' }}</td>
                                        <td>{{ $response->question->question_text }}</td>
                                        <td>{{ $response->answer }}</td>
                                        <td>
                                            @if($response->is_correct === null)
                                                <span class="status-badge open-q">Abierta</span>
                                            @elseif($response->is_correct)
                                                <span class="status-badge correct">Correcta</span>
                                            @else
                                                <span class="status-badge incorrect">Incorrecta</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
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

            <div id="users-tab" class="tab-content">
                <div id="usersAccordionContainer">
                    @foreach($users as $user)
                        <div class="user-report-card" data-username="{{ strtolower($user->name) }}" data-useremail="{{ strtolower($user->email) }}">
                            <div class="user-report-header" onclick="toggleAccordion(this)">
                                <div class="user-info-meta">
                                    <i class="fas fa-user-circle" style="font-size: 24px; color: #2a2a2a; flex-shrink:0;"></i>
                                    <div class="user-info-text">
                                        <h3 class="user-name-title">{{ $user->name }}</h3>
                                        <span class="user-email-subtitle">{{ $user->email }}</span>
                                        <div style="margin-top: 4px;">
                                            <span style="background: #e1e7ff; color: #404eed; padding: 2px 8px; font-size: 11px; font-weight: bold; border-radius: 4px;">
                                                {{ $user->tipo_usuario }} @if($user->area) | {{ $user->area }} @endif
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="user-header-right" onclick="event.stopPropagation();">
                                    <form method="POST" action="{{ route('admin.sendReminder', $user->id) }}" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="export-btn" style="background-color: #2a2a2a; color: white; padding: 6px 12px; font-size: 11px; border-radius: 6px; display: flex; align-items: center; gap: 5px;" title="Enviar recordatorio de inducción">
                                            <i class="fas fa-envelope"></i> Avisar
                                        </button>
                                    </form>
                                    
                                    <span class="user-badge-count">{{ $user->responses->count() }} Respuestas</span>
                                    <i class="fas fa-chevron-down toggle-arrow" onclick="toggleAccordion(this.closest('.user-report-header'))" style="cursor: pointer;"></i>
                                </div>
                            </div>
                            
                            <div class="user-report-body">
                                @if($user->responses->count() > 0)
                                    <div class="table-wrapper">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th style="background:#444;">PÁGINA</th>
                                                    <th style="background:#444;">PREGUNTA</th>
                                                    <th style="background:#444;">RESPUESTA COMENTADA</th>
                                                    <th style="background:#444;">EVALUACIÓN</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($user->responses as $response)
                                                    <tr class="user-row-item">
                                                        <td><strong>{{ $response->question->page->title ?? 'N/A' }}</strong></td>
                                                        <td>{{ $response->question->question_text }}</td>
                                                        <td>{{ $response->answer }}</td>
                                                        <td>
                                                            @if($response->is_correct === null)
                                                                <span class="status-badge open-q">Abierta</span>
                                                            @elseif($response->is_correct)
                                                                <span class="status-badge correct">Correcta</span>
                                                            @else
                                                                <span class="status-badge incorrect">Incorrecta</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="no-data-msg">Este usuario aún no ha respondido ninguna evaluación en la inducción.</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <script>
        // --- CONFIGURACIÓN GLOBAL DE LOGO VIA LARAVEL ---
        const COMPANY_LOGO_BASE64 = "data:image/jpeg;base64,{{ base64_encode(file_get_contents(public_path('images/I-DEBLogo.jpg'))) }}";

        // --- VARIABLES DE PAGINACIÓN GLOBAL ---
        let currentPage = 1;
        const rowsPerPage = 10;
        let currentTab = 'general-tab';

        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            sidebar.classList.toggle('active');
            overlay.classList.toggle('active');
        }

        function toggleGearMenu(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('gearDropdown');
            dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
        }

        // --- CONTROLADOR DE PESTAÑAS (TABS) ---
        function switchTab(event, tabId) {
            currentTab = tabId;
            document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
            document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

            event.currentTarget.classList.add('active');
            document.getElementById(tabId).classList.add('active');
            
            document.getElementById('tableSearch').value = '';
            
            document.querySelectorAll('.general-row-item').forEach(row => {
                row.style.display = '';
                row.classList.remove('search-filtered-out');
            });
            
            document.querySelectorAll('.user-report-card').forEach(card => {
                card.style.display = '';
                card.classList.remove('open');
                const body = card.querySelector('.user-report-body');
                if(body) body.style.display = 'none';
            });

            document.querySelectorAll('.user-row-item').forEach(row => {
                row.style.display = '';
            });

            if(tabId === 'general-tab') {
                currentPage = 1;
                applyPagination();
            }
        }

        // --- MANEJO DE ACORDEÓN POR USUARIO ---
        function toggleAccordion(headerElement) {
            const card = headerElement.parentElement;
            const body = card.querySelector('.user-report-body');
            
            if (card.classList.contains('open')) {
                card.classList.remove('open');
                if(body) body.style.display = 'none';
            } else {
                document.querySelectorAll('.user-report-card').forEach(c => {
                    c.classList.remove('open');
                    const b = c.querySelector('.user-report-body');
                    if(b) b.style.display = 'none';
                });

                card.classList.add('open');
                if(body) body.style.display = 'block';
            }
        }

        // --- SISTEMA DE PAGINACIÓN DEL HISTORIAL GENERAL ---
        function applyPagination() {
            const visibleRows = Array.from(document.querySelectorAll('.general-row-item')).filter(row => !row.classList.contains('search-filtered-out'));
            const totalRows = visibleRows.length;
            const totalPages = Math.ceil(totalRows / rowsPerPage) || 1;

            if (currentPage > totalPages) currentPage = totalPages;
            if (currentPage < 1) currentPage = 1;

            const start = (currentPage - 1) * rowsPerPage;
            const end = start + rowsPerPage;

            document.querySelectorAll('.general-row-item').forEach(row => row.style.display = 'none');

            visibleRows.forEach((row, index) => {
                if (index >= start && index < end) {
                    row.style.display = '';
                }
            });

            document.getElementById('pageInfo').innerText = `Página ${currentPage} de ${totalPages}`;
            document.getElementById('btnPrev').disabled = currentPage === 1;
            document.getElementById('btnNext').disabled = currentPage === totalPages;
        }

        function changePage(direction) {
            currentPage += direction;
            applyPagination();
        }

        // --- SISTEMA DE BÚSQUEDA CORREGIDO ---
        document.getElementById('tableSearch').addEventListener('input', function() {
            const filter = this.value.toLowerCase().trim();
            
            // 1. Historial General
            const generalRows = document.querySelectorAll('.general-row-item');
            generalRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                if (text.includes(filter)) {
                    row.classList.remove('search-filtered-out');
                } else {
                    row.classList.add('search-filtered-out');
                }
            });
            currentPage = 1;
            applyPagination();

            // 2. Por Usuario Separado (Lógica Corregida)
            const userCards = document.querySelectorAll('.user-report-card');
            
            userCards.forEach(card => {
                const username = card.getAttribute('data-username') || '';
                const useremail = card.getAttribute('data-useremail') || '';
                const innerRows = card.querySelectorAll('.user-row-item');
                
                const isUserDataMatch = username.includes(filter) || useremail.includes(filter);
                let matchFoundInDetails = false;

                innerRows.forEach(row => {
                    const rowText = row.textContent.toLowerCase();
                    
                    if (filter === '') {
                        row.style.display = ''; 
                    } else if (isUserDataMatch) {
                        row.style.display = ''; 
                        matchFoundInDetails = true;
                    } else if (rowText.includes(filter)) {
                        row.style.display = ''; 
                        matchFoundInDetails = true;
                    } else {
                        row.style.display = 'none'; 
                    }
                });

                if (isUserDataMatch || matchFoundInDetails) {
                    card.style.display = ''; 
                    const body = card.querySelector('.user-report-body');
                    
                    if (filter !== '') {
                        card.classList.add('open');
                        if(body) body.style.display = 'block';
                    } else {
                        card.classList.remove('open');
                        if(body) body.style.display = 'none';
                    }
                } else {
                    card.style.display = 'none';
                }
            });
        });

        // --- RECOPILACIÓN DE DATOS FILTRADOS ---
        function getExportData() {
            let data = [];
            if (currentTab === 'general-tab') {
                const rows = document.querySelectorAll('.general-row-item');
                rows.forEach(row => {
                    if (!row.classList.contains('search-filtered-out')) {
                        const cells = row.querySelectorAll('td');
                        data.push({
                            'Usuario': cells[0].innerText.trim(),
                            'Tipo de Usuario': cells[1].innerText.trim(), 
                            'Fecha Registro': cells[2].innerText.trim(),  
                            'Página': cells[3].innerText.trim(),          
                            'Pregunta': cells[4].innerText.trim(),        
                            'Respuesta': cells[5].innerText.trim(),       
                            'Resultado': cells[6].innerText.trim()        
                        });
                    }
                });
            } else {
                const cards = document.querySelectorAll('.user-report-card');
                cards.forEach(card => {
                    if (card.style.display !== 'none') {
                        const userName = card.querySelector('.user-name-title').innerText.trim();
                        const userEmail = card.querySelector('.user-email-subtitle').innerText.trim();
                        
                        const userTypeBlock = card.querySelector('.user-info-text div span');
                        const userType = userTypeBlock ? userTypeBlock.innerText.trim() : 'N/A';

                        const rows = card.querySelectorAll('.user-report-body tbody tr');
                        
                        rows.forEach(row => {
                            if (row.style.display !== 'none') {
                                const cells = row.querySelectorAll('td');
                                data.push({
                                    'Usuario': userName,
                                    'Email': userEmail,
                                    'Tipo / Perfil': userType, 
                                    'Página': cells[0].innerText.trim(),
                                    'Pregunta': cells[1].innerText.trim(),
                                    'Respuesta': cells[2].innerText.trim(),
                                    'Resultado': cells[3].innerText.trim()
                                });
                            }
                        });
                    }
                });
            }
            return data;
        }

        // --- EXPORTACIÓN A EXCEL CON LOGO ---
        function exportToExcel() {
            const data = getExportData();
            if (data.length === 0) {
                alert('No hay datos disponibles para exportar.');
                return;
            }

            const workbook = XLSX.utils.book_new();
            const worksheet = XLSX.utils.json_to_sheet(data, { origin: "A5" });

            XLSX.utils.sheet_add_aoa(worksheet, [
                ["REPORTE DE EVALUACIONES - PROCESO DE INDUCCIÓN"]
            ], { origin: "C2" });

            XLSX.utils.sheet_add_aoa(worksheet, [
                [`Generado el: ${new Date().toLocaleString()}`]
            ], { origin: "C3" });

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

            XLSX.utils.book_append_sheet(workbook, worksheet, "Reporte Evaluaciones");

            const filename = `Reporte_Evaluaciones_${new Date().toISOString().slice(0,10)}.xlsx`;
            XLSX.writeFile(workbook, filename);
        }

        // --- EXPORTACIÓN A PDF CON LOGO ---
        function exportToPDF() {
            const data = getExportData();
            if (data.length === 0) {
                alert('No hay datos disponibles para exportar.');
                return;
            }
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF('l', 'mm', 'a4');

            try {
                doc.addImage(COMPANY_LOGO_BASE64, 'JPEG', 14, 8, 38, 15);
            } catch (e) {
                console.error("Error al pintar el logo en el PDF: ", e);
            }

            doc.setFont("Helvetica", "bold");
            doc.setFontSize(18);
            doc.text("Reporte de Evaluaciones de Inducción", 56, 15);
            
            doc.setFont("Helvetica", "normal");
            doc.setFontSize(10);
            doc.text(`Fecha de generación: ${new Date().toLocaleString()}`, 56, 21);

            doc.setDrawColor(68, 68, 68);
            doc.setLineWidth(0.4);
            doc.line(14, 27, 283, 27);

            const headers = Object.keys(data[0]);
            const rows = data.map(obj => headers.map(key => obj[key]));

            doc.autoTable({
                head: [headers],
                body: rows,
                startY: 32,
                theme: 'striped',
                headStyles: { fillColor: [42, 42, 42], fontStyle: 'bold' },
                styles: { fontSize: 9, overflow: 'linebreak' },
                columnStyles: {
                    3: { cellWidth: 55 }, 
                    4: { cellWidth: 55 }
                }
            });

            const filename = `Reporte_Evaluaciones_${new Date().toISOString().slice(0,10)}.pdf`;
            doc.save(filename);
        }

        // Cierre de dropdown al hacer clic fuera
        window.addEventListener('click', function(event) {
            if (!event.target.closest('.sidebar-user')) {
                const dropdown = document.getElementById('gearDropdown');
                if (dropdown) dropdown.style.display = 'none';
            }
        });

        document.addEventListener("DOMContentLoaded", function() {
            applyPagination();
        });
    </script>
</body>
</html>