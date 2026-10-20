<?php

namespace App\Http\Controllers\Integration;

use App\Http\Controllers\Controller;
use App\Models\{Page, User};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class InductionResultController extends Controller
{
    public function __invoke(Request $request)
    {
        $data = $request->validate(['employee_code' => ['required', 'string', 'max:255']]);
        if (!Schema::hasColumn('users', 'employee_code')) {
            return response()->json(['message' => 'La integración requiere configurar los códigos de empleado.'], 503);
        }
        $users = User::where('employee_code', $data['employee_code'])->limit(2)->get();
        if ($users->count() > 1) return response()->json(['message' => 'Código de empleado duplicado.'], 409);
        $user = $users->first();
        // No usar coincidencias aproximadas, correos ni IDs de A11 como alternativa.
        if (!$user || $user->employee_code !== $data['employee_code']) {
            return response()->json(['message' => 'Colaborador no encontrado.'], 404);
        }
        $user->load(['responses', 'inductionViewed']);
        $pages = Page::with(['questions' => fn ($q) => $q->orderBy('order')->orderBy('id')])
            ->orderBy('order')->orderBy('id')->get()->filter(fn ($page) => $page->accessibleBy($user));
        $summary = $user->inductionSummary($pages);
        $responses = $user->responses->keyBy('question_id');
        $modules = $pages->map(function ($page) use ($user, $responses, $summary) {
            $questions = $page->questions->where('is_active', 1)->map(function ($question) use ($responses) {
                $response = $responses->get($question->id);
                $answered = $response && trim((string) $response->answer) !== '';
                return [
                    'id' => $question->id, 'text' => $question->question_text,
                    'type' => $question->question_type, 'answer' => $answered ? $response->answer : null,
                    'correct' => $answered && $question->question_type === 'opcion_multiple' && $response->is_correct !== null
                        ? (bool) $response->is_correct : null,
                    'answered_at' => $answered ? $response->updated_at?->toIso8601String() : null,
                ];
            })->values();
            $scored = $questions->whereNotNull('correct');
            $correct = $scored->where('correct', true)->count();
            $module = [
                'id' => $page->id, 'title' => $page->title,
                'completed' => in_array($page->id, $summary['completed_ids']),
                'answered' => $questions->whereNotNull('answer')->count(),
                'total_questions' => $questions->count(), 'scored' => $scored->count(), 'correct' => $correct,
                'accuracy' => $scored->count() ? (int) round($correct / $scored->count() * 100) : null,
                'questions' => $questions->all(),
            ];
            // Incluye la identidad y el contenido para detectar cambios entre consulta y guardado.
            $module['revision'] = hash('sha256', json_encode([$user->id, $user->employee_code, $module], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
            return $module;
        })->values();

        return response()->json(['schema_version' => 1, 'source' => 'D06', 'retrieved_at' => now()->toIso8601String(),
            'employee_code' => $user->employee_code, 'user_id' => $user->id,
            'summary' => collect($summary)->only(['total', 'completed', 'percentage', 'scored', 'correct', 'accuracy'])->all(),
            'modules' => $modules]);
    }
}
