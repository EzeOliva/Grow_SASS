@extends('layout.wrapper')

@section('content')
<div class="container-fluid">
	<div class="row page-titles mb-4">
		<div class="col-md-6">
			<h3 class="text-dark fw-bold mb-2">
				<i class="fas fa-plug text-primary me-3"></i>WhatsApp Connections
			</h3>
			<p class="text-muted fs-6 mb-0">Manage your WhatsApp API connections and integration settings</p>
		</div>
		<div class="col-md-6 text-end">
			<a href="{{ route('whatsapp.connections.create') }}" class="btn btn-primary btn-lg shadow-sm">
				<i class="fas fa-plus-circle me-2"></i>Add New Connection
			</a>
		</div>
	</div>

	<div class="card border-0 shadow-sm">
		<div class="card-header bg-white border-0 py-3">
			<div class="row align-items-center">
				<div class="col-md-6">
					<h5 class="text-dark fw-semibold mb-0">
						<i class="fas fa-list-ul text-info me-2"></i>Connection Management
					</h5>
				</div>
				<div class="col-md-6 text-end">
					<span class="badge bg-light text-dark fs-6 px-3 py-2">
						<i class="fas fa-info-circle me-1"></i>{{ isset($connections) ? $connections->count() : 0 }} Connections
					</span>
				</div>
			</div>
		</div>
		<div class="card-body p-0">
			@if(isset($connections) && $connections->count())
				<div class="table-responsive">
					<table class="table table-hover mb-0">
						<thead class="table-light">
							<tr>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-plug me-2 text-primary"></i>Connection Details
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-layer-group me-2 text-info"></i>Type
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-toggle-on me-2 text-success"></i>Status
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-clock me-2 text-warning"></i>Last Updated
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold text-center">
									<i class="fas fa-cogs me-2 text-secondary"></i>Actions
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($connections as $connection)
								<tr class="border-bottom">
									<td class="py-3 px-4">
										<div class="d-flex align-items-center">
											<div class="connection-icon me-3">
												<i class="fas fa-plug fa-2x text-primary"></i>
											</div>
											<div>
												<h6 class="text-dark fw-semibold mb-1">{{ $connection->connection_name }}</h6>
												<small class="text-muted">
													<i class="fas fa-server me-1"></i>
													{{ $connection->api_endpoint ?? 'No endpoint configured' }}
												</small>
											</div>
										</div>
									</td>
									<td class="py-3 px-4">
										<span class="badge bg-info bg-opacity-10 text-info fs-6 px-3 py-2">
											<i class="fas fa-layer-group me-1"></i>{{ ucfirst($connection->connection_type) }}
										</span>
									</td>
									<td class="py-3 px-4">
										<span class="badge {{ $connection->is_active ? 'bg-success' : 'bg-secondary' }} fs-6 px-3 py-2">
											<i class="fas {{ $connection->is_active ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
											{{ $connection->is_active ? 'Active' : 'Inactive' }}
										</span>
									</td>
									<td class="py-3 px-4">
										<small class="text-muted">
											<i class="fas fa-clock me-1"></i>
											{{ $connection->updated_at ? $connection->updated_at->diffForHumans() : 'Never' }}
										</small>
									</td>
									<td class="py-3 px-4 text-center">
										<div class="btn-group" role="group">
											<a href="{{ route('whatsapp.connections.show', $connection) }}" class="btn btn-outline-primary btn-sm me-2">
												<i class="fas fa-eye me-1"></i>View
											</a>
											<a href="{{ route('whatsapp.connections.edit', $connection) }}" class="btn btn-outline-warning btn-sm">
												<i class="fas fa-edit me-1"></i>Edit
											</a>
										</div>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				@if(method_exists($connections, 'links'))
					<div class="card-footer bg-white border-0 py-3">
						{{ $connections->links() }}
					</div>
				@endif
			@else
				<div class="text-center py-5">
					<div class="empty-state">
						<i class="fas fa-plug fa-4x text-muted mb-4"></i>
						<h5 class="text-dark fw-semibold mb-3">No Connections Found</h5>
						<p class="text-muted fs-6 mb-4">Set up your first WhatsApp API connection to start integrating with the platform.</p>
						<a href="{{ route('whatsapp.connections.create') }}" class="btn btn-primary">
							<i class="fas fa-plus me-2"></i>Add First Connection
						</a>
					</div>
				</div>
			@endif
		</div>
	</div>
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

.table th {
	font-weight: 600;
	font-size: 0.9rem;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.table td {
	vertical-align: middle;
}

.btn-lg {
	padding: 0.75rem 1.5rem;
	font-weight: 600;
	border-radius: 8px;
}

.btn-sm {
	padding: 0.5rem 1rem;
	font-weight: 500;
	border-radius: 6px;
}

.badge {
	font-weight: 500;
	border-radius: 6px;
}

.empty-state {
	padding: 2rem;
}

.connection-icon {
	width: 48px;
	height: 48px;
	display: flex;
	align-items: center;
	justify-content: center;
	background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
	border-radius: 12px;
}

.table-hover tbody tr:hover {
	background-color: #f8f9fa;
	transform: translateY(-1px);
	box-shadow: 0 2px 8px rgba(0,0,0,0.1);
	transition: all 0.2s ease;
}

.bg-opacity-10 {
	background-color: rgba(13, 202, 240, 0.1) !important;
}
</style>
@endsection
