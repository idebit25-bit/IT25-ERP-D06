import subprocess
import tkinter as tk
from tkinter import messagebox
import os
import webbrowser

tasks = []

# Ruta de instalación de XAMPP (ajústala según sea necesario)
XAMPP_PATH = "C:\\xampp"
LARAVEL_PATH = "C:\\xampp\\htdocs\\Proyecto-CursoInduc"

def start_services():
    """Inicia Apache y MySQL en XAMPP en segundo plano."""
    try:
        subprocess.Popen([os.path.join(XAMPP_PATH, "apache_start.bat")], shell=True, creationflags=subprocess.CREATE_NEW_CONSOLE)
        subprocess.Popen([os.path.join(XAMPP_PATH, "mysql_start.bat")], shell=True, creationflags=subprocess.CREATE_NEW_CONSOLE)
        messagebox.showinfo("XAMPP", "Servicios Apache y MySQL iniciados en segundo plano.")
    except Exception as e:
        messagebox.showerror("Error", f"No se pudo iniciar XAMPP: {e}")

def run_laravel():
    """Inicia Laravel con php artisan serve, npm run dev y abre la carpeta en Visual Studio Code."""
    try:
        os.chdir(LARAVEL_PATH)
        tasks.append(subprocess.Popen("php artisan migrate --force", shell=True))
        tasks.append(subprocess.Popen("php artisan serve", shell=True, creationflags=subprocess.CREATE_NEW_CONSOLE))
        tasks.append(subprocess.Popen("npm run dev", shell=True, creationflags=subprocess.CREATE_NEW_CONSOLE))
        
        # Abrir la carpeta en Visual Studio Code (asegúrate de tener el comando "code" en tu PATH)
        subprocess.Popen(["code", LARAVEL_PATH], shell=True)
        
        messagebox.showinfo("Laravel", "Laravel iniciado correctamente y carpeta abierta en VS Code.")
        webbrowser.open("http://127.0.0.1:8000/")
    except Exception as e:
        messagebox.showerror("Error", f"No se pudo iniciar Laravel: {e}")

def stop_laravel():
    """Detiene los procesos de Laravel y los servicios de XAMPP."""
    try:
        for task in tasks:
            task.terminate()
        subprocess.run([os.path.join(XAMPP_PATH, "xampp_stop.exe")], check=True)
        messagebox.showinfo("Laravel", "Laravel y XAMPP detenidos correctamente.")
    except Exception as e:
        messagebox.showerror("Error", f"No se pudo detener Laravel: {e}")

# Interfaz gráfica con Tkinter
root = tk.Tk()
root.title("Laravel Launcher")
root.geometry("300x200")

btn_start = tk.Button(root, text="Iniciar", command=lambda: [start_services(), run_laravel()], height=2, width=20)
btn_start.pack(pady=20)

btn_stop = tk.Button(root, text="Detener", command=stop_laravel, height=2, width=20)
btn_stop.pack(pady=20)

root.mainloop()
