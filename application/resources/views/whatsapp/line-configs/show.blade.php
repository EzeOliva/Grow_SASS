@extends('layout.wrapper')

@section('content')
<div class="container-fluid">
	<div class="row page-titles">
		<div class="col-md-6">
			<h4 class="mb-0">{{ $lineConfig->line_name }}</h4>
			<p class="text-muted mb-0">Line Configuration Details</p>
		</div>
		<div class="col-md-6 text-right">
			<a href="{{ route('whatsapp.line-configs.edit', $lineConfig) }}" class="btn btn-primary">
				<i class="fas fa-edit me-2"></i>Edit
			</a>
			<a href="{{ route('whatsapp.line-configs.index') }}" class="btn btn-secondary">
				<i class="fas fa-arrow-left me-2"></i>Back to List
			</a>
		</div>
	</div>

	<div class="row">
		<div class="col-md-4">
			<div class="card">
				<div class="card-header">
					<h5 class="card-title mb-0">Line Information</h5>
				</div>
				<div class="card-body">
					<div class="mb-3">
						<small class="text-muted">Line Name</small>
						<div class="fw-bold">{{ $lineConfig->line_name }}</div>
					</div>
					
					<div class="mb-3">
						<small class="text-muted">Connection</small>
						<div>
							@if($lineConfig->connection)
								<a href="{{ route('whatsapp.connections.show', $lineConfig->connection) }}" class="text-decoration-none">
									{{ $lineConfig->connection->connection_name }}
								</a>
							@else
								<span class="text-muted">No connection</span>
							@endif
						</div>
					</div>
					
					<div class="mb-3">
						<small class="text-muted">Assignment Mode</small>
						<div>
							<span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $lineConfig->assignment_mode)) }}</span>
						</div>
					</div>
					
					<div class="mb-3">
						<small class="text-muted">Auto Assignment</small>
						<div>
							<span class="badge {{ $lineConfig->auto_assign_enabled ? 'bg-success' : 'bg-secondary' }}">
								{{ $lineConfig->auto_assign_enabled ? 'Enabled' : 'Disabled' }}
							</span>
						</div>
					</div>
					
					<div class="mb-3">
						<small class="text-muted">Status</small>
						<div>
							<span class="badge {{ $lineConfig->is_active ? 'bg-success' : 'bg-secondary' }}">
								{{ $lineConfig->is_active ? 'Active' : 'Inactive' }}
							</span>
						</div>
					</div>
					
					@if($lineConfig->inactivity_timeout_minutes)
						<div class="mb-3">
							<small class="text-muted">Inactivity Timeout</small>
							<div>{{ $lineConfig->inactivity_timeout_minutes }} minutes</div>
						</div>
					@endif
					
					@if($lineConfig->auto_assign_agents && count($lineConfig->auto_assign_agents) > 0)
						<hr>
						<small class="text-muted">Auto-Assign Agents</small>
						<div>
							@foreach($agents as $agent)
								<span class="badge bg-light text-dark me-1">{{ $agent->first_name }} {{ $agent->last_name }}</span>
							@endforeach
						</div>
					@endif
				</div>
			</div>
		</div>
		
		<div class="col-md-8">
			<div class="card">
				<div class="card-header">
					<h5 class="card-title mb-0">Recent Tickets</h5>
				</div>
				<div class="card-body">
					@if($recentTickets && $recentTickets->count())
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>ID</th>
										<th>Subject</th>
										<th>Status</th>
										<th>Agent</th>
										<th>Created</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach($recentTickets as $ticket)
										<tr>
											<td>#{{ $ticket->id }}</td>
											<td>
												<a href="{{ route('whatsapp.tickets.show', $ticket) }}" class="text-decoration-none">
													{{ strlen($ticket->subject) > 50 ? substr($ticket->subject, 0, 50) . '...' : $ticket->subject }}
												</a>
											</td>
											<td>
												<span class="badge bg-{{ $ticket->status === 'open' ? 'warning' : ($ticket->status === 'in_progress' ? 'info' : 'success') }}">
													{{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
												</span>
											</td>
											<td>
												@if($ticket->agent)
													{{ $ticket->agent->first_name }} {{ $ticket->agent->last_name }}
												@else
													<span class="text-muted">Unassigned</span>
												@endif
											</td>
											<td>{{ $ticket->created_at->format('M j, Y') }}</td>
											<td>
												<a href="{{ route('whatsapp.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">View</a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@else
						<div class="text-center text-muted py-4">
							<i class="fas fa-ticket-alt fa-3x mb-3"></i>
							<p>No tickets found for this line yet.</p>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
