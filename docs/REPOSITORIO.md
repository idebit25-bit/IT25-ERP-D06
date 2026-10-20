# Repositorio y versiones

Repositorio de trabajo: https://github.com/idebit25-bit/IT25-ERP-D06

- `main`: código actual con las mejoras locales.
- `original-antes-de-intervencion`: código anterior a las mejoras, tomado del commit `ca51d87` del 7 de agosto de 2026. Para su publicación se excluyeron archivos de ejecución, bases de datos, adjuntos y ejecutables generados; la clave incrustada de `config/app.php` se sustituyó por `env('APP_KEY')`. Se agregaron reglas de exclusión y una plantilla de entorno sin secretos. No es un respaldo de los datos.
- `respaldo-original-local`: referencia local al commit original exacto, que no debe subirse por contener material de la instalación anterior.

Las ramas publicadas empiezan con una instantánea limpia y no incluyen el historial que contenía datos sensibles. Las mejoras actuales parten de esa instantánea para poder comparar ambas versiones.

La configuración real `.env`, MariaDB local, respaldos, sesiones, registros, archivos subidos y herramientas de referencia visual se conservan únicamente en este equipo. `.gitignore` evita agregarlos de nuevo. No usar `git push --all` ni `git push --mirror`, pues existe un respaldo local que no está destinado a publicación.

Para otra instalación se requiere configurar `.env` a partir de `.env.example`, generar una clave propia e importar por un medio privado la base autorizada y los archivos necesarios. Consultar `PRUEBAS_LOCALES.md` para los detalles del entorno actual. La publicación del código no despliega el sistema en producción.
