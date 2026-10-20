<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class PageController extends Controller
{
    public function index()
    {
        if (Auth::user() && Auth::user()->role === 'admin') {
            $pages = Page::with('questions')->orderBy('order', 'asc')->get();
        } else {
            $pages = Page::with(['questions' => function ($q) {
                $q->where('is_active', 1);
            }])->orderBy('order', 'asc')->get();
        }

        return view('pages.index', compact('pages'));
    }

    /**
     * Valida si un usuario tiene acceso a una página específica analizando todo el arreglo destinado_a.
     */
    private function userCanAccessPage($user, $page)
    {
        if (!$user) return false;

        // Helper para remover tildes y convertir a minúsculas en UTF-8
        $clean = function ($value) {
            $str = mb_strtolower(trim((string)$value), 'UTF-8');
            return str_replace(['í', 'á', 'é', 'ó', 'ú', 'ñ'], ['i', 'a', 'e', 'o', 'u', 'n'], $str);
        };

        $roleClean = $clean($user->role ?? '');
        $tipoClean = $clean($user->tipo_usuario ?? '');
        $puestoClean = $clean($user->puesto ?? '');
        $areaClean = $clean($user->area ?? '');

        if ($roleClean === 'admin' || $tipoClean === 'admin') {
            return true;
        }

        $destinadoRaw = $page->destinado_a;
        $listaPuestos = [];

        if (is_string($destinadoRaw)) {
            if (str_starts_with(trim($destinadoRaw), '[') || str_starts_with(trim($destinadoRaw), '{')) {
                $decoded = json_decode($destinadoRaw, true);
                $listaPuestos = is_array($decoded) ? $decoded : [$destinadoRaw];
            } else {
                $listaPuestos = explode(',', $destinadoRaw);
            }
        } else {
            $listaPuestos = (array) $destinadoRaw;
        }

        // Normalizar la lista asignada a la página
        $puestosNormalizados = array_map($clean, $listaPuestos);

        // Permiso universal (Ambos, Todos, General)
        if (array_intersect(['ambos', 'todos', 'general'], $puestosNormalizados)) {
            return true;
        }

        // Evaluar perfil Estadía (tolera 'estadia' y 'estadía')
        $esEstadia = str_contains($roleClean, 'estadia') ||
            str_contains($tipoClean, 'estadia') ||
            str_contains($puestoClean, 'estadia');

        if ($esEstadia) {
            return in_array('estadia', $puestosNormalizados);
        }

        // Evaluar perfiles/puestos de Colaboradores
        if (in_array('colaborador', $puestosNormalizados)) {
            return true;
        }

        // Verificar si el puesto, área o tipo exacto coincide
        return ($puestoClean && in_array($puestoClean, $puestosNormalizados)) ||
            ($areaClean && in_array($areaClean, $puestosNormalizados)) ||
            ($tipoClean && in_array($tipoClean, $puestosNormalizados));
    }

    public function startInduction()
    {
        $user = Auth::user();

        if (strtolower($user->role ?? '') === 'admin' || strtolower($user->tipo_usuario ?? '') === 'admin') {
            $firstPage = Page::orderBy('order', 'asc')->first();
            if ($firstPage) {
                $identifier = !empty($firstPage->slug) ? $firstPage->slug : $firstPage->id;
                return redirect()->route('induction.show', ['identifier' => $identifier]);
            }
            return redirect()->route('pages.index');
        }

        $pages = Page::orderBy('order', 'asc')->get();
        $firstPage = $pages->first(fn($page) => $this->userCanAccessPage($user, $page));

        if (!$firstPage) {
            $firstPage = $pages->first();
        }

        if ($firstPage) {
            $identifier = !empty($firstPage->slug) ? $firstPage->slug : $firstPage->id;
            return redirect()->route('induction.show', ['identifier' => $identifier]);
        }

        return redirect()->route('lobby')->with('error', 'No hay módulos de inducción disponibles.');
    }

    public function showInduction(mixed $identifier)
    {
        $currentUser = Auth::user();

        if ($currentUser && ($currentUser->role === 'admin' || $currentUser->tipo_usuario === 'admin')) {
            $allPagesAllowed = Page::orderBy('order', 'asc')->get();
        } else {
            $allPages = Page::orderBy('order', 'asc')->get();
            $allPagesAllowed = $allPages->filter(fn($page) => $this->userCanAccessPage($currentUser, $page))->values();
        }

        $currentIndex = $allPagesAllowed->search(function ($item) use ($identifier) {
            return $item->slug == $identifier || $item->id == $identifier;
        });

        if ($currentIndex === false) {
            $firstValid = $allPagesAllowed->first();
            return $firstValid
                ? redirect()->route('induction.show', $firstValid->slug ?? $firstValid->id)
                : redirect()->route('lobby')->with('error', 'No tienes acceso a esta sección.');
        }

        $item = $allPagesAllowed[$currentIndex];

        if ($identifier == $item->id && !empty($item->slug)) {
            return redirect()->route('induction.show', $item->slug);
        }

        $questions = $item->questions()->where('is_active', 1)->get();

        if (Auth::check() && Auth::user()->role === 'user') {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            if (method_exists($user, 'inductionViewed')) {
                $user->inductionViewed()->syncWithoutDetaching([$item->id]);
            }
        }

        // Obtener páginas previamente vistas por el usuario
        $paginasVistasIds = ($currentUser && method_exists($currentUser, 'inductionViewed'))
            ? $currentUser->inductionViewed()->pluck('pages.id')->toArray()
            : [];

        // Validar estado de completado de la página actual
        $isCompleted = false;
        if ($currentUser) {
            if ($currentUser->role === 'admin') {
                $isCompleted = true;
            } elseif ($questions->count() > 0) {
                $answeredCount = \App\Models\QuestionUserResponse::whereIn('question_id', $questions->pluck('id'))
                    ->where('user_id', $currentUser->id)
                    ->count();
                $isCompleted = ($answeredCount === $questions->count());
            } else {
                $isCompleted = in_array($item->id, $paginasVistasIds);
            }
        }

        // Construir barra lateral con lógica de progreso actualizada
        $sidebarProgress = [];
        $canAccessNext = true;

        foreach ($allPagesAllowed as $p) {
            $pQuestions = $p->questions()->where('is_active', 1)->get();
            $pCompleted = false;

            if ($currentUser->role === 'admin') {
                $pCompleted = true;
            } elseif ($pQuestions->count() > 0) {
                $pAnswered = \App\Models\QuestionUserResponse::whereIn('question_id', $pQuestions->pluck('id'))
                    ->where('user_id', $currentUser->id)
                    ->count();
                $pCompleted = ($pAnswered === $pQuestions->count());
            } else {
                $pCompleted = in_array($p->id, $paginasVistasIds);
            }

            $isActive = ($p->id == $item->id);

            $sidebarProgress[] = [
                'id'           => $p->id,
                'slug'         => $p->slug,
                'title'        => $p->title,
                'is_active'    => $isActive,
                'is_completed' => $pCompleted,
                'is_locked'    => !$canAccessNext && !$isActive && $currentUser->role !== 'admin',
            ];

            if (!$pCompleted) {
                $canAccessNext = false;
            }
        }

        $prevPageUrl = null;
        $nextPageUrl = null;

        if ($currentIndex > 0) {
            $prevItem = $allPagesAllowed[$currentIndex - 1];
            $prevPageUrl = route('induction.show', !empty($prevItem->slug) ? $prevItem->slug : $prevItem->id);
        }

        if ($currentIndex < $allPagesAllowed->count() - 1) {
            $nextItem = $allPagesAllowed[$currentIndex + 1];
            $nextPageUrl = route('induction.show', !empty($nextItem->slug) ? $nextItem->slug : $nextItem->id);
        } else {
            $nextPageUrl = ($currentUser && $currentUser->role === 'admin') ? null : route('induction.completed');
        }

        return view('pages.template', [
            'page'            => $item,
            'item'            => $item,
            'pages'           => $allPagesAllowed,
            'questions'       => $questions,
            'isCompleted'     => $isCompleted,
            'prevPageUrl'     => $prevPageUrl,
            'nextPageUrl'     => $nextPageUrl,
            'sidebarProgress' => $sidebarProgress,
        ]);
    }

    public function create()
    {
        return view('pages.create');
    }

    public function store(Request $request)
    {
        if ($request->has('slug')) {
            $request->merge(['slug' => \Illuminate\Support\Str::slug($request->slug)]);
        }

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'slug'         => 'required|string|unique:pages,slug',
            'order'        => 'nullable|integer|min:1',
            'destinado_a'  => 'required|array',
            'destinado_a.*' => 'string',
            'video_url'    => 'nullable|url',
            'video_file'   => 'nullable|file|mimes:mp4,mov,ogg,qt,webm,avi|max:512000', // Hasta 500MB
            'content'      => 'nullable|string',
            'attachment'   => 'nullable|file|mimes:pdf,jpg,png,jpeg,doc,docx|max:10240',
        ]);

        if (empty($validated['order'])) {
            $maxOrder = Page::max('order');
            $validated['order'] = $maxOrder ? $maxOrder + 1 : 1;
        }

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('attachments', 'public');
        }

        if ($request->hasFile('video_file')) {
            $validated['video_file'] = $request->file('video_file')->store('videos', 'public');
        }

        Page::create($validated);

        return redirect()->route('pages.index')->with('success', 'Página creada correctamente.');
    }

    public function edit(Page $page)
    {
        return view('pages.edit', compact('page'));
    }

    public function update(Request $request, mixed $id)
    {
        $page = Page::findOrFail($id);

        if ($request->has('slug')) {
            $request->merge(['slug' => \Illuminate\Support\Str::slug($request->slug)]);
        }

        $request->validate([
            'title'        => 'required|string|max:255',
            'slug'         => 'required|string|max:255|unique:pages,slug,' . $page->id,
            'destinado_a'  => 'required|array',
            'destinado_a.*' => 'string',
            'order'        => 'nullable|integer|min:1',
            'video_url'    => 'nullable|string|max:255',
            'video_file'   => 'nullable|file|mimes:mp4,mov,ogg,qt,webm,avi|max:512000',
            'content'      => 'nullable|string',
            'attachment'   => 'nullable|file|max:50240',
        ]);

        $pageData = $request->only(['title', 'slug', 'destinado_a', 'order', 'video_url', 'content']);

        if ($request->hasFile('attachment')) {
            if ($page->attachment && Storage::disk('public')->exists($page->attachment)) {
                Storage::disk('public')->delete($page->attachment);
            }
            $pageData['attachment'] = $request->file('attachment')->store('attachments', 'public');
        }

        if ($request->hasFile('video_file')) {
            if ($page->video_file && Storage::disk('public')->exists($page->video_file)) {
                Storage::disk('public')->delete($page->video_file);
            }
            $pageData['video_file'] = $request->file('video_file')->store('videos', 'public');
        }

        $page->update($pageData);
        return redirect()->route('pages.index')->with('success', 'Página actualizada con éxito.');
    }

    public function destroy(Page $page)
    {
        $page->delete();
        $this->reorderAll();
        return redirect()->route('pages.index')->with('success', 'Página eliminada.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'ids'   => 'required|array',
            'ids.*' => 'integer|exists:pages,id'
        ]);
        $ids = $request->input('ids');
        DB::transaction(function () use ($ids) {
            foreach ($ids as $index => $id) {
                Page::where('id', $id)->update(['order' => $index + 1]);
            }
        });
        return response()->json(['success' => true, 'message' => 'Orden actualizado.']);
    }

    public function sort(Request $request)
    {
        $sortedItems = $request->input('sortedItems');
        if (!$sortedItems) return response()->json(['success' => false], 400);
        foreach ($sortedItems as $index => $item) {
            if ($item['type'] === 'page') {
                Page::where('id', $item['id'])->update(['order' => $index + 1]);
            }
        }
        return response()->json(['success' => true]);
    }

    private function reorderAll()
    {
        $pages = Page::orderBy('order')->get();
        foreach ($pages as $index => $page) {
            $page->order = $index + 1;
            $page->save();
        }
    }

    public function show(string $slug)
    {
        $user = Auth::user();
        $page = Page::with(['questions' => function ($q) {
            $q->where('is_active', 1)->orderBy('order', 'asc');
        }])->where('slug', $slug)->firstOrFail();

        if ($user && method_exists($user, 'inductionViewed')) {
            $user->inductionViewed()->syncWithoutDetaching([$page->id]);
        }

        $questions = $page->questions;
        $totalQuestions = $questions->count();
        $answeredCount = 0;

        if ($user) {
            foreach ($questions as $q) {
                if ($q->userResponses()->where('user_id', $user->id)->exists()) {
                    $answeredCount++;
                }
            }
        }

        if ($user && $user->role === 'admin') {
            $isCompleted = true;
            $prevPage = Page::where('order', '<', $page->order)->orderBy('order', 'desc')->first();
            $nextPage = Page::where('order', '>', $page->order)->orderBy('order', 'asc')->first();
            $allPages = Page::orderBy('order', 'asc')->get();
        } else {
            if ($totalQuestions > 0) {
                $isCompleted = ($answeredCount === $totalQuestions);
            } else {
                $paginasVistasIds = ($user && method_exists($user, 'inductionViewed'))
                    ? $user->inductionViewed()->pluck('pages.id')->toArray()
                    : [];
                $isCompleted = in_array($page->id, $paginasVistasIds);
            }

            // Usamos la colección con el filtro php flexible para omitir problemas de casing en SQL
            $allPages = Page::orderBy('order', 'asc')->get()->filter(function ($p) use ($user) {
                return $this->userCanAccessPage($user, $p);
            })->values();

            $currentIndex = $allPages->search(function ($item) use ($page) {
                return $item->id === $page->id;
            });

            $prevPage = ($currentIndex !== false && $currentIndex > 0) ? $allPages[$currentIndex - 1] : null;
            $nextPage = ($currentIndex !== false && $currentIndex < $allPages->count() - 1) ? $allPages[$currentIndex + 1] : null;
        }

        return view('pages.template', [
            'page'            => $page,
            'item'            => $page,
            'pages'           => $allPages,
            'questions'       => $questions,
            'isCompleted'     => $isCompleted,
            'prevPageUrl'     => $prevPage ? route('pages.show', $prevPage->slug) : null,
            'nextPageUrl'     => $nextPage ? route('pages.show', $nextPage->slug) : (($user && $user->role === 'admin') ? null : route('induction.completed')),
        ]);
    }

    public function showLobby()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Obtener páginas ordenadas
        $pages = Page::with('questions')->orderBy('order', 'asc')->get();
        $paginasAsignadas = $pages->filter(fn($page) => $this->userCanAccessPage($user, $page));

        $totalAsignadas = $paginasAsignadas->count();
        $completadas = 0;

        if ($totalAsignadas > 0 && $user) {
            // Obtenemos los IDs de las páginas que el usuario ya visitó
            $paginasVistasIds = method_exists($user, 'inductionViewed')
                ? $user->inductionViewed()->pluck('pages.id')->toArray()
                : [];

            foreach ($paginasAsignadas as $p) {
                if ($user->role === 'admin') {
                    $completadas++;
                    continue;
                }

                $pQuestions = $p->questions->where('is_active', 1);

                if ($pQuestions->count() > 0) {
                    // Si tiene preguntas, DEBE haber respondido TODAS
                    $pAnswered = \App\Models\QuestionUserResponse::whereIn('question_id', $pQuestions->pluck('id'))
                        ->where('user_id', $user->id)
                        ->count();

                    if ($pAnswered === $pQuestions->count()) {
                        $completadas++;
                    }
                } else {
                    // Si NO tiene preguntas, DEBE haber visitado la página al menos una vez
                    if (in_array($p->id, $paginasVistasIds)) {
                        $completadas++;
                    }
                }
            }
        }

        $progreso = $totalAsignadas > 0 ? round(($completadas / $totalAsignadas) * 100) : 0;

        // Normalización para verificar si es perfil de Estadía
        $clean = function ($value) {
            $str = mb_strtolower(trim((string)$value), 'UTF-8');
            return str_replace(['í', 'á', 'é', 'ó', 'ú', 'ñ'], ['i', 'a', 'e', 'o', 'u', 'n'], $str);
        };

        $role = $clean($user->role ?? '');
        $tipo = $clean($user->tipo_usuario ?? '');
        $puesto = $clean($user->puesto ?? '');

        $esEstadia = str_contains($role, 'estadia') ||
            str_contains($tipo, 'estadia') ||
            str_contains($puesto, 'estadia');

        return view('induction.lobby', compact('user', 'esEstadia', 'progreso'));
    }

    public function completedInduction()
    {
        return view('induction.completed');
    }

    public function downloadAttachment($id)
    {
        $page = Page::findOrFail($id);
        $filePath = public_path('storage/' . $page->attachment);

        if (!$page->attachment || !file_exists($filePath)) {
            return back()->with('error', 'El archivo no existe.');
        }

        return response()->download($filePath);
    }
}
