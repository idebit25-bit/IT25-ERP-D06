<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Page;
use App\Mail\InductionReminderMail;
use App\Mail\InductionContentMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin', $this->reportData());
    }

    public function userReport()
    {
        return view('users.reports', $this->reportData());
    }

    private function reportData(): array
    {
        $pages = Page::with('questions')->orderBy('order')->orderBy('id')->get();
        $users = User::where('role', 'user')->with(['responses.question.page', 'inductionViewed'])->get();
        $summaries = $users->mapWithKeys(fn($user) => [$user->id => $user->inductionSummary($pages)]);
        $assigned = $summaries->filter(fn($s) => $s['total'] > 0);
        $completed = $assigned->filter(fn($s) => $s['completed'] === $s['total'])->count();
        $notStarted = $assigned->where('percentage', 0)->count();
        $scored = $summaries->sum('scored');
        $stats = [
            'users' => $users->count(), 'completed' => $completed, 'not_started' => $notStarted,
            'in_progress' => $assigned->count() - $completed - $notStarted,
            'unassigned' => $users->count() - $assigned->count(),
            'overdue' => $summaries->where('overdue', true)->count(),
            'average' => $assigned->count() ? (int) round($assigned->avg('percentage')) : 0,
            'accuracy' => $scored ? (int) round($summaries->sum('correct') / $scored * 100) : null,
            'scored' => $scored,
        ];
        $overdueUsers = $users->filter(fn($user) => $summaries[$user->id]['overdue']);
        return compact('users', 'summaries', 'stats', 'overdueUsers');
    }

    public function sendReminder($id)
    {
        $user = User::findOrFail($id);
        abort_unless($user->role === 'user', 422, 'El recordatorio es para participantes.');
        $summary = $user->inductionSummary();
        if (!$summary['total'] || $summary['completed'] === $summary['total']) {
            return back()->with('error', 'No se pudo hacer el recordatorio a '.$user->name.' porque no tiene módulos pendientes.');
        }
        try {
            Mail::to($user->email)->send(new InductionReminderMail($user));
        } catch (\Throwable $e) {
            Log::error('No se pudo enviar el recordatorio.', ['exception' => $e]);
            return back()->with('error', 'No se pudo hacer el recordatorio a '.$user->name.'. Revisa la configuración y el registro de errores.');
        }
        return back()->with('success', config('mail.default') === 'log'
            ? 'Se hizo el recordatorio a '.$user->name.' con éxito (prueba: no se envió un correo real).'
            : 'Se hizo el recordatorio a '.$user->name.' con éxito.');
    }

    public function notifyContent($id)
    {
        $page = Page::findOrFail($id);
        $sent = 0;
        $failed = 0;
        User::where('role', 'user')->chunkById(100, function ($users) use ($page, &$sent, &$failed) {
            foreach ($users as $user) {
                if (!$page->accessibleBy($user)) continue;
                try {
                    Mail::to($user->email)->send(new InductionContentMail($user, $page));
                    $sent++;
                } catch (\Throwable $e) {
                    $failed++;
                    Log::error('No se pudo enviar el aviso de contenido.', ['page_id' => $page->id, 'exception' => $e]);
                }
            }
        });
        $message = config('mail.default') === 'log'
            ? "Avisos guardados en el log local: $sent. No se enviaron correos reales."
            : "Avisos enviados: $sent.";
        if ($failed) return back()->with('error', "$message Fallidos: $failed.");
        return back()->with('success', $sent ? $message : 'No hay participantes asignados a esta página.');
    }
}
