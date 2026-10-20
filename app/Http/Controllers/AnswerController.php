<?php

namespace App\Http\Controllers;

use App\Models\Answer;
use App\Models\Question;
use Illuminate\Http\Request;

class AnswerController extends Controller
{
    // Guarda la respuesta de un usuario
    public function store(Request $request)
    {
        $request->validate([
            'question_id' => 'required|exists:questions,id',
            // Si es abierta => answer_text
            // Si es opción múltiple => question_option_id
        ]);

        // Lógica para saber si la pregunta es abierta o no
        $question = Question::findOrFail($request->question_id);

        $data = [
            'question_id' => $question->id,
            'user_id' => auth()->id(),
        ];

        if ($question->question_type === 'abierta') {
            $data['answer_text'] = $request->answer_text;
        } else {
            $data['question_option_id'] = $request->question_option_id;
        }

        Answer::create($data);

        return redirect()->back()->with('success', 'Respuesta guardada.');
    }
}
