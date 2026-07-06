@php
    $stats = $payload['stats'] ?? [];
    $expectationPercent = (int) ($stats['expectation_percent'] ?? 0);
    $averageFeedback = number_format((float) ($stats['average_feedback'] ?? 0), 1);

    $recentComments = $stats['recent_comments'] ?? [];
    if (is_object($recentComments) && method_exists($recentComments, 'count')) {
        $recentCommentsCount = (int) $recentComments->count();
    } elseif (is_array($recentComments)) {
        $recentCommentsCount = count($recentComments);
    } else {
        $recentCommentsCount = 0;
    }

    $barClass = 'bg-danger';
    if ($expectationPercent >= 70) {
        $barClass = 'bg-success';
    } elseif ($expectationPercent >= 40) {
        $barClass = 'bg-warning';
    }
@endphp

<div class="col-lg-3 col-md-6 d-flex">
    <div class="card w-100 h-100 d-flex flex-column">
        <div class="card-body p-l-15 p-r-15">
            <div class="d-flex p-10 no-block">
                <span class="align-slef-center">
                    <h2 class="m-b-0">{{ $expectationPercent }}%</h2>
                    <h6 class="text-muted m-b-0">Cumplimiento de expectativas</h6>
                    <small class="text-muted">Feedback: {{ $averageFeedback }}/10 | Comentarios recientes: {{ $recentCommentsCount }}</small>
                </span>
                <div class="align-self-center display-6 ml-auto"><i class="text-info sl-icon-graph"></i></div>
            </div>
        </div>
        <div class="progress">
            <div class="progress-bar {{ $barClass }} w-100 h-px-3" role="progressbar" aria-valuenow="{{ $expectationPercent }}" aria-valuemin="0"
                aria-valuemax="100"></div>
        </div>
    </div>
</div>
