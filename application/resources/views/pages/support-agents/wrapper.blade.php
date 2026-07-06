@extends('layout.wrapper')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        @include('misc.heading-crumbs')
        <div class="col-md-12 col-lg-5 align-self-center text-right">
            <a href="{{ url('/support-agents/create') }}" class="btn btn-danger btn-sm">
                <i class="ti-plus"></i> Nuevo agente
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Visibilidad</th>
                                    <th>Estado</th>
                                    <th>Categorias KB</th>
                                    <th>Flags IA</th>
                                    <th>Probar</th>
                                    <th class="text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($agents as $agent)
                                    <tr>
                                        <td>
                                            <strong>{{ $agent->agent_name }}</strong>
                                        </td>
                                        <td>
                                            @if($agent->agent_visibility === 'team')
                                                <span class="label label-outline-warning">Solo equipo</span>
                                            @elseif($agent->agent_visibility === 'client')
                                                <span class="label label-outline-info">Clientes</span>
                                            @else
                                                <span class="label label-outline-success">Todos</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($agent->is_active)
                                                <span class="label label-outline-success">Activo</span>
                                            @else
                                                <span class="label label-outline-default">Inactivo</span>
                                            @endif
                                        </td>
                                        <td>{{ $agent->kb_categories_count }}</td>
                                        <td>
                                            <small>
                                                Chat: {{ $agent->allow_client_chat ? 'Si' : 'No' }}<br>
                                                Tickets IA: {{ $agent->allow_ticket_suggestions ? 'Si' : 'No' }}<br>
                                                Docs: {{ $agent->allow_document_sources ? 'Si' : 'No' }}
                                            </small>
                                        </td>
                                        <td>
                                            <a href="{{ url('/support-agents/' . $agent->id . '/test') }}" class="btn btn-info btn-sm">
                                                <i class="ti-control-play"></i> PROBAR IA
                                            </a>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ url('/support-agents/' . $agent->id . '/edit') }}" class="btn btn-outline-success btn-sm">Editar</a>
                                            <form method="POST" action="{{ url('/support-agents/' . $agent->id) }}" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Eliminar este agente?');">Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">No hay agentes creados</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $agents->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
