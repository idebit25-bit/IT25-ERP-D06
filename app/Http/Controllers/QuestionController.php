<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\QuestionUserResponse;
use Illuminate\Http\Request;
use App\Models\Page;
use Illuminate\Support\Facades\Auth;

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
        ]);

        foreach ($request->questions as $questionData) {
            $maxOrder = Question::where('page_id', $request->page_id)->max('order') ?? 0;

            $question = new Question();
            $question->page_id = $request->page_id;
            $question->question_text = $questionData['question_text'];
            $question->question_type = $questionData['question_type'];
            $question->order = $maxOrder + 1;

            if ($questionData['question_type'] === 'opcion_multiple' && isset($questionData['options'])) {
                $filteredOptions = array_filter($questionData['options'], fn($op) => !empty($op));
                $question->options = json_encode(array_values($filteredOptions));
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
            'options' => 'array',
        ]);

        $question->question_text = $request->question_text;
        $question->question_type = $request->question_type;

        if ($request->question_type === 'opcion_multiple') {
            $filtered = array_filter($request->options, fn($op) => !empty($op));
            $question->options = json_encode(array_values($filtered));
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
            'page_id' => 'required|exists:pages,id'
        ]);

        $answers = $request->input('answers');
        $pageId = $request->input('page_id');
        $user = Auth::user();

        foreach ($answers as $questionId => $value) {
            $question = Question::find($questionId);
            $isCorrectValue = null;

            if ($question) {
                if ($question->question_type === 'opcion_multiple') {
                    $optionsArray = json_decode($question->options, true);
                    $correctIndex = $question->correct_option;

                    if (is_array($optionsArray) && isset($optionsArray[$correctIndex])) {
                        $realCorrectText = $optionsArray[$correctIndex];

                        $userAnswer = trim(strtolower($value));
                        $correctAnswer = trim(strtolower($realCorrectText));

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

        // =========================================================================
        //  CORRECCIÓN CLAVE: No redirigir de forma automática al usuario.
        // =========================================================================
        // Al regresar a la misma página, la vista Blade cambiará automáticamente
        // el formulario por el mensaje de éxito y habilitará el botón manual "SIGUIENTE".
        return back()->with('success', 'Evaluación guardada con éxito. Ya puedes avanzar al siguiente módulo cuando gustes.');
    }

    public function reorder(Request $request)
    {
        $orders = $request->input('orders');
        foreach ($orders as $item) {
            Question::where('id', $item['id'])->update(['order' => $item['order']]);
        }
        return response()->json(['status' => 'success']);
    }
}
