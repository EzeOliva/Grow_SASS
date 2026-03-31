@extends('layout.wrapper')

@section('content')
<div class="container-fluid">
	<div class="row page-titles">
		<div class="col-md-6">
			<h4 class="mb-0">{{ $ticketType->name }}</h4>
			<p class="text-muted mb-0">Ticket Type Details</p>
		</div>
		<div class="col-md-6 text-right">
			<a href="{{ route('whatsapp.ticket-types.edit', $ticketType) }}" class="btn btn-primary">
				<i class="fas fa-edit me-2"></i>Edit
			</a>
			<a href="{{ route('whatsapp.ticket-types.index') }}" class="btn btn-secondary">
				<i class="fas fa-arrow-left me-2"></i>Back to List
			</a>
		</div>
	</div>

	<div class="row">
		<div class="col-md-4">
			<div class="card">
				<div class="card-header">
					<h5 class="card-title mb-0">Type Information</h5>
				</div>
				<div class="card-body">
					<div class="d-flex align-items-center mb-3">
						<div class="color-preview me-3" style="width: 20px; height: 20px; background-color: {{ $ticketType->color }}; border-radius: 4px;"></div>
						<span class="fw-bold">{{ $ticketType->name }}</span>
					</div>
					
					@if($ticketType->description)
						<p class="text-muted">{{ $ticketType->description }}</p>
					@endif
					
					<div class="row">
						<div class="col-6">
							<small class="text-muted">Status</small>
							<div>
								<span class="badge {{ $ticketType->is_active ? 'bg-success' : 'bg-secondary' }}">
									{{ $ticketType->is_active ? 'Active' : 'Inactive' }}
								</span>
							</div>
						</div>
						<div class="col-6">
							<small class="text-muted">Sort Order</small>
							<div class="fw-bold">{{ $ticketType->sort_order }}</div>
						</div>
					</div>
					
					@if($ticketType->creator)
						<hr>
						<small class="text-muted">Created by</small>
						<div>{{ $ticketType->creator->first_name }} {{ $ticketType->creator->last_name }}</div>
						<small class="text-muted">{{ $ticketType->created_at->format('M j, Y g:i A') }}</small>
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
							<p>No tickets found for this type yet.</p>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
