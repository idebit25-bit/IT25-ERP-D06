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
        // 1. SI ES ADMINISTRADOR O NO ESTÁ LOGUEADO, SE SALTA LAS RESTRICCIONES
        if (!Auth::check() || Auth::user()->role === 'admin') {
            return $next($request);
        }

        $currentPageId = $request->route('identifier'); 

        // 2. CORRECCIÓN CRÍTICA: Si el identificador es la palabra "completed", ignorar el flujo de bloqueo
        if (!$currentPageId || $currentPageId === 'completed') {
            return $next($request);
        }

        $currentUser = Auth::user();

        // 3. OBTENER MISMO FILTRADO QUE EL CONTROLADOR PARA CONOCER SUS PÁGINAS PERMITIDAS
        $tipoUsuario = strtolower(trim($currentUser->tipo_usuario));

        $allPagesAllowed = Page::where(function ($query) use ($tipoUsuario) {
            $query->where(function ($subQuery) use ($tipoUsuario) {
                if (str_contains($tipoUsuario, 'estadia') || str_contains($tipoUsuario, 'estadí')) {
                    $subQuery->whereIn('destinado_a', ['Estadía', 'Estadia', 'estadia', 'estadía']);
                } else {
                    $subQuery->where('destinado_a', 'LIKE', '%' . $tipoUsuario . '%');
                }
            })->orWhere('destinado_a', 'Ambos');
        })->orderBy('order', 'asc')->get();

        // 4. ENCONTRAR LA POSICIÓN DE LA PÁGINA ACTUAL (Busca por slug o por ID)
        $currentIndex = $allPagesAllowed->search(function ($item) use ($currentPageId) {
            return $item->slug == $currentPageId || $item->id == $currentPageId;
        });

        // 5. SI EXISTE UNA PÁGINA PREVIA EN EL FLUJO REAL, EVALUAMOS SU PROGRESO
        if ($currentIndex !== false && $currentIndex > 0) {
            $prevPage = $allPagesAllowed[$currentIndex - 1];
            
            // Contamos preguntas activas de la página anterior inmediata
            $questionsCount = $prevPage->questions()->where('is_active', 1)->count();
            
            if ($questionsCount > 0) {
                $responsesCount = \App\Models\QuestionUserResponse::whereIn('question_id', $prevPage->questions()->where('is_active', 1)->pluck('id'))
                                    ->where('user_id', $currentUser->id)
                                    ->count();

                if ($responsesCount < $questionsCount) {
                    // Modificado para usar $prevPage->slug en lugar de id
                    return redirect()->route('induction.show', $prevPage->slug)
                                     ->with('error', 'Debes completar la evaluación de esta página antes de poder avanzar a la siguiente.');
                }
            }
        }

        return $next($request);
    }
}