<?php

namespace App\Services;

use App\Models\{Page, User};
use Illuminate\Support\Facades\Http;

class A11EventNotifier
{
    public function moduleCompleted(User $user, Page $page, array $module): void
    {
        $url = rtrim((string) config('d06_events.a11_events_url'), '/');
        $token = (string) config('d06_events.token');
        if (!$url || strlen($token) < 32) return;
        $secure = parse_url($url, PHP_URL_SCHEME) === 'https';
        $local = app()->environment(['local', 'testing']) && in_array(parse_url($url, PHP_URL_HOST), ['127.0.0.1', 'localhost', '::1'], true);
        if (!$secure && !$local) return;
        try {
            $response = Http::acceptJson()->withToken($token)->withoutRedirecting()->connectTimeout(2)->timeout(5)->post($url, [
                'event_id' => hash('sha256', implode('|', [$user->employee_code, $page->id, $module['revision']])),
                'event' => 'induccion.modulo_completado', 'source' => 'D06', 'employee_code' => $user->employee_code,
                'module_id' => $page->id, 'module_title' => $page->title, 'completed_at' => $module['completed_at'] ?? now()->toIso8601String(),
                'result_revision' => $module['revision'],
            ]);
            if (!$response->successful() || $response->json('accepted') !== true) {
                logger()->warning('A11 no aceptó el evento de D06.', ['employee_code' => $user->employee_code, 'status' => $response->status()]);
            }
        } catch (\Throwable $e) {
            logger()->warning('No se pudo enviar el evento de inducción a A11.', ['employee_code' => $user->employee_code, 'error' => $e->getMessage()]);
        }
    }
}
