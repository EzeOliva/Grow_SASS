@php
    $overview = $payload['health_overview'] ?? [];
    $canViewClients = auth()->user()->is_team && (int) (auth()->user()->role->role_clients ?? 0) > 0;
    $canViewReports = auth()->user()->is_team && (auth()->user()->role->role_reports ?? 'no') === 'yes';
@endphp

<div class="row">
    <div class="col-lg-3 col-md-6 {{ $canViewClients ? 'click-url cursor-pointer' : '' }}"
        @if ($canViewClients) data-url="{{ url('clients?ref=list&filter_health_status[]=green') }}" @endif>
        <div class="card">
            <div class="card-body p-l-15 p-r-15">
                <div class="d-flex p-10 no-block">
                    <span class="align-slef-center">
                        <h2 class="m-b-0">{{ (int) ($overview['green'] ?? 0) }}</h2>
                        <h6 class="text-muted m-b-0">Clientes saludables</h6>
                    </span>
                    <div class="align-self-center display-6 ml-auto"><i class="text-success sl-icon-like"></i></div>
                </div>
            </div>
            <div class="progress">
                <div class="progress-bar bg-success w-100 h-px-3" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                    aria-valuemax="100"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 {{ $canViewClients ? 'click-url cursor-pointer' : '' }}"
        @if ($canViewClients) data-url="{{ url('clients?ref=list&filter_health_status[]=yellow') }}" @endif>
        <div class="card">
            <div class="card-body p-l-15 p-r-15">
                <div class="d-flex p-10 no-block">
                    <span class="align-slef-center">
                        <h2 class="m-b-0">{{ (int) ($overview['yellow'] ?? 0) }}</h2>
                        <h6 class="text-muted m-b-0">En seguimiento</h6>
                    </span>
                    <div class="align-self-center display-6 ml-auto"><i class="text-warning sl-icon-hourglass"></i></div>
                </div>
            </div>
            <div class="progress">
                <div class="progress-bar bg-warning w-100 h-px-3" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                    aria-valuemax="100"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 {{ $canViewClients ? 'click-url cursor-pointer' : '' }}"
        @if ($canViewClients) data-url="{{ url('clients?ref=list&filter_health_status[]=red') }}" @endif>
        <div class="card">
            <div class="card-body p-l-15 p-r-15">
                <div class="d-flex p-10 no-block">
                    <span class="align-slef-center">
                        <h2 class="m-b-0">{{ (int) ($overview['red'] ?? 0) }}</h2>
                        <h6 class="text-muted m-b-0">En riesgo</h6>
                    </span>
                    <div class="align-self-center display-6 ml-auto"><i class="text-danger sl-icon-close"></i></div>
                </div>
            </div>
            <div class="progress">
                <div class="progress-bar bg-danger w-100 h-px-3" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                    aria-valuemax="100"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-md-6 {{ $canViewReports ? 'click-url cursor-pointer' : '' }}"
        @if ($canViewReports) data-url="{{ url('report/clients/health-by-stage') }}" @endif>
        <div class="card">
            <div class="card-body p-l-15 p-r-15">
                <div class="d-flex p-10 no-block">
                    <span class="align-slef-center">
                        <h2 class="m-b-0">{{ (int) ($overview['without_recent_feedback'] ?? 0) }}</h2>
                        <h6 class="text-muted m-b-0">Sin feedback (90 dias)</h6>
                        <small class="text-muted">Riesgo global: {{ (int) ($overview['at_risk_percent'] ?? 0) }}%</small>
                    </span>
                    <div class="align-self-center display-6 ml-auto"><i class="text-info sl-icon-bubble"></i></div>
                </div>
            </div>
            <div class="progress">
                <div class="progress-bar bg-info w-100 h-px-3" role="progressbar" aria-valuenow="100" aria-valuemin="0"
                    aria-valuemax="100"></div>
            </div>
        </div>
    </div>
</div>