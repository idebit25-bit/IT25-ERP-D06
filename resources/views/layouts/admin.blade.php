<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Administración')</title>
    <script src="https://cdn.ckeditor.com/4.22.1/standard/ckeditor.js"></script>
    <style>
        .hidden-field {
            display: none;
            opacity: 0;
            transition: opacity 0.3s ease;
            margin-top: 15px;
            margin-bottom: 15px;
        }
        .hidden-field.show {
            display: block;
            opacity: 1;
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
            @if($errors->any())
                <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: left;">
                    <ul style="margin: 0; padding-left: 20px;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script>
        if (document.getElementById('content')) {
            CKEDITOR.replace('content', {
                width: '100%',
                height: 250
            });
        }

        function convertToSlug(text) {
            return text
                .toLowerCase()
                .normalize("NFD").replace(/[\u0300-\u036f]/g, "")
                .replace(/[^a-z0-9 -]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-');
        }

        const titleInput = document.getElementById('title');
        const slugInput = document.getElementById('slug');

        if (slugInput) {
            slugInput.addEventListener('input', function() {
                this.value = convertToSlug(this.value);
            });
        }

        @yield('slug_script')

        // --- LÓGICA DE FILTRADO EN CASCADA PARA COLABORADORES ---
        const destinadoSelect = document.getElementById('destinado_a');
        const contenedorArea = document.getElementById('contenedor_area_colaborador');
        const areaSelect = document.getElementById('area_colaborador');
        const contenedorPuesto = document.getElementById('contenedor_puesto_colaborador');
        const puestoSelect = document.getElementById('tipo_colaborador');

        // Mapeo estructurado de tus puestos de trabajo
        const puestosPorArea = {
            'Administrativo': [
                'Capital Humano',
                'Gestión de pedidos',
                'Ingeniero de recursos materiales',
                'Coordinadora General',
                'Gerente General',
                'Auxiliar administrativo'
            ],
            'Operativo': [
                'Coordinador técnico',
                'Ingeniero de control y Soporte técnico',
                'Ingeniería y proyectos',
                'Ingeniero Junior',
                'Ingeniero en mantenimiento y recurso materiales'
            ]
        };

        function gestionarCascadaColaboradores() {
            if (!destinadoSelect) return;

            // Paso 1: ¿Es Colaborador?
            if (destinadoSelect.value === 'Colaborador') {
                contenedorArea.classList.add('show');
                if (areaSelect) areaSelect.required = true;
                
                // Paso 2: Si ya hay un área seleccionada, cargar sus puestos
                gestionarPuestos();
            } else {
                // Resetear y ocultar todo si no es Colaborador
                contenedorArea.classList.remove('show');
                contenedorPuesto.classList.remove('show');
                if (areaSelect) { areaSelect.required = false; areaSelect.value = ''; }
                if (puestoSelect) { puestoSelect.required = false; puestoSelect.value = ''; }
            }
        }

        function gestionarPuestos() {
            if (!areaSelect || !puestoSelect) return;

            const areaSeleccionada = areaSelect.value;
            const valorActualPuesto = puestoSelect.getAttribute('data-selected') || puestoSelect.value;

            // Limpiar opciones anteriores manteniendo la por defecto
            puestoSelect.innerHTML = '<option value="">-- Selecciona el puesto específico --</option>';

            if (areaSeleccionada && puestosPorArea[areaSeleccionada]) {
                contenedorPuesto.classList.add('show');
                puestoSelect.required = true;

                // Inyectar los puestos correspondientes al área elegida
                puestosPorArea[areaSeleccionada].forEach(puesto => {
                    const option = document.createElement('option');
                    option.value = puesto;
                    option.textContent = puesto;
                    if (puesto === valorActualPuesto) {
                        option.selected = true;
                    }
                    puestoSelect.appendChild(option);
                });
            } else {
                contenedorPuesto.classList.remove('show');
                puestoSelect.required = false;
                puestoSelect.value = '';
            }
        }

        if (destinadoSelect) {
            destinadoSelect.addEventListener('change', gestionarCascadaColaboradores);
            if (areaSelect) areaSelect.addEventListener('change', gestionarPuestos);
            
            // Re-evaluar al cargar por si regresan datos viejos o de base de datos
            window.addEventListener('DOMContentLoaded', () => {
                gestionarCascadaColaboradores();
            });
        }

        // Sidebar y menús básicos
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('active');
        }

        function toggleGearMenu(event) {
            event.stopPropagation();
            const dropdown = document.getElementById('gearDropdown');
            dropdown.style.display = (dropdown.style.display === 'block') ? 'none' : 'block';
        }

        window.addEventListener('click', function(event) {
            if (!event.target.closest('.sidebar-user')) {
                const dropdown = document.getElementById('gearDropdown');
                if (dropdown) dropdown.style.display = 'none';
            }
            if (!event.target.closest('#sidebar') && !event.target.closest('.menu-toggle')) {
                const sidebar = document.getElementById('sidebar');
                if (sidebar) sidebar.classList.remove('active');
            }
        });
    </script>
</body>
</html>