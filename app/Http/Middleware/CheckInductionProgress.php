<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Page;
use Illuminate\Support\Facades\Auth;

class CheckInductionProgress
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        if (!$user || $user->role === 'admin') return $next($request);
        $identifier = $request->route('identifier') ?? $request->route('slug');
        $pages = Page::with('questions')->orderBy('order')->orderBy('id')->get()
            ->filter(fn($page) => $page->accessibleBy($user))->values();
        $currentIndex = $pages->search(fn($page) => $page->slug == $identifier || $page->id == $identifier);
        abort_if($currentIndex === false, 403, 'No tienes acceso a este módulo.');

        $completedIds = $user->inductionSummary($pages)['completed_ids'];
        foreach ($pages->take($currentIndex) as $page) {
            if (!in_array($page->id, $completedIds)) {
                return redirect()->route('induction.show', $page->slug ?: $page->id)
                    ->with('error', 'Debes completar el módulo anterior antes de avanzar.');
            }
        }
        return $next($request);
    }
}
