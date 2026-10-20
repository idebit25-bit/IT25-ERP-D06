# Recuento del proyecto — 19 de septiembre de 2026

Este documento describe el estado de **esta copia local** de I-DEB Inducción a la fecha indicada. El inventario de archivos de código se contrastó con el estado registrado en la carpeta del proyecto; no se hizo ningún commit ni se modificó un repositorio. Los archivos generados durante la ejecución cambian de nombre y cantidad con cada prueba, por lo que se identifican por carpeta. No se incluyen contraseñas ni datos personales del respaldo.

## Implementaciones y funcionamiento

| Área | Qué se implementó y para qué sirve | Situación local |
|---|---|---|
| Acceso administrativo | Las rutas de gestión exigen sesión con rol `admin`; los participantes conservan acceso a su inducción. | Probado con cuentas del respaldo. |
| Asignación y avance | Una regla compartida determina las páginas visibles según tipo de usuario, área o puesto. El avance usa páginas completadas y respuestas guardadas. | Acepta destinatarios antiguos en texto y nuevos en lista JSON. |
| Alertas de retraso | El panel identifica participantes con módulos pendientes después del plazo configurado, actualmente siete días desde su registro. | Visible en `/admin` y Reportes. |
| Métricas y reportes | Muestra participantes, avance, módulos terminados y porcentaje de aciertos de respuestas calificables. | Usa los datos importados; las preguntas abiertas quedan sin calificación automática. |
| Evaluación | Valida preguntas completas, opciones correctas y orden de módulos; muestra al participante su porcentaje de aciertos al terminar. | Permite continuar evaluaciones que ya tenían respuestas parciales. |
| Recordatorios | El administrador puede enviar un recordatorio individual a un participante con inducción pendiente. | En local se escribe en `storage/logs/laravel.log`; no sale un correo real. |
| Avisos de contenido | El administrador puede avisar manualmente de una página a los participantes activos a quienes corresponde. | También usa el correo en modo registro local. |
| Respaldo | El comando `php artisan induccion:respaldo` genera SQL y ZIP con los archivos subidos. | Programado a las 02:00 solo si se ejecuta `php artisan schedule:work` en local. |
| Inicio local | `iniciar-local.bat` inicia/comprueba la instancia MariaDB del proyecto antes del servidor Laravel. | Base en `127.0.0.1:3307`; la copia local se llama `induccion_real_local`. |
| Ajustes visuales | Métricas y alertas siguen el estilo de la aplicación; se centraron las cuatro tarjetas del panel, se corrigió la etiqueta larga de tipo de usuario en Reportes y se separaron los íconos de exportación en Usuarios. | Se reutiliza `public/images/logob.png` como ícono de pestaña en las páginas de la aplicación. |

Los avisos de contenido son **manuales**, no automáticos al crear una página. Los correos se envían de forma síncrona al pulsar el botón; no se añadió una cola de trabajo. El respaldo programado solo funciona mientras esté activo el programador local. La actualización de los contenidos corporativos sigue siendo una tarea administrativa, no una carga automática.

## Base de datos local

Se importó el archivo `dbs15806821.sql` entregado por el usuario, cuyo encabezado indica un corte del 23/07/2026. En la copia local hay 10 usuarios, 7 páginas, 5 preguntas, 7 respuestas y 12 registros de avance. Para compatibilidad con el código actual se agregaron **solo en esa base local** `pages.video_file`, `users.puesto` y la tabla `user_page_views`; `pages.destinado_a` se amplió a `TEXT`. Se limpiaron sesiones y tokens temporales copiados del respaldo, sin cambiar contraseñas ni respuestas. No se ejecutaron migraciones.

La cuenta administradora incluida en el respaldo se probó en local y abrió el panel. El archivo `.env` de esta copia apunta a la base importada y mantiene `MAIL_MAILER=log`. No se accedió al host de producción.

## Archivos existentes modificados

Se enumeran los archivos de aplicación que figuran modificados respecto al estado registrado del proyecto:

| Archivo | Propósito principal del cambio |
|---|---|
| `app/Http/Controllers/AdminController.php` | Resúmenes, alertas, recordatorios y avisos de contenido; textos de resultado. |
| `app/Http/Controllers/PageController.php` | Asignación y navegación de módulos; respuestas y porcentaje por módulo. |
| `app/Http/Controllers/QuestionController.php` | Validación, guardado y calificación de evaluaciones. |
| `app/Http/Controllers/UserController.php` | Gestión y presentación coherente de usuarios y avance. |
| `app/Http/Middleware/AdminMiddleware.php` | Bloqueo de rutas administrativas para otros roles. |
| `app/Http/Middleware/CheckInductionProgress.php` | Control del orden de módulos y acceso según avance. |
| `app/Models/Page.php` | Lectura de destinatarios nuevos y antiguos; acceso por perfil. |
| `app/Models/User.php` | Resumen común de inducción, retraso y aciertos. |
| `bootstrap/app.php` | Registro del middleware administrativo. |
| `config/app.php` | Uso de las variables de entorno para entorno, depuración, URL y clave. |
| `database/seeders/DatabaseSeeder.php` | Dejó de llamar al seeder de la cuenta ficticia. |
| `routes/console.php` | Programación del respaldo local. |
| `routes/web.php` | Rutas y permisos de gestión, reportes, recordatorios y avisos. |
| `resources/views/admin.blade.php` | Indicadores, alertas y posición de las cuatro tarjetas. |
| `resources/views/auth/custom-login.blade.php` | Ícono de pestaña. |
| `resources/views/auth/reset-password.blade.php` | Ícono de pestaña. |
| `resources/views/induction/completed.blade.php` | Ícono de pestaña. |
| `resources/views/induction/lobby.blade.php` | Presentación de avance e ícono de pestaña. |
| `resources/views/layouts/admin.blade.php` | Ícono de pestaña. |
| `resources/views/layouts/app.blade.php` | Ícono de pestaña. |
| `resources/views/layouts/guest.blade.php` | Ícono de pestaña. |
| `resources/views/pages/create.blade.php` | Ícono de pestaña. |
| `resources/views/pages/edit.blade.php` | Ícono de pestaña. |
| `resources/views/pages/index.blade.php` | Aviso manual de contenido e ícono de pestaña. |
| `resources/views/pages/template.blade.php` | Evaluación, respuestas parciales, resultado de aciertos e ícono de pestaña. |
| `resources/views/pages/template_unified.blade.php` | Ícono de pestaña. |
| `resources/views/questions/create.blade.php` | Formulario de preguntas e ícono de pestaña. |
| `resources/views/questions/edit.blade.php` | Formulario de preguntas e ícono de pestaña. |
| `resources/views/questions/index.blade.php` | Ícono de pestaña. |
| `resources/views/users/create.blade.php` | Formulario de usuarios e ícono de pestaña. |
| `resources/views/users/edit.blade.php` | Formulario de usuarios e ícono de pestaña. |
| `resources/views/users/index.blade.php` | Presentación de usuarios y separación entre íconos y texto de exportación. |
| `resources/views/users/reports.blade.php` | Indicadores, respuestas, alertas y etiqueta de tipo/área que admite varias líneas. |

Además, `.env` contiene la configuración **local** de base, correo en modo registro y plazo de retraso. Está excluido del seguimiento de Git y no debe copiarse como configuración de producción.

## Archivos nuevos de código y documentación

| Archivo | Para qué sirve |
|---|---|
| `app/Console/Commands/BackupInduction.php` | Comando de respaldo SQL y archivos subidos. |
| `app/Mail/InductionContentMail.php` | Estructura del aviso de contenido. |
| `config/induction.php` | Plazo de retraso y ruta de `mysqldump` configurables. |
| `resources/views/emails/induction_content.blade.php` | Plantilla del aviso de contenido. |
| `resources/views/partials/induction_messages.blade.php` | Mensajes de resultado reutilizados. |
| `resources/views/partials/induction_metrics.blade.php` | Indicadores y gráficos sencillos reutilizados. |
| `scripts/iniciar-mysql-local.ps1` | Inicio de MariaDB aislado dentro del proyecto. |
| `scripts/detener-mysql-local.ps1` | Detención de esa instancia local. |
| `scripts/iniciar-local.ps1` | Comprobación de MariaDB e inicio de Laravel. |
| `iniciar-local.bat` | Acceso sencillo al iniciador local. |
| `tests/Feature/InductionTest.php` | Pruebas funcionales con base en memoria. |
| `docs/CAMBIOS_LOCALES.md` | Registro previo de cambios. |
| `docs/PRUEBAS_LOCALES.md` | Guía para iniciar, probar y respaldar. |
| `docs/RECUENTO_CAMBIOS_2026-09-19.md` | Este inventario. |

`app/Mail/InductionReminderMail.php` y `resources/views/emails/induction_reminder.blade.php` ya existían y se reutilizan para los recordatorios.

## Archivos retirados

| Archivo | Motivo |
|---|---|
| `database/seeders/DefaultUserSeeder.php` | Creaba una cuenta de demostración con contraseña fija. |
| `app/Http/Controllers/VideoController.php` | Controlador de una pantalla de ejemplo fuera del flujo actual. |
| `resources/views/video.blade.php` | Pantalla de ejemplo con enlaces y video fijos. |

También se retiraron las herramientas de carga de datos de demostración que se habían agregado para pruebas (`app/Console/Commands/LocalInductionData.php`, `scripts/preparar-base-local.php` y `database/local/estructura.sql`) y la base local ficticia anterior. Ninguno forma parte del estado actual.

## Archivos locales generados o conservados

| Ruta | Situación |
|---|---|
| `storage/app/mysql-local/` | Archivos internos de MariaDB, incluida `induccion_real_local`; necesarios para seguir probando con esta base local. |
| `storage/app/private/backups/induccion-20260919-092400-504263.sql` y `.zip` | Respaldo verificado de la base local importada y archivos. |
| `storage/app/style-reference/` | Texto, capturas y herramientas temporales usados para estudiar la guía visual; se conservan hasta terminar las mejoras, según lo acordado. |
| `storage/framework/views/` | Vistas PHP compiladas que Laravel reemplaza al verificar o usar el sitio. La extensión Git muestra altas y bajas aquí porque algunos archivos antiguos ya estaban registrados. |
| `storage/framework/sessions/`, `storage/framework/cache/`, `storage/logs/laravel.log`, `.phpunit.result.cache` | Sesiones, caché, mensajes de correo de prueba, errores y resultados de pruebas; se generan durante el uso. |

El logo `public/images/logob.png` ya estaba en el proyecto y coincide con el archivo proporcionado. Se reutilizó como favicon mediante referencias en las vistas; no se reemplazó ni se duplicó la imagen. Los ejecutables antiguos de `build/` y `dist/` y los iniciadores originales tampoco se tocaron.

## Verificación y trabajo pendiente fuera de local

La última ejecución completa de pruebas funcionales pasó con **14 pruebas y 60 comprobaciones**; además se compiló la vista después de los cambios visuales recientes y pasaron las pruebas puntuales del panel y los recordatorios. Las mediciones orientativas de generación local fueron: módulo ~42 ms, panel ~62 ms, Usuarios ~153 ms y Reportes ~259 ms; no incluyen red ni navegador.

Antes de enviar correos reales desde el host habrá que verificar la estructura de su base frente a las columnas/tablas de compatibilidad locales, configurar un servicio de correo y remitente válidos, ajustar `APP_URL` y probar la entrega. El modo local `MAIL_MAILER=log` no envía correo real. La programación de respaldos en producción requerirá un mecanismo del host para ejecutar el programador y una política de conservación fuera de esta carpeta. No se hicieron cambios en producción ni se ejecutaron migraciones.
