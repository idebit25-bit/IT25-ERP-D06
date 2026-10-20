<?php

namespace App\Http\Controllers;

use App\Models\Step;
use Illuminate\Http\Request;

class StepController extends Controller {
    public function index() {
        $steps = Step::orderBy('order')->get();
        return view('steps.index', compact('steps'));
    }

    public function create() {
        $steps = Step::all();
        return view('steps.create', compact('steps'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:video,document,quiz',
            'content_url' => 'nullable|url',
            'order' => 'required|integer|min:1',
            'dependency_id' => 'nullable|exists:steps,id',
        ]);

        Step::create($validated);

        return redirect()->route('steps.index')->with('success', 'Paso agregado correctamente.');
    }

    public function edit(Step $step) {
        $steps = Step::all();
        return view('steps.edit', compact('step', 'steps'));
    }

    public function update(Request $request, Step $step) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'content_type' => 'required|in:video,document,quiz',
            'content_url' => 'nullable|url',
            'order' => 'required|integer|min:1',
            'dependency_id' => 'nullable|exists:steps,id',
        ]);

        $step->update($validated);

        return redirect()->route('steps.index')->with('success', 'Paso actualizado correctamente.');
    }

    public function destroy(Step $step) {
        $step->delete();
        return redirect()->route('steps.index')->with('success', 'Paso eliminado correctamente.');
    }
}
