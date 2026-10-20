<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionUserResponse;
use Illuminate\Http\Request;
use App\Models\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = Question::orderBy('order')->get();
        return view('questions.index', compact('questions'));
    }

    public function create()
    {
        $pages = Page::all(); // Obtener todas las páginas disponibles
        return view('questions.create', compact('pages'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'page_id' => 'required|exists:pages,id',
            'questions' => 'required|array',
            'questions.*.question_text' => 'required|string',
            'questions.*.question_type' => 'required|in:abierta,opcion_multiple',
            'questions.*.options' => 'nullable|array|max:20',
            'questions.*.options.*' => 'nullable|string|max:1000',
            'questions.*.correct_option' => 'nullable|integer|min:0',
        ]);

        // Validar el conjunto antes de guardar para evitar altas incompletas.
        foreach ($request->questions as $data) {
            if ($data['question_type'] === 'opcion_multiple') $this->choiceOptions($data);
        }

        foreach ($request->questions as $questionData) {
            $maxOrder = Question::where('page_id', $request->page_id)->max('order') ?? 0;

            $question = new Question();
            $question->page_id = $request->page_id;
            $question->question_text = $questionData['question_text'];
            $question->question_type = $questionData['question_type'];
            $question->order = $maxOrder + 1;

            if ($questionData['question_type'] === 'opcion_multiple') {
                $question->options = json_encode($this->choiceOptions($questionData), JSON_UNESCAPED_UNICODE);
                $question->correct_option = $questionData['correct_option'] ?? null;
            } else {
                $question->options = null;
                $question->correct_option = null;
            }

            $question->save();
        }

        return redirect()->route('pages.index')->with('success', 'Preguntas creadas con éxito.');
    }

    public function edit($id)
    {
        $question = Question::findOrFail($id);
        return view('questions.edit', compact('question'));
    }

    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:abierta,opcion_multiple',
            'options' => 'nullable|array|max:20',
            'options.*' => 'nullable|string|max:1000',
            'correct_option' => 'nullable|integer|min:0',
        ]);

        $question->question_text = $request->question_text;
        $question->question_type = $request->question_type;

        if ($request->question_type === 'opcion_multiple') {
            $question->options = json_encode($this->choiceOptions($request->all()), JSON_UNESCAPED_UNICODE);
            $question->correct_option = $request->correct_option ?? null;
        } else {
            $question->options = null;
            $question->correct_option = null;
        }

        $question->save();

        return redirect()->route('pages.index')->with('success', 'Pregunta actualizada.');
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return redirect()->route('pages.index')->with('success', 'Pregunta eliminada.');
    }

    public function answer(Request $request)
    {
        $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required|string|max:10000',
            'page_id' => 'required|exists:pages,id'
        ]);

        $answers = $request->input('answers');
        $pageId = $request->input('page_id');
        $user = Auth::user();

        $page = Page::with('questions')->findOrFail($pageId);
        abort_unless($page->accessibleBy($user), 403, 'No tienes acceso a este módulo.');
        $active = $page->questions->where('is_active', 1)->keyBy('id');
        $expectedIds = $active->keys()->map(fn($id) => (string) $id)->all();
        $receivedIds = array_map('strval', array_keys($answers));
        if (!$expectedIds || array_diff($expectedIds, $receivedIds) || array_diff($receivedIds, $expectedIds)) {
            throw ValidationException::withMessages(['answers' => 'Responde todas las preguntas activas de este módulo.']);
        }
        // Impide saltarse el orden enviando directamente el formulario.
        if ($user->role !== 'admin') {
            $previous = \App\Models\Page::with('questions')->orderBy('order')->orderBy('id')->get()
                ->filter(fn($p) => $p->accessibleBy($user));
            foreach ($previous as $p) {
                if ($p->id === $page->id) break;
                if ($user->inductionSummary(collect([$p]))['completed'] === 0) {
                    throw ValidationException::withMessages(['answers' => 'Completa los módulos anteriores antes de responder.']);
                }
            }
        }
        foreach ($answers as $questionId => $value) {
            $question = $active->get($questionId);
            if ($question->question_type === 'opcion_multiple'
                && !in_array($value, json_decode($question->options, true) ?? [], true)) {
                throw ValidationException::withMessages(['answers' => 'Selecciona una opción válida para cada pregunta.']);
            }
        }

        DB::transaction(function () use ($answers, $active, $user) {
        foreach ($answers as $questionId => $value) {
            $question = $active->get($questionId);
            $isCorrectValue = null;

            if ($question) {
                if ($question->question_type === 'opcion_multiple') {
                    $optionsArray = json_decode($question->options, true);
                    $correctIndex = $question->correct_option;

                    if (is_array($optionsArray) && isset($optionsArray[$correctIndex])) {
                        $realCorrectText = $optionsArray[$correctIndex];

                        $userAnswer = trim(mb_strtolower($value, 'UTF-8'));
                        $correctAnswer = trim(mb_strtolower($realCorrectText, 'UTF-8'));

                        $isCorrectValue = ($userAnswer === $correctAnswer) ? 1 : 0;
                    } else {
                        $isCorrectValue = 0;
                    }
                }
            }

            QuestionUserResponse::updateOrCreate(
                ['user_id' => $user->id, 'question_id' => $questionId],
                [
                    'answer' => $value,
                    'is_correct' => $isCorrectValue
                ]
            );
        }
        });

        // Aviso idempotente por cada versión completada de respuestas.
        $page->load('questions');
        $summary = $user->fresh()->inductionSummary(collect([$page]));
        if ($summary['completed'] === 1 && $user->employee_code) {
            $revision = hash('sha256', json_encode([
                $user->employee_code, $page->id,
                $user->fresh()->responses()->whereIn('question_id', $active->keys())
                    ->get(['question_id', 'answer', 'is_correct'])->sortBy('question_id')->values()->toArray(),
            ], JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
            app(\App\Services\A11EventNotifier::class)->moduleCompleted($user, $page, ['revision' => $revision]);
        }

        // =========================================================================
        //  CORRECCIÓN CLAVE: No redirigir de forma automática al usuario.
        // =========================================================================
        // Al regresar a la misma página, la vista Blade cambiará automáticamente
        // el formulario por el mensaje de éxito y habilitará el botón manual "SIGUIENTE".
        return back()->with('success', 'Evaluación guardada con éxito. Ya puedes avanzar al siguiente módulo cuando gustes.');
    }

    private function choiceOptions(array $data): array
    {
        $options = array_values($data['options'] ?? []);
        $index = $data['correct_option'] ?? null;
        if (count($options) < 2 || in_array('', array_map(fn($v) => trim((string) $v), $options), true)
            || count(array_unique($options)) !== count($options)
            || $index === null || !array_key_exists($index, $options)) {
            throw ValidationException::withMessages(['options' => 'Escribe al menos dos opciones distintas, sin espacios vacíos, e indica un índice de respuesta correcta válido.']);
        }
        return $options;
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*.id' => 'required|integer|exists:questions,id',
            'orders.*.order' => 'required|integer|min:0',
        ]);
        $orders = $request->input('orders');
        foreach ($orders as $item) {
            Question::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['status' => 'success']);
    }
}
