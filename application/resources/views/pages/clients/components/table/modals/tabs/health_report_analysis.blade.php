@php
    $notes = $healthData['notes'] ?? collect();
    $minutas = $healthData['minutas'] ?? collect();
    $capacitaciones = $healthData['capacitaciones'] ?? collect();
    $comments = $healthData['comments'] ?? collect();
    $expectations = $healthData['expectations'] ?? collect();
    $criticalNoNotes = count($notes) === 0;
    $criticalNoExpectations = !($healthData['has_expectation_last_3_months'] ?? false);
    $criticalNoRecentFeedback = !($healthData['has_feedback_last_3_months'] ?? false);
    $criticalNoRecentMinuta = !($healthData['has_minuta_last_3_months'] ?? false);
    $hasCriticalHealthAlert = $criticalNoNotes || $criticalNoExpectations || $criticalNoRecentFeedback || $criticalNoRecentMinuta;
@endphp

<div class="client-health-analysis">
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            <h6><i class="fas fa-heartbeat"></i> Informe de Salud del Cliente - {{ $healthData['period_label'] ?? 'Período' }}</h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Notas</strong><br>
                        {{ count($notes) }} registradas
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Minutas</strong><br>
                        {{ count($minutas) }} registradas
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Capacitaciones</strong><br>
                        {{ count($capacitaciones) }} registradas
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Etapa</strong><br>
                        {{ $healthData['client_stage_title'] ?? 'Sin etapa' }}
                        @if(!empty($healthData['client_stage_description'] ?? ''))
                            <span class="d-block text-muted small">{{ str_limit($healthData['client_stage_description'], 100) }}</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Tareas</strong><br>
                        {{ $healthData['tasks_completed'] ?? 0 }} completadas / {{ $healthData['tasks_pending'] ?? 0 }} pendientes
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Comentarios</strong><br>
                        {{ count($comments) }} del cliente
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Expectativas</strong><br>
                        {{ $healthData['expectations_fulfilled'] ?? 0 }} cumplidas de {{ $healthData['expectations_total'] ?? 0 }}
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Feedback</strong><br>
                        {{ $healthData['feedback_count'] ?? 0 }} registros
                    </div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="alert alert-light mb-0">
                        <strong>Promedio feedback</strong><br>
                        {{ $healthData['feedback_average'] ?? 'N/A' }} ({{ $healthData['feedback_trend'] ?? 'N/A' }})
                    </div>
                </div>
            </div>

            @if($hasCriticalHealthAlert)
                <div class="alert mt-2 mb-0" style="background-color:#fdecec;border:1px solid #f5c2c7;color:#842029;">
                    <div class="d-flex align-items-start">
                        <i class="fas fa-exclamation-triangle mr-2" style="margin-top:2px;"></i>
                        <div>
                            <strong>Alerta de salud del cliente</strong>
                            <div class="mt-1">
                                @if($criticalNoNotes)
                                    <div>- No hay notas registradas en este período.</div>
                                @endif
                                @if($criticalNoExpectations)
                                    <div>- No hay expectativas cargadas para este cliente en los últimos 3 meses.</div>
                                @endif
                                @if($criticalNoRecentFeedback)
                                    <div>- No hay feedback registrado en los últimos 3 meses.</div>
                                @endif
                                @if($criticalNoRecentMinuta)
                                    <div>- No hay minutas registradas en los últimos 3 meses.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @elseif(count($comments) === 0 && count($notes) === 0 && count($minutas) === 0 && count($capacitaciones) === 0 && ($healthData['tasks_total'] ?? 0) === 0 && ($healthData['expectations_total'] ?? 0) === 0 && ($healthData['feedback_count'] ?? 0) === 0)
                <div class="alert alert-warning mt-2 mb-0">No hay datos suficientes en este período para un informe completo.</div>
            @endif
        </div>
    </div>

    <div class="ai-analysis-section mb-2">
        <div class="card">
            <div class="card-header">
                <h5><i class="fas fa-robot text-primary"></i> Análisis IA de Éxito al Cliente</h5>
            </div>
            <div class="card-body">
                <div class="mt-2">
                    <button
                        class="btn btn-sm btn-primary ai-analysis-btn"
                        data-toggle="tooltip"
                        data-placement="top"
                        title="Generar informe IA de salud del cliente"
                        onclick="generateClientHealthAnalysis({{ $client->client_id }}, '{{ $healthData['period'] ?? 'week' }}', this)">
                        <i class="fas fa-magic"></i> Generar Informe de Salud
                    </button>
                </div>

                <div id="ai-response-health" class="mt-3" style="display:none;">
                    <div class="alert alert-info">
                        <i class="fas fa-spinner fa-spin"></i> Generando informe...
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
window.__clientHealthLastReport = window.__clientHealthLastReport || null;

window.clientHealthNotify = function (type, message) {
    if (window.NX && typeof NX.notification === 'function') {
        NX.notification({ type: type, message: message });
        return;
    }

    // Keep silent fallback to avoid browser blocking alerts.
    console.log(message);
}

window.generateClientHealthAnalysis = function (clientId, period, buttonEl) {
    const responseDiv = document.getElementById('ai-response-health');
    const button = buttonEl || event.target.closest('button');
    const originalText = button.innerHTML;

    responseDiv.style.display = 'block';
    responseDiv.innerHTML = `
        <div class="alert alert-info">
            <i class="fas fa-spinner fa-spin"></i> Generando informe...
        </div>
    `;

    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Generando...';

    $.ajax({
        url: `/clients/${clientId}/generate-ai-health-analysis`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            period: period
        },
        success: function (response) {
            if (response.success) {
                window.__clientHealthLastReport = {
                    clientId: clientId,
                    period: period,
                    analysis: response.analysis || ''
                };

                const htmlContent = marked.parse(response.analysis);
                responseDiv.innerHTML = `
                    <div class="alert alert-success">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0"><i class="fas fa-check-circle"></i> Informe generado</h6>
                            <div class="d-flex align-items-center">
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-primary mr-2"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Agregar este resumen a la línea de tiempo del cliente"
                                    onclick="publishClientHealthSummary(this)">
                                    <i class="fas fa-stream"></i> Agregar a línea de tiempo
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Imprimir o guardar como PDF"
                                    onclick="printClientHealthReport(this)">
                                    <i class="fas fa-file-pdf"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mt-3 ai-report-printable">
                            <div class="ai-analysis-content" style="font-size: 14px; line-height: 1.6;">
                                ${htmlContent}
                            </div>
                        </div>
                    </div>
                `;

                const analysisLinks = responseDiv.querySelectorAll('.ai-analysis-content a');
                analysisLinks.forEach(function (link) {
                    link.setAttribute('target', '_blank');
                    link.setAttribute('rel', 'noopener noreferrer');
                });
            } else {
                responseDiv.innerHTML = `
                    <div class="alert alert-danger">
                        <h6><i class="fas fa-exclamation-triangle"></i> Error al generar informe</h6>
                        <p>${response.message}</p>
                    </div>
                `;
            }
        },
        error: function () {
            responseDiv.innerHTML = `
                <div class="alert alert-danger">
                    <h6><i class="fas fa-exclamation-triangle"></i> Error al generar informe</h6>
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

window.publishClientHealthSummary = function (buttonEl) {
    const report = window.__clientHealthLastReport || {};
    const button = buttonEl || event.target.closest('button');

    if (!report.clientId || !report.analysis) {
        window.clientHealthNotify('warning', 'Primero generá el informe IA antes de agregarlo a la línea de tiempo.');
        return;
    }

    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publicando...';

    $.ajax({
        url: `/clients/${report.clientId}/publish-ai-health-summary`,
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        data: {
            period: report.period || 'week',
            analysis: report.analysis || ''
        },
        success: function (response) {
            if (response.success) {
                window.clientHealthNotify('success', response.message || 'Resumen IA agregado a la línea de tiempo.');
            } else {
                window.clientHealthNotify('error', response.message || 'No se pudo agregar el resumen a la línea de tiempo.');
            }
        },
        error: function (xhr) {
            let message = 'Ocurrió un error al agregar el resumen a la línea de tiempo.';
            if (xhr && xhr.responseJSON && xhr.responseJSON.message) {
                message = xhr.responseJSON.message;
            }

            window.clientHealthNotify('error', message);
        },
        complete: function () {
            button.disabled = false;
            button.innerHTML = originalText;
        }
    });
}

if (typeof window.printClientHealthReport !== 'function') {
    window.printClientHealthReport = function (buttonEl) {
        const reportContainer = buttonEl.closest('.alert').querySelector('.ai-analysis-content');

        if (!reportContainer) {
            return;
        }

        const printWindow = window.open('', '_blank', 'width=900,height=700');
        const reportHtml = reportContainer.innerHTML;

        printWindow.document.write(`
            <html>
                <head>
                    <title>Informe de Salud del Cliente</title>
                    <style>
                        body { font-family: Arial, sans-serif; margin: 24px; color: #333; }
                        h1, h2, h3, h4, h5, h6 { margin-top: 0; }
                        p, li { line-height: 1.6; }
                        ul, ol { padding-left: 20px; }
                    </style>
                </head>
                <body>
                    ${reportHtml}
                </body>
            </html>
        `);

        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    }
}
</script>
