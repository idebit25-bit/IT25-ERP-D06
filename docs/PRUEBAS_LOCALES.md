# Trabajo local con datos del respaldo recibido

La aplicación usa una instancia MariaDB de XAMPP independiente en 127.0.0.1:3307. La conexión de .env apunta a induccion_real_local. No se conectó al host externo ni se modificó producción.

Se importó el volcado dbs15806821.sql proporcionado por el usuario. El encabezado del archivo indica que fue generado el 23/07/2026, aunque fue recibido el 19/09/2026. Por ello los datos locales reflejan ese corte, no necesariamente el estado actual del host.

## Inicio

En la raíz de este proyecto, abre iniciar-local.bat. Comprueba/inicia la instancia de MySQL local y luego Laravel en http://127.0.0.1:8000. El usuario ya dispone de credenciales administradoras incluidas en el respaldo; no se crearon usuarios ni contraseñas de demostración.

Para iniciar manualmente:
    powershell -ExecutionPolicy Bypass -File .\scripts\iniciar-mysql-local.ps1
    php artisan serve --host=127.0.0.1 --port=8000

Para detener el servidor web usa Ctrl+C. Para detener MySQL local:
    powershell -ExecutionPolicy Bypass -File .\scripts\detener-mysql-local.ps1

La instancia mantiene sus archivos dentro de storage/app/mysql-local. No se usa ni se altera el servicio MySQL principal de XAMPP.

## Datos importados

Al importar se verificaron 10 usuarios, 7 páginas, 5 preguntas, 7 respuestas y 12 registros de avance. Cinco participantes están activos; los otros usuarios del volcado conservan su estado/rol. Se preservaron las relaciones y el contenido del volcado. No se importaron datos de las antiguas demostraciones.

Para que el código actual funcione con esta estructura anterior, solo en la base local se añadieron:
- pages.video_file (nullable), usado al subir videos;
- users.puesto (nullable), usado en la asignación por perfil;
- user_page_views, relación prevista en el modelo User.

El campo pages.destinado_a de este volcado es texto simple. El modelo admite tanto ese texto antiguo como los arreglos JSON que guardan los formularios actuales. Se amplió la columna local a TEXT para admitir ambos formatos. No se transformaron los destinatarios existentes.

Se vaciaron sessions y password_reset_tokens y se limpiaron remember_token de la copia local, para no reutilizar credenciales temporales ni sesiones del host. Las contraseñas existentes no fueron cambiadas.

Las páginas del volcado no referencian adjuntos o videos locales, así que el respaldo SQL fue suficiente para ver ese contenido en las pruebas. Si posteriormente se necesitara un archivo físico que no está en esta copia, tendría que obtenerse por separado.

El SQL original permanece en la ruta en que fue entregado. No se copió al proyecto para evitar otra copia de datos personales. La base de demostración induccion_mysql fue eliminada después de respaldarla y verificar la importación real.

## Verificación y respaldos

El panel y la vista Reportes se renderizaron usando los datos importados. Se calcularon los resúmenes de los cinco participantes activos. Se verificó por HTTP que una cuenta administradora existente inicia sesión y abre el panel local, sin cambiar su contraseña.
También se comprobó que las credenciales incorrectas reciben el mensaje esperado, sin error de base de datos. La base importada asigna módulos a los cinco participantes activos y muestra tres alertas de retraso con la regla local de siete días.

Se generó un respaldo de la base real local y se restauró en una base temporal para contrastar las 19 tablas y los conteos de usuarios, páginas y respuestas. Después se eliminó la base temporal. Los respaldos que contenían datos de demostración fueron retirados; permanece únicamente el respaldo de la base importada.

Para repetir pruebas automatizadas (usan una base en memoria, nunca modifican los datos reales):
    php artisan test --testsuite=Feature

Al completar un módulo con preguntas de opción múltiple calificadas, el participante ve su porcentaje de aciertos. Si tenía respuestas parciales del respaldo, puede completar las preguntas restantes sin repetir las anteriores.

Para respaldar la base actual y los archivos subidos:
    php artisan induccion:respaldo

Los respaldos están en storage/app/private/backups. El respaldo anterior al cambio se conservó solo durante la transición y se retiró al concluirla. Para activar el respaldo diario a las 02:00 mientras la terminal esté abierta:
    php artisan schedule:work

La automatización no crea una tarea de Windows. Su salida se registra en storage/logs/respaldos.log. Revisa periódicamente ese log y el espacio disponible. Un respaldo dentro del proyecto no sustituye una copia externa.

MAIL_MAILER=log permanece activo: los botones de recordatorio y aviso de contenido no envían correos reales. Los mensajes se guardan en storage/logs/laravel.log. El trabajo en producción, SMTP y validación de entrega siguen pendientes de acceso y autorización.

## Alcance de esta importación

No se ejecutaron migraciones ni seeders. No uses Iniciar.py para este entorno: apunta a otra ruta y ejecuta migraciones automáticamente. Se conservaron los iniciadores originales, pero para estas pruebas se usa iniciar-local.bat.

El seeder predeterminado ya no crea una cuenta de demostración. Se retiró la pantalla aislada `/video`, que contenía enlaces de ejemplo y un video fijo; la inducción activa utiliza las páginas y medios guardados en la base importada.

No se modificaron las carpetas de respaldo externas ni se realizaron operaciones con repositorios. Los ajustes se limitaron a la instancia y a los archivos de este proyecto.
