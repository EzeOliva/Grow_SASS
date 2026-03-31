@extends('layout.wrapper')

@section('content')
<div class="container-fluid">
	<div class="row page-titles mb-4">
		<div class="col-md-6">
			<h3 class="text-dark fw-bold mb-2">
				<i class="fas fa-address-book text-primary me-3"></i>WhatsApp Contacts
			</h3>
			<p class="text-muted fs-6 mb-0">Manage your WhatsApp contacts and customer information</p>
		</div>
		<div class="col-md-6 text-end">
			<a href="{{ route('whatsapp.contacts.create') }}" class="btn btn-primary btn-lg shadow-sm">
				<i class="fas fa-plus-circle me-2"></i>Add New Contact
			</a>
		</div>
	</div>

	<div class="card border-0 shadow-sm">
		<div class="card-header bg-white border-0 py-3">
			<div class="row align-items-center">
				<div class="col-md-6">
					<h5 class="text-dark fw-semibold mb-0">
						<i class="fas fa-list-ul text-info me-2"></i>Contact Management
					</h5>
				</div>
				<div class="col-md-6 text-end">
					<span class="badge bg-light text-dark fs-6 px-3 py-2">
						<i class="fas fa-info-circle me-1"></i>{{ isset($contacts) ? $contacts->count() : 0 }} Contacts
					</span>
				</div>
			</div>
		</div>
		<div class="card-body p-0">
			@if(isset($contacts) && $contacts->count())
				<div class="table-responsive">
					<table class="table table-hover mb-0">
						<thead class="table-light">
							<tr>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-user me-2 text-primary"></i>Contact Details
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-phone me-2 text-success"></i>Contact Info
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-tags me-2 text-info"></i>Tags
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-user-tie me-2 text-warning"></i>Assigned Agent
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-clock me-2 text-secondary"></i>Last Contact
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold text-center">
									<i class="fas fa-cogs me-2 text-dark"></i>Actions
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($contacts as $contact)
								<tr class="border-bottom">
									<td class="py-3 px-4">
										<div class="d-flex align-items-center">
											<div class="contact-avatar me-3">
												@if($contact->avatar)
													<img src="{{ $contact->avatar }}" alt="Avatar" class="rounded-circle" width="48" height="48">
												@else
													<i class="fas fa-user fa-2x text-primary"></i>
												@endif
											</div>
											<div>
												<h6 class="text-dark fw-semibold mb-1">{{ $contact->name ?? 'Unknown Name' }}</h6>
												<small class="text-muted">
													<i class="fas fa-map-marker-alt me-1"></i>
													{{ $contact->location ?? 'No location' }}
												</small>
											</div>
										</div>
									</td>
									<td class="py-3 px-4">
										<div class="d-flex flex-column">
											<div class="fw-semibold">
												<i class="fas fa-phone me-1 text-success"></i>
												{{ $contact->phone ?? 'No phone' }}
											</div>
											@if($contact->email)
												<small class="text-muted">
													<i class="fas fa-envelope me-1"></i>
													{{ $contact->email }}
												</small>
											@endif
										</div>
									</td>
									<td class="py-3 px-4">
										@if($contact->tags && $contact->tags->count())
											<div class="d-flex flex-wrap gap-1">
												@foreach($contact->tags->take(3) as $tag)
													<span class="badge bg-light text-dark fs-6 px-2 py-1" style="border: 1px solid {{ $tag->color }}; color: {{ $tag->color }} !important;">
														{{ $tag->name }}
													</span>
												@endforeach
												@if($contact->tags->count() > 3)
													<span class="badge bg-secondary fs-6 px-2 py-1">
														+{{ $contact->tags->count() - 3 }}
													</span>
												@endif
											</div>
										@else
											<span class="text-muted">No tags</span>
										@endif
									</td>
									<td class="py-3 px-4">
										@if($contact->assigned_agent)
											<span class="badge bg-info bg-opacity-10 text-info fs-6 px-3 py-2">
												<i class="fas fa-user-tie me-1"></i>{{ $contact->assigned_agent->first_name }} {{ $contact->assigned_agent->last_name }}
											</span>
										@else
											<span class="badge bg-secondary fs-6 px-3 py-2">
												<i class="fas fa-user-slash me-1"></i>Unassigned
											</span>
										@endif
									</td>
									<td class="py-3 px-4">
										<small class="text-muted">
											<i class="fas fa-clock me-1"></i>
											{{ $contact->last_contact_at ? $contact->last_contact_at->diffForHumans() : 'Never' }}
										</small>
									</td>
									<td class="py-3 px-4 text-center">
										<div class="btn-group" role="group">
											<a href="{{ route('whatsapp.contacts.show', $contact) }}" class="btn btn-outline-primary btn-sm me-2">
												<i class="fas fa-eye me-1"></i>View
											</a>
											<a href="{{ route('whatsapp.contacts.edit', $contact) }}" class="btn btn-outline-warning btn-sm">
												<i class="fas fa-edit me-1"></i>Edit
											</a>
										</div>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				@if(method_exists($contacts, 'links'))
					<div class="card-footer bg-white border-0 py-3">
						{{ $contacts->links() }}
					</div>
				@endif
			@else
				<div class="text-center py-5">
					<div class="empty-state">
						<i class="fas fa-address-book fa-4x text-muted mb-4"></i>
						<h5 class="text-dark fw-semibold mb-3">No Contacts Found</h5>
						<p class="text-muted fs-6 mb-4">Start building your contact list by adding your first WhatsApp contact.</p>
						<a href="{{ route('whatsapp.contacts.create') }}" class="btn btn-primary">
							<i class="fas fa-plus me-2"></i>Add First Contact
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

.contact-avatar {
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

.gap-1 {
	gap: 0.25rem;
}
</style>
@endsection

