@extends('layout.wrapper')

@section('content')
<div class="container-fluid">
	<div class="row page-titles">
		<div class="col-md-6">
			<h4 class="mb-0">Edit Ticket Type</h4>
			<p class="text-muted mb-0">Modify ticket classification: {{ $ticketType->name }}</p>
		</div>
		<div class="col-md-6 text-right">
			<a href="{{ route('whatsapp.ticket-types.show', $ticketType) }}" class="btn btn-secondary">
				<i class="fas fa-arrow-left me-2"></i>Back to Details
			</a>
		</div>
	</div>

	<div class="card">
		<div class="card-body">
			<form action="{{ route('whatsapp.ticket-types.update', $ticketType) }}" method="POST">
				@csrf
				@method('PUT')
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="name" class="form-label">Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('name') is-invalid @enderror" 
								id="name" name="name" value="{{ old('name', $ticketType->name) }}" 
								placeholder="e.g., Bug, Inquiry, Feature Request" required>
							@error('name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="color" class="form-label">Color <span class="text-danger">*</span></label>
							<div class="input-group">
								<input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
									id="color" name="color" value="{{ old('color', $ticketType->color) }}" required>
								<input type="text" class="form-control" value="{{ old('color', $ticketType->color) }}" 
									placeholder="#6c757d" readonly>
							</div>
							@error('color')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
				</div>

				<div class="row mt-3">
					<div class="col-md-6">
						<div class="form-group">
							<label for="sort_order" class="form-label">Sort Order</label>
							<input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
								id="sort_order" name="sort_order" value="{{ old('sort_order', $ticketType->sort_order) }}" 
								placeholder="Leave empty for auto-assignment" min="0">
							@error('sort_order')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
							<small class="form-text text-muted">Lower numbers appear first in lists</small>
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="description" class="form-label">Description</label>
							<textarea class="form-control @error('description') is-invalid @enderror" 
								id="description" name="description" rows="3" 
								placeholder="Optional description of this ticket type">{{ old('description', $ticketType->description) }}</textarea>
							@error('description')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
				</div>

				<div class="row mt-4">
					<div class="col-12">
						<div class="form-group">
							<button type="submit" class="btn btn-primary">
								<i class="fas fa-save me-2"></i>Update Ticket Type
							</button>
							<a href="{{ route('whatsapp.ticket-types.show', $ticketType) }}" class="btn btn-secondary ms-2">Cancel</a>
						</div>
					</div>
				</div>
			</form>
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
});
</script>
@endsection
