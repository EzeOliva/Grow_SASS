@extends('layout.wrapper')

@section('content')
<div class="container-fluid">
    <div class="row page-titles">
        @include('misc.heading-crumbs')
        <div class="col-md-12 col-lg-5 align-self-center text-right">
            @if($agent->id)
                <a href="{{ url('/support-agents/' . $agent->id . '/test') }}" class="btn btn-info btn-sm">
                    <i class="ti-control-play"></i> PROBAR IA
                </a>
            @endif
            <a href="{{ url('/support-agents') }}" class="btn btn-outline-secondary btn-sm">Volver</a>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-lg-10 m-auto">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card">
                <div class="card-body">
                    @php
                        $defaultIdentityPrompt = "Sos Violeta, agente virtual de soporte al cliente de Tasklist.\n\n"
                            . "Tu objetivo es resolver dudas con claridad, calidez y precision, usando la base de conocimiento disponible como fuente principal.\n\n"
                            . "Reglas de respuesta:\n"
                            . "1. Responde en espanol neutro, en tono profesional y amable.\n"
                            . "2. Si existe un procedimiento, explicalo paso a paso.\n"
                            . "3. Si faltan datos para ayudar, hace preguntas concretas antes de asumir.\n"
                            . "4. No inventes informacion: si no esta en la base, indicalo y sugiere escalar a un ticket.\n"
                            . "5. Priorizá respuestas breves y accionables.\n"
                            . "6. Cuando corresponda, cerra con una recomendacion clara del siguiente paso.\n\n"
                            . "Seguridad y cumplimiento:\n"
                            . "1. Nunca compartas datos sensibles (claves, tokens, contrasenas, datos de pago o informacion privada de terceros).\n"
                            . "2. Si el usuario pide acciones con riesgo (borrar, transferir, cambiar permisos, exponer datos), solicita validacion humana o deriva al equipo.\n"
                            . "3. No ejecutes acciones fuera del alcance de soporte ni afirmes haberlas ejecutado si no hay confirmacion del sistema.\n"
                            . "4. Si detectas posible fraude, suplantacion o abuso, corta la asistencia operativa y escala de inmediato al equipo.";
                    @endphp
                    <form method="POST" action="{{ $agent->id ? url('/support-agents/' . $agent->id) : url('/support-agents') }}">
                        @csrf
                        @if($agent->id)
                            @method('PUT')
                        @endif

                        <div class="form-group">
                            <label>Nombre del agente</label>
                            <input type="text" name="agent_name" class="form-control" maxlength="190" value="{{ old('agent_name', $agent->agent_name) }}" required>
                        </div>

                        <div class="form-group">
                            <label>Prompt de identidad</label>
                            <textarea name="agent_identity_prompt" class="form-control" rows="8" required>{{ old('agent_identity_prompt', $agent->agent_identity_prompt ?: $defaultIdentityPrompt) }}</textarea>
                            <small class="text-muted">Este prompt define personalidad, tono, limites y formato de respuesta.</small>
                        </div>

                        <div class="form-group">
                            <label>Visibilidad del agente</label>
                            <select name="agent_visibility" class="form-control" required>
                                <option value="team" {{ old('agent_visibility', $agent->agent_visibility) === 'team' ? 'selected' : '' }}>Solo equipo</option>
                                <option value="client" {{ old('agent_visibility', $agent->agent_visibility) === 'client' ? 'selected' : '' }}>Solo cliente</option>
                                <option value="everyone" {{ old('agent_visibility', $agent->agent_visibility) === 'everyone' ? 'selected' : '' }}>Equipo y cliente</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Categorias de base de conocimiento</label>
                            <select name="kbcategory_ids[]" class="form-control form-control-sm select2-basic select2-multiple" multiple data-placeholder="Selecciona una o mas categorias">
                                @foreach($categories as $category)
                                    <option value="{{ $category->kbcategory_id }}" {{ in_array($category->kbcategory_id, old('kbcategory_ids', $selectedCategories)) ? 'selected' : '' }}>
                                        {{ $category->kbcategory_title }} ({{ $category->kbcategory_visibility }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Podes buscar y seleccionar multiples categorias.</small>
                        </div>

                        <hr>

                        <h5 class="mb-3">Preparacion para IA</h5>

                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" {{ old('is_active', $agent->is_active) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="is_active">Agente activo</label>
                        </div>

                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="allow_client_chat" name="allow_client_chat" value="1" {{ old('allow_client_chat', $agent->allow_client_chat) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="allow_client_chat">Habilitar chat para cliente</label>
                        </div>

                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="allow_ticket_suggestions" name="allow_ticket_suggestions" value="1" {{ old('allow_ticket_suggestions', $agent->allow_ticket_suggestions) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="allow_ticket_suggestions">Habilitar sugerencias IA para tickets (futuro)</label>
                        </div>

                        <div class="custom-control custom-checkbox mb-4">
                            <input type="checkbox" class="custom-control-input" id="allow_document_sources" name="allow_document_sources" value="1" {{ old('allow_document_sources', $agent->allow_document_sources) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="allow_document_sources">Permitir documentos como fuente (estructura lista)</label>
                        </div>

                        <div class="alert alert-info">
                            Ya queda preparada la estructura para documentos y sugerencias de tickets. La interfaz de carga/procesamiento de documentos se implementa en la siguiente fase.
                        </div>

                        <button type="submit" class="btn btn-danger">{{ $agent->id ? 'Actualizar agente' : 'Crear agente' }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
