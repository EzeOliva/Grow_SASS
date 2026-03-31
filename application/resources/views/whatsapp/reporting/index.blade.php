@extends('layout.wrapper')

@section('content')
<div class="container-fluid">
	<div class="row page-titles mb-4">
		<div class="col-md-6">
			<h3 class="text-dark fw-bold mb-2">
				<i class="fas fa-chart-line text-primary me-3"></i>WhatsApp Analytics
			</h3>
			<p class="text-muted fs-6 mb-0">Comprehensive insights and performance metrics for your WhatsApp support operations</p>
		</div>
		<div class="col-md-6 text-end">
			<a href="{{ route('whatsapp.dashboard') }}" class="btn btn-outline-primary btn-lg shadow-sm">
				<i class="fas fa-tachometer-alt me-2"></i>Dashboard
			</a>
		</div>
	</div>

	<!-- Date Range Filter -->
	<div class="card border-0 shadow-sm mb-4">
		<div class="card-body">
			<form method="GET" class="row g-3 align-items-end">
				<div class="col-md-3">
					<label for="start_date" class="form-label fw-semibold">Start Date</label>
					<input type="date" class="form-control" id="start_date" name="start_date" 
						value="{{ $dateRange['start'] ?? date('Y-m-d', strtotime('-30 days')) }}">
				</div>
				<div class="col-md-3">
					<label for="end_date" class="form-label fw-semibold">End Date</label>
					<input type="date" class="form-control" id="end_date" name="end_date" 
						value="{{ $dateRange['end'] ?? date('Y-m-d') }}">
				</div>
				<div class="col-md-3">
					<label for="status_filter" class="form-label fw-semibold">Status</label>
					<select class="form-control" id="status_filter" name="status">
						<option value="">All Statuses</option>
						<option value="open" {{ ($filters['status'] ?? '') === 'open' ? 'selected' : '' }}>Open</option>
						<option value="in_progress" {{ ($filters['status'] ?? '') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
						<option value="resolved" {{ ($filters['status'] ?? '') === 'resolved' ? 'selected' : '' }}>Resolved</option>
					</select>
				</div>
				<div class="col-md-3">
					<button type="submit" class="btn btn-primary w-100">
						<i class="fas fa-search me-2"></i>Generate Report
					</button>
				</div>
			</form>
		</div>
	</div>

	<!-- KPI Summary Cards -->
	@if(isset($reportData))
	<div class="row mb-4">
		<div class="col-md-3 mb-3">
			<div class="card border-0 shadow-sm bg-gradient-primary text-white">
				<div class="card-body p-4">
					<div class="d-flex align-items-center">
						<div class="kpi-icon me-3">
							<i class="fas fa-ticket-alt fa-2x"></i>
						</div>
						<div>
							<h4 class="mb-1 fw-bold">{{ $reportData['total_tickets'] ?? 0 }}</h4>
							<p class="mb-0 opacity-75">Total Tickets</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3 mb-3">
			<div class="card border-0 shadow-sm bg-gradient-success text-white">
				<div class="card-body p-4">
					<div class="d-flex align-items-center">
						<div class="kpi-icon me-3">
							<i class="fas fa-check-circle fa-2x"></i>
						</div>
						<div>
							<h4 class="mb-1 fw-bold">{{ $reportData['resolved_tickets'] ?? 0 }}</h4>
							<p class="mb-0 opacity-75">Resolved</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3 mb-3">
			<div class="card border-0 shadow-sm bg-gradient-info text-white">
				<div class="card-body p-4">
					<div class="d-flex align-items-center">
						<div class="kpi-icon me-3">
							<i class="fas fa-clock fa-2x"></i>
						</div>
						<div>
							<h4 class="mb-1 fw-bold">{{ $reportData['avg_resolution_time'] ?? '0' }}h</h4>
							<p class="mb-0 opacity-75">Avg Resolution</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3 mb-3">
			<div class="card border-0 shadow-sm bg-gradient-warning text-white">
				<div class="card-body p-4">
					<div class="d-flex align-items-center">
						<div class="kpi-icon me-3">
							<i class="fas fa-smile fa-2x"></i>
						</div>
						<div>
							<h4 class="mb-1 fw-bold">{{ $reportData['satisfaction_score'] ?? '0' }}%</h4>
							<p class="mb-0 opacity-75">Satisfaction</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Detailed Analytics -->
	<div class="row">
		<div class="col-md-8 mb-4">
			<div class="card border-0 shadow-sm">
				<div class="card-header bg-white border-0 py-3">
					<h5 class="text-dark fw-semibold mb-0">
						<i class="fas fa-chart-area text-info me-2"></i>Ticket Volume Trends
					</h5>
				</div>
				<div class="card-body">
					<div class="chart-placeholder text-center py-5">
						<i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
						<h6 class="text-muted">Chart visualization would be displayed here</h6>
						<p class="text-muted small">Showing ticket volume over the selected date range</p>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-4 mb-4">
			<div class="card border-0 shadow-sm">
				<div class="card-header bg-white border-0 py-3">
					<h5 class="text-dark fw-semibold mb-0">
						<i class="fas fa-chart-pie text-warning me-2"></i>Status Distribution
					</h5>
				</div>
				<div class="card-body">
					<div class="status-distribution">
						@if(isset($reportData['status_distribution']))
							@foreach($reportData['status_distribution'] as $status => $count)
								<div class="d-flex justify-content-between align-items-center mb-3">
									<span class="fw-semibold text-capitalize">{{ str_replace('_', ' ', $status) }}</span>
									<span class="badge bg-primary fs-6 px-3 py-2">{{ $count }}</span>
								</div>
							@endforeach
						@else
							<div class="text-center text-muted py-4">
								<i class="fas fa-chart-pie fa-2x mb-2"></i>
								<p class="small">No data available</p>
							</div>
						@endif
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- Performance Metrics -->
	<div class="row">
		<div class="col-md-6 mb-4">
			<div class="card border-0 shadow-sm">
				<div class="card-header bg-white border-0 py-3">
					<h5 class="text-dark fw-semibold mb-0">
						<i class="fas fa-user-tie text-success me-2"></i>Agent Performance
					</h5>
				</div>
				<div class="card-body">
					@if(isset($reportData['agent_performance']) && count($reportData['agent_performance']))
						<div class="table-responsive">
							<table class="table table-sm">
								<thead>
									<tr>
										<th>Agent</th>
										<th>Tickets</th>
										<th>Avg Time</th>
									</tr>
								</thead>
								<tbody>
									@foreach($reportData['agent_performance'] as $agent)
										<tr>
											<td>{{ $agent['name'] }}</td>
											<td>{{ $agent['tickets'] }}</td>
											<td>{{ $agent['avg_time'] }}h</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@else
						<div class="text-center text-muted py-4">
							<i class="fas fa-user-tie fa-2x mb-2"></i>
							<p class="small">No agent performance data</p>
						</div>
					@endif
				</div>
			</div>
		</div>
		<div class="col-md-6 mb-4">
			<div class="card border-0 shadow-sm">
				<div class="card-header bg-white border-0 py-3">
					<h5 class="text-dark fw-semibold mb-0">
						<i class="fas fa-tags text-info me-2"></i>Popular Ticket Types
					</h5>
				</div>
				<div class="card-body">
					@if(isset($reportData['ticket_types']) && count($reportData['ticket_types']))
						@foreach($reportData['ticket_types'] as $type)
							<div class="d-flex justify-content-between align-items-center mb-3">
								<span class="fw-semibold">{{ $type['name'] }}</span>
								<span class="badge bg-info fs-6 px-3 py-2">{{ $type['count'] }}</span>
							</div>
						@endforeach
					@else
						<div class="text-center text-muted py-4">
							<i class="fas fa-tags fa-2x mb-2"></i>
							<p class="small">No ticket type data</p>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
	@else
	<div class="text-center py-5">
		<div class="empty-state">
			<i class="fas fa-chart-line fa-4x text-muted mb-4"></i>
			<h5 class="text-dark fw-semibold mb-3">No Report Data Available</h5>
			<p class="text-muted fs-6 mb-4">Select a date range and generate a report to view analytics.</p>
		</div>
	</div>
	@endif
</div>

<style>
.page-titles h3 {
	font-size: 1.75rem;
	font-weight: 700;
}

.page-titles p {
	font-size: 1rem;
	line-height: 1.5;
}

.card {
	border-radius: 12px;
	overflow: hidden;
}

.card-header {
	border-bottom: 1px solid #e9ecef;
}

.btn-lg {
	padding: 0.75rem 1.5rem;
	font-weight: 600;
	border-radius: 8px;
}

.kpi-icon {
	width: 60px;
	height: 60px;
	display: flex;
	align-items: center;
	justify-content: center;
	background: rgba(255, 255, 255, 0.2);
	border-radius: 12px;
}

.bg-gradient-primary {
	background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-success {
	background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
}

.bg-gradient-info {
	background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.bg-gradient-warning {
	background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
}

.chart-placeholder {
	background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
	border-radius: 8px;
}

.empty-state {
	padding: 2rem;
}

.status-distribution .badge {
	font-weight: 500;
}

.table-sm th,
.table-sm td {
	padding: 0.5rem;
	font-size: 0.875rem;
}
</style>
@endsection

