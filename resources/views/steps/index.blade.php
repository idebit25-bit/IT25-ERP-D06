<x-app-layout>
    <div class="container mx-auto p-6">
        <h2 class="text-2xl font-bold text-white mb-4">Gestión de Pasos</h2>

        <a href="{{ route('steps.create') }}" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">Agregar Paso</a>

        <table class="w-full mt-4 bg-gray-700 text-white rounded-lg shadow-lg">
            <thead>
                <tr class="bg-gray-800">
                    <th class="p-2">Orden</th>
                    <th class="p-2">Título</th>
                    <th class="p-2">Tipo</th>
                    <th class="p-2">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($steps as $step)
                    <tr class="border-t border-gray-600">
                        <td class="p-2">{{ $step->order }}</td>
                        <td class="p-2">{{ $step->title }}</td>
                        <td class="p-2">{{ ucfirst($step->content_type) }}</td>
                        <td class="p-2">
                            <a href="{{ route('steps.edit', $step) }}" class="text-blue-400 hover:underline">Editar</a>
                            <form action="{{ route('steps.destroy', $step) }}" method="POST" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:underline">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
