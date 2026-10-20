<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Página</title>
    
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

        /* Estilos del bloque para seleccionar múltiples puestos */
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

        /* BADGE INFO TAMAÑO ARCHIVO */
        .file-info-badge {
            display: none;
            margin-top: 6px;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 13px;
            font-weight: bold;
        }
        .file-info-badge.ok {
            background-color: #e8f8f5;
            color: #117a65;
            border: 1px solid #a3e4d7;
        }
        .file-info-badge.error {
            background-color: #fadbd8;
            color: #78281f;
            border: 1px solid #f5b7b1;
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
            transition: 0.3s;
        }
        .btn-submit:hover { background: #333; transform: scale(1.02); }

        .btn-volver {
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

    @section('title', 'Crear Nueva Página')

    @section('content')
    <h1>Crear Nueva Página</h1>

    <div class="form-inner-container">
        <form id="form-create-page" action="{{ route('pages.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <label for="title">Título de la Página:</label>
            <input type="text" id="title" name="title" value="{{ old('title') }}" placeholder="Ej: Introducción a la Seguridad" required>

            <label for="slug">URL Amigable (Slug):</label>
            <input type="text" id="slug" name="slug" value="{{ old('slug') }}" placeholder="ej-introduccion-seguridad" required>

            <label for="order">Número de Paso / Orden (Opcional):</label>
            <input type="number" id="order" name="order" value="{{ old('order') }}" placeholder="Ej: 1 (Si se deja vacío, irá al final)" min="1">

            <!-- 1. PERFIL DESTINO -->
            <label for="perfil_destino">¿A qué perfil va dirigida esta página?:</label>
            <select id="perfil_destino" onchange="actualizarCamposFiltro()">
                <option value="Ambos">Ambos / Todos los Usuarios</option>
                <option value="Colaborador" selected>Solo Colaboradores (Contratados)</option>
                <option value="Estadía">Solo Personal en Estadía</option>
                <option value="Multiple">-- Asignar a Varios Puestos a la vez --</option>
            </select>

            <!-- 2. ÁREA DEL COLABORADOR -->
            <div id="wrapper_area">
                <label for="area_colaborador">Área del Colaborador:</label>
                <select id="area_colaborador" onchange="actualizarPuestosPorArea()">
                    <option value="Administrativo" selected>Administrativo</option>
                    <option value="Operativo">Operativo</option>
                </select>
            </div>

            <!-- 3. PUESTO / TIPO DE COLABORADOR (SELECCIÓN ÚNICA) -->
            <div id="wrapper_puesto_unico">
                <label for="destinado_a_single">Puesto / Tipo de Colaborador:</label>
                <select name="destinado_a[]" id="destinado_a_single">
                    <!-- Se llena dinámicamente según el Área seleccionada -->
                </select>
            </div>

            <!-- 4. SELECCIÓN MÚLTIPLE DE PUESTOS (OPCIONAL) -->
            <div id="wrapper_puestos_multiples" class="multiple-selection-box" style="display: none;">
                <span style="font-weight: bold; font-size: 14px; display: block; margin-bottom: 8px;">Marca todos los puestos/áreas a los que va dirigida:</span>
                
                <div class="group-title">Área Administrativo</div>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Capital Humano" class="chk-puesto"> Capital Humano</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Gestión de Pedidos" class="chk-puesto"> Gestión de Pedidos</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Ingeniero de Recursos Materiales" class="chk-puesto"> Ingeniero de Recursos Materiales</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Coordinadora General" class="chk-puesto"> Coordinadora General</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Gerente General" class="chk-puesto"> Gerente General</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Auxiliar Administrativo" class="chk-puesto"> Auxiliar Administrativo</label>

                <div class="group-title">Área Operativo</div>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Coordinador Técnico" class="chk-puesto"> Coordinador Técnico</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Ingeniero de Control y Soporte Técnico" class="chk-puesto"> Ingeniero de Control y Soporte Técnico</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Ingeniería y Proyectos" class="chk-puesto"> Ingeniería y Proyectos</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Ingeniero Junior" class="chk-puesto"> Ingeniero Junior</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Ingeniero en Mantenimiento y Recursos Materiales" class="chk-puesto"> Ingeniero en Mantenimiento y Recursos Materiales</label>

                <div class="group-title">Generales</div>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Administrativo" class="chk-puesto"> Toda el área Administrativa</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Operativo" class="chk-puesto"> Toda el área Operativa</label>
                <label class="checkbox-item"><input type="checkbox" name="destinado_a[]" value="Estadía" class="chk-puesto"> Personal en Estadía</label>
            </div>

            <label for="video_url">Video de YouTube (Link Opcional):</label>
            <input type="text" id="video_url" name="video_url" value="{{ old('video_url') }}" placeholder="https://youtube.com/...">

            <label for="video_file">O Subir Archivo de Video Local (MP4, WebM, MOV):</label>
            
            <!-- LEYENDA TAMAÑO MÁXIMO PERMITIDO -->
            <small style="display: block; color: #333; margin-top: 3px; font-weight: bold;">
                <i class="fas fa-info-circle"></i> Tamaños pesados demoran más en subir. Máximo recomendado: <strong>128 MB</strong>.
            </small>

            <input type="file" id="video_file" name="video_file" accept="video/mp4,video/webm,video/quicktime,video/avi,video/x-msvideo">

            <!-- ETIQUETA VISUAL DE TAMAÑO EN TIEMPO REAL -->
            <div id="video-size-info" class="file-info-badge"></div>

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

            <label for="content">Contenido Detallado:</label>
            <textarea name="content" id="content">{{ old('content') }}</textarea>

            <label for="attachment">Documento Adjunto (PDF, Imágenes, etc):</label>
            <input type="file" id="attachment" name="attachment">

            <button type="submit" id="btn-submit" class="btn-submit">GUARDAR PÁGINA</button>
        </form>
        
        <div style="text-align: center;">
            <a href="{{ route('pages.index') }}" class="btn-volver">← Volver al listado</a>
        </div>
    </div>

    <script>
        const puestosAdministrativos = [
            "Capital Humano",
            "Gestión de Pedidos",
            "Ingeniero de Recursos Materiales",
            "Coordinadora General",
            "Gerente General",
            "Auxiliar Administrativo"
        ];

        const puestosOperativos = [
            "Coordinador Técnico",
            "Ingeniero de Control y Soporte Técnico",
            "Ingeniería y Proyectos",
            "Ingeniero Junior",
            "Ingeniero en Mantenimiento y Recursos Materiales"
        ];

        function actualizarPuestosPorArea() {
            const area = document.getElementById('area_colaborador').value;
            const selectPuesto = document.getElementById('destinado_a_single');
            
            selectPuesto.innerHTML = '';

            let defaultOption = document.createElement('option');
            defaultOption.value = area; 
            defaultOption.text = '-- Selecciona el puesto específico --';
            selectPuesto.appendChild(defaultOption);

            let listaPuestos = (area === 'Operativo') ? puestosOperativos : puestosAdministrativos;

            listaPuestos.forEach(puesto => {
                let opt = document.createElement('option');
                opt.value = puesto;
                opt.text = puesto;
                selectPuesto.appendChild(opt);
            });
        }

        function actualizarCamposFiltro() {
            const perfil = document.getElementById('perfil_destino').value;
            const wrapArea = document.getElementById('wrapper_area');
            const wrapPuestoUnico = document.getElementById('wrapper_puesto_unico');
            const wrapPuestosMultiples = document.getElementById('wrapper_puestos_multiples');
            
            const selectSingle = document.getElementById('destinado_a_single');
            const chkPuestos = document.querySelectorAll('.chk-puesto');

            if (perfil === 'Colaborador') {
                wrapArea.style.display = 'block';
                wrapPuestoUnico.style.display = 'block';
                wrapPuestosMultiples.style.display = 'none';

                selectSingle.disabled = false;
                chkPuestos.forEach(chk => { chk.disabled = true; chk.checked = false; });
                actualizarPuestosPorArea();
            } else if (perfil === 'Multiple') {
                wrapArea.style.display = 'none';
                wrapPuestoUnico.style.display = 'none';
                wrapPuestosMultiples.style.display = 'block';

                selectSingle.disabled = true;
                chkPuestos.forEach(chk => chk.disabled = false);
            } else {
                wrapArea.style.display = 'none';
                wrapPuestoUnico.style.display = 'block';
                wrapPuestosMultiples.style.display = 'none';

                selectSingle.disabled = false;
                selectSingle.innerHTML = `<option value="${perfil}">${perfil}</option>`;
                chkPuestos.forEach(chk => { chk.disabled = true; chk.checked = false; });
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            actualizarCamposFiltro();

            const form = document.getElementById('form-create-page');
            const videoInput = document.getElementById('video_file');
            const sizeInfo = document.getElementById('video-size-info');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressPercent = document.getElementById('progress-percent');
            const progressStatus = document.getElementById('progress-status');
            const submitBtn = document.getElementById('btn-submit');

            const MAX_SIZE_MB = 128;
            const MAX_SIZE_BYTES = MAX_SIZE_MB * 1024 * 1024;

            // Función para formatear los bytes a MB o KB
            function formatBytes(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }

            // Detección del tamaño en tiempo real al seleccionar un archivo
            if (videoInput) {
                videoInput.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const file = this.files[0];
                        const formattedSize = formatBytes(file.size);

                        sizeInfo.style.display = 'block';

                        if (file.size > MAX_SIZE_BYTES) {
                            sizeInfo.className = 'file-info-badge error';
                            sizeInfo.innerHTML = `⚠️ <strong>Archivo demasiado pesado:</strong> ${formattedSize} (El límite permitido es de ${MAX_SIZE_MB} MB).`;
                        } else {
                            sizeInfo.className = 'file-info-badge ok';
                            sizeInfo.innerHTML = `📹 <strong>Tamaño seleccionado:</strong> ${formattedSize} / ${MAX_SIZE_MB} MB permitidos.`;
                        }
                    } else {
                        sizeInfo.style.display = 'none';
                    }
                });
            }

            // Lógica de envío AJAX y barra de progreso
            if (form) {
                form.addEventListener('submit', function(e) {
                    if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.content) {
                        CKEDITOR.instances.content.updateElement();
                    }

                    if (videoInput && videoInput.files.length > 0) {
                        const file = videoInput.files[0];

                        if (file.size > MAX_SIZE_BYTES) {
                            e.preventDefault();
                            alert(`El archivo que intentas subir (${formatBytes(file.size)}) supera el límite máximo permitido de ${MAX_SIZE_MB} MB.`);
                            return;
                        }

                        e.preventDefault();

                        const formData = new FormData(form);
                        const xhr = new XMLHttpRequest();

                        progressContainer.style.display = 'block';
                        submitBtn.disabled = true;
                        submitBtn.innerText = 'Subiendo video...';

                        xhr.upload.addEventListener('progress', function(event) {
                            if (event.lengthComputable) {
                                const percent = Math.round((event.loaded / event.total) * 100);
                                progressBar.style.width = percent + '%';
                                progressPercent.innerText = percent + '%';

                                if (percent === 100) {
                                    progressStatus.innerText = 'Procesando en el servidor...';
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
                                alert('Error al guardar en el servidor. Código: ' + xhr.status);
                                submitBtn.disabled = false;
                                submitBtn.innerText = 'GUARDAR PÁGINA';
                                progressContainer.style.display = 'none';
                            }
                        });

                        xhr.addEventListener('error', function() {
                            alert('Ocurrió un problema con la conexión al subir el archivo.');
                            submitBtn.disabled = false;
                            submitBtn.innerText = 'GUARDAR PÁGINA';
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

    @section('slug_script')
        if (titleInput && slugInput) {
            titleInput.addEventListener('input', function() {
                if (slugInput.dataset.edited !== 'true') {
                    slugInput.value = convertToSlug(this.value);
                }
            });

            slugInput.addEventListener('change', function() {
                this.dataset.edited = 'true';
            });
        }
    @endsection
</body>
</html>