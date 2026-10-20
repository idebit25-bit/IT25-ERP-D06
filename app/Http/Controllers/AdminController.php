<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\InductionReminderMail;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin');
    }

    public function userReport()
    {
        $users = \App\Models\User::with(['responses.question.page'])->get();
        return view('users.reports', compact('users'));
    }

    // NUEVA FUNCIÓN PARA ENVIAR EL CORREO MANUALMENTE
    public function sendReminder($id)
    {
        $user = User::findOrFail($id);

        // Enviamos el mailable pasándole el modelo del usuario
        Mail::to($user->email)->send(new InductionReminderMail($user));

        return redirect()->back()->with('success', 'Correo de recordatorio enviado con éxito a ' . $user->name);
    }
}