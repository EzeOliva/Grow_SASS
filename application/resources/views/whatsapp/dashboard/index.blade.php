@extends('whatsapp.layouts.app')

@section('whatsapp-content')
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="whatsapp-card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-chart-bar me-2"></i>
                WhatsApp Dashboard
            </h5>
        </div>
        <div class="card-body">
            <p class="mb-0">Welcome to your WhatsApp ticket management dashboard. Here you can monitor performance, manage tickets, and track customer satisfaction.</p>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="whatsapp-card text-center">
                <div class="card-body">
                    <i class="fas fa-ticket-alt fa-2x text-primary mb-2"></i>
                    <h3 class="text-primary">{{ $kpis['total_tickets'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Total Tickets</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="whatsapp-card text-center">
                <div class="card-body">
                    <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                    <h3 class="text-warning">{{ $kpis['open_tickets'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Open Tickets</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="whatsapp-card text-center">
                <div class="card-body">
                    <i class="fas fa-user-clock fa-2x text-info mb-2"></i>
                    <h3 class="text-info">{{ $kpis['in_progress_tickets'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">In Progress</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="whatsapp-card text-center">
                <div class="card-body">
                    <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                    <h3 class="text-success">{{ $kpis['closed_tickets'] ?? 0 }}</h3>
                    <p class="text-muted mb-0">Closed Tickets</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="whatsapp-card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-stopwatch me-2"></i>
                        Response Time Metrics
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <h4 class="text-primary">{{ $kpis['avg_first_response_time'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">Avg First Response (min)</p>
                        </div>
                        <div class="col-6">
                            <h4 class="text-success">{{ $kpis['avg_resolution_time'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">Avg Resolution (min)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="whatsapp-card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-bar me-2"></i>
                        Tickets Overview
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="ticketsStatusChart" height="140"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="whatsapp-card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Topic Distribution
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="topicPieChart" height="140"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Message Count & Agent Performance -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="whatsapp-card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-comments me-2"></i>
                        Message Count
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <h4 class="text-primary">{{ $kpis['total_messages'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">Total Messages</p>
                        </div>
                        <div class="col-4">
                            <h4 class="text-info">{{ $kpis['client_messages'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">Client Messages</p>
                        </div>
                        <div class="col-4">
                            <h4 class="text-success">{{ $kpis['agent_messages'] ?? 0 }}</h4>
                            <p class="text-muted mb-0">Agent Messages</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="whatsapp-card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-user-tie me-2"></i>
                        Top Agents by Tickets Resolved
                    </h6>
                </div>
                <div class="card-body">
                    <div id="agentRanking">
                        @if(isset($kpis['top_agents']) && count($kpis['top_agents']) > 0)
                            @foreach($kpis['top_agents'] as $index => $agent)
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                        <span class="fw-semibold">{{ $agent['name'] }}</span>
                                    </div>
                                    <span class="badge bg-success">{{ $agent['tickets_resolved'] }} tickets</span>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center text-muted py-3">
                                <i class="fas fa-user-tie fa-2x mb-2"></i>
                                <p class="small mb-0">No agent data available</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="whatsapp-card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-bolt me-2"></i>
                Quick Actions
            </h6>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-6 col-sm-4 col-md-3 text-center mb-3">
                    <a href="{{ route('whatsapp.tickets.create') }}" class="text-decoration-none">
                        <div class="p-3 border rounded hover-shadow">
                            <i class="fas fa-plus fa-2x text-primary mb-2"></i>
                            <h6>Create Ticket</h6>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-sm-4 col-md-3 text-center mb-3">
                    <a href="{{ route('whatsapp.tickets.index') }}" class="text-decoration-none">
                        <div class="p-3 border rounded hover-shadow">
                            <i class="fas fa-list fa-2x text-info mb-2"></i>
                            <h6>View Tickets</h6>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-sm-4 col-md-3 text-center mb-3">
                    <a href="{{ route('whatsapp.connections.index') }}" class="text-decoration-none">
                        <div class="p-3 border rounded hover-shadow">
                            <i class="fas fa-cog fa-2x text-warning mb-2"></i>
                            <h6>Manage Connections</h6>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-sm-4 col-md-3 text-center mb-3">
                    <a href="{{ route('whatsapp.reporting.index') }}" class="text-decoration-none">
                        <div class="p-3 border rounded hover-shadow">
                            <i class="fas fa-chart-bar fa-2x text-success mb-2"></i>
                            <h6>View Reports</h6>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-sm-4 col-md-3 text-center mb-3">
                    <a href="{{ route('whatsapp.line-configs.index') }}" class="text-decoration-none">
                        <div class="p-3 border rounded hover-shadow">
                            <i class="fas fa-random fa-2x text-secondary mb-2"></i>
                            <h6>Assignment & Routing</h6>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-sm-4 col-md-3 text-center mb-3">
                    <a href="{{ route('whatsapp.tags.index') }}" class="text-decoration-none">
                        <div class="p-3 border rounded hover-shadow">
                            <i class="fas fa-tags fa-2x text-muted mb-2"></i>
                            <h6>Manage Tags</h6>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-shadow:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
    transition: all 0.3s ease;
}

.hover-shadow {
    transition: all 0.3s ease;
}
</style>
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
    var kpis = @json($kpis ?? []);
    
    // Tickets Status Chart
    var ctx1 = document.getElementById('ticketsStatusChart');
    if (ctx1) {
        var data1 = {
            labels: ['Open','In Progress','Closed'],
            datasets: [{
                label: 'Tickets',
                data: [
                    Number(kpis.open_tickets || 0),
                    Number(kpis.in_progress_tickets || 0),
                    Number(kpis.closed_tickets || 0)
                ],
                backgroundColor: ['#f6c453','#4dabf7','#51cf66'],
                borderColor: ['#f0b23a','#339af0','#40c057'],
                borderWidth: 1,
                borderRadius: 8,
            }]
        };
        new Chart(ctx1, {
            type: 'bar',
            data: data1,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { beginAtZero: true, ticks: { precision:0 } }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: { enabled: true }
                }
            }
        });
    }
    
    // Topic Pie Chart
    var ctx2 = document.getElementById('topicPieChart');
    if (ctx2) {
        var topicData = kpis.topic_distribution || {};
        var labels = Object.keys(topicData);
        var data = Object.values(topicData);
        
        if (labels.length === 0) {
            labels = ['General', 'Technical', 'Billing', 'Support'];
            data = [25, 30, 20, 25];
        }
        
        var colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'];
        
        var data2 = {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors.slice(0, labels.length),
                borderColor: colors.slice(0, labels.length),
                borderWidth: 2,
            }]
        };
        new Chart(ctx2, {
            type: 'pie',
            data: data2,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.parsed || 0;
                                var total = context.dataset.data.reduce((a, b) => a + b, 0);
                                var percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection


 
