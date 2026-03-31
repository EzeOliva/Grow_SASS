@extends('layout.wrapper')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="row page-titles">
        <div class="col-md-6">
            <div class="d-flex align-items-center">
                <div class="mr-3">
                    <i class="fas fa-ticket-alt fa-2x text-primary"></i>
                </div>
                <div>
                    <h4 class="mb-0">Edit Ticket #{{ $ticket->id }}</h4>
                    <p class="text-muted mb-0">Update ticket information and details</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('whatsapp.tickets.show', $ticket) }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-eye me-2"></i>View Ticket
            </a>
            <a href="{{ route('whatsapp.tickets.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to Tickets
            </a>
        </div>
    </div>

    <!-- Ticket Edit Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Edit Ticket Information
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('whatsapp.tickets.update', $ticket) }}" method="POST" id="ticket-edit-form">
                        @csrf
                        @method('PUT')
                        
                        <!-- Contact Information Section -->
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-user me-2"></i>Contact Information
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_name" class="control-label required">
                                        Contact Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('contact_name') is-invalid @enderror" 
                                           id="contact_name" 
                                           name="contact_name" 
                                           value="{{ old('contact_name', $ticket->contact_name) }}"
                                           placeholder="Enter contact name"
                                           required>
                                    @error('contact_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_email" class="control-label">
                                        Email Address
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('contact_email') is-invalid @enderror" 
                                           id="contact_email" 
                                           name="contact_email" 
                                           value="{{ old('contact_email', $ticket->contact_email) }}"
                                           placeholder="Enter email address">
                                    @error('contact_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_phone" class="control-label required">
                                        Phone Number <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('contact_phone') is-invalid @enderror" 
                                           id="contact_phone" 
                                           name="contact_phone" 
                                           value="{{ old('contact_phone', $ticket->contact_phone) }}"
                                           placeholder="Enter phone number"
                                           required>
                                    @error('contact_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject" class="control-label required">
                                        Subject <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('subject') is-invalid @enderror" 
                                           id="subject" 
                                           name="subject" 
                                           value="{{ old('subject', $ticket->subject) }}"
                                           placeholder="Enter ticket subject"
                                           required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Details Section -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-cog me-2"></i>Ticket Details
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status" class="control-label required">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status" 
                                            required>
                                        <option value="open" {{ old('status', $ticket->status) == 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="in_progress" {{ old('status', $ticket->status) == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="closed" {{ old('status', $ticket->status) == 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="priority" class="control-label required">
                                        Priority <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('priority') is-invalid @enderror" 
                                            id="priority" 
                                            name="priority" 
                                            required>
                                        <option value="low" {{ old('priority', $ticket->priority) == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority', $ticket->priority) == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority', $ticket->priority) == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ old('priority', $ticket->priority) == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @error('priority')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="channel" class="control-label required">
                                        Channel <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-control @error('channel') is-invalid @enderror" 
                                            id="channel" 
                                            name="channel" 
                                            required>
                                        <option value="whatsapp" {{ old('channel', $ticket->channel) == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                        <option value="email" {{ old('channel', $ticket->channel) == 'email' ? 'selected' : '' }}>Email</option>
                                    </select>
                                    @error('channel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="category" class="control-label">
                                        Category
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('category') is-invalid @enderror" 
                                           id="category" 
                                           name="category" 
                                           value="{{ old('category', $ticket->category) }}"
                                           placeholder="Enter category">
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="agent_id" class="control-label">
                                        Assign Agent
                                    </label>
                                    <select class="form-control @error('agent_id') is-invalid @enderror" 
                                            id="agent_id" 
                                            name="agent_id">
                                        <option value="">Unassigned</option>
                                        @foreach($agents as $agent)
                                            <option value="{{ $agent->id }}" {{ old('agent_id', $ticket->agent_id) == $agent->id ? 'selected' : '' }}>
                                                {{ $agent->first_name }} {{ $agent->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('agent_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        @if(isset($ticketTypes) && $ticketTypes->count() > 0)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="ticket_type_id" class="control-label">
                                        Ticket Type
                                    </label>
                                    <select class="form-control @error('ticket_type_id') is-invalid @enderror" 
                                            id="ticket_type_id" 
                                            name="ticket_type_id">
                                        <option value="">Select Type</option>
                                        @foreach($ticketTypes as $type)
                                            <option value="{{ $type->id }}" {{ old('ticket_type_id', $ticket->ticket_type_id) == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ticket_type_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Internal Notes Section -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-sticky-note me-2"></i>Internal Notes
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="internal_notes" class="control-label">
                                        Internal Notes
                                    </label>
                                    <textarea class="form-control @error('internal_notes') is-invalid @enderror" 
                                              id="internal_notes" 
                                              name="internal_notes" 
                                              rows="4"
                                              placeholder="Enter internal notes for agents">{{ old('internal_notes', $ticket->internal_notes) }}</textarea>
                                    @error('internal_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Ticket
                                    </button>
                                    <a href="{{ route('whatsapp.tickets.show', $ticket) }}" class="btn btn-outline-secondary ml-2">
                                        <i class="fas fa-times me-2"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Form validation
    $('#ticket-edit-form').on('submit', function(e) {
        let isValid = true;
        
        // Check required fields
        $(this).find('[required]').each(function() {
            if (!$(this).val()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields.');
        }
    });
    
    // Remove validation errors on input
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>
@endpush
@endsection
