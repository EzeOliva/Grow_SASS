@extends('layout.wrapper')

@section('content')
<div class="container-fluid">
	<div class="row page-titles mb-4">
		<div class="col-md-6">
			<h3 class="text-dark fw-bold mb-2">
				<i class="fas fa-tags text-primary me-3"></i>WhatsApp Tags
			</h3>
			<p class="text-muted fs-6 mb-0">Organize contacts and tickets with custom tags for better categorization</p>
		</div>
		<div class="col-md-6 text-end">
			<a href="{{ route('whatsapp.tags.create') }}" class="btn btn-primary btn-lg shadow-sm">
				<i class="fas fa-plus-circle me-2"></i>Create New Tag
			</a>
		</div>
	</div>

	<!-- Filters and Search -->
	<div class="card border-0 shadow-sm mb-4">
		<div class="card-body">
			<form method="GET" action="{{ route('whatsapp.tags.index') }}" class="row g-3">
				<div class="col-md-4">
					<label for="search" class="form-label fw-semibold">
						<i class="fas fa-search me-2 text-primary"></i>Search Tags
					</label>
					<input type="text" class="form-control" id="search" name="search" 
						   value="{{ request('search') }}" placeholder="Search by tag name...">
				</div>
				<div class="col-md-3">
					<label for="type" class="form-label fw-semibold">
						<i class="fas fa-filter me-2 text-info"></i>Filter by Type
					</label>
					<select class="form-select" id="type" name="type">
						<option value="">All Types</option>
						@foreach($tagTypes as $key => $label)
							<option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>
								{{ $label }}
							</option>
						@endforeach
					</select>
				</div>
				<div class="col-md-3">
					<label for="status" class="form-label fw-semibold">
						<i class="fas fa-toggle-on me-2 text-success"></i>Filter by Status
					</label>
					<select class="form-select" id="status" name="status">
						<option value="">All Status</option>
						<option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
						<option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
					</select>
				</div>
				<div class="col-md-2 d-flex align-items-end">
					<button type="submit" class="btn btn-primary me-2">
						<i class="fas fa-search me-1"></i>Search
					</button>
					<a href="{{ route('whatsapp.tags.index') }}" class="btn btn-outline-secondary">
						<i class="fas fa-times me-1"></i>Clear
					</a>
				</div>
			</form>
		</div>
	</div>

	<div class="card border-0 shadow-sm">
		<div class="card-header bg-white border-0 py-3">
			<div class="row align-items-center">
				<div class="col-md-6">
					<h5 class="text-dark fw-semibold mb-0">
						<i class="fas fa-list-ul text-info me-2"></i>Tag Management
					</h5>
				</div>
				<div class="col-md-6 text-end">
					<span class="badge bg-light text-dark fs-6 px-3 py-2">
						<i class="fas fa-info-circle me-1"></i>{{ isset($tags) ? $tags->total() : 0 }} Tags
					</span>
				</div>
			</div>
		</div>
		<div class="card-body p-0">
			@if(isset($tags) && $tags->count())
				<div class="table-responsive">
					<table class="table table-hover mb-0">
						<thead class="table-light">
							<tr>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-tag me-2 text-primary"></i>Tag Details
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-layer-group me-2 text-info"></i>Type
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-toggle-on me-2 text-success"></i>Status
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold text-center">
									<i class="fas fa-cogs me-2 text-warning"></i>Actions
								</th>
							</tr>
						</thead>
						<tbody>
							@foreach($tags as $tag)
								<tr class="border-bottom">
									<td class="py-3 px-4">
										<div class="d-flex align-items-center">
											<div class="color-dot me-3" style="width: 18px; height: 18px; background-color: {{ $tag->color ?? '#6c757d' }}; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
											<div>
												<h6 class="text-dark fw-semibold mb-1">{{ $tag->name }}</h6>
												@if($tag->description)
													<small class="text-muted">{{ strlen($tag->description) > 70 ? substr($tag->description, 0, 70) . '...' : $tag->description }}</small>
												@endif
											</div>
										</div>
									</td>
									<td class="py-3 px-4">
										<span class="badge bg-info bg-opacity-10 text-info fs-6 px-3 py-2">
											<i class="fas fa-layer-group me-1"></i>{{ ucfirst($tag->type) }}
										</span>
									</td>
									<td class="py-3 px-4">
										<span class="badge {{ $tag->is_active ? 'bg-success' : 'bg-secondary' }} fs-6 px-3 py-2">
											<i class="fas {{ $tag->is_active ? 'fa-check-circle' : 'fa-times-circle' }} me-1"></i>
											{{ $tag->is_active ? 'Active' : 'Inactive' }}
										</span>
									</td>
									<td class="py-3 px-4 text-center">
										<div class="btn-group" role="group">
											<a href="{{ route('whatsapp.tags.show', $tag) }}" class="btn btn-outline-primary btn-sm" title="View Details">
												<i class="fas fa-eye"></i>
											</a>
											<a href="{{ route('whatsapp.tags.edit', $tag) }}" class="btn btn-outline-warning btn-sm" title="Edit Tag">
												<i class="fas fa-edit"></i>
											</a>
											<button type="button" class="btn btn-outline-danger btn-sm" 
													onclick="confirmDelete({{ $tag->id }}, '{{ $tag->name }}')" title="Delete Tag">
												<i class="fas fa-trash"></i>
											</button>
										</div>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				@if(method_exists($tags, 'links') && $tags->hasPages())
					<div class="card-footer bg-white border-0 py-3">
						<div class="d-flex justify-content-between align-items-center">
							<div class="text-muted">
								Showing {{ $tags->firstItem() }} to {{ $tags->lastItem() }} of {{ $tags->total() }} results
							</div>
							{{ $tags->appends(request()->query())->links() }}
						</div>
					</div>
				@endif
			@else
				<div class="text-center py-5">
					<div class="empty-state">
						<i class="fas fa-tags fa-4x text-muted mb-4"></i>
						<h5 class="text-dark fw-semibold mb-3">No Tags Found</h5>
						<p class="text-muted fs-6 mb-4">Start organizing your contacts and tickets by creating your first tag.</p>
						<a href="{{ route('whatsapp.tags.create') }}" class="btn btn-primary">
							<i class="fas fa-plus me-2"></i>Create First Tag
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

.color-dot {
	transition: transform 0.2s ease;
}

.color-dot:hover {
	transform: scale(1.2);
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

/* Responsive adjustments */
@media (max-width: 768px) {
	.page-titles .col-md-6:last-child {
		text-align: left !important;
		margin-top: 1rem;
	}
	
	.btn-group .btn {
		padding: 0.375rem 0.5rem;
		font-size: 0.875rem;
	}
	
	.table-responsive {
		font-size: 0.875rem;
	}
}

/* Enhanced form styling */
.form-control, .form-select {
	border-radius: 8px;
	border: 1px solid #e9ecef;
	transition: all 0.2s ease;
}

.form-control:focus, .form-select:focus {
	border-color: #0d6efd;
	box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Pagination styling */
.pagination {
	margin-bottom: 0;
}

.page-link {
	border-radius: 6px;
	margin: 0 2px;
	border: 1px solid #e9ecef;
	color: #6c757d;
}

.page-link:hover {
	background-color: #e9ecef;
	border-color: #dee2e6;
	color: #495057;
}

.page-item.active .page-link {
	background-color: #0d6efd;
	border-color: #0d6efd;
}
</style>

<script>
function confirmDelete(tagId, tagName) {
	if (confirm(`Are you sure you want to delete the tag "${tagName}"? This action cannot be undone.`)) {
		// Create a form and submit it
		const form = document.createElement('form');
		form.method = 'POST';
		form.action = `/whatsapp/tags/${tagId}`;
		
		// Add CSRF token
		const csrfToken = document.querySelector('meta[name="csrf-token"]');
		if (csrfToken) {
			const tokenInput = document.createElement('input');
			tokenInput.type = 'hidden';
			tokenInput.name = '_token';
			tokenInput.value = csrfToken.getAttribute('content');
			form.appendChild(tokenInput);
		}
		
		// Add method override for DELETE
		const methodInput = document.createElement('input');
		methodInput.type = 'hidden';
		methodInput.name = '_method';
		methodInput.value = 'DELETE';
		form.appendChild(methodInput);
		
		document.body.appendChild(form);
		form.submit();
	}
}

// Auto-submit form on filter change
document.addEventListener('DOMContentLoaded', function() {
	const typeSelect = document.getElementById('type');
	const statusSelect = document.getElementById('status');
	
	if (typeSelect) {
		typeSelect.addEventListener('change', function() {
			this.form.submit();
		});
	}
	
	if (statusSelect) {
		statusSelect.addEventListener('change', function() {
			this.form.submit();
		});
	}
});
</script>
@endsection


