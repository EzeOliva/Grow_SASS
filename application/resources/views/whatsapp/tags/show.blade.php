@extends('layout.wrapper')

@section('content')
<div class="container-fluid">
	<div class="row page-titles mb-4">
		<div class="col-md-6">
			<h3 class="text-dark fw-bold mb-2">
				<div class="d-flex align-items-center">
					<div class="color-dot me-3" style="width: 24px; height: 24px; background-color: {{ $tag->color }}; border-radius: 50%; border: 3px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.15);"></div>
					{{ $tag->name }}
				</div>
			</h3>
			<p class="text-muted fs-6 mb-0">Tag details and usage statistics</p>
		</div>
		<div class="col-md-6 text-end">
			<div class="btn-group" role="group">
				<a href="{{ route('whatsapp.tags.edit', $tag) }}" class="btn btn-warning btn-lg">
					<i class="fas fa-edit me-2"></i>Edit Tag
				</a>
				<a href="{{ route('whatsapp.tags.index') }}" class="btn btn-outline-secondary btn-lg">
					<i class="fas fa-arrow-left me-2"></i>Back to List
				</a>
			</div>
		</div>
	</div>

	<div class="row g-4">
		<div class="col-lg-4">
			<div class="card border-0 shadow-sm">
				<div class="card-header bg-white border-0 py-4">
					<h5 class="text-dark fw-semibold mb-0">
						<i class="fas fa-info-circle text-primary me-2"></i>Tag Information
					</h5>
				</div>
				<div class="card-body p-4">
					<div class="d-flex align-items-center mb-4">
						<div class="color-preview me-3" style="width: 32px; height: 32px; background-color: {{ $tag->color }}; border-radius: 8px; border: 2px solid #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.15);"></div>
						<div>
							<h6 class="text-dark fw-bold mb-0">{{ $tag->name }}</h6>
							<small class="text-muted">Tag ID: #{{ $tag->id }}</small>
						</div>
					</div>
					
					<div class="mb-4">
						<label class="form-label fw-semibold text-muted">
							<i class="fas fa-layer-group me-2 text-info"></i>Type
						</label>
						<div>
							<span class="badge bg-info bg-opacity-10 text-info fs-6 px-3 py-2">
								<i class="fas fa-layer-group me-1"></i>{{ ucfirst($tag->type) }}
							</span>
						</div>
					</div>
					
					@if($tag->description)
						<div class="mb-4">
							<label class="form-label fw-semibold text-muted">
								<i class="fas fa-align-left me-2 text-secondary"></i>Description
							</label>
							<div class="bg-light p-3 rounded">
								<p class="mb-0 text-dark">{{ $tag->description }}</p>
							</div>
						</div>
					@endif
					
					<div class="mb-4">
						<label class="form-label fw-semibold text-muted">
							<i class="fas fa-toggle-on me-2 text-success"></i>Status
						</label>
						<div>
							<span class="badge {{ $tag->is_active ? 'bg-success' : 'bg-secondary' }} fs-6 px-3 py-2">
								<i class="fas {{ $tag->is_active ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
								{{ $tag->is_active ? 'Active' : 'Inactive' }}
							</span>
						</div>
					</div>
					
					@if($tag->creator)
						<hr class="my-4">
						<div class="mb-3">
							<label class="form-label fw-semibold text-muted">
								<i class="fas fa-user me-2 text-primary"></i>Created by
							</label>
							<div class="d-flex align-items-center">
								<div class="avatar-sm me-3">
									<div class="avatar-title bg-primary text-white rounded-circle">
										{{ substr($tag->creator->first_name, 0, 1) }}{{ substr($tag->creator->last_name, 0, 1) }}
									</div>
								</div>
								<div>
									<div class="fw-semibold">{{ $tag->creator->first_name }} {{ $tag->creator->last_name }}</div>
									<small class="text-muted">{{ $tag->created_at->format('M j, Y g:i A') }}</small>
								</div>
							</div>
						</div>
					@endif
				</div>
			</div>
		</div>
		
		<div class="col-lg-8">
			<div class="card border-0 shadow-sm">
				<div class="card-header bg-white border-0 py-4">
					<h5 class="text-dark fw-semibold mb-0">
						<i class="fas fa-chart-bar text-success me-2"></i>Usage Statistics
					</h5>
				</div>
				<div class="card-body p-4">
					<div class="row g-4">
						<div class="col-md-4">
							<div class="text-center p-4 bg-primary bg-opacity-10 rounded-3">
								<div class="h1 text-primary mb-2 fw-bold">{{ $usageStats['total_usage'] ?? 0 }}</div>
								<div class="fw-semibold text-dark">Total Usage</div>
								<small class="text-muted">All applications</small>
							</div>
						</div>
						<div class="col-md-4">
							<div class="text-center p-4 bg-success bg-opacity-10 rounded-3">
								<div class="h1 text-success mb-2 fw-bold">{{ $usageStats['ticket_usage'] ?? 0 }}</div>
								<div class="fw-semibold text-dark">Ticket Usage</div>
								<small class="text-muted">WhatsApp tickets</small>
							</div>
						</div>
						<div class="col-md-4">
							<div class="text-center p-4 bg-info bg-opacity-10 rounded-3">
								<div class="h1 text-info mb-2 fw-bold">{{ $usageStats['contact_usage'] ?? 0 }}</div>
								<div class="fw-semibold text-dark">Contact Usage</div>
								<small class="text-muted">WhatsApp contacts</small>
							</div>
						</div>
					</div>
					
					@if(($usageStats['total_usage'] ?? 0) > 0)
						<hr class="my-4">
						<div class="text-center p-4 bg-light rounded-3">
							<i class="fas fa-check-circle text-success fa-2x mb-3"></i>
							<h6 class="text-dark fw-semibold mb-2">Active Tag</h6>
							<p class="text-muted mb-0">This tag is actively used across your WhatsApp system.</p>
						</div>
					@else
						<hr class="my-4">
						<div class="text-center p-4 bg-light rounded-3">
							<i class="fas fa-tag text-muted fa-3x mb-3"></i>
							<h6 class="text-dark fw-semibold mb-2">Unused Tag</h6>
							<p class="text-muted mb-3">This tag hasn't been used yet.</p>
							<small class="text-muted">Start using it by assigning it to tickets or contacts.</small>
						</div>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

<style>
/* Enhanced styling for show page */
.card {
	border-radius: 12px;
	overflow: hidden;
}

.card-header {
	border-bottom: 1px solid #e9ecef;
	background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.color-dot {
	transition: transform 0.2s ease;
}

.color-dot:hover {
	transform: scale(1.1);
}

.avatar-sm {
	width: 40px;
	height: 40px;
}

.avatar-title {
	width: 100%;
	height: 100%;
	display: flex;
	align-items: center;
	justify-content: center;
	font-weight: 600;
	font-size: 0.875rem;
}

.bg-opacity-10 {
	background-color: rgba(13, 202, 240, 0.1) !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
	.page-titles .col-md-6:last-child {
		text-align: left !important;
		margin-top: 1rem;
	}
	
	.btn-group {
		flex-direction: column;
		width: 100%;
	}
	
	.btn-group .btn {
		margin-bottom: 0.5rem;
	}
	
	.btn-group .btn:last-child {
		margin-bottom: 0;
	}
}

/* Enhanced button styling */
.btn-warning {
	background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
	border: none;
	box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
	color: #000;
}

.btn-warning:hover {
	background: linear-gradient(135deg, #e0a800 0%, #d39e00 100%);
	transform: translateY(-1px);
	box-shadow: 0 6px 16px rgba(255, 193, 7, 0.4);
	color: #000;
}

.btn-outline-secondary {
	border: 2px solid #6c757d;
	color: #6c757d;
	background: transparent;
}

.btn-outline-secondary:hover {
	background: #6c757d;
	border-color: #6c757d;
	color: white;
	transform: translateY(-1px);
}
</style>
@endsection
