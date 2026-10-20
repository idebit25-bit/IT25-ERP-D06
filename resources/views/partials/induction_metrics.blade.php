<style>
    .induction-metrics { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 15px; margin: 20px 0; }
    .induction-metric { background: #fff; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px 16px; color: #222; }
    .induction-metric strong { display: block; font-size: 26px; margin-bottom: 8px; font-weight: 700; }
    .induction-metric span { font-size: 12px; line-height: 1.5; }
    .induction-chart { background: #fff; border: 1px solid #dee2e6; border-radius: 8px; padding: 20px; color: #333; margin-bottom: 25px; }
    .induction-chart-title { display: block; color: #222; font-size: 14px; margin-bottom: 15px; }
    .induction-chart-row { display: grid; grid-template-columns: 115px minmax(0, 1fr) 35px; align-items: center; gap: 10px; font-size: 12px; margin: 12px 0; }
    .induction-chart-track { background: #e9ecef; height: 10px; border: 1px solid #dcdcdc; border-radius: 10px; overflow: hidden; }
    .induction-chart-fill { height: 100%; background: #3498db; border-radius: 10px; }
    .induction-chart-row > span:last-child { text-align: right; font-weight: bold; color: #495057; }
    .induction-chart-note { font-size: 12px; line-height: 1.6; color: #555; margin-top: 15px; }
    /* En el panel se conservan las tarjetas negras que ya usan los accesos. */
    .main-content .induction-metric { background: var(--button-black, #1a1a1a); color: #fff; border: 2px solid #666; border-radius: 15px; }
    .main-content .induction-metric span { color: #b8b8b8; }
    .induction-followup { background: #c4c4c4; border-radius: 15px; padding: 25px; margin-bottom: 30px; color: #222; }
    .induction-followup h3 { font-size: 18px; margin: 0 0 10px; }
    .induction-followup > p { font-size: 13px; line-height: 1.6; margin: 0 0 20px; color: #333; }
    .induction-followup-table { background: #fff; border: 1px solid #dee2e6; border-radius: 8px; overflow-x: auto; }
    .induction-followup table { width: 100%; min-width: 440px; text-align: left; font-size: 13px; border-collapse: collapse; }
    .induction-followup th { background: #222; color: #fff; padding: 12px; font-size: 12px; }
    .induction-followup td { padding: 12px; border-bottom: 1px solid #eee; color: #333; }
    .induction-followup tr:last-child td { border-bottom: 0; }
    .induction-followup button { background: #2a2a2a; color: #fff; font: inherit; font-size: 12px; padding: 8px 14px; border: 0; border-radius: 6px; cursor: pointer; }
    .induction-followup button:hover { background: #444; }
    .induction-followup button:focus-visible { outline: 2px solid #3498db; outline-offset: 2px; }
    @media (max-width: 600px) {
        .induction-metrics { grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
        .induction-metric { padding: 15px 12px; }
        .induction-metric strong { font-size: 22px; }
        .induction-chart, .induction-followup { padding: 15px; }
        .induction-chart-row { grid-template-columns: 95px minmax(0, 1fr) 25px; }
    }
</style>
<div class="induction-metrics">
    <div class="induction-metric"><strong>{{ $stats['users'] }}</strong><span>Participantes activos</span></div>
    <div class="induction-metric"><strong>{{ $stats['average'] }}%</strong><span>Avance promedio de asignados</span></div>
    <div class="induction-metric"><strong>{{ $stats['completed'] }}</strong><span>Inducciones completas</span></div>
    <div class="induction-metric"><strong>{{ $stats['overdue'] }}</strong><span>Con atraso ({{ config('induction.delay_days') }} días)</span></div>
    <div class="induction-metric"><strong>{{ $stats['accuracy'] === null ? 'Sin datos' : $stats['accuracy'].'%' }}</strong><span>Aciertos en {{ $stats['scored'] }} respuestas calificables</span></div>
</div>
<div class="induction-chart" role="img" aria-label="Participantes: {{ $stats['not_started'] }} sin iniciar, {{ $stats['in_progress'] }} en proceso, {{ $stats['completed'] }} completos y {{ $stats['unassigned'] }} sin módulos asignados.">
    <strong class="induction-chart-title">Estado de las inducciones</strong>
    @foreach(['not_started' => 'Sin iniciar', 'in_progress' => 'En proceso', 'completed' => 'Completadas', 'unassigned' => 'Sin asignación'] as $key => $label)
        <div class="induction-chart-row">
            <span>{{ $label }}</span>
            <div class="induction-chart-track"><div class="induction-chart-fill" style="width: {{ $stats['users'] ? round($stats[$key] / $stats['users'] * 100) : 0 }}%;"></div></div>
            <span>{{ $stats[$key] }}</span>
        </div>
    @endforeach
    <p class="induction-chart-note">El avance mide módulos completados. Los aciertos consideran respuestas de opción múltiple calificadas; las preguntas abiertas no se califican automáticamente.</p>
</div>
