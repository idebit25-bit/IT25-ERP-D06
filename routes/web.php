<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\StepController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\QuestionController;

/*
|--------------------------------------------------------------------------
| Ruta Raíz Global (Fallback)
|--------------------------------------------------------------------------
*/
// Accesible siempre de manera global para evitar errores de redirección
Route::get('/', fn() => redirect()->route('login'))->name('home');

/*
|--------------------------------------------------------------------------
| Rutas para invitados (no autenticados)
|--------------------------------------------------------------------------
*/
Route::middleware(['guest'])->group(function () {
     // Login personalizado
     Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
     Route::post('/login', [AuthController::class, 'authenticate']);

     // Recuperación de contraseña
     Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
     Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
     Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
     Route::post('/reset-password', [ForgotPasswordController::class, 'reset'])->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Rutas para usuarios autenticados
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

     // Cerrar sesión
     Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

     // Página principal de Admin
     Route::get('/admin', [AdminController::class, 'index'])->name('admin');

     // Perfil
     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

     // Video
     Route::get('/video', [VideoController::class, 'index'])->name('video');

     // Páginas (CRUD)
     Route::resource('pages', PageController::class)->except(['show']);
     Route::post('/pages/reorder', [PageController::class, 'reorder'])->name('pages.reorder');
     Route::post('/pages/sort', [PageController::class, 'sort'])->name('pages.sort');
     Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

     // Pasos (Steps)
     Route::resource('steps', StepController::class)->except(['show']);

     // Usuarios
     Route::resource('users', UserController::class);
     Route::patch('/users/{id}/restore', [UserController::class, 'restore'])->name('users.restore');
     Route::delete('/users/{id}/forceDestroy', [UserController::class, 'forceDestroy'])->name('users.forceDestroy');
     Route::get('/users/{id}/download-responses', [UserController::class, 'downloadResponses'])->name('users.downloadResponses');

     // Reporte de progreso
     Route::get('/admin/reportes', [AdminController::class, 'userReport'])->name('admin.reports');

     // Preguntas (CRUD y Ordenamiento)
     Route::resource('questions', QuestionController::class);
     Route::post('/questions/sort', [QuestionController::class, 'sort'])->name('questions.sort');
     Route::post('/questions/reorder', [QuestionController::class, 'reorder'])->name('questions.reorder');
     Route::post('/questions/answer', [QuestionController::class, 'answer'])->name('questions.answer');

     // Ruta para el envío manual de correos de aviso
     Route::post('/admin/send-reminder/{id}', [AdminController::class, 'sendReminder'])->name('admin.sendReminder');

     /*
|--------------------------------------------------------------------------
| Flujo de Inducción Unificada
|--------------------------------------------------------------------------
*/
     Route::get('/lobby', [PageController::class, 'showLobby'])->name('lobby');

     // Iniciar inducción según rol
     Route::get('/induction/start', [PageController::class, 'startInduction'])->name('induction.start');

     // Finalización e Inducción activa
     Route::get('/induction/completed', [PageController::class, 'completedInduction'])->name('induction.completed');
     Route::get('/induction/{identifier}', [PageController::class, 'showInduction'])
          ->name('induction.show')
          ->middleware('check.progress');

     Route::get('/pages/{id}/download', [PageController::class, 'downloadAttachment'])->name('pages.download');
});
