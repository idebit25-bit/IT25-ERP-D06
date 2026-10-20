# Registro de cambios locales

Fecha: 17 de septiembre de 2026.

| Cambio | Archivos principales | Motivo |
|---|---|---|
| Permisos administrativos | bootstrap/app.php, routes/web.php, AdminMiddleware.php | Exigir el rol de administrador en las funciones de gestión |
| Asignación y progreso compartidos | Page.php, User.php, PageController.php, UserController.php, CheckInductionProgress.php | Dar el mismo resultado en navegación, panel, listas y correo |
| Configuración por entorno | config/app.php, config/induction.php, .env | Usar URL, clave y conexión locales; permitir ajustar el plazo de retraso |
| Métricas y alertas | AdminController.php, admin.blade.php, users/reports.blade.php, partials/induction_metrics.blade.php | Reutilizar las respuestas y avances existentes con indicadores sencillos |
| Avisos de contenido y recordatorios | AdminController.php, InductionContentMail.php, emails/induction_content.blade.php, pages/index.blade.php | Incorporar avisos manuales por perfil y probar correo sin envíos reales |
| Validaciones de evaluación | QuestionController.php, pages/template.blade.php | Impedir respuestas ajenas/incompletas y preguntas con opciones incorrectas |
| Respaldo local y programación | BackupInduction.php, routes/console.php | Copiar base y archivos, con ejecución diaria mientras el programador esté activo |
| Preparación y ejemplos locales | database/local/estructura.sql, scripts/, LocalInductionData.php | Preparar pruebas dentro de este proyecto, sin migraciones ni tocar otros servidores |
| Pruebas y documentación | tests/Feature/InductionTest.php, docs/ | Comprobar los cambios y explicar su uso |

También se corrigió la ruta questions.sort para usar el método de ordenamiento existente y se validó el área que envían los formularios actuales de usuarios. No se rediseñaron los formularios ni se cambiaron los recursos frontend compilados.

Los archivos existentes solo se ajustaron donde eran necesarios para estas funciones. Las migraciones, seeders, lanzadores originales y carpetas de respaldo externas no se modificaron. Los archivos generados por dependencias, caché, sesiones, correos de prueba y MySQL están dentro de esta instalación.

Reglas provisionales para pruebas: retraso a los 7 días desde el registro y aviso de contenido manual. Regla confirmada por el usuario: porcentaje de aciertos con las preguntas actuales, sin ampliar los intentos ni establecer nota mínima.

Consulta PRUEBAS_LOCALES.md para iniciar, probar, respaldar y entender cada cambio.

Corrección del inicio local: MySQL detenido provocaba el error de conexión 2002 al entrar. Se agregó iniciar-local.bat y scripts/iniciar-local.ps1 para iniciar/comprobar la instancia del proyecto antes de Laravel, conservando los iniciadores originales.

Homologación visual: se revisó GeneralidadesV2.8.pdf como referencia, dando prioridad al estilo de las pantallas existentes. Los indicadores del panel adoptan las tarjetas negras de sus accesos; Reportes conserva tarjetas blancas dentro de su contenedor gris. La tabla de retrasos usa encabezado negro, filas blancas y botón oscuro con el icono de correo existente. Las barras reutilizan la apariencia azul del avance de usuarios y los avisos los colores de éxito/error de Reportes. Se mantuvieron las fuentes de cada pantalla y se ajustó la distribución de indicadores en móvil. No se cambiaron controladores, rutas, consultas ni la base de datos en este ajuste. Se verificaron capturas del panel y Reportes y volvieron a pasar las 13 pruebas (56 comprobaciones).

Actualización del 19/09/2026: se recibió dbs15806821.sql y se importó en induccion_real_local de la instancia local. El volcado es de 23/07/2026. Se añadieron únicamente tres piezas de esquema que el código actual usa (pages.video_file, users.puesto y user_page_views) y se amplió pages.destinado_a a TEXT para admitir texto heredado y listas JSON. Se limpiaron sesiones y tokens de recuperación de la copia local. Se retiró induccion_mysql con sus datos de demostración, el comando para cargarlos y los scripts de creación de aquella estructura. .env apunta ahora a la base importada. No se cambiaron las contraseñas de los usuarios reales y no se tocó producción.

Limpieza posterior: se comprobó que una cuenta administradora del respaldo abre el panel local. Se quitó el seeder que creaba `admin@example.com` con contraseña fija y se dejó `DatabaseSeeder` sin datos ficticios. También se retiró la ruta, controlador y vista `/video`, ajenos al flujo de inducción actual y con contenido de ejemplo fijo. Pasaron 13 pruebas funcionales (55 comprobaciones). No se modificó la base importada en esta limpieza.

Evaluaciones con el respaldo real: la vista de cada módulo muestra al participante su porcentaje de aciertos al completar las preguntas de opción múltiple calificadas, usando las respuestas existentes. Las preguntas abiertas no se califican automáticamente. En el respaldo hay módulos con respuestas parciales: el formulario ahora conserva las respuestas anteriores al enviar las pendientes, evitando que la validación bloquee la finalización. Pasaron 14 pruebas funcionales (60 comprobaciones) y la compilación de vistas. Las verificaciones del respaldo fueron de lectura; no se cambiaron sus respuestas.
