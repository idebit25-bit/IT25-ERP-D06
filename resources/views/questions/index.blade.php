<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Preguntas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f0f0f0; }
        .container { background-color: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1); max-width: 900px; margin: auto; }
        h1 { margin-bottom: 20px; font-size: 24px; color: #333; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #f5f5f5; }
        .btn { padding: 8px 16px; border: none; cursor: pointer; text-decoration: none; display: inline-block; border-radius: 4px; font-size: 14px; }
        .btn-primary { background-color: #007bff; color: white; }
        .btn-warning { background-color: #ffc107; color: black; }
        .btn-danger { background-color: #dc3545; color: white; }
        .btn-sm { padding: 5px 10px; }
        .handle { cursor: move; color: #999; margin-right: 10px; }
        tr.sortable-ghost { opacity: 0.4; background-color: #e2e6ea; }
    </style>
</head>
<body>

<div class="container">
    <h1>Listado de Preguntas</h1>

    <div style="display: flex; justify-content: space-between; align-items: center;">
        <a href="{{ route('questions.create') }}" class="btn btn-primary">Crear nueva pregunta</a>
        <span style="color: #666; font-size: 13px;"><i class="fas fa-info-circle"></i> Arrastra el icono para reordenar</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">Orden</th>
                <th>Pregunta</th>
                <th>Tipo</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="sortable-questions">
            @foreach($questions as $q)
                <tr class="question-row" data-id="{{ $q->id }}">
                    <td class="text-center">
                        <i class="fas fa-grip-lines handle"></i>
                    </td>
                    <td>{{ $q->question_text }}</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $q->question_type)) }}</td>
                    <td>
                        <a href="{{ route('questions.edit', $q->id) }}" class="btn btn-warning btn-sm">Editar</a>
                        <form action="{{ route('questions.destroy', $q->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta pregunta?')">Eliminar</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script>
    const el = document.getElementById('sortable-questions');
    Sortable.create(el, {
        animation: 150,
        handle: '.handle', // Solo se puede arrastrar desde el icono
        ghostClass: 'sortable-ghost',
        onEnd: function() {
            let orders = [];
            document.querySelectorAll('.question-row').forEach((row, index) => {
                orders.push({
                    id: row.dataset.id,
                    order: index + 1
                });
            });

            // Enviar el nuevo orden al servidor vía AJAX
            fetch("{{ route('questions.reorder') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ orders: orders })
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    console.log("Orden guardado");
                }
            })
            .catch(error => console.error('Error:', error));
        }
    });
</script>

</body>
</html>