<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Inducción Unificada - Grupo I-DEB</title>

    <style>

        body { margin: 0; padding: 0; background-color: #a0a0a0; text-align: center; font-family: Arial, sans-serif; }

        .header { background-color: #2a2a2a; padding: 10px; display: flex; align-items: center; position: fixed; top: 0; width: 100%; z-index: 1000; }

        .logo-link img { width: 100px; }

        .welcome-center { position: absolute; left: 50%; transform: translateX(-50%); color: white; font-size: 18px; font-weight: bold; }

        .logout { background-color: red; color: white; padding: 10px 25px; border: none; cursor: pointer; border-radius: 5px; }

        .header form.logout-form { margin-left: auto; margin-right: 20px; }

        .nav-buttons { position: fixed; top: 150px; width: 100%; display: flex; justify-content: space-between; padding: 0 30px; z-index: 100; box-sizing: border-box; }

        .nav-buttons button { background-color: black; color: white; padding: 15px; border: 3px solid gray; cursor: pointer; width: 180px; }

        .nav-buttons button:disabled { opacity: 0.5; cursor: not-allowed; }

        .container { padding: 20px; margin-top: 150px; }

        .page-box { background-color: white; padding: 40px; display: inline-block; border: 3px solid gray; max-width: 900px; width: 95%; text-align: center; }

        .content-section { margin-top: 25px; text-align: left; line-height: 1.6; }

        .attachment-section { margin-top: 30px; border-top: 1px solid #eee; padding-top: 20px; }

        .pdf-viewer { width: 100%; height: 500px; border: 1px solid #ccc; margin-top: 15px; }

        .questions-section { margin-top: 40px; text-align: left; background: #f9f9f9; padding: 20px; border-radius: 10px; }

        .question-block { margin-bottom: 25px; padding: 15px; border-left: 5px solid #2a2a2a; background: white; }

        .answer-btn { background-color: #2a2a2a; color: white; padding: 10px 20px; border: none; cursor: pointer; border-radius: 5px; }

        .answered-message { color: #28a745; font-weight: bold; background: #e9f7ef; padding: 10px; border-radius: 5px; }

    </style>

</head>

<body>

    <div class="header">

        <a href="{{ Auth::user()->role === 'admin' ? url('/admin') : '#' }}" class="logo-link">

            <img src="{{ asset('images/logob.png') }}" alt="Logo">

        </a>

        <div class="welcome-center">¡Bienvenido, {{ Auth::user()->name }}!</div>

        <form action="{{ route('logout') }}" method="POST" class="logout-form">

            @csrf

            <button type="submit" class="logout">SALIR</button>

        </form>

    </div>



    <div class="nav-buttons">

        <button onclick="window.location.href='{{ $prevUrl ?? '#' }}'" {{ !isset($prevUrl) ? 'disabled' : '' }}>← VOLVER</button>

       

        @if(isset($nextUrl))

            @if($isCompleted)

                <button onclick="window.location.href='{{ $nextUrl }}'">SIGUIENTE →</button>

            @else

                <button disabled title="Responde las preguntas para continuar">SIGUIENTE (Bloqueado)</button>

            @endif

        @endif

    </div>



    <div class="container">

        <div class="page-box">

            <h2>{{ $item->title }}</h2>



            @if($item->video_url)

                @php

                    $videoId = '';

                    if(strpos($item->video_url, 'v=') !== false) {

                        parse_str(parse_url($item->video_url, PHP_URL_QUERY), $vars);

                        $videoId = $vars['v'] ?? '';

                    } elseif(strpos($item->video_url, 'youtu.be/') !== false) {

                        $videoId = basename(parse_url($item->video_url, PHP_URL_PATH));

                    }

                @endphp

                @if($videoId)

                    <iframe width="100%" height="450" src="https://www.youtube.com/embed/{{ $videoId }}" frameborder="0" allowfullscreen></iframe>

                @endif

            @endif



            @if($item->content)

                <div class="content-section">{!! $item->content !!}</div>

            @endif



            @if($item->attachment)

                <div class="attachment-section">

                    <h3>Documentación</h3>

                    <a href="{{ Storage::url($item->attachment) }}" target="_blank">Descargar Archivo</a>

                    @if(Str::endsWith(strtolower($item->attachment), '.pdf'))

                        <iframe src="{{ Storage::url($item->attachment) }}#toolbar=0" class="pdf-viewer"></iframe>

                    @endif

                </div>

            @endif



            @if($questions->count() > 0)

                <div class="questions-section">

                    <h3>Evaluación</h3>

                    @foreach($questions as $question)

                        <div class="question-block">

                            <h4>{{ $question->question_text }}</h4>

                            @php

                                // USANDO OPCIÓN B: userResponses()

                                $response = $question->userResponses()->where('user_id', Auth::id())->first();

                            @endphp



                            @if($response)

                                <p class="answered-message">✓ Respondido: {{ $response->answer }}</p>

                            @else

                                <form action="{{ route('questions.answer') }}" method="POST">

                                    @csrf

                                    <input type="hidden" name="question_id" value="{{ $question->id }}">

                                    @if($question->question_type === 'opcion_multiple')

                                        @foreach(json_decode($question->options, true) as $opt)

                                            <label><input type="radio" name="answer" value="{{ $opt }}" required> {{ $opt }}</label><br>

                                        @endforeach

                                    @else

                                        <textarea name="answer" rows="2" style="width:100%" required></textarea>

                                    @endif

                                    <button type="submit" class="answer-btn">Enviar</button>

                                </form>

                            @endif

                        </div>

                    @endforeach

                </div>

            @endif

        </div>

    </div>

</body>

</html>