@php
    $stageSnapshot = collect($payload['stage_health_snapshot'] ?? [])->take(5);
    $canViewReports = auth()->user()->is_team && (auth()->user()->role->role_reports ?? 'no') === 'yes';
@endphp

<div class="col-lg-4 col-md-12" id="dashboard-widgets-stage-health-snapshot">
    <div class="card">
        <div class="card-body">
            <div class="d-flex m-b-20 no-block">
                <h5 class="card-title m-b-0 align-self-center">Riesgo por etapa</h5>
                <div class="ml-auto text-muted">Mes/Trimestre</div>
            </div>

            <div>
                @forelse($stageSnapshot as $stage)
                    <div class="d-flex justify-content-between align-items-center p-t-8 p-b-8 border-bottom">
                        <div>
                            <div class="font-weight-bold">{{ $stage['stage_title'] }}</div>
                            <small class="text-muted">
                                {{ (int) $stage['at_risk_clients'] }} con seguimiento/riesgo de {{ (int) $stage['total_clients'] }}
                            </small>
                        </div>
                        <span class="label label-danger label-rounded">{{ (int) $stage['at_risk_percent'] }}%</span>
                    </div>
                @empty
                    <div class="text-muted">No hay datos de etapas para mostrar.</div>
                @endforelse
            </div>

            @if ($canViewReports)
                <div class="m-t-15 text-right">
                    <a href="{{ url('report/clients/health-by-stage') }}" class="btn btn-sm btn-outline-info">Ver reporte completo</a>
                </div>
            @endif
        </div>
    </div>
</div>
