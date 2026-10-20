<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Page;
use App\Models\QuestionUserResponse;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Muestra la lista de usuarios (incluyendo los de baja) paginados de 10 en 10.
     */
    public function index()
    {
        // 1. Traemos TODOS los usuarios en un array crudo de PHP para procesarlos primero
        $allUsers = User::withTrashed()
            ->withCount('inductionViewed')
            ->get();

        // 2. Procesamos los datos usando PHP puro sin tocar objetos complejos de Laravel
        $processedUsers = $allUsers->map(function ($user) {
            // --- NUEVO: Formateamos la fecha de registro para la vista y exportaciones ---
            $user->fecha_registro = $user->created_at
                ? $user->created_at->format('d/m/Y H:i')
                : '---';

            if ($user->role === 'user') {
                // Agrupamos las condiciones con una función anónima
                $totalInductionForUser = Page::where(function ($query) use ($user) {
                    $query->where('destinado_a', $user->tipo_usuario)
                        ->orWhere('destinado_a', 'Ambos');
                })->count();

                $pagesViewed = $user->induction_viewed_count;

                $user->inductionViewedPercentage = $totalInductionForUser > 0
                    ? round(($pagesViewed / $totalInductionForUser) * 100)
                    : 0;
            } else {
                $user->inductionViewedPercentage = null;
            }
            return $user;
        });

        // 3. Creamos una paginación manual sobre la colección ya procesada.
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $perPage = 10;

        // CORRECCIÓN: Usamos ->values() para retornar una estructura de colección en lugar de un array nativo plano
        $currentItems = $processedUsers->slice(($currentPage - 1) * $perPage, $perPage)->values();

        // Creamos el objeto paginador definitivo que sí o sí tiene el método links()
        $users = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            $processedUsers->count(),
            $perPage,
            $currentPage,
            [
                'path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()
            ]
        );

        // Enviamos tanto los paginados ($users) como TODOS ($processedUsers renombrado a allUsers)
        $allUsers = $processedUsers;

        return view('users.index', compact('users', 'allUsers'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required',
            'email'            => 'required|email|unique:users',
            'password'         => 'required|min:6',
            'role'             => 'required|in:admin,user',
            'tipo_usuario'     => 'required|in:Colaborador,Estadía',
            'tipo_colaborador' => 'nullable|string|max:255', // <-- CORREGIDO
        ]);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);

        User::create($data);

        return redirect()->route('users.index')->with('success', 'Usuario creado exitosamente.');
    }

    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|unique:users,email,' . $user->id,
            'role'             => 'required|in:admin,user',
            'tipo_usuario'     => 'required_if:role,user|nullable|in:Colaborador,Estadía',
            'tipo_colaborador' => 'nullable|string|max:255', // <-- CORREGIDO
        ]);

        $data = $request->all();

        if ($data['role'] === 'admin') {
            $data['tipo_usuario'] = 'Ambos';
            $data['tipo_colaborador'] = null; // <-- CORREGIDO
        }

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            $data['password'] = $user->password;
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Usuario actualizado exitosamente.');
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Usuario dado de baja exitosamente.');
    }

    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('users.index')->with('success', 'Usuario reactivado exitosamente.');
    }

    public function forceDestroy($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();

        return redirect()->route('users.index')->with('success', 'Usuario eliminado definitivamente.');
    }

    public function downloadResponses($id)
    {
        $user = User::withTrashed()->findOrFail($id);

        $responses = QuestionUserResponse::with('question')
            ->where('user_id', $user->id)
            ->get();

        if ($responses->isEmpty()) {
            return redirect()->back()->with('error', 'Este usuario no tiene respuestas registradas.');
        }

        $filename = 'respuestas_usuario_' . $user->id . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function () use ($responses) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Pregunta', 'Opción Correcta', 'Respuesta del Usuario', 'Fecha']);
            foreach ($responses as $resp) {
                $question = $resp->question;
                $title = $question ? $question->question_text : 'N/A';
                $correct = ($question && $question->question_type === 'abierta') ? 'no aplica' : ($question ? $question->correct_option : 'N/A');
                $fecha = $resp->created_at->setTimezone('America/Mexico_City')->toDateTimeString();
                fputcsv($file, [$title, $correct, $resp->answer, $fecha]);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, $filename, $headers);
    }

    public function startInduction()
    {
        $currentUser = Auth::user();

        if ($currentUser->role === 'admin') {
            $pages = Page::orderBy('order', 'asc')->get();
        } else {
            $pages = Page::where(function ($query) use ($currentUser) {
                $query->where('destinado_a', $currentUser->tipo_usuario)
                    ->orWhere('destinado_a', 'Ambos');
            })->orderBy('order', 'asc')->get();
        }

        $questions = Question::orderBy('order', 'asc')->get();

        $items = $pages->concat($questions)->sortBy('order')->values();
        $firstItem = $items->first();

        if (!$firstItem) {
            return back()->with('error', 'No se encontró ningún contenido asignado a tu perfil de inducción.');
        }

        return redirect()->route('induction.show', $firstItem->id);
    }
}
