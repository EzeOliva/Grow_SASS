@if(count($stage_groups ?? []) > 0)
    <div class="report-results-table-container" id="report-results-table">
        @foreach($stage_groups as $stage)
            <div class="card m-b-20">
                <div class="card-header bg-light">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="m-b-0">{{ $stage['stage_title'] ?? 'Sin etapa' }}</h4>
                        <span class="badge badge-info">{{ count($stage['clients'] ?? []) }} clientes</span>
                    </div>
                    @if(!empty($stage['stage_description'] ?? ''))
                        <div class="text-muted m-t-5">{{ $stage['stage_description'] }}</div>
                    @endif
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover no-wrap m-b-0">
                            <thead>
                                <tr>
                                    <th>Cliente</th>
                                    <th>Salud</th>
                                    <th>Tareas pendientes</th>
                                    <th>Hitos (2-3)</th>
                                    <th>Análisis breve</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(($stage['clients'] ?? []) as $client)
                                    <tr>
                                        <td>
                                            <a href="{{ url('clients/' . $client['client_id']) }}">{{ $client['client_name'] }}</a>
                                        </td>
                                        <td>
                                            @php
                                                $health = strtolower((string)($client['health_status'] ?? 'red'));
                                                $badgeClass = 'badge-danger';
                                                if ($health === 'green') {
                                                    $badgeClass = 'badge-success';
                                                } elseif ($health === 'yellow') {
                                                    $badgeClass = 'badge-warning';
                                                }
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ ucfirst($health) }}</span>
                                        </td>
                                        <td>{{ $client['tasks_pending'] ?? 0 }}</td>
                                        <td>
                                            <ul class="m-b-0 p-l-20">
                                                @foreach(($client['hitos'] ?? []) as $hito)
                                                    <li>{{ $hito }}</li>
                                                @endforeach
                                            </ul>
                                        </td>
                                        <td>{{ $client['brief'] ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@else
    @include('notifications.no-results-found')
@endif
