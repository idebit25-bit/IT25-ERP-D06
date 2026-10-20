# API de inducción para A11 — implementación local

La versión publicada en https://gestorinduccion.i-deb.com.mx es anterior a esta copia local.
Esta entrega no modifica producción.

Activación local verificada: base `induccion_real_local`, API en `http://127.0.0.1:8003`,
migración de employee_code aplicada y secreto local configurado. Solo se vinculó la cuenta
verificada de Iván Pintor (usuario local 52, código ID1801). No aplicar el SQL de usuarios
de producción sobre esta copia: sus cuentas e IDs difieren. No se modificaron contraseñas ni roles.
18 pruebas / 85 aserciones aprobadas con PHP 8.2 y consulta real desde A11 comprobada.

## Preparación

- Aplicar únicamente `php artisan migrate --path=database/migrations/2026_10_19_000001_add_employee_code_for_integration.php`
  sobre la base local elegida. No ejecutar migraciones históricas ni seeders.
- Asignar códigos de empleado coincidentes con A11. No copiar los IDs internos ni reutilizar códigos entre personas.
  La migración preserva códigos existentes y exige unicidad; no genera ni modifica códigos por su cuenta.
- Configurar `A11_INTEGRATION_TOKEN` en `.env` con un secreto aleatorio de al menos 32 caracteres.
  Ese mismo secreto va en `D06_API_TOKEN` de A11; nunca en el navegador ni en Git.
- Limpiar caché de configuración e iniciar en un puerto libre, por ejemplo 8003, para no competir con A11.

## Contrato v1

`GET /api/v1/induccion?employee_code=CODIGO`, cabeceras `Accept: application/json`
y `Authorization: Bearer <secreto>`.

Respuesta: `schema_version=1`, `source=D06`, `retrieved_at`, `employee_code`, `user_id`,
`summary` (`total`, `completed`, `percentage`, `scored`, `correct`, `accuracy`) y `modules`.
Cada módulo contiene `id`, `title`, `completed`, `answered`, `total_questions`, `scored`,
`correct`, `accuracy`, `revision` SHA-256 y `questions` con `id`, `text`, `type`, `answer`,
`correct` nullable y `answered_at` nullable. No se publican correos, contraseñas ni respuestas correctas del catálogo.

Solo incluye módulos accesibles para esa cuenta y sus preguntas activas, según la lógica actual de D06.
Las preguntas abiertas no se califican automáticamente. Avance de módulos y porcentaje de aciertos
son medidas distintas. La revisión cambia si cambia la identidad, contenido, respuesta o resultado del módulo.
No incluye la hora de consulta para que consultar nuevamente no invalide un formulario por sí solo.

401 sin token o token inválido; 404 cuenta ausente/eliminada; 409 código duplicado;
503 columna de códigos sin configurar; 422 parámetros inválidos; 429 límite de 60 peticiones/minuto por IP.
Los errores de infraestructura también deben tratarse como indisponibilidad, nunca como resultado cero.

La API es de solo lectura. A11 decide quién consulta y cuándo crear un plan, almacena una copia
del resultado y mantiene su propio seguimiento. No sincroniza usuarios, permisos ni contraseñas.
D06 no conserva intentos históricos; los datos corresponden al catálogo y respuestas actuales.

## Comprobación

`php vendor/phpunit/phpunit/phpunit tests/Feature` usa SQLite en memoria.
Las pruebas cubren autenticación, códigos ausentes/duplicados, cuentas eliminadas, privacidad,
separación avance/aciertos, preguntas abiertas y cambios de revisión. No utilizan la base real.

## Avisos de finalización hacia A11

Configurar `A11_EVENTS_URL` con la ruta completa `/api/v1/integraciones/d06/eventos`
y `A11_EVENTS_TOKEN` con el secreto del receptor A11. No reutilizar ni duplicar la
variable `A11_INTEGRATION_TOKEN`, que autentica las consultas entrantes desde A11.
Se exige HTTPS salvo servidores locales de desarrollo.

El aviso incluye la fecha con su desplazamiento horario. A11 muestra esa fecha en
Ciudad de México. Los rechazos HTTP quedan registrados; no hay aún una cola
persistente de reintentos. El aviso corresponde al módulo completado al enviar
respuestas, no a cada respuesta individual ni a una garantía de inducción total.
