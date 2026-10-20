<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Inducción I-DEB</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-body: #1e1e1e;
            --card-black: #121212;
            --card-inner: #1a1a1a;
            --text-light: #ffffff;
            --text-muted: #a0a0a0;
            --border-gray: #262626;
            --border-light: #333333;
            --accent-green: #10b981;
            --accent-amber: #f59e0b;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background-color: var(--bg-body);
            color: var(--text-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 30px 20px;
        }

        .dashboard-container {
            max-width: 1000px;
            width: 100%;
            display: flex;
            flex-direction: column;
            gap: 24px;
        }

        /* Top Navbar */
        .top-navbar {
            background-color: var(--card-black);
            border: 1px solid var(--border-gray);
            border-radius: 16px;
            padding: 18px 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand-logo img {
            max-width: 140px;
            height: auto;
            object-fit: contain;
        }

        .logout-btn {
            color: var(--text-muted);
            text-decoration: none;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 8px;
            border: 1px solid var(--border-gray);
            background-color: var(--card-inner);
            transition: all 0.2s ease;
        }

        .logout-btn:hover {
            color: #ffffff;
            border-color: var(--border-light);
            background-color: #222222;
        }

        /* Profile Banner Card */
        .profile-card {
            background-color: var(--card-black);
            border: 1px solid var(--border-gray);
            border-radius: 20px;
            padding: 32px;
            display: flex;
            align-items: center;
            gap: 24px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.4);
        }

        .avatar-box {
            width: 80px;
            height: 80px;
            background: #1f1f1f;
            border: 2px solid var(--border-light);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #ffffff;
            flex-shrink: 0;
        }

        .profile-details { flex-grow: 1; }

        .tags-wrapper {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-bottom: 8px;
            flex-wrap: wrap;
        }

        .tag-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 6px;
        }

        .tag-main { background-color: #ffffff; color: #000000; }
        .tag-sub { background-color: #262626; color: var(--text-light); border: 1px solid var(--border-light); }

        .user-name {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 4px;
            letter-spacing: -0.5px;
        }

        .user-email {
            color: var(--text-muted);
            font-size: 14px;
        }

        /* Dashboard Grid Layout */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 24px;
        }

        @media (max-width: 850px) {
            .dashboard-grid { grid-template-columns: 1fr; }
            .profile-card { flex-direction: column; text-align: center; }
            .tags-wrapper { justify-content: center; }
        }

        .dashboard-card {
            background-color: var(--card-black);
            border: 1px solid var(--border-gray);
            border-radius: 20px;
            padding: 28px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .card-header-title {
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-header-title i { color: var(--text-muted); }

        .card-description {
            font-size: 14px;
            color: var(--text-muted);
            line-height: 1.6;
            margin-bottom: 20px;
        }

        /* Features List */
        .features-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            background-color: var(--card-inner);
            border: 1px solid var(--border-gray);
            padding: 18px;
            border-radius: 12px;
            margin-bottom: 24px;
        }

        .feature-item {
            font-size: 13.5px;
            color: #e5e5e5;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .feature-item i { color: var(--accent-green); font-size: 12px; }

        /* Widgets & Progress Bar */
        .info-widget {
            background-color: var(--card-inner);
            border: 1px solid var(--border-gray);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }

        .info-label {
            font-size: 12px;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-value {
            font-size: 16px;
            font-weight: 700;
            color: #ffffff;
        }

        .progress-bar-container {
            width: 100%;
            height: 10px;
            background-color: #262626;
            border-radius: 20px;
            overflow: hidden;
            margin-top: 8px;
            border: 1px solid var(--border-light);
        }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #2563eb, #10b981);
            border-radius: 20px;
            transition: width 0.4s ease;
        }

        /* Action Button */
        .action-button {
            background-color: #ffffff;
            color: #000000;
            font-size: 14px;
            font-weight: 700;
            padding: 16px 28px;
            border: 1px solid #ffffff;
            border-radius: 10px;
            cursor: pointer;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.25s ease;
            text-decoration: none;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .action-button:hover {
            background-color: #000000;
            color: #ffffff;
            border-color: var(--border-light);
        }
    </style>
</head>
<body>

    <div class="dashboard-container">
        
        <!-- Top Navbar -->
        <header class="top-navbar">
            <div class="brand-logo">
                <img src="{{ asset('images/I-DEBLogo.jpg') }}" alt="Logo Corporativo">
            </div>
            <div>
                <a href="#" class="logout-btn" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </header>

        <!-- Profile Card -->
        <section class="profile-card">
            <div class="avatar-box">
                @if($esEstadia)
                    <i class="fas fa-graduation-cap"></i>
                @else
                    <i class="fas fa-user-tie"></i>
                @endif
            </div>

            <div class="profile-details">
                <div class="tags-wrapper">
                    @if($esEstadia)
                        <span class="tag-badge tag-main"><i class="fas fa-university"></i> Perfil: Estadía</span>
                    @else
                        <span class="tag-badge tag-main"><i class="fas fa-briefcase"></i> Perfil: Colaborador</span>
                        <span class="tag-badge tag-sub"><i class="fas fa-id-badge"></i> Puesto: {{ $user->puesto ?? $user->area ?? 'General' }}</span>
                    @endif
                </div>
                <h1 class="user-name">{{ $user->name }}</h1>
                <p class="user-email"><i class="far fa-envelope"></i> {{ $user->email }}</p>
            </div>
        </section>

        <!-- Main Grid -->
        <div class="dashboard-grid">
            
            <!-- Left Main Card -->
            <main class="dashboard-card">
                <div>
                    @if($esEstadia)
                        <div class="card-header-title">
                            <i class="fas fa-folder-open"></i> Módulos Escolares y Objetivos
                        </div>
                        <p class="card-description">
                            Bienvenido a tu panel de inducción para estadías. Revisa el contenido educativo, tus normas de estadía y evaluaciones asignadas.
                        </p>
                        
                        <div class="features-grid">
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Reglamento de Estadías</div>
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Fechas de Entregables</div>
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Estructura de Reportes</div>
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Contacto con Asesores</div>
                        </div>
                    @else
                        <div class="card-header-title">
                            <i class="fas fa-shield-alt"></i> Módulos Corporativos
                        </div>
                        <p class="card-description">
                            Bienvenido a tu panel de capacitación inicial. A través de este módulo conocerás la cultura organizacional, políticas internas y tus flujos de trabajo.
                        </p>
                        
                        <div class="features-grid">
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Filosofía y Cultura</div>
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Código de Ética</div>
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Prestaciones y Beneficios</div>
                            <div class="feature-item"><i class="fas fa-check-circle"></i> Seguridad y Operaciones</div>
                        </div>
                    @endif
                </div>

                <!-- BOTÓN DINÁMICO SEGÚN PROGRESO CALCULADO -->
                @php
                    $progresoVal = (int) ($progreso ?? 0);
                @endphp

                @if($progresoVal === 0)
                    <a href="{{ route('induction.start') }}" class="action-button">
                        Iniciar Inducción <i class="fas fa-play" style="margin-left: 8px;"></i>
                    </a>
                @elseif($progresoVal > 0 && $progresoVal < 100)
                    <a href="{{ route('induction.start') }}" class="action-button">
                        Continuar Inducción <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
                    </a>
                @else
                    <a href="{{ route('induction.start') }}" class="action-button">
                        Repasar Inducción <i class="fas fa-redo" style="margin-left: 8px;"></i>
                    </a>
                @endif
            </main>

            <!-- Right Sidebar Status Card -->
            <aside class="dashboard-card">
                <div>
                    <div class="card-header-title">
                        <i class="fas fa-chart-line"></i> Progreso e Información
                    </div>

                    <!-- BARRA DE PROGRESO -->
                    <div class="info-widget">
                        <div class="info-label">
                            <span>Avance de Capacitación</span>
                            <strong style="color: #ffffff;">{{ $progresoVal }}%</strong>
                        </div>
                        <div class="progress-bar-container">
                            <div class="progress-bar-fill" style="width: {{ $progresoVal }}%;"></div>
                        </div>
                    </div>

                    <!-- ESTADO DEL PROGRESO -->
                    <div class="info-widget">
                        <div class="info-label">Estado de Avance</div>
                        <div class="info-value">
                            @if($progresoVal === 0)
                                <span style="color: var(--text-muted, #9ca3af);"><i class="fas fa-clock"></i> Sin Iniciar</span>
                            @elseif($progresoVal < 100)
                                <span style="color: var(--accent-amber, #f59e0b);"><i class="fas fa-spinner fa-spin"></i> En Progreso</span>
                            @else
                                <span style="color: var(--accent-green, #10b981);"><i class="fas fa-check-circle"></i> Completado</span>
                            @endif
                        </div>
                    </div>

                    @if(!$esEstadia)
                        <div class="info-widget">
                            <div class="info-label">Puesto / Área Asignada</div>
                            <div class="info-value" style="font-size: 14px;">
                                {{ $user->puesto ?? $user->area ?? 'General / Operaciones' }}
                            </div>
                        </div>
                    @endif
                </div>
            </aside>

        </div>

    </div>

</body>
</html>