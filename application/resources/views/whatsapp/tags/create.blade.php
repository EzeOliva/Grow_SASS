@extends('layout.wrapper')

@section('content')
<div class="container-fluid">
	<div class="row page-titles mb-4">
		<div class="col-md-6">
			<h3 class="text-dark fw-bold mb-2">
				<i class="fas fa-plus-circle text-primary me-3"></i>Create New Tag
			</h3>
			<p class="text-muted fs-6 mb-0">Add a new tag for organizing contacts and tickets with custom colors and categories</p>
		</div>
		<div class="col-md-6 text-end">
			<a href="{{ route('whatsapp.tags.index') }}" class="btn btn-outline-secondary btn-lg">
				<i class="fas fa-arrow-left me-2"></i>Back to Tags
			</a>
		</div>
	</div>

	<div class="row justify-content-center">
		<div class="col-lg-8">
			<div class="card border-0 shadow-sm">
				<div class="card-header bg-white border-0 py-4">
					<h5 class="text-dark fw-semibold mb-0">
						<i class="fas fa-tag text-primary me-2"></i>Tag Information
					</h5>
				</div>
				<div class="card-body p-4">
					<form action="{{ route('whatsapp.tags.store') }}" method="POST" id="tagForm">
						@csrf
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="name" class="form-label">Tag Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('name') is-invalid @enderror" 
								id="name" name="name" value="{{ old('name') }}" 
								placeholder="e.g., VIP, Priority, New Customer" required>
							@error('name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="type" class="form-label">Tag Type <span class="text-danger">*</span></label>
							<select class="form-control @error('type') is-invalid @enderror" id="type" name="type" required>
								<option value="">Select tag type</option>
								@foreach($tagTypes as $value => $label)
									<option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>
										{{ $label }}
									</option>
								@endforeach
							</select>
							@error('type')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
				</div>

				<div class="row mt-3">
					<div class="col-md-6">
						<div class="form-group">
							<label for="color" class="form-label">Color <span class="text-danger">*</span></label>
							<div class="input-group">
								<input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
									id="color" name="color" value="{{ old('color', '#6c757d') }}" required>
								<input type="text" class="form-control" value="{{ old('color', '#6c757d') }}" 
									placeholder="#6c757d" readonly>
							</div>
							@error('color')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="description" class="form-label">Description</label>
							<textarea class="form-control @error('description') is-invalid @enderror" 
								id="description" name="description" rows="3" 
								placeholder="Optional description of this tag">{{ old('description') }}</textarea>
							@error('description')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
				</div>

				<div class="row mt-5">
					<div class="col-12">
						<div class="d-flex justify-content-end gap-3">
							<a href="{{ route('whatsapp.tags.index') }}" class="btn btn-outline-secondary btn-lg">
								<i class="fas fa-times me-2"></i>Cancel
							</a>
							<button type="submit" class="btn btn-primary btn-lg">
								<i class="fas fa-save me-2"></i>Create Tag
							</button>
						</div>
					</div>
				</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const colorInput = document.getElementById('color');
    const colorText = colorInput.nextElementSibling;
    
    colorInput.addEventListener('input', function() {
        colorText.value = this.value;
    });
    
    colorText.addEventListener('input', function() {
        if (this.value.match(/^#[0-9A-Fa-f]{6}$/)) {
            colorInput.value = this.value;
        }
    });
    
    // Simple form validation without preventing submission
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // Check if all required fields are filled
            const name = document.getElementById('name').value;
            const type = document.getElementById('type').value;
            const color = document.getElementById('color').value;
            
            if (!name || !type || !color) {
                e.preventDefault();
                alert('Please fill in all required fields');
                return false;
            }
            
            // Let the form submit normally
        });
    }
});
</script>

<style>
/* Enhanced form styling */
.card {
	border-radius: 12px;
	overflow: hidden;
}

.card-header {
	border-bottom: 1px solid #e9ecef;
	background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
}

.form-control, .form-select {
	border-radius: 8px;
	border: 1px solid #e9ecef;
	transition: all 0.2s ease;
	padding: 0.75rem 1rem;
}

.form-control:focus, .form-select:focus {
	border-color: #0d6efd;
	box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.form-control-lg {
	padding: 1rem 1.25rem;
	font-size: 1.1rem;
}

.form-select-lg {
	padding: 1rem 1.25rem;
	font-size: 1.1rem;
}

.btn-lg {
	padding: 0.75rem 1.5rem;
	font-weight: 600;
	border-radius: 8px;
}

.form-label {
	font-weight: 600;
	color: #495057;
	margin-bottom: 0.5rem;
}

.form-text {
	font-size: 0.875rem;
	color: #6c757d;
	margin-top: 0.25rem;
}

.input-group .form-control-color {
	width: 60px;
	height: 48px;
	border-radius: 8px 0 0 8px;
}

.input-group .form-control:last-child {
	border-radius: 0 8px 8px 0;
}

/* Responsive adjustments */
@media (max-width: 768px) {
	.page-titles .col-md-6:last-child {
		text-align: left !important;
		margin-top: 1rem;
	}
	
	.btn-lg {
		padding: 0.625rem 1.25rem;
		font-size: 1rem;
	}
	
	.gap-3 {
		gap: 0.75rem !important;
	}
}

/* Animation for form elements */
.form-control, .form-select {
	transition: all 0.3s ease;
}

.form-control:hover, .form-select:hover {
	border-color: #ced4da;
}

/* Enhanced button styling */
.btn-primary {
	background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
	border: none;
	box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
}

.btn-primary:hover {
	background: linear-gradient(135deg, #0056b3 0%, #004085 100%);
	transform: translateY(-1px);
	box-shadow: 0 6px 16px rgba(13, 110, 253, 0.4);
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
