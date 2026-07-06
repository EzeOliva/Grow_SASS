@php
    $stageTitle = trim((string) ($payload['client_stage_title'] ?? 'Sin etapa'));
    if ($stageTitle === '') {
        $stageTitle = 'Sin etapa';
    }

    $pendingProjects = (int) ($payload['projects']['pending'] ?? 0);
    $completedProjects = (int) ($payload['projects']['completed'] ?? 0);
@endphp

<div class="col-lg-3 col-md-6 d-flex">
    <div class="card w-100 h-100 d-flex flex-column">
        <div class="card-body p-l-15 p-r-15">
            <div class="d-flex p-10 no-block">
                <span class="align-slef-center">
                    <h2 class="m-b-0 text-info">{{ $stageTitle }}</h2>
                    <h6 class="text-muted m-b-0">Etapa actual</h6>
                    <small class="text-muted">Pendientes: {{ $pendingProjects }} | Terminados: {{ $completedProjects }}</small>
                </span>
                <div class="align-self-center display-6 ml-auto"><i class="text-info sl-icon-briefcase"></i></div>
            </div>
        </div>
        <div class="progress">
            <div class="progress-bar bg-info w-100 h-px-3" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                aria-valuemax="100"></div>
        </div>
    </div>
</div>
