@extends('layout.wrapper')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="row page-titles">
        <div class="col-md-6">
            <div class="d-flex align-items-center">
                <div class="mr-3">
                    <i class="fab fa-whatsapp fa-2x text-success"></i>
                </div>
                <div>
                    <h4 class="mb-0">{{ $connection->connection_name }}</h4>
                    <p class="text-muted mb-0">Connection Details</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('whatsapp.connections.edit', $connection) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Edit
            </a>
            <a href="{{ route('whatsapp.connections.index') }}" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Connections
            </a>
        </div>
    </div>

    <!-- Connection Details -->
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Connection Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">Connection Name:</label>
                                <p>{{ $connection->connection_name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">Type:</label>
                                <p><span class="{{ $connection->type_badge_class }}">{{ ucfirst($connection->connection_type) }}</span></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">Status:</label>
                                <p><span class="{{ $connection->status_badge_class }}">{{ ucfirst($connection->status) }}</span></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold">Active:</label>
                                <p>
                                    @if($connection->is_active)
                                        <span class="badge bg-success">Yes</span>
                                    @else
                                        <span class="badge bg-secondary">No</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    @if($connection->phone_number)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">Phone Number:</label>
                                    <p>{{ $connection->phone_number }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($connection->last_connected_at)
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="fw-bold">Last Connected:</label>
                                    <p>{{ $connection->last_connected_at->format('M d, Y H:i:s') }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                    
                    @if($connection->error_message)
                        <div class="row">
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label class="fw-bold text-danger">Last Error:</label>
                                    <p class="text-danger">{{ $connection->error_message }}</p>
                                    @if($connection->last_error_at)
                                        <small class="text-muted">Error occurred at: {{ $connection->last_error_at->format('M d, Y H:i:s') }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($connection->status === 'connected')
                            <form action="{{ route('whatsapp.connections.disconnect', $connection) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100">
                                    <i class="fas fa-power-off me-2"></i>Disconnect
                                </button>
                            </form>
                        @else
                            <form action="{{ route('whatsapp.connections.connect', $connection) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="fas fa-plug me-2"></i>Connect
                                </button>
                            </form>
                        @endif
                        
                        @if($connection->connection_type === 'baileys')
                            <button type="button" class="btn btn-info w-100" onclick="showQRCode({{ $connection->id }})" id="qrCodeBtn{{ $connection->id }}">
                                <i class="fas fa-qrcode me-2"></i>Show QR Code
                            </button>
                        @endif
                        
                        <a href="{{ route('whatsapp.connections.edit', $connection) }}" class="btn btn-outline-primary w-100">
                            <i class="fas fa-edit me-2"></i>Edit Connection
                        </a>
                        
                        <form action="{{ route('whatsapp.connections.destroy', $connection) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Are you sure you want to delete this connection?')">
                                <i class="fas fa-trash me-2"></i>Delete Connection
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeModalLabel">QR Code</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" data-dismiss="modal" aria-label="Close" onclick="closeQRModal()"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrCodeContainer">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">Loading...</span>
                    </div>
                </div>
                <p class="mt-3 text-muted small">Scan this QR code with WhatsApp to connect</p>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-radius: 0.5rem;
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    border-radius: 0.5rem 0.5rem 0 0;
}

.badge {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
}

.btn {
    border-radius: 6px;
    padding: 0.5rem 1rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
}

.page-titles {
    margin-bottom: 2rem;
}

.fa-2x {
    font-size: 2em;
}

@media (max-width: 768px) {
    .page-titles .text-right {
        text-align: left !important;
        margin-top: 1rem;
    }
}

/* QR Code Modal Styles */
#qrCodeModal .modal-dialog {
    max-width: 300px;
}

#qrCodeModal .modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
}

#qrCodeModal .modal-header {
    background: linear-gradient(135deg, #25d366 0%, #128c7e 100%);
    color: white;
    border-radius: 12px 12px 0 0;
    border-bottom: none;
}

#qrCodeModal .modal-header .close {
    color: white;
    opacity: 0.8;
}

#qrCodeModal .modal-header .close:hover {
    opacity: 1;
}

#qrCodeModal .modal-body {
    padding: 2rem;
    text-align: center;
}

#qrCodeContainer img {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transition: transform 0.3s ease;
}

#qrCodeContainer img:hover {
    transform: scale(1.05);
}

.alert {
    border-radius: 8px;
    border: none;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.alert-warning {
    background-color: #fff3cd;
    color: #856404;
}

.alert-success {
    background-color: #d1e7dd;
    color: #0f5132;
}

/* Fix for modal backdrop issues */
body.modal-open {
    overflow: hidden !important;
    padding-right: 0 !important;
}

/* Ensure modal backdrop is properly removed */
.modal-backdrop {
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    z-index: 1040 !important;
    width: 100vw !important;
    height: 100vh !important;
    background-color: #000 !important;
}

/* Force remove any stuck modal states */
body:not(.modal-open) {
    overflow: auto !important;
    padding-right: 0 !important;
}

/* Emergency fix for stuck modal */
body.emergency-cleanup {
    overflow: auto !important;
    padding-right: 0 !important;
    padding-left: 0 !important;
}

body.emergency-cleanup .modal-backdrop {
    display: none !important;
}
</style>

<!-- JavaScript -->
<script>
// Make sure jQuery is loaded and document is ready
$(document).ready(function() {
    
    // Auto-refresh QR code every 30 seconds when modal is open
    let qrRefreshInterval;
    
    $('#qrCodeModal').on('shown.bs.modal', function () {
        const connectionId = $(this).data('connection-id');
        if (connectionId) {
            qrRefreshInterval = setInterval(() => {
                showQRCode(connectionId);
            }, 30000); // 30 seconds
        }
    });

    $('#qrCodeModal').on('hidden.bs.modal', function () {
        if (qrRefreshInterval) {
            clearInterval(qrRefreshInterval);
            qrRefreshInterval = null;
        }
    });
    
    // Add event listeners to QR code buttons
    $('[id^="qrCodeBtn"]').on('click', function() {
        const connectionId = $(this).attr('onclick').match(/\d+/)[0];
        showQRCode(connectionId);
    });
    
    // Add event listeners for modal close buttons
    $('#qrCodeModal .btn-close, #qrCodeModal [data-bs-dismiss="modal"], #qrCodeModal [data-dismiss="modal"]').on('click', function(e) {
        e.preventDefault();
        closeQRModal();
    });
    
    // Add event listener for backdrop click
    $('#qrCodeModal').on('click', function(e) {
        if (e.target === this) {
            closeQRModal();
        }
    });
    
    // Add event listener for ESC key
    $(document).on('keydown', function(e) {
        if (e.key === 'Escape' && $('#qrCodeModal').hasClass('show')) {
            closeQRModal();
        }
    });
});

// Also add vanilla JavaScript fallback
document.addEventListener('DOMContentLoaded', function() {
    // Add click event listeners to all QR code buttons
    const qrButtons = document.querySelectorAll('[id^="qrCodeBtn"]');
    qrButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const connectionId = this.getAttribute('onclick').match(/\d+/)[0];
            showQRCode(connectionId);
        });
    });
});



// Custom close function for QR modal
function closeQRModal() {
    try {
        const modalElement = document.getElementById('qrCodeModal');
        if (!modalElement) {
            console.error('Modal element not found for closing');
            return;
        }
        
        // Try Bootstrap 5 first
        if (typeof bootstrap !== 'undefined') {
            const modal = bootstrap.Modal.getInstance(modalElement);
            if (modal) {
                modal.hide();
            } else {
                // Create new instance and hide
                const newModal = new bootstrap.Modal(modalElement);
                newModal.hide();
            }
        } else {
            $('#qrCodeModal').modal('hide');
        }
        
        // Always ensure cleanup after a delay
        setTimeout(() => {
            cleanupModal();
        }, 150);
        
    } catch (error) {
        console.error('Error closing modal:', error);
        // Force cleanup immediately
        cleanupModal();
    }
}

// Function to ensure complete modal cleanup
function cleanupModal() {
    const modalElement = document.getElementById('qrCodeModal');
    if (!modalElement) {
        console.error('Modal element not found for cleanup');
        return;
    }
    
    // Remove modal show classes and styles
    modalElement.classList.remove('show');
    modalElement.style.display = 'none';
    modalElement.setAttribute('aria-hidden', 'true');
    modalElement.removeAttribute('aria-modal');
    
    // Remove body modal classes
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    
    // Remove all modal backdrops
    const backdrops = document.querySelectorAll('.modal-backdrop');
    backdrops.forEach(backdrop => {
        backdrop.remove();
    });
    
    // Remove any remaining backdrop elements
    const remainingBackdrops = document.querySelectorAll('[class*="backdrop"]');
    remainingBackdrops.forEach(backdrop => {
        if (backdrop.classList.contains('modal-backdrop') || backdrop.classList.contains('fade')) {
            backdrop.remove();
        }
    });
    
    // Clear any inline styles that might be blocking
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    document.body.style.paddingLeft = '';
    
    // Force remove any fixed positioning issues
    const fixedElements = document.querySelectorAll('[style*="position: fixed"]');
    fixedElements.forEach(element => {
        if (element.classList.contains('modal-backdrop')) {
            element.remove();
        }
    });
    
    // Verify cleanup
    setTimeout(() => {
        const stillHasBackdrop = document.querySelector('.modal-backdrop');
        const stillModalOpen = document.body.classList.contains('modal-open');
        const stillShow = modalElement.classList.contains('show');
        
        if (stillHasBackdrop || stillModalOpen || stillShow) {
            forceCleanupModal();
        }
    }, 100);
}

// Force cleanup function for stubborn cases
function forceCleanupModal() {
    // Add emergency cleanup class
    document.body.classList.add('emergency-cleanup');
    
    // Remove all modal-related classes and styles
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    document.body.style.paddingLeft = '';
    
    // Remove all backdrops
    const allBackdrops = document.querySelectorAll('.modal-backdrop, [class*="backdrop"]');
    allBackdrops.forEach(backdrop => {
        backdrop.remove();
    });
    
    // Reset modal element
    const modalElement = document.getElementById('qrCodeModal');
    if (modalElement) {
        modalElement.classList.remove('show', 'fade');
        modalElement.style.display = 'none';
        modalElement.setAttribute('aria-hidden', 'true');
        modalElement.removeAttribute('aria-modal');
    }
    
    // Remove any remaining fixed elements
    const fixedElements = document.querySelectorAll('[style*="position: fixed"]');
    fixedElements.forEach(element => {
        if (element.classList.contains('modal-backdrop') || element.classList.contains('fade')) {
            element.remove();
        }
    });
    
    // Clear any remaining inline styles
    document.body.style.overflow = 'auto';
    document.body.style.paddingRight = '0';
    document.body.style.paddingLeft = '0';
    
    // Remove emergency class after a delay
    setTimeout(() => {
        document.body.classList.remove('emergency-cleanup');
    }, 1000);
}

// Global function for QR code display
function showQRCode(connectionId) {
    // Check if jQuery is available
    if (typeof $ === 'undefined') {
        console.error('jQuery is not loaded!');
        return;
    }
    
    // Check if modal exists
    if ($('#qrCodeModal').length === 0) {
        console.error('QR code modal not found!');
        return;
    }
    
    $('#qrCodeModal').data('connection-id', connectionId);
    
    // Try multiple ways to show the modal
    try {
        // Method 1: Bootstrap 5 modal
        if (typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
            modal.show();
        } else {
            // Method 2: jQuery/Bootstrap 4 modal
            $('#qrCodeModal').modal('show');
        }
        
        // Method 3: Check if modal is visible after 100ms
        setTimeout(() => {
            const modalElement = document.getElementById('qrCodeModal');
            const isVisible = modalElement && (modalElement.classList.contains('show') || modalElement.style.display === 'block');
            
            if (!isVisible) {
                // Fallback 1: Try jQuery modal again
                $('#qrCodeModal').modal('show');
                
                // Fallback 2: Direct CSS manipulation
                setTimeout(() => {
                    const stillNotVisible = !modalElement.classList.contains('show') && modalElement.style.display !== 'block';
                    if (stillNotVisible) {
                        modalElement.classList.add('show');
                        modalElement.style.display = 'block';
                        document.body.classList.add('modal-open');
                        
                        // Remove existing backdrop
                        const existingBackdrop = document.querySelector('.modal-backdrop');
                        if (existingBackdrop) {
                            existingBackdrop.remove();
                        }
                        
                        // Add new backdrop
                        const backdrop = document.createElement('div');
                        backdrop.className = 'modal-backdrop fade show';
                        document.body.appendChild(backdrop);
                    }
                }, 100);
            }
        }, 100);
        
    } catch (error) {
        console.error('Error showing modal:', error);
    }
    
    // Reset container to show loading state
    document.getElementById('qrCodeContainer').innerHTML = `
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Loading...</span>
        </div>
        <p class="mt-2 text-muted">Generating QR code...</p>
    `;
    
    // Fetch QR code
    fetch(`/whatsapp/connections/${connectionId}/qr-code`, {
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.qr_code) {
            document.getElementById('qrCodeContainer').innerHTML = 
                `<img src="${data.qr_code}" alt="QR Code" class="img-fluid" style="max-width: 200px; border: 1px solid #ddd; border-radius: 8px;">
                 <p class="mt-2 text-success small">QR code generated successfully!</p>`;
        } else if (data.error) {
            document.getElementById('qrCodeContainer').innerHTML = 
                `<div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Error:</strong> ${data.error}
                </div>`;
        } else {
            document.getElementById('qrCodeContainer').innerHTML = 
                `<div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> No QR code data received
                </div>`;
        }
    })
    .catch(error => {
        console.error('QR code fetch error:', error);
        document.getElementById('qrCodeContainer').innerHTML = 
            `<div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <strong>Error:</strong> Failed to load QR code
                <br><small class="text-muted">${error.message}</small>
            </div>`;
    });
}

// Initialize QR code functionality
console.log('QR Code functionality loaded');

// Make sure the function is globally accessible
window.showQRCode = showQRCode;
window.closeQRModal = closeQRModal;
window.cleanupModal = cleanupModal;
window.forceCleanupModal = forceCleanupModal;
</script>
@endsection

