@extends('layout.wrapper')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        @include('misc.heading-crumbs')
        <div class="col-md-12 col-lg-5 align-self-center text-right">
            <a href="{{ url('/support-agents/' . $agent->id . '/edit') }}" class="btn btn-outline-secondary btn-sm">Editar</a>
            <a href="{{ url('/support-agents') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-lg-10 m-auto">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(!empty($lastRunStatus))
                <div class="alert alert-info">{{ $lastRunStatus }}</div>
            @endif

            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">Probar agente IA</h5>
                    <p class="text-muted mb-3">
                        Este entorno simula respuestas del agente usando su prompt de identidad y articulos de las categorias KB seleccionadas.
                    </p>

                    <div class="mb-2">
                        <span class="label label-outline-info">Agente: {{ $agent->agent_name }}</span>
                        <span class="label label-outline-default">Categorias KB: {{ $agent->kbCategories->count() }}</span>
                    </div>

                    <form method="POST" action="{{ url('/support-agents/' . $agent->id . '/test') }}">
                        @csrf

                        <div class="form-group">
                            <label>Audiencia de prueba</label>
                            <select name="audience" class="form-control" required>
                                <option value="team" {{ ($audience ?? old('audience', 'team')) === 'team' ? 'selected' : '' }}>Equipo interno</option>
                                <option value="client" {{ ($audience ?? old('audience', 'team')) === 'client' ? 'selected' : '' }}>Cliente</option>
                            </select>
                            <small class="text-muted">Usa Cliente para validar lo que veria el cliente segun visibilidad y chat habilitado.</small>
                        </div>

                        <div class="form-group">
                            <label>Pregunta</label>
                            <textarea name="question" class="form-control" rows="5" maxlength="5000" required>{{ old('question', $question ?? '') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-danger">Generar respuesta</button>
                    </form>
                </div>
            </div>

            @if(!empty($answer))
                <div class="card mb-3">
                    <div class="card-body">
                        <h5 class="card-title">Respuesta del agente</h5>
                        <div class="alert alert-light" style="white-space: pre-wrap;">{{ $answer }}</div>
                    </div>
                </div>
            @endif

            @if(!empty($sources))
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Fuentes usadas</h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Fuente</th>
                                        <th>Articulo</th>
                                        <th>Categoria</th>
                                        <th>Visibilidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($sources as $source)
                                        <tr>
                                            <td>{{ $source['code'] }}</td>
                                            <td>{{ $source['title'] }}</td>
                                            <td>{{ $source['category'] }}</td>
                                            <td>{{ $source['visibility'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            @if(!empty($unansweredQueries) && $unansweredQueries->count() > 0)
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Consultas no respondidas (pendientes)</h5>
                        <p class="text-muted mb-3">
                            Estas consultas se guardan para mejorar contenido KB, prompt o futuras reglas del agente.
                        </p>
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th>Fecha</th>
                                        <th>Audiencia</th>
                                        <th>Motivo</th>
                                        <th>Consulta</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($unansweredQueries as $item)
                                        <tr>
                                            <td>{{ optional($item->created_at)->format('d/m/Y H:i') }}</td>
                                            <td>{{ $item->unanswered_audience }}</td>
                                            <td>{{ $item->unanswered_reason }}</td>
                                            <td>{{ \Illuminate\Support\Str::limit($item->unanswered_question, 180) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
