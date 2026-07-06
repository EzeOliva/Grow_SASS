<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Encuesta de Satisfacción</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background: #f7f9fc; font-family: 'Segoe UI', sans-serif; }
        .feedback-card { max-width: 700px; margin: 40px auto; background: #fff; border-radius: 12px; box-shadow: 0 2px 20px rgba(0,0,0,0.08); padding: 40px; }
        .feedback-header { text-align: center; margin-bottom: 30px; }
        .feedback-header h2 { color: #333; font-weight: 600; }
        .feedback-header p { color: #888; }
        .impact-summary { background: #f3f9ff; border: 1px solid #c8e4ff; border-radius: 8px; padding: 16px; margin-bottom: 25px; color: #1f3d5a; }
        .impact-summary .badge { background: #fff; border: 1px solid #ddd; color: #333; font-size: 13px; padding: 6px 12px; }
        .question-block { background: #f9fbfd; border-radius: 8px; padding: 20px; margin-bottom: 20px; }
        .question-block label { font-weight: 600; color: #333; margin-bottom: 12px; display: block; }
        .score-buttons { display: flex; flex-wrap: wrap; gap: 8px; }
        .score-btn { width: 42px; height: 42px; border-radius: 50%; border: 2px solid #5f9ee9; background: #fff; color: #5f9ee9; font-weight: 600; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; justify-content: center; }
        .score-btn:hover { background: #e8f2ff; }
        .score-btn.active { background: #5f9ee9; color: #fff; }
        .btn-submit { background: #5f9ee9; border: none; padding: 14px; font-size: 16px; font-weight: 600; border-radius: 8px; }
        .btn-submit:hover { background: #4a8cd4; }
        .btn-submit:disabled { background: #ccc; cursor: not-allowed; }
        .powered-by { text-align: center; margin-top: 20px; color: #aaa; font-size: 12px; }
        .ai-hint { font-size: 12px; color: #888; margin-top: 8px; }
        .ai-hint i { color: #5f9ee9; }
        .comment-loading { display: none; font-size: 12px; color: #5f9ee9; margin-top: 5px; }
        .comment-loading.visible { display: block; }
        @media (max-width: 576px) {
            .feedback-card { margin: 15px; padding: 25px 20px; }
            .score-btn { width: 36px; height: 36px; font-size: 13px; }
        }
    </style>
</head>
<body>
    @php
        $tasksCompleted = (int) ($feedbackImpact['tasks_completed'] ?? 0);
        $trainingsCount = (int) ($feedbackImpact['capacitaciones_count'] ?? 0);
        $expectationsMet = (int) ($feedbackImpact['expectations_fulfilled'] ?? 0);
        $meetingsCount = (int) ($feedbackImpact['minutas_count'] ?? 0);
        $hasImpact = ($tasksCompleted + $trainingsCount + $expectationsMet + $meetingsCount) > 0;
    @endphp

    <div class="feedback-card">
        <div class="feedback-header">
            <h2><i class="fa-regular fa-star-half-stroke mr-2"></i>Encuesta de Satisfacción</h2>
            <p>Hola <strong>{{ $client->client_company_name }}</strong>, tu opinión nos ayuda a mejorar.</p>
        </div>

        @if($hasImpact)
        <div class="impact-summary">
            <div class="small mb-2"><strong>En los últimos 3 meses trabajamos junto a ustedes en:</strong></div>
            <div class="d-flex flex-wrap align-items-center">
                @if($tasksCompleted > 0)
                    <span class="badge mr-2 mb-2">{{ $tasksCompleted }} tareas completadas</span>
                @endif
                @if($trainingsCount > 0)
                    <span class="badge mr-2 mb-2">{{ $trainingsCount }} capacitaciones</span>
                @endif
                @if($expectationsMet > 0)
                    <span class="badge mr-2 mb-2">{{ $expectationsMet }} expectativas cumplidas</span>
                @endif
                @if($meetingsCount > 0)
                    <span class="badge mb-2">{{ $meetingsCount }} reuniones</span>
                @endif
            </div>
            <div class="small text-muted mb-0">Tu feedback nos ayuda a mejorar aún más.</div>
        </div>
        @endif

        <form method="POST" action="/feedback/external/{{ $token }}" id="feedbackExternalForm">
            @csrf

            @foreach($feedbackQueries as $index => $item)
                <div class="question-block">
                    <label>{{ $item->content }}</label>
                    @if($item->type == 1 || $item->type == 3)
                        <div class="score-buttons">
                            @for($i = 1; $i <= $item->range; $i++)
                                <button type="button" class="score-btn" data-question="{{ $item->feedback_query_id }}" data-value="{{ $i }}">{{ $i }}</button>
                            @endfor
                        </div>
                        <input type="hidden" name="q_{{ $item->feedback_query_id }}" id="input_q_{{ $item->feedback_query_id }}" value="" data-question="{{ $item->feedback_query_id }}">
                    @elseif($item->type == 2)
                        <div class="score-buttons">
                            @for($i = 1; $i <= $item->range; $i++)
                                <button type="button" class="score-btn star-btn" data-question="{{ $item->feedback_query_id }}" data-value="{{ $i }}">
                                    <i class="far fa-star"></i>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="q_{{ $item->feedback_query_id }}" id="input_q_{{ $item->feedback_query_id }}" value="" data-question="{{ $item->feedback_query_id }}">
                    @endif
                </div>
            @endforeach

            <div class="question-block">
                <label>Comentario <span class="text-danger">*</span></label>
                <textarea name="comment" id="commentField" class="form-control" rows="5" placeholder="Contanos tu experiencia..." style="resize: vertical;" required></textarea>
                <div class="comment-loading" id="aiLoading"><i class="fas fa-spinner fa-spin mr-1"></i> Generando sugerencia con IA...</div>
                <div class="ai-hint"><i class="fas fa-magic mr-1"></i> La sugerencia es generada por IA en base al puntaje y datos del período. Podés editarla libremente.</div>
            </div>

            <button type="submit" class="btn btn-primary btn-submit btn-block" id="btnSubmit" disabled>
                <i class="fas fa-paper-plane mr-2"></i>Enviar Feedback
            </button>
        </form>

        <div class="powered-by mt-3">
            Powered by Tasklist
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var totalQuestions = document.querySelectorAll('#feedbackExternalForm input[type="hidden"][data-question]').length;
            var commentField = document.getElementById('commentField');
            var aiLoading = document.getElementById('aiLoading');
            var suggestUrl = '/feedback/external/{{ $token }}/suggest';
            var aiTimer = null;
            var aiLastKey = '';
            var aiLastSuggestion = '';
            var csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            document.querySelectorAll('.score-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var question = this.getAttribute('data-question');
                    var value = this.getAttribute('data-value');

                    this.closest('.score-buttons').querySelectorAll('.score-btn').forEach(function(b) {
                        b.classList.remove('active');
                        if (b.querySelector('i')) b.querySelector('i').className = 'far fa-star';
                    });

                    if (this.classList.contains('star-btn')) {
                        this.closest('.score-buttons').querySelectorAll('.score-btn').forEach(function(b) {
                            if (parseInt(b.getAttribute('data-value')) <= parseInt(value)) {
                                b.classList.add('active');
                                if (b.querySelector('i')) b.querySelector('i').className = 'fas fa-star';
                            }
                        });
                    } else {
                        this.classList.add('active');
                    }

                    document.getElementById('input_q_' + question).value = value;
                    checkComplete();
                    scheduleAISuggestion();
                });
            });

            function checkComplete() {
                var answered = 0;
                document.querySelectorAll('#feedbackExternalForm input[type="hidden"][data-question]').forEach(function(input) {
                    if (input.value !== '') answered++;
                });
                var hasComment = commentField.value.trim() !== '';
                document.getElementById('btnSubmit').disabled = !(answered >= totalQuestions && hasComment);
            }

            commentField.addEventListener('input', checkComplete);

            function collectDetails() {
                var details = [];
                document.querySelectorAll('#feedbackExternalForm input[type="hidden"][data-question]').forEach(function(input) {
                    if (input.value !== '') {
                        details.push({
                            feedback_query_id: parseInt(input.getAttribute('data-question')),
                            value: parseInt(input.value)
                        });
                    }
                });
                return details;
            }

            function canAutoFill() {
                var current = commentField.value.trim();
                return current === '' || current === aiLastSuggestion;
            }

            function scheduleAISuggestion() {
                clearTimeout(aiTimer);
                aiTimer = setTimeout(requestAISuggestion, 800);
            }

            function requestAISuggestion() {
                var details = collectDetails();
                if (details.length === 0) return;

                var key = JSON.stringify(details);
                if (key === aiLastKey && aiLastSuggestion) return;
                if (!canAutoFill()) return;

                aiLastKey = key;
                aiLoading.classList.add('visible');

                fetch(suggestUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ details: details, _token: csrfToken })
                })
                .then(function(res) { return res.json(); })
                .then(function(data) {
                    aiLoading.classList.remove('visible');
                    if (data.suggestion && canAutoFill()) {
                        aiLastSuggestion = data.suggestion.trim();
                        commentField.value = aiLastSuggestion;
                        checkComplete();
                    }
                })
                .catch(function() {
                    aiLoading.classList.remove('visible');
                    if (canAutoFill()) {
                        var avg = details.reduce(function(s, d) { return s + d.value; }, 0) / details.length;
                        var text = avg >= 8
                            ? 'Muy conforme con el servicio. La atención fue excelente y el seguimiento muy profesional.'
                            : avg >= 6
                            ? 'Buena experiencia en general. Hay puntos positivos y oportunidades de mejora.'
                            : 'Valoro el trabajo del equipo. Sería bueno mejorar algunos aspectos para la próxima.';
                        aiLastSuggestion = text;
                        commentField.value = text;
                        checkComplete();
                    }
                });
            }
        });
    </script>
</body>
</html>
