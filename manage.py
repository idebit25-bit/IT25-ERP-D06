#!/usr/bin/env python3
import subprocess
import time
import webbrowser
import platform
import os
import sys

def start_project():
    print("=== Iniciando proyecto ===")
    serve_process = subprocess.Popen(["php", "artisan", "serve"], shell=True)
    npm_process = subprocess.Popen(["npm", "run", "dev"], shell=True)
    time.sleep(3)
    webbrowser.open("http://127.0.0.1:8000")

    print("Proyecto iniciado. Pulsa Ctrl + C para detener.\n")

    try:
        serve_process.wait()
        npm_process.wait()
    except KeyboardInterrupt:
        print("\nSe recibió Ctrl+C, deteniendo procesos...")
        stop_project()

    # <<< AÑADIMOS LA LÍNEA DE ESPERA >>>
    input("Pulsa Enter para cerrar la ventana...")

def stop_project():
    print("=== Deteniendo proyecto ===")
    system_os = platform.system().lower()
    if "windows" in system_os:
        os.system("taskkill /f /im php.exe")
        os.system("taskkill /f /im node.exe")
    else:
        os.system("pkill -f 'php artisan serve'")
        os.system("pkill -f 'npm run dev'")
    print("Procesos detenidos.\n")
    # <<< AÑADIMOS LA LÍNEA DE ESPERA >>>
    input("Pulsa Enter para cerrar la ventana...")

if __name__ == "__main__":
    if len(sys.argv) < 2:
        print("Uso: python manage.py [start|stop]")
        input("Pulsa Enter para cerrar...")
        sys.exit(1)

    command = sys.argv[1].lower()

    if command == "start":
        start_project()
    elif command == "stop":
        stop_project()
    else:
        print("Opción desconocida:", command)
        print("Uso: python manage.py [start|stop]")
        input("Pulsa Enter para cerrar...")
