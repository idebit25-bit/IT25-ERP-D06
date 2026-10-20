<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Página</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Archivo+Black&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.ckeditor.com/4.18.0/standard/ckeditor.js"></script>

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
            justify-content: center; 
            align-items: flex-start;
            margin-left: 260px;
            transition: margin-left 0.3s ease;
            box-sizing: border-box;
        }

        .card { 
            background: #c4c4c4; 
            padding: 25px; 
            border-radius: 15px; 
            width: 100%; 
            max-width: 1200px; 
            box-sizing: border-box;
        }

        h1 { 
            font-family: 'Archivo Black', sans-serif; 
            font-size: 26px; 
            margin-top: 0; 
            margin-bottom: 20px; 
            color: #222; 
        }

        /* --- ESTILOS FORMULARIO --- */
        .form-inner-container {
            max-width: 600px;
            margin: 0 auto;
            text-align: left;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #222;
        }

        input[type="text"], 
        input[type="file"],
        input[type="number"],
        select,
        textarea {
            width: 100%;
            padding: 12px;
            margin-top: 5px;
            border: 1px solid #000;
            border-radius: 5px;
            background-color: #ddd;
            font-size: 16px;
            box-sizing: border-box;
        }

        .current-file-box {
            background: #dcdcdc;
            padding: 10px;
            border-radius: 5px;
            border: 1px dashed #666;
            margin-top: 10px;
            font-size: 13px;
        }

        /* Estilos del bloque de selección múltiple */
        .multiple-selection-box {
            background-color: #cfcfcf;
            border: 1px solid #888;
            border-radius: 5px;
            padding: 12px;
            margin-top: 10px;
            max-height: 250px;
            overflow-y: auto;
        }

        .group-title {
            font-weight: bold;
            font-size: 13px;
            color: #333;
            margin-top: 10px;
            margin-bottom: 6px;
            text-transform: uppercase;
            border-bottom: 1px solid #a0a0a0;
            padding-bottom: 3px;
        }

        .checkbox-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
            font-weight: normal;
            cursor: pointer;
        }

        .checkbox-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        /* CAJA MOSTRADORA DE TAMAÑO DE VIDEO */
        .file-size-info {
            display: none;
            margin-top: 10px;
            padding: 12px 15px;
            background-color: #e6f7f0;
            border: 1px solid #a3e0c4;
            border-radius: 8px;
            color: #0d5c3a;
            font-weight: bold;
            font-size: 15px;
            align-items: center;
            gap: 10px;
        }

        .file-size-info.error {
            background-color: #fde8e8;
            border-color: #f8b4b4;
            color: #9b1c1c;
        }

        /* BARRA DE PROGRESO DE CARGA */
        .progress-box {
            display: none;
            margin-top: 12px;
            background: #2a2a2a;
            padding: 12px;
            border-radius: 6px;
            border: 1px solid #000;
        }
        .progress-header {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #fff;
            margin-bottom: 6px;
            font-weight: bold;
        }
        .progress-track {
            width: 100%;
            background-color: #444;
            border-radius: 4px;
            overflow: hidden;
            height: 16px;
        }
        .progress-bar-fill {
            width: 0%;
            height: 100%;
            background-color: #27ae60;
            transition: width 0.2s ease;
        }

        .btn-update {
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
            transition: 0.3s;
        }
        .btn-update:hover { background: #333; transform: scale(1.02); }

        .link-back {
            margin-top: 20px;
            display: inline-block;
            font-family: 'Archivo Black', sans-serif;
            color: #000;
            text-decoration: none;
        }

        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .menu-toggle { display: flex; }
            .report-container { margin-left: 0; padding-top: 80px; }
        }
    </style>
</head>
<body>

    @extends('layouts.admin')

    @section('title', 'Editar Página')

    @section('content')
    <h1>Editar Página</h1>

    <div class="form-inner-container">
        <form id="form-edit-page" action="{{ route('pages.update', $page->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            @php
                $destinos = is_array($page->destinado_a) ? $page->destinado_a : json_decode($page->destinado_a, true) ?? [$page->destinado_a];
            @endphp

            <label for="title">Título de la Página:</label>
            <input type="text" id="title" name="title" value="{{ old('title', $page->title) }}" required>

            <label for="slug">Identificador URL (Slug):</label>
            <input type="text" id="slug" name="slug" value="{{ old('slug', $page->slug) }}" required>

            <label>A quién va dirigida la página:</label>
            <div class="multiple-selection-box">
                <div class="group-title">Generales</div>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Ambos" {{ in_array('Ambos', $destinos) ? 'checked' : '' }}> Todos los usuarios / Ambos</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Colaborador" {{ in_array('Colaborador', $destinos) ? 'checked' : '' }}> Todos los Colaboradores</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Estadía" {{ in_array('Estadía', $destinos) ? 'checked' : '' }}> Personal en Estadía</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Administrativo" {{ in_array('Administrativo', $destinos) ? 'checked' : '' }}> Toda el área Administrativa</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Operativo" {{ in_array('Operativo', $destinos) ? 'checked' : '' }}> Toda el área Operativa</label>

                <div class="group-title">Área Administrativo</div>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Capital Humano" {{ in_array('Capital Humano', $destinos) ? 'checked' : '' }}> Capital Humano</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Gestión de Pedidos" {{ in_array('Gestión de Pedidos', $destinos) ? 'checked' : '' }}> Gestión de Pedidos</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Ingeniero de Recursos Materiales" {{ in_array('Ingeniero de Recursos Materiales', $destinos) ? 'checked' : '' }}> Ingeniero de Recursos Materiales</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Coordinadora General" {{ in_array('Coordinadora General', $destinos) ? 'checked' : '' }}> Coordinadora General</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Gerente General" {{ in_array('Gerente General', $destinos) ? 'checked' : '' }}> Gerente General</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Auxiliar Administrativo" {{ in_array('Auxiliar Administrativo', $destinos) ? 'checked' : '' }}> Auxiliar Administrativo</label>

                <div class="group-title">Área Operativo</div>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Coordinador Técnico" {{ in_array('Coordinador Técnico', $destinos) ? 'checked' : '' }}> Coordinador Técnico</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Ingeniero de Control y Soporte Técnico" {{ in_array('Ingeniero de Control y Soporte Técnico', $destinos) ? 'checked' : '' }}> Ingeniero de Control y Soporte Técnico</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Ingeniería y Proyectos" {{ in_array('Ingeniería y Proyectos', $destinos) ? 'checked' : '' }}> Ingeniería y Proyectos</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Ingeniero Junior" {{ in_array('Ingeniero Junior', $destinos) ? 'checked' : '' }}> Ingeniero Junior</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Ingeniero en Mantenimiento y Recursos Materiales" {{ in_array('Ingeniero en Mantenimiento y Recursos Materiales', $destinos) ? 'checked' : '' }}> Ingeniero en Mantenimiento y Recursos Materiales</label>
            </div>

            <label for="order">Número de Orden / Posición:</label>
            <input type="number" id="order" name="order" value="{{ old('order', $page->order) }}" min="1" required>

            <label for="video_url">Enlace de YouTube:</label>
            <input type="text" id="video_url" name="video_url" value="{{ old('video_url', $page->video_url) }}" placeholder="https://www.youtube.com/watch?v=...">

            <label for="video_file">Reemplazar/Subir Video Local (MP4, WebM, MOV):</label>
            
            <!-- LEYENDA TAMAÑO MÁXIMO PERMITIDO -->
            <small style="display: block; color: #333; margin-top: 3px; font-weight: bold;">
                <i class="fas fa-info-circle"></i> Tamaños pesados demoran más en subir. Máximo recomendado: <strong>128 MB</strong>.
            </small>

            <input type="file" id="video_file" name="video_file" accept="video/mp4,video/webm,video/quicktime,video/avi,video/x-msvideo">

            <!-- CAJA VERDE PARA MOSTRAR EL TAMAÑO DEL VIDEO SELECCIONADO -->
            <div id="file-size-info" class="file-size-info">
                <i class="fas fa-video"></i>
                <span id="file-size-text">Tamaño seleccionado: 0 MB / 128 MB permitidos.</span>
            </div>

            @if($page->video_file)
            <div class="current-file-box" style="margin-top: 5px; margin-bottom: 15px;">
                <strong>🎥 Video Local Actual:</strong> 
                <span style="word-break: break-all;">{{ basename($page->video_file) }}</span>
            </div>
            @endif

            <!-- CONTENEDOR BARRA DE PROGRESO -->
            <div id="progress-container" class="progress-box">
                <div class="progress-header">
                    <span id="progress-status">Subiendo video...</span>
                    <span id="progress-percent">0%</span>
                </div>
                <div class="progress-track">
                    <div id="progress-bar" class="progress-bar-fill"></div>
                </div>
            </div>

            <label for="content">Contenido / Descripción:</label>
            <textarea name="content" id="content" rows="6">{{ old('content', $page->content) }}</textarea>

            <label for="attachment">Reemplazar Archivo Adjunto (opcional):</label>
            <input type="file" id="attachment" name="attachment">
            
            @if($page->attachment)
            <div class="current-file-box" style="margin-top: 5px; margin-bottom: 15px;">
                <strong>📁 Archivo actual:</strong> 
                <span style="word-break: break-all;">{{ basename($page->attachment) }}</span>
            </div>
            @endif

            <button type="submit" id="btn-update" class="btn-update">GUARDAR CAMBIOS</button>
        </form>
        
        <div style="text-align: center;">
            <a href="{{ route('pages.index') }}" class="link-back">← Volver al listado</a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar CKEditor en el textarea de contenido
            if (document.getElementById('content')) {
                CKEDITOR.replace('content');
            }

            // JS PARA SUBIDA CON BARRA DE PROGRESO Y MUESTRA DE TAMAÑO EN EDICIÓN
            const form = document.getElementById('form-edit-page');
            const videoInput = document.getElementById('video_file');
            const fileSizeInfo = document.getElementById('file-size-info');
            const fileSizeText = document.getElementById('file-size-text');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressPercent = document.getElementById('progress-percent');
            const progressStatus = document.getElementById('progress-status');
            const updateBtn = document.getElementById('btn-update');

            const maxSizeBytes = 128 * 1024 * 1024; // 128 MB

            // EVENTO CHANGE PARA MOSTRAR EL TAMAÑO APENAS SE SELECCIONA EL ARCHIVO
            if (videoInput) {
                videoInput.addEventListener('change', function() {
                    if (this.files.length > 0) {
                        const file = this.files[0];
                        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(2);

                        fileSizeText.innerText = `Tamaño seleccionado: ${fileSizeMB} MB / 128 MB permitidos.`;
                        fileSizeInfo.style.display = 'flex';

                        if (file.size > maxSizeBytes) {
                            fileSizeInfo.classList.add('error');
                            alert('El archivo supera el tamaño máximo permitido (128 MB).');
                        } else {
                            fileSizeInfo.classList.remove('error');
                        }
                    } else {
                        fileSizeInfo.style.display = 'none';
                    }
                });
            }

            if (form) {
                form.addEventListener('submit', function(e) {
                    // Sincronizar el contenido HTML escrito en CKEditor con la textarea antes de enviar
                    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.content) {
                        CKEDITOR.instances.content.updateElement();
                    }

                    // Se activa la barra si se seleccionó un archivo nuevo en el campo de video
                    if (videoInput && videoInput.files.length > 0) {
                        e.preventDefault();

                        const file = videoInput.files[0];

                        if (file.size > maxSizeBytes) {
                            alert('El archivo supera el tamaño máximo permitido (128 MB).');
                            return;
                        }

                        const formData = new FormData(form);
                        const xhr = new XMLHttpRequest();

                        progressContainer.style.display = 'block';
                        updateBtn.disabled = true;
                        updateBtn.innerText = 'Subiendo video...';

                        xhr.upload.addEventListener('progress', function(event) {
                            if (event.lengthComputable) {
                                const percent = Math.round((event.loaded / event.total) * 100);
                                progressBar.style.width = percent + '%';
                                progressPercent.innerText = percent + '%';

                                if (percent === 100) {
                                    progressStatus.innerText = 'Procesando cambios en el servidor...';
                                }
                            }
                        });

                        xhr.addEventListener('load', function() {
                            if (xhr.status >= 200 && xhr.status < 400) {
                                if (xhr.responseURL) {
                                    window.location.href = xhr.responseURL;
                                } else {
                                    window.location.reload();
                                }
                            } else {
                                alert('Error al actualizar en el servidor. Código: ' + xhr.status);
                                updateBtn.disabled = false;
                                updateBtn.innerText = 'GUARDAR CAMBIOS';
                                progressContainer.style.display = 'none';
                            }
                        });

                        xhr.addEventListener('error', function() {
                            alert('Ocurrió un problema con la conexión.');
                            updateBtn.disabled = false;
                            updateBtn.innerText = 'GUARDAR CAMBIOS';
                            progressContainer.style.display = 'none';
                        });

                        xhr.open(form.method, form.action, true);
                        
                        const csrfToken = document.querySelector('input[name="_token"]');
                        if (csrfToken) {
                            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.value);
                        }

                        xhr.send(formData);
                    }
                });
            }
        });
    </script>
    @endsection
</body>
</html>