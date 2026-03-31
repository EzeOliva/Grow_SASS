@extends('whatsapp.layouts.app')

@section('whatsapp-content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">
                <i class="fas fa-tasks me-2"></i>
                Tick Lists for Ticket #{{ $ticket->id }}
            </h4>
            <p class="text-muted mb-0">{{ $ticket->subject }}</p>
        </div>
        <div>
            <a href="{{ route('whatsapp.tickets.show', $ticket) }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>Back to Ticket
            </a>
            <a href="{{ route('whatsapp.tickets.tick-lists.create', $ticket) }}" class="btn btn-whatsapp">
                <i class="fas fa-plus me-1"></i>Add Tick List Item
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="whatsapp-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('whatsapp.tickets.tick-lists.index', $ticket) }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Search tick lists..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="priority" class="form-select">
                        <option value="">All Priorities</option>
                        <option value="1" {{ request('priority') == '1' ? 'selected' : '' }}>Low</option>
                        <option value="2" {{ request('priority') == '2' ? 'selected' : '' }}>Medium</option>
                        <option value="3" {{ request('priority') == '3' ? 'selected' : '' }}>High</option>
                        <option value="4" {{ request('priority') == '4' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="assigned_to" class="form-select">
                        <option value="">All Assignees</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ request('assigned_to') == $agent->id ? 'selected' : '' }}>
                                {{ $agent->first_name }} {{ $agent->last_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-whatsapp w-100">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tick Lists -->
    <div class="whatsapp-card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-list-check me-2"></i>
                    Tick Lists ({{ $tickLists->total() }})
                </h5>
                @if($tickLists->count() > 0)
                    <button type="button" class="btn btn-sm btn-outline-danger" id="bulkDeleteBtn" style="display: none;">
                        <i class="fas fa-trash me-1"></i>Delete Selected
                    </button>
                @endif
            </div>
        </div>
        <div class="card-body p-0">
            @if($tickLists->count() > 0)
                <form id="bulkDeleteForm" action="{{ route('whatsapp.tickets.tick-lists.bulk-delete', $ticket) }}" method="POST">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="40">
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Assigned To</th>
                                    <th>Due Date</th>
                                    <th>Created By</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tickLists as $tickList)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="tick_list_ids[]" value="{{ $tickList->id }}" 
                                                   class="form-check-input tick-list-checkbox">
                                        </td>
                                        <td>
                                            <div class="fw-bold">{{ $tickList->title }}</div>
                                            @if($tickList->description)
                                                									<small class="text-muted">{{ strlen($tickList->description) > 50 ? substr($tickList->description, 0, 50) . '...' : $tickList->description }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="status-badge status-{{ $tickList->status }}">
                                                {{ ucfirst($tickList->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="priority-badge priority-{{ $tickList->priority_color }}">
                                                {{ $tickList->priority_text }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($tickList->assignee)
                                                <span>{{ $tickList->assignee->first_name }} {{ $tickList->assignee->last_name }}</span>
                                            @else
                                                <span class="text-muted">Unassigned</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($tickList->due_date)
                                                <div class="{{ $tickList->is_overdue ? 'text-danger' : '' }}">
                                                    {{ $tickList->due_date->format('M d, Y') }}
                                                </div>
                                                @if($tickList->is_overdue)
                                                    <small class="text-danger">Overdue</small>
                                                @endif
                                            @else
                                                <span class="text-muted">No due date</span>
                                            @endif
                                        </td>
                                        <td>
                                            <span>{{ $tickList->creator->first_name }} {{ $tickList->creator->last_name }}</span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <button type="button" class="btn btn-sm btn-outline-success toggle-status-btn" 
                                                        data-ticket-id="{{ $ticket->id }}" 
                                                        data-tick-list-id="{{ $tickList->id }}"
                                                        title="{{ $tickList->status === 'pending' ? 'Mark as Completed' : 'Mark as Pending' }}">
                                                    <i class="fas fa-{{ $tickList->status === 'pending' ? 'check' : 'undo' }}"></i>
                                                </button>
                                                <a href="{{ route('whatsapp.tickets.tick-lists.edit', [$ticket, $tickList]) }}" 
                                                   class="btn btn-sm btn-outline-primary" title="Edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger delete-btn" 
                                                        data-ticket-id="{{ $ticket->id }}" 
                                                        data-tick-list-id="{{ $tickList->id }}"
                                                        title="Delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
                
                <!-- Pagination with 5 items per page -->
                <div class="d-flex justify-content-center p-3">
                    {{ $tickLists->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-tasks fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">No tick list items found</h5>
                    <p class="text-muted">Create your first tick list item to get started</p>
                    <a href="{{ route('whatsapp.tickets.tick-lists.create', $ticket) }}" class="btn btn-whatsapp">
                        <i class="fas fa-plus me-2"></i>Add Tick List Item
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                                 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                     <i class="ti-close"></i>
                 </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this tick list item? This action cannot be undone.
            </div>
            <div class="modal-footer">
                                 <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    const selectAll = document.getElementById('selectAll');
    const tickListCheckboxes = document.querySelectorAll('.tick-list-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

    if (selectAll) {
        selectAll.addEventListener('change', function() {
            tickListCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkDeleteButton();
        });
    }

    tickListCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkDeleteButton);
    });

    function updateBulkDeleteButton() {
        const checkedCount = document.querySelectorAll('.tick-list-checkbox:checked').length;
        if (checkedCount > 0) {
            bulkDeleteBtn.style.display = 'inline-block';
            bulkDeleteBtn.innerHTML = `<i class="fas fa-trash me-1"></i>Delete Selected (${checkedCount})`;
        } else {
            bulkDeleteBtn.style.display = 'none';
        }
    }

    // Bulk delete functionality
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            const checkedCount = document.querySelectorAll('.tick-list-checkbox:checked').length;
            if (checkedCount > 0) {
                if (confirm(`Are you sure you want to delete ${checkedCount} tick list item(s)?`)) {
                    document.getElementById('bulkDeleteForm').submit();
                }
            }
        });
    }

    // Individual delete functionality
    const deleteBtns = document.querySelectorAll('.delete-btn');
    deleteBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const ticketId = this.dataset.ticketId;
            const tickListId = this.dataset.tickListId;
            
            // Set the delete form action
            const deleteForm = document.getElementById('deleteForm');
            deleteForm.action = `/whatsapp/tickets/${ticketId}/tick-lists/${tickListId}`;
            
            // Show the modal
            const deleteModal = $('#deleteModal');
            deleteModal.modal('show');
        });
    });

    // Toggle status functionality
    const toggleStatusBtns = document.querySelectorAll('.toggle-status-btn');
    toggleStatusBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const ticketId = this.dataset.ticketId;
            const tickListId = this.dataset.tickListId;
            
            fetch(`/whatsapp/tickets/${ticketId}/tick-lists/${tickListId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to show updated status
                    location.reload();
                } else {
                    alert('Error updating status: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating status. Please try again.');
            });
        });
    });
});
</script>
@endpush

