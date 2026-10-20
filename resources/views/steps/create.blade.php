<x-app-layout>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold text-white mb-4">Agregar Nuevo Paso</h2>

        <form method="POST" action="{{ route('steps.store') }}" class="bg-gray-700 p-6 rounded-lg shadow-lg">
            @csrf

            <div class="mb-4">
                <label class="block text-white">Título</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="w-full p-2 bg-gray-600 text-white rounded">
            </div>

            <div class="mb-4">
                <label class="block text-white">Descripción</label>
                <textarea name="description" class="w-full p-2 bg-gray-600 text-white rounded">{{ old('description') }}</textarea>
            </div>

            <div class="mb-4">
                <label class="block text-white">Tipo de Contenido</label>
                <select name="content_type" class="w-full p-2 bg-gray-600 text-white rounded">
                    <option value="video">Video</option>
                    <option value="document">Documento</option>
                    <option value="quiz">Cuestionario</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-white">URL del Contenido (opcional)</label>
                <input type="url" name="content_url" value="{{ old('content_url') }}" class="w-full p-2 bg-gray-600 text-white rounded">
            </div>

            <div class="mb-4">
                <label class="block text-white">Orden</label>
                <input type="number" name="order" value="{{ old('order', 1) }}" required class="w-full p-2 bg-gray-600 text-white rounded">
            </div>

            <div class="mb-4">
                <label class="block text-white">Dependencia (opcional)</label>
                <select name="dependency_id" class="w-full p-2 bg-gray-600 text-white rounded">
                    <option value="">Sin dependencia</option>
                    @foreach($steps as $step)
                        <option value="{{ $step->id }}">{{ $step->title }}</option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="mt-4 bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">
                Guardar Paso
            </button>
            <a href="{{ route('steps.index') }}" class="ml-4 text-gray-300 hover:text-white">Cancelar</a>
        </form>
    </div>
</x-app-layout>
