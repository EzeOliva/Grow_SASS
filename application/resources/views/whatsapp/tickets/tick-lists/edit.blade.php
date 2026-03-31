@extends('whatsapp.layouts.app')

@section('whatsapp-content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-edit me-2"></i>
                Edit Tick List Item
            </h4>
            <p class="text-muted mb-0">Ticket #{{ $ticket->id }} - {{ $ticket->subject }}</p>
        </div>
        <div>
            <a href="{{ route('whatsapp.tickets.tick-lists.index', $ticket) }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back to Tick Lists
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="whatsapp-card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-edit me-2"></i>
                Edit Tick List Item: {{ $tickList->title }}
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('whatsapp.tickets.tick-lists.update', [$ticket, $tickList]) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title', $tickList->title) }}" 
                                   placeholder="Enter tick list item title" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Enter detailed description (optional)">{{ old('description', $tickList->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                            <select class="form-select @error('priority') is-invalid @enderror" 
                                    id="priority" name="priority" required>
                                <option value="">Select Priority</option>
                                <option value="1" {{ old('priority', $tickList->priority) == '1' ? 'selected' : '' }}>Low</option>
                                <option value="2" {{ old('priority', $tickList->priority) == '2' ? 'selected' : '' }}>Medium</option>
                                <option value="3" {{ old('priority', $tickList->priority) == '3' ? 'selected' : '' }}>High</option>
                                <option value="4" {{ old('priority', $tickList->priority) == '4' ? 'selected' : '' }}>Urgent</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="assigned_to" class="form-label">Assign To</label>
                            <select class="form-select @error('assigned_to') is-invalid @enderror" 
                                    id="assigned_to" name="assigned_to">
                                <option value="">Unassigned</option>
                                @foreach($agents as $agent)
                                    <option value="{{ $agent->id }}" {{ old('assigned_to', $tickList->assigned_to) == $agent->id ? 'selected' : '' }}>
                                        {{ $agent->first_name }} {{ $agent->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('assigned_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="due_date" class="form-label">Due Date</label>
                            <input type="date" class="form-control @error('due_date') is-invalid @enderror" 
                                   id="due_date" name="due_date" 
                                   value="{{ old('due_date', $tickList->due_date ? $tickList->due_date->format('Y-m-d') : '') }}">
                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Leave empty if no due date is required</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Current Status</label>
                            <div class="d-flex align-items-center">
                                <span class="status-badge status-{{ $tickList->status }} me-2">
                                    {{ ucfirst($tickList->status) }}
                                </span>
                                <a href="{{ route('whatsapp.tickets.tick-lists.index', $ticket) }}" 
                                   class="btn btn-sm btn-outline-secondary">
                                    Change Status
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('whatsapp.tickets.tick-lists.index', $ticket) }}" class="btn btn-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-whatsapp">
                        <i class="fas fa-save me-1"></i>Update Tick List Item
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Priority color preview
    const prioritySelect = document.getElementById('priority');
    const priorityPreview = document.createElement('div');
    priorityPreview.className = 'mt-2';
    prioritySelect.parentNode.appendChild(priorityPreview);

    function updatePriorityPreview() {
        const priority = prioritySelect.value;
        const colors = {
            '1': 'success',
            '2': 'info', 
            '3': 'warning',
            '4': 'danger'
        };
        const texts = {
            '1': 'Low',
            '2': 'Medium',
            '3': 'High', 
            '4': 'Urgent'
        };

        if (priority && colors[priority]) {
            priorityPreview.innerHTML = `<span class="badge bg-${colors[priority]}">${texts[priority]}</span>`;
        } else {
            priorityPreview.innerHTML = '';
        }
    }

    prioritySelect.addEventListener('change', updatePriorityPreview);
    updatePriorityPreview();
});
</script>
@endpush

