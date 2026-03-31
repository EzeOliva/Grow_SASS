@php
    $meetingData = $meetingData ?? [];
    $lastMinuta = $meetingData['last_minuta'] ?? null;
    $clientData = $meetingData['client'] ?? null;
@endphp

<div class="client-meeting-prep-analysis">
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            <h6><i class="fas fa-handshake"></i> Preparemos una reunión</h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-3" role="alert">
                <strong>Nota interna del equipo:</strong>
                Esta pestaña sirve para llegar a la reunión con contexto claro desde la última interacción.
                Para que el informe sea preciso, mantener siempre actualizadas: <strong>etapa y descripción de etapa</strong>,
                <strong>minutas</strong>, <strong>capacitaciones</strong>, <strong>expectativas</strong>, <strong>tareas</strong> y <strong>feedback</strong>.
            </div>

            <div class="row">
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Etapa actual</strong><br>
                        {{ $clientData->client_stage_title ?? 'Sin etapa' }}
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Referencia</strong><br>
                        {{ $meetingData['reference_label'] ?? 'Desde última referencia' }}
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Desde fecha</strong><br>
                        {{ $meetingData['reference_date'] ?? 'N/A' }}
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Implementación (referencia)</strong><br>
                        {{ $meetingData['implementation_reference_label'] ?? 'Sin hito de Implementación' }}
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Desde Implementación</strong><br>
                        {{ $meetingData['implementation_reference_date'] ?? 'N/A' }}
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Tareas completadas (Implementación)</strong><br>
                        {{ $meetingData['tasks_completed_since_implementation'] ?? 0 }}
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Cambios de etapa</strong><br>
                        {{ count($meetingData['stage_changes'] ?? []) }}
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Capacitaciones</strong><br>
                        {{ count($meetingData['capacitaciones_since_reference'] ?? []) }}
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Feedbacks con comentario</strong><br>
                        {{ count($meetingData['comments_since_reference'] ?? []) }}
                    </div>
                </div>

                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Tareas completadas</strong><br>
                        {{ $meetingData['tasks_completed_since_reference'] ?? 0 }}
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Tareas pendientes</strong><br>
                        {{ $meetingData['tasks_pending_since_reference'] ?? 0 }}
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Expectativas vencidas</strong><br>
                        {{ count($meetingData['expectations_overdue'] ?? []) }}
                    </div>
                </div>
            </div>

            @if($lastMinuta)
                <div class="alert alert-secondary mt-2 mb-0">
                    <strong>Última minuta ({{ $lastMinuta->minuta_date ?? 'N/A' }}):</strong>
                    <div class="mt-1">{{ str_limit($lastMinuta->minuta_detail ?? '', 260) }}</div>
                </div>
            @endif
        </div>
    </div>

    <div class="ai-analysis-section mb-2">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-robot text-primary"></i> Brief IA para la reunión</h5>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    <button
                        class="btn btn-sm btn-primary"
                        data-toggle="tooltip"
                        data-placement="top"
                        title="Generar brief de preparación de reunión"
                        onclick="generateMeetingPrepAnalysis({{ $client->client_id }}, this)">
                        <i class="fas fa-magic"></i> Generar Brief de Reunión
                    </button>
                </div>

                <div id="ai-response-meeting-prep" class="mt-3" style="display:none;">
                    <div class="alert alert-info">
                        <i class="fas fa-spinner fa-spin"></i> Generando brief...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
if (typeof window.generateMeetingPrepAnalysis !== 'function') {
    window.generateMeetingPrepAnalysis = function (clientId, buttonEl) {
        const responseDiv = document.getElementById('ai-response-meeting-prep');
        const button = buttonEl || event.target.closest('button');
        const originalText = button.innerHTML;

        responseDiv.style.display = 'block';
        responseDiv.innerHTML = `
            <div class="alert alert-info">
                <i class="fas fa-spinner fa-spin"></i> Generando brief...
            </div>
        `;

        button.disabled = true;
        button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';

        $.ajax({
            url: `/clients/${clientId}/generate-ai-meeting-prep`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if (response.success) {
                    const htmlContent = marked.parse(response.analysis);
                    responseDiv.innerHTML = `
                        <div class="alert alert-success">
                            <h6><i class="fas fa-check-circle"></i> Brief generado</h6>
                            <div class="mt-3">
                                <div class="ai-analysis-content" style="font-size: 14px; line-height: 1.6;">
                                    ${htmlContent}
                                </div>
                            </div>
                        </div>
                    `;
                } else {
                    responseDiv.innerHTML = `
                        <div class="alert alert-danger">
                            <h6><i class="fas fa-exclamation-triangle"></i> Error al generar brief</h6>
                            <p>${response.message}</p>
                        </div>
                    `;
                }
            },
            error: function () {
                responseDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle"></i> Error al generar brief</h6>
                        <p>Ocurrió un error al generar el informe. Intenta nuevamente.</p>
                    </div>
                `;
            },
            complete: function () {
                button.disabled = false;
                button.innerHTML = originalText;
            }
        });
    }
}
</script>
