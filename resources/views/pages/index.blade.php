<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Páginas y Preguntas</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        /* --- ESTILOS GENERALES --- */
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
            overflow-y: auto;
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
            display: none; /* Oculto por defecto */
            position: absolute;
            bottom: 65px;  /* Ajusta según el alto de tu footer */
            left: 15px;
            right: 15px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.3);
            padding: 8px;
            z-index: 9999; /* Super alto para que no lo tape el footer */
            text-align: left;
        }

        /* Esta clase nueva manejará la visibilidad */
        .gear-dropdown.show {
            display: block !important;
        }
        .logout-button {
            background-color: #dc3545;
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

        .mobile-header {
            display: none;
            align-items: center;
            justify-content: flex-start;
            gap: 20px;
            background: #212529;
            padding: 15px 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1010;
            box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        }
        .mobile-title {
            color: white;
            font-family: 'Archivo Black', sans-serif;
            font-size: 16px;
            text-transform: uppercase;
        }
        .menu-toggle {
            display: flex;
            flex-direction: column;
            gap: 5px;
            cursor: pointer;
            background: transparent;
            border: none;
            padding: 5px;
        }
        .menu-toggle span {
            width: 25px;
            height: 3px;
            background-color: white;
            border-radius: 2px;
        }
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            backdrop-filter: blur(2px);
        }

        /* --- CONTENEDOR PRINCIPAL --- */
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
            background: #ffffff;
            padding: 30px; 
            border-radius: 12px; 
            width: 100%; 
            max-width: 1200px; 
            box-sizing: border-box;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #dee2e6;
        }

        h1 { 
            font-family: 'Archivo Black', sans-serif; 
            font-size: 26px; 
            margin-top: 0; 
            margin-bottom: 25px; 
            color: #212529; 
        }

        .actions-top {
            margin-bottom: 24px;
            display: flex;
            gap: 12px;
            justify-content: flex-start;
            flex-wrap: wrap;
        }

        /* --- FILTROS DE PÚBLICO --- */
        .filter-container {
            margin-bottom: 25px;
            display: flex;
            justify-content: flex-start;
            gap: 8px;
            flex-wrap: wrap;
            background: #f8f9fa;
            padding: 10px;
            border-radius: 10px;
            border: 1px solid #dee2e6;
        }
        .filter-btn {
            padding: 8px 16px;
            font-size: 13px;
            font-family: 'Archivo Black', sans-serif;
            border-radius: 6px;
            border: 1px solid #ced4da;
            background: #ffffff;
            color: #495057;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .filter-btn:hover {
            background: #e9ecef;
            color: #212529;
        }
        .filter-btn.active[data-filter="all"] { background: #212529; color: white; border-color: #212529; }
        .filter-btn.active[data-filter="Estadía"] { background: #2980b9; color: white; border-color: #2980b9; }
        .filter-btn.active[data-filter="Colaborador"] { background: #8e44ad; color: white; border-color: #8e44ad; }
        .filter-btn.active[data-filter="Ambos"] { background: #27ae60; color: white; border-color: #27ae60; }

        .table-wrapper { 
            background: white; 
            border-radius: 8px; 
            overflow-x: auto; 
            border: 1px solid #dee2e6;
        }
        table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        th { 
            background: #343a40; 
            color: white; 
            font-family: 'Archivo Black', sans-serif; 
            padding: 14px 12px; 
            font-size: 12px; 
            text-align: left; 
            white-space: nowrap;
        }
        td { 
            padding: 14px 12px; 
            border-bottom: 1px solid #dee2e6; 
            font-size: 13px; 
            color: #212529; 
            vertical-align: middle;
            word-wrap: break-word;
            word-break: break-word;
        }

        .page-block {
            border-left: 4px solid #6c757d;
        }
        .page-row {
            background-color: #fff;
            transition: background 0.2s;
        }
        .page-row:hover {
            background-color: #f8f9fa;
        }

        .questions-row td {
            padding: 10px 15px 15px 30px;
            background-color: #f8f9fa;
        }
        .questions-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .question-item {
            background: #ffffff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 12px 15px;
            margin-bottom: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap; 
        }
        .question-item span {
            word-break: break-word;
            flex: 1;
            min-width: 200px;
        }

        .order-cell {
            font-weight: bold;
            color: #212529;
            background: #e9ecef;
            border-radius: 4px;
            padding: 4px 12px;
            display: inline-block;
            font-size: 12px;
        }

        /* --- BADGES --- */
        .badge-profile {
            display: inline-block;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: bold;
            border-radius: 12px;
            color: white;
            white-space: nowrap;
        }
        .badge-estadia { background-color: #2980b9; }
        .badge-colaborador { background-color: #8e44ad; }
        .badge-ambos { background-color: #27ae60; }
        .badge-null { background-color: #e63946; }

        .btn {
            padding: 8px 14px;
            font-size: 12px;
            border-radius: 6px;
            text-decoration: none;
            font-family: 'Archivo Black', sans-serif;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }
        .btn-dark { background: #212529; color: white; }
        .btn-dark:hover { background: #000; }
        .btn-edit { background: #f39c12; color: white; }
        .btn-danger { background: #c0392b; color: white; }
        .btn-view { background: #16a085; color: white; }
        .btn:hover { opacity: 0.9; transform: translateY(-1px); }

        .actions-cell {
            display: flex;
            gap: 5px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        /* --- RESPONSIVIDAD --- */
        @media (max-width: 992px) {
        .sidebar { 
            transform: translateX(-100%); 
            /* Cambios críticos para que funcione el scroll en celular */
            height: 100vh;
            height: -webkit-fill-available; /* Evita que la barra de Chrome/Safari tape el fondo */
            display: flex;
            flex-direction: column;
            overflow-y: auto; /* Permite deslizar el menú hacia abajo si no cabe */
        }
        .sidebar.active { transform: translateX(0); }
        .sidebar.active ~ .sidebar-overlay { display: block; }
        
        .mobile-header { display: flex; }
        
        .report-container {
            margin-left: 0;
            padding: 100px 10px 40px 10px;
            width: 100%;
        }
        .card { padding: 15px; }

        /* Forzamos al footer a quedarse abajo del todo */
        .sidebar-footer {
            position: sticky;
            bottom: 0;
            left: 0;
            width: 100%;
            background-color: #222222 !important; /* Mismo color oscuro del fondo */
            z-index: 1010;
            box-sizing: border-box;
            margin-top: auto; /* Empuja el footer al fondo si el menú es corto */
            padding: 15px 15px 35px 15px; /* Agregamos espacio extra abajo para la barra de gestos de iOS/Android */
            border-top: 1px solid #444;
        }
    }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
</head>
<body>

    <div class="mobile-header">
        <div class="menu-toggle" onclick="toggleSidebar()">
            <span></span><span></span><span></span>
        </div>
        <div class="mobile-title">Administrar Contenido</div>
    </div>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <aside class="sidebar" id="sidebar">
        <div class="sidebar-top">
            @if(auth()->user()->role === 'admin')
                <a href="/admin" class="logo-link">
                    <img src="{{ asset('images/logob.png') }}" alt="Logo">
                </a>
            @else
                <img src="{{ asset('images/logob.png') }}" alt="Logo" style="cursor: default; pointer-events: none;">
            @endif
        </div>

        <nav class="sidebar-nav">
            @if(auth()->user()->role !== 'admin')
                <a href="/lobby" class="{{ request()->is('lobby*') ? 'active' : '' }}">
                    <i class="fas fa-home"></i> LOBBY
                </a>
            @endif

            <a href="{{ route('induction.start') }}" class="{{ request()->routeIs('induction.*') ? 'active' : '' }}">
                <i class="fas fa-graduation-cap"></i> INDUCCIÓN
            </a>

            @if(auth()->user()->role === 'admin')
                <a href="/users" class="{{ request()->is('users*') ? 'active' : '' }}">
                    <i class="fas fa-users"></i> USUARIOS
                </a>
                <a href="/pages" class="{{ request()->is('pages*') ? 'active' : '' }}">
                    <i class="fas fa-file-alt"></i> PÁGINAS
                </a>
                <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                    <i class="fas fa-chart-bar"></i> REPORTES
                </a>
            @endif
        </nav>

        <div class="sidebar-footer">
            @if(Auth::check())
            <div class="sidebar-user" onclick="toggleGearMenu(event)">
                <img src="{{ asset('images/gear.png') }}" alt="Ajustes" class="gear-icon">
                <div class="user-info">
                    <span class="user-name">{{ Auth::user()->name }}</span>
                    <span class="user-role">{{ Auth::user()->role === 'admin' ? 'Administrador' : 'Usuario' }}</span>
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
            <h1>Administrar Contenido</h1>

            <div class="actions-top">
                <a href="{{ route('pages.create') }}" class="btn btn-dark">+ Nueva Página</a>
                <a href="{{ route('questions.create') }}" class="btn btn-dark">+ Nueva Pregunta</a>
            </div>

            <div class="filter-container" style="margin-bottom: 10px;">
                <button class="filter-btn active" data-filter="all">Ver Todas</button>
                <button class="filter-btn" data-filter="Colaborador" id="btn-colaboradores">Colaboradores (Puestos)</button>
                <button class="filter-btn" data-filter="Estadía">Estadía</button>
                <button class="filter-btn" data-filter="Ambos">Ambos públicos</button>
            </div>

            <div id="sub-filter-colaboradores" style="display: none; background: #f8f9fa; padding: 10px; border-radius: 6px; margin-bottom: 20px; border-left: 4px solid #34495e;">
                <span style="font-size: 12px; font-weight: bold; color: #555; display: block; margin-bottom: 8px;">Filtrar por Puesto de Colaborador:</span>
                <div style="display: flex; flex-wrap: wrap; gap: 6px;">
                    <button class="sub-filter-btn" data-puesto="all" style="padding: 4px 10px; font-size: 11px; background: #34495e; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold;">[Todos los puestos]</button>
                    <button class="sub-filter-btn" data-puesto="personal humano" style="padding: 4px 10px; font-size: 11px; background: #fff; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">Personal Humano</button>
                    <button class="sub-filter-btn" data-puesto="capital humano" style="padding: 4px 10px; font-size: 11px; background: #fff; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">Capital Humano</button>
                    <button class="sub-filter-btn" data-puesto="gestion de pedidos" style="padding: 4px 10px; font-size: 11px; background: #fff; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">Gestión de pedidos</button>
                    <button class="sub-filter-btn" data-puesto="ingeniero de recursos materiales" style="padding: 4px 10px; font-size: 11px; background: #fff; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">Ing. Recursos Materiales</button>
                    <button class="sub-filter-btn" data-puesto="coordinadora general" style="padding: 4px 10px; font-size: 11px; background: #fff; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">Coordinadora General</button>
                    <button class="sub-filter-btn" data-puesto="gerente general" style="padding: 4px 10px; font-size: 11px; background: #fff; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">Gerente General</button>
                    <button class="sub-filter-btn" data-puesto="auxiliar administrativo" style="padding: 4px 10px; font-size: 11px; background: #fff; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">Auxiliar Administrativo</button>
                    <button class="sub-filter-btn" data-puesto="coordinador tecnico" style="padding: 4px 10px; font-size: 11px; background: #fff; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">Coordinador Técnico</button>
                    <button class="sub-filter-btn" data-puesto="ingeniero de control y soporte tecnico" style="padding: 4px 10px; font-size: 11px; background: #fff; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">Ing. Control y Soporte</button>
                    <button class="sub-filter-btn" data-puesto="ingenieria y proyectos" style="padding: 4px 10px; font-size: 11px; background: #fff; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">Ingeniería y Proyectos</button>
                    <button class="sub-filter-btn" data-puesto="ingeniero junior" style="padding: 4px 10px; font-size: 11px; background: #fff; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">Ingeniero Junior</button>
                    <button class="sub-filter-btn" data-puesto="ingeniero en mantenimiento y recurso materiales" style="padding: 4px 10px; font-size: 11px; background: #fff; border: 1px solid #ccc; border-radius: 4px; cursor: pointer;">Ing. Mantenimiento y Rec.</button>
                </div>
            </div>

            <div class="table-wrapper">
                <table id="pagesTable">
                    <thead>
                        <tr>
                            <th>PÁGINA / PREGUNTAS</th>
                            <th>SLUG / TIPO</th>
                            <th>DESTINADO A</th>
                            <th style="text-align: center;">ORDEN</th>
                            <th style="text-align: right;">ACCIONES</th>
                        </tr>
                    </thead>
                    @foreach($pages as $page)
                        @php
                            // Tratamiento seguro contra arreglos
                            if (is_array($page->destinado_a)) {
                                $rawDestinado = implode(', ', array_filter($page->destinado_a));
                                $firstItem = !empty($page->destinado_a) ? $page->destinado_a[0] : '';
                            } else {
                                $rawDestinado = (string)$page->destinado_a;
                                $firstItem = $rawDestinado;
                            }

                            $val = strtolower(trim($firstItem));
                            $valNormalizada = str_replace(['í','á','é','ó','ú'], ['i','a','e','o','u'], $val);

                            $esEstadia = ($valNormalizada === 'estadia');
                            $esAmbos = ($valNormalizada === 'ambos');
                            $esColaborador = !$esEstadia && !$esAmbos && !empty($valNormalizada);
                        @endphp
                        
                        <tbody class="page-block" 
                               data-id="{{ $page->id }}" 
                               data-destinado="{{ $esEstadia ? 'Estadía' : ($esAmbos ? 'Ambos' : 'Colaborador') }}"
                               data-puesto="{{ strtolower($rawDestinado) }}">
                            
                            <tr class="page-row">
                                <td><i class="fas fa-file-alt" style="color: #444; margin-right: 5px;"></i> <strong>{{ $page->title }}</strong></td>
                                <td><small style="color: #555; font-weight: bold;">{{ $page->slug }}</small></td>
                                <td>
                                    @if($esEstadia)
                                        <span class="badge-profile badge-estadia">Estadía</span>
                                    @elseif($esAmbos)
                                        <span class="badge-profile badge-ambos">Ambos</span>
                                    @elseif($esColaborador)
                                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: flex-start;">
                                            <span class="badge-profile badge-colaborador">Colaborador</span>
                                            <small style="background: #f4ecf7; color: #7d3c98; padding: 2px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; white-space: normal; max-width: 210px; text-transform: uppercase;">
                                                👤 {{ $rawDestinado }}
                                            </small>
                                        </div>
                                    @else
                                        <span class="badge-profile badge-null">Sin Asignar</span>
                                    @endif
                                </td>
                                <td style="text-align: center;">
                                    <span class="order-cell">{{ $page->order }}</span>
                                </td>
                                <td class="actions-cell" style="text-align: right; white-space: nowrap;">
                                    <a href="{{ route('pages.edit', $page->id) }}" class="btn btn-edit">Editar</a>
                                    <a href="{{ route('pages.show', $page->slug) }}" class="btn btn-view">Ver</a>
                                    <form action="{{ route('pages.destroy', $page->id) }}" method="POST" style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger" onclick="return confirm('¿Eliminar página?')">X</button>
                                    </form>
                                </td>
                            </tr>

                            @if($page->questions && $page->questions->count() > 0)
                                <tr class="questions-row">
                                    <td colspan="5">
                                        <ul class="questions-list">
                                            @foreach($page->questions as $question)
                                                <li class="question-item">
                                                    <span style="color: #333;">
                                                        <i class="fas fa-question-circle" style="color: #3498db; margin-right: 5px;"></i> 
                                                        {{ $question->question_text }} 
                                                        <small style="color: #777;">({{ $question->question_type }})</small>
                                                    </span>
                                                    <div>
                                                        <a href="{{ route('questions.edit', $question->id) }}" class="btn btn-edit" style="padding: 4px 8px; font-size: 11px;">Editar</a>
                                                        <form action="{{ route('questions.destroy', $question->id) }}" method="POST" style="display:inline;">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-danger" style="padding: 4px 8px; font-size: 11px;" onclick="return confirm('¿Eliminar pregunta?')">Eliminar</button>
                                                        </form>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

    <script>
        // Funciones nativas de control de interfaz
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        function toggleGearMenu(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('gearDropdown');
            if (dropdown) dropdown.classList.toggle('show');
        }

        window.addEventListener('click', function(event) {
            if (!event.target.closest('.sidebar-user')) {
                const dropdown = document.getElementById('gearDropdown');
                if (dropdown) dropdown.classList.remove('show');
            }
            if (!event.target.closest('#sidebar') && !event.target.closest('.menu-toggle') && !event.target.closest('.sidebar-overlay')) {
                const sidebar = document.getElementById('sidebar');
                if (sidebar) sidebar.classList.remove('active');
            }
        });

        // Lógica interactiva con jQuery
        $(function() {
            function cleanText(str) {
                if (!str) return '';
                return str.toLowerCase()
                        .normalize("NFD")
                        .replace(/[\u0300-\u036f]/g, "")
                        .trim();
            }

            // Lógica de Filtros Principales
            $('.filter-btn').on('click', function() {
                $('.filter-btn').removeClass('active');
                $(this).addClass('active');

                let targetFilter = cleanText($(this).data('filter') || $(this).attr('data-filter'));

                if (targetFilter === "colaborador") {
                    $('#sub-filter-colaboradores').slideDown(250);
                    $('.sub-filter-btn').css({'background': '#fff', 'color': '#000'});
                    $('.sub-filter-btn[data-puesto="all"]').css({'background': '#34495e', 'color': '#fff'});
                } else {
                    $('#sub-filter-colaboradores').slideUp(200);
                }

                if (targetFilter === "all" || targetFilter === "todos") {
                    $('.page-block').show();
                } else {
                    $('.page-block').each(function() {
                        let destinadosRaw = cleanText($(this).attr('data-destinado') || $(this).data('destinado') || '');
                        // Separar los puestos asignados a la página por comas
                        let listaDestinados = destinadosRaw.split(',').map(item => cleanText(item));

                        if (listaDestinados.includes(targetFilter) || listaDestinados.includes('todos') || listaDestinados.includes('ambos')) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
            });

            // Lógica del Submenú (Tipos de Puestos de Colaborador)
            $('.sub-filter-btn').on('click', function() {
                $('.sub-filter-btn').css({'background': '#fff', 'color': '#000'});
                $(this).css({'background': '#34495e', 'color': '#fff'});

                let puestoBuscado = cleanText($(this).attr('data-puesto') || $(this).data('puesto'));

                if (puestoBuscado === "all" || puestoBuscado === "todos") {
                    $('.page-block').hide();
                    $('.page-block').each(function() {
                        let destinadosRaw = cleanText($(this).attr('data-destinado') || $(this).data('destinado') || '');
                        let listaDestinados = destinadosRaw.split(',').map(item => cleanText(item));

                        if (listaDestinados.includes('colaborador') || listaDestinados.includes('ambos') || listaDestinados.includes('todos')) {
                            $(this).show();
                        }
                    });
                } else {
                    $('.page-block').each(function() {
                        // Leer de data-puesto y de data-destinado por seguridad
                        let puestosRaw = cleanText($(this).attr('data-puesto') || $(this).data('puesto') || $(this).attr('data-destinado') || '');
                        let listaPuestos = puestosRaw.split(',').map(item => cleanText(item));

                        if (listaPuestos.includes(puestoBuscado) || listaPuestos.includes('todos') || listaPuestos.includes('ambos')) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                }
            });

            // Lógica de Ordenamiento (Sortable UI)
            var fixHelper = function(e, ui) {
                ui.children().each(function() {
                    $(this).children().each(function() {
                        $(this).width($(this).width());
                    });
                });
                return ui;
            };

            if ($(window).width() > 600) {
                $("#pagesTable").sortable({
                    items: ".page-block", 
                    handle: ".page-row",  
                    cursor: "move",
                    opacity: 0.8,
                    helper: fixHelper,
                    placeholder: "ui-sortable-placeholder",
                    start: function(event, ui) {
                        let colCount = $("#pagesTable thead th").length;
                        ui.placeholder.html('<tr class="page-row"><td colspan="' + colCount + '">&nbsp;</td></tr>');
                    },
                    update: function(event, ui) {
                        let sortedIDs = [];
                        $(".page-block").each(function() {
                            sortedIDs.push($(this).data("id"));
                        });

                        $.ajax({
                            url: "{{ route('pages.reorder') }}", 
                            method: "POST",
                            data: {
                                _token: "{{ csrf_token() }}",
                                ids: sortedIDs
                            },
                            success: function(response) {
                                $(".page-block").each(function(index) {
                                    let cell = $(this).find(".order-cell");
                                    cell.text(index + 1);
                                    cell.css({"background": "#27ae60", "color": "#fff"});
                                    setTimeout(() => {
                                        cell.css({"background": "#e9ecef", "color": "#212529"});
                                    }, 900);
                                });
                            },
                            error: function(xhr) {
                                $(".page-block").find(".order-cell").css({"background": "#c0392b", "color": "#fff"});
                                alert("No se pudo guardar el nuevo orden. Por favor, vuelve a intentarlo.");
                            }
                        });
                    }
                });
            }
        });
    </script>
</body>
</html>