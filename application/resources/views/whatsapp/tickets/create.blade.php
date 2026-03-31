@extends('whatsapp.layouts.app')

@section('title', 'Create New Ticket')

<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
<div class="container-fluid">
    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Create New Ticket</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('whatsapp.dashboard') }}">WhatsApp</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('whatsapp.tickets.index') }}">Tickets</a></li>
                    <li class="breadcrumb-item active">Create</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Ticket Information</h5>
                </div>
                <div class="card-body">
                    <form id="ticket-create-form" method="POST" action="{{ route('whatsapp.tickets.store') }}">
                        @csrf
                        
                        <!-- Contact Information -->
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
                                           value="{{ old('contact_name') }}"
                                           placeholder="Enter contact name"
                                           required>
                                    @error('contact_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_phone" class="control-label required">
                                        Contact Phone <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('contact_phone') is-invalid @enderror" 
                                           id="contact_phone" 
                                           name="contact_phone" 
                                           value="{{ old('contact_phone') }}"
                                           placeholder="Enter phone number"
                                           required>
                                    @error('contact_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="contact_email" class="control-label">
                                        Contact Email
                                    </label>
                                    <input type="email" 
                                           class="form-control @error('contact_email') is-invalid @enderror" 
                                           id="contact_email" 
                                           name="contact_email" 
                                           value="{{ old('contact_email') }}"
                                           placeholder="Enter email address (optional)">
                                    @error('contact_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="channel" class="control-label required">
                                        Channel <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('channel') is-invalid @enderror" 
                                            id="channel" 
                                            name="channel" 
                                            onchange="toggleApiSelection()"
                                            required>
                                        <option value="">Select channel</option>
                                        <option value="whatsapp" {{ old('channel') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                        <option value="email" {{ old('channel') == 'email' ? 'selected' : '' }}>Email</option>
                                    </select>
                                    @error('channel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Ticket Details -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="subject" class="control-label required">
                                        Subject <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('subject') is-invalid @enderror" 
                                           id="subject" 
                                           name="subject" 
                                           value="{{ old('subject') }}"
                                           placeholder="Enter ticket subject"
                                           required>
                                    @error('subject')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="priority" class="control-label required">
                                        Priority <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('priority') is-invalid @enderror" 
                                            id="priority" 
                                            name="priority" 
                                            required>
                                        <option value="">Select priority</option>
                                        <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>Low</option>
                                        <option value="medium" {{ old('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>High</option>
                                        <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
                                    </select>
                                    @error('priority')
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
                                           value="{{ old('category') }}"
                                           placeholder="Enter category (optional)">
                                    @error('category')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- API Selection Section -->
                        <div class="row mt-4" id="api-selection-section" style="display: none;">
                            <div class="col-md-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-cog me-2"></i>API Configuration
                                </h6>
                            </div>
                        </div>

                        <!-- WhatsApp API Selection -->
                        <div class="row" id="whatsapp-api-section" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="whatsapp_api_type" class="control-label">
                                        WhatsApp API Type
                                    </label>
                                    <select class="form-select" id="whatsapp_api_type" name="whatsapp_api_type">
                                        <option value="cloud">WhatsApp Cloud API (Recommended)</option>
                                        <option value="legacy">Legacy WhatsApp API</option>
                                    </select>
                                    <small class="form-text text-muted">Choose the WhatsApp API to use for this ticket</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <button type="button" class="btn btn-outline-primary mt-4" onclick="openWhatsAppCredentials()">
                                        <i class="fas fa-key me-2"></i>Configure WhatsApp Credentials
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Email API Selection -->
                        <div class="row" id="email-api-section" style="display: none;">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email_api_type" class="control-label">
                                        Email Service Type
                                    </label>
                                    <select class="form-select" id="email_api_type" name="email_api_type">
                                        <option value="enhanced">Enhanced Email Service (Recommended)</option>
                                        <option value="legacy">Legacy Email Service</option>
                                    </select>
                                    <small class="form-text text-muted">Choose the email service to use for this ticket</small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <button type="button" class="btn btn-outline-info mt-4" onclick="openEmailCredentials()">
                                        <i class="fas fa-envelope me-2"></i>Configure Email Credentials
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Initial Message Section -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-comment me-2"></i>Initial Message
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="initial_message" class="control-label required">
                                        Message <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('initial_message') is-invalid @enderror" 
                                              id="initial_message" 
                                              name="initial_message" 
                                              rows="6"
                                              placeholder="Enter the initial message or description of the issue"
                                              required>{{ old('initial_message') }}</textarea>
                                    @error('initial_message')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Tags Section -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-tags me-2"></i>Tags (Optional)
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="tags" class="control-label">
                                        Tags
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('tags') is-invalid @enderror" 
                                           id="tags" 
                                           name="tags" 
                                           value="{{ old('tags') }}"
                                           placeholder="Enter tags separated by commas (e.g., bug, feature, support)">
                                    <small class="form-text text-muted">Separate multiple tags with commas</small>
                                    @error('tags')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <hr class="my-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="{{ route('whatsapp.tickets.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Create Ticket
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

<!-- Simple Modal Container -->
<div id="modalContainer" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div id="modalContent" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; min-width: 500px; max-width: 800px; max-height: 90vh; overflow-y: auto;">
        <div id="modalHeader" style="border-bottom: 1px solid #ddd; padding-bottom: 15px; margin-bottom: 20px;">
            <h4 id="modalTitle" style="margin: 0; color: #333;"></h4>
            <button type="button" onclick="closeModal()" style="position: absolute; top: 10px; right: 15px; background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
        </div>
        <div id="modalBody">
            <!-- Modal content will be inserted here -->
        </div>
        <div id="modalFooter" style="border-top: 1px solid #ddd; padding-top: 15px; margin-top: 20px; text-align: right;">
            <button type="button" onclick="closeModal()" style="background: #6c757d; color: white; border: none; padding: 8px 16px; border-radius: 4px; margin-right: 10px; cursor: pointer;">Close</button>
            <button type="button" id="modalSaveBtn" style="background: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; display: none;">Save</button>
        </div>
    </div>
</div>

<!-- Simple Modal Container -->
<div id="modalContainer" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div id="modalContent" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; min-width: 500px; max-width: 800px; max-height: 90vh; overflow-y: auto;">
        <div id="modalHeader" style="border-bottom: 1px solid #ddd; padding-bottom: 15px; margin-bottom: 20px;">
            <h4 id="modalTitle" style="margin: 0; color: #333;"></h4>
            <button type="button" onclick="closeModal()" style="position: absolute; top: 10px; right: 15px; background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">&times;</button>
        </div>
        <div id="modalBody">
            <!-- Modal content will be inserted here -->
        </div>
        <div id="modalFooter" style="border-top: 1px solid #ddd; padding-top: 15px; margin-top: 20px; text-align: right;">
            <button type="button" onclick="closeModal()" style="background: #6c757d; color: white; border: none; padding: 8px 16px; border-radius: 4px; margin-right: 10px; cursor: pointer;">Close</button>
            <button type="button" id="modalSaveBtn" style="background: #007bff; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer; display: none;">Save</button>
        </div>
    </div>
</div>



@endsection

@push('styles')
<style>
.required {
    color: #dc3545;
}

.form-group {
    margin-bottom: 1rem;
}

.form-label {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 1px solid #ced4da;
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus, .form-select:focus {
    border-color: #86b7fe;
    outline: 0;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.btn {
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-block;
    text-align: center;
    vertical-align: middle;
    cursor: pointer;
    transition: color 0.15s ease-in-out, background-color 0.15s ease-in-out, border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.btn-primary {
    color: #fff;
    background-color: #0d6efd;
    border: 1px solid #0d6efd;
}

.btn-primary:hover {
    background-color: #0b5ed7;
    border-color: #0a58ca;
}

.btn-outline-primary {
    color: #0d6efd;
    border: 1px solid #0d6efd;
    background-color: transparent;
}

.btn-outline-primary:hover {
    color: #fff;
    background-color: #0d6efd;
    border-color: #0d6efd;
}

.btn-outline-info {
    color: #0dcaf0;
    border: 1px solid #0dcaf0;
    background-color: transparent;
}

.btn-outline-info:hover {
    color: #000;
    background-color: #0dcaf0;
    border-color: #0dcaf0;
}

.btn-outline-secondary {
    color: #6c757d;
    border: 1px solid #6c757d;
    background-color: transparent;
}

.btn-outline-secondary:hover {
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
}

.text-primary {
    color: #0d6efd !important;
}

.text-danger {
    color: #dc3545 !important;
}

.text-muted {
    color: #6c757d !important;
}

.me-2 {
    margin-right: 0.5rem !important;
}

.mt-4 {
    margin-top: 1.5rem !important;
}

.mb-3 {
    margin-bottom: 1rem !important;
}

.mb-4 {
    margin-bottom: 1.5rem !important;
}

.my-4 {
    margin-top: 1.5rem !important;
    margin-bottom: 1.5rem !important;
}

@media (max-width: 768px) {
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn {
        width: 100%;
    }
}

/* Modal specific styles */
#modalContainer {
    backdrop-filter: blur(2px);
}

#modalContent {
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    border: 1px solid #ddd;
}

#modalContent .row {
    margin-left: -10px;
    margin-right: -10px;
}

#modalContent .col-md-6 {
    padding-left: 10px;
    padding-right: 10px;
}

#modalContent .form-group {
    margin-bottom: 15px;
}

#modalContent .form-control,
#modalContent .form-select {
    width: 100%;
}

#modalContent .btn {
    margin-top: 5px;
}

#modalContent .w-100 {
    width: 100% !important;
}

/* Loading state styles */
.loading {
    opacity: 0.7;
    pointer-events: none;
}

.loading button[type="submit"] {
    background-color: #6c757d !important;
    border-color: #6c757d !important;
}

/* Alert message styles */
.alert {
    border-radius: 0.375rem;
    border: 1px solid transparent;
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
}

.alert-success {
    color: #0f5132;
    background-color: #d1e7dd;
    border-color: #badbcc;
}

.alert-danger {
    color: #842029;
    background-color: #f8d7da;
    border-color: #f5c2c7;
}

.alert-dismissible .btn-close {
    position: absolute;
    top: 0;
    right: 0;
    z-index: 2;
    padding: 0.75rem 1.25rem;
}

.btn-close {
    background: none;
    border: 0;
    font-size: 1.25rem;
    line-height: 1;
    color: #000;
    text-shadow: 0 1px 0 #fff;
    opacity: 0.5;
    cursor: pointer;
}

.btn-close:hover {
    opacity: 0.75;
}

/* Form validation styles */
.form-control.is-invalid,
.form-select.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}
</style>
@endpush

@push('scripts')
<script>
// Global variables for credentials
let currentCredentials = {
    whatsapp: {},
    email: {}
};

// Toggle API selection based on channel
function toggleApiSelection() {
    const channel = document.getElementById('channel').value;
    const apiSection = document.getElementById('api-selection-section');
    const whatsappSection = document.getElementById('whatsapp-api-section');
    const emailSection = document.getElementById('email-api-section');
    
    console.log('Channel selected:', channel);
    
    if (channel === 'whatsapp') {
        apiSection.style.display = 'block';
        whatsappSection.style.display = 'block';
        emailSection.style.display = 'none';
        console.log('Showing WhatsApp section');
    } else if (channel === 'email') {
        apiSection.style.display = 'block';
        whatsappSection.style.display = 'none';
        emailSection.style.display = 'block';
        console.log('Showing Email section');
    } else {
        apiSection.style.display = 'none';
        whatsappSection.style.display = 'none';
        emailSection.style.display = 'none';
        console.log('Hiding all sections');
    }
}

// Simple modal functions
function showModal(title, content, showSaveButton = false, saveCallback = null) {
    document.getElementById('modalTitle').textContent = title;
    document.getElementById('modalBody').innerHTML = content;
    document.getElementById('modalContainer').style.display = 'block';
    
    const saveBtn = document.getElementById('modalSaveBtn');
    if (showSaveButton && saveCallback) {
        saveBtn.style.display = 'inline-block';
        saveBtn.onclick = saveCallback;
    } else {
        saveBtn.style.display = 'none';
    }
}

function closeModal() {
    document.getElementById('modalContainer').style.display = 'none';
}

// WhatsApp credentials modal
function openWhatsAppCredentials() {
    console.log('Opening WhatsApp credentials modal');
    
    const content = `
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Access Token</label>
                    <input type="text" class="form-control" id="whatsappToken" placeholder="EAA..." value="${currentCredentials.whatsapp.access_token || ''}">
                    <small class="form-text text-muted">Get this from Meta Developer Console</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Phone Number ID</label>
                    <input type="text" class="form-control" id="whatsappPhoneId" placeholder="123456789" value="${currentCredentials.whatsapp.phone_number_id || ''}">
                    <small class="form-text text-muted">Your WhatsApp Business phone number ID</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Business Account ID</label>
                    <input type="text" class="form-control" id="whatsappBusinessId" placeholder="123456789" value="${currentCredentials.whatsapp.business_account_id || ''}">
                    <small class="form-text text-muted">Your Meta Business account ID</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Test Connection</label>
                    <button type="button" class="btn btn-outline-success w-100" onclick="testWhatsAppConnection()">
                        <i class="fas fa-plug me-2"></i>Test API Connection
                    </button>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <strong>Testing Instructions:</strong><br>
                    1. Enter your WhatsApp Business API credentials<br>
                    2. Click "Test API Connection" to verify API access<br>
                    3. Check the result below for connection status<br>
                    4. Save credentials if test is successful
                </div>
            </div>
        </div>
        <div id="whatsappTestResult" class="mt-3" style="display: none;"></div>
    `;
    
    showModal('Configure WhatsApp Credentials', content, true, saveWhatsAppCredentials);
}

// Email credentials modal
function openEmailCredentials() {
    console.log('Opening Email credentials modal');
    
    const content = `
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>SMTP Host</label>
                    <input type="text" class="form-control" id="smtpHost" placeholder="smtp.gmail.com" value="${currentCredentials.email.smtp_host || ''}">
                    <small class="form-text text-muted">Gmail: smtp.gmail.com, Outlook: smtp-mail.outlook.com</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>SMTP Port</label>
                    <input type="number" class="form-control" id="smtpPort" value="${currentCredentials.email.smtp_port || 587}">
                    <small class="form-text text-muted">TLS: 587, SSL: 465, StartTLS: 25</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" id="smtpUsername" placeholder="yourname@gmail.com" value="${currentCredentials.email.smtp_username || ''}">
                    <small class="form-text text-muted">Your full email address</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" id="smtpPassword" placeholder="your_app_password" value="${currentCredentials.email.smtp_password || ''}">
                    <small class="form-text text-muted">App password (not regular password)</small>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Encryption</label>
                    <select class="form-select" id="smtpEncryption">
                        <option value="tls" ${(currentCredentials.email.smtp_encryption || 'tls') === 'tls' ? 'selected' : ''}>TLS</option>
                        <option value="ssl" ${(currentCredentials.email.smtp_encryption || 'tls') === 'ssl' ? 'selected' : ''}>SSL</option>
                    </select>
                    <small class="form-text text-muted">TLS recommended for most providers</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Test Connection</label>
                    <button type="button" class="btn btn-outline-info w-100" onclick="testSmtpConnection()">
                        <i class="fas fa-plug me-2"></i>Test SMTP & Send Email
                    </button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>From Address</label>
                    <input type="email" class="form-control" id="fromAddress" placeholder="yourname@gmail.com" value="${currentCredentials.email.from_address || ''}">
                    <small class="form-text text-muted">Sender email address</small>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>From Name</label>
                    <input type="text" class="form-control" id="fromName" placeholder="Your Name" value="${currentCredentials.email.from_name || ''}">
                    <small class="form-text text-muted">Display name for sender</small>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <strong>Testing Instructions:</strong><br>
                    1. Enter your SMTP server credentials<br>
                    2. Click "Test SMTP & Send Email" to verify connection<br>
                    3. A test email will be sent to your email address<br>
                    4. Check the result below and your inbox for confirmation<br>
                    5. Save credentials if test is successful
                </div>
            </div>
        </div>
        <div id="emailTestResult" class="mt-3" style="display: none;"></div>
    `;
    
    showModal('Configure Email Credentials', content, true, saveEmailCredentials);
}

// Save functions
function saveWhatsAppCredentials() {
    currentCredentials.whatsapp = {
        access_token: document.getElementById('whatsappToken').value,
        phone_number_id: document.getElementById('whatsappPhoneId').value,
        business_account_id: document.getElementById('whatsappBusinessId').value
    };
    
    localStorage.setItem('whatsapp_credentials', JSON.stringify(currentCredentials));
    closeModal();
    alert('WhatsApp credentials saved successfully!');
}

function saveEmailCredentials() {
    currentCredentials.email = {
        smtp_host: document.getElementById('smtpHost').value,
        smtp_port: parseInt(document.getElementById('smtpPort').value),
        smtp_username: document.getElementById('smtpUsername').value,
        smtp_password: document.getElementById('smtpPassword').value,
        smtp_encryption: document.getElementById('smtpEncryption').value,
        from_address: document.getElementById('fromAddress').value,
        from_name: document.getElementById('fromName').value
    };
    
    localStorage.setItem('whatsapp_credentials', JSON.stringify(currentCredentials));
    closeModal();
    alert('Email credentials saved successfully!');
}

// Load saved credentials
function loadSavedCredentials() {
    const saved = localStorage.getItem('whatsapp_credentials');
    if (saved) {
        currentCredentials = JSON.parse(saved);
    }
}

// Test connection functions
function testWhatsAppConnection() {
    if (!currentCredentials.whatsapp.access_token || !currentCredentials.whatsapp.phone_number_id) {
        alert('Please enter WhatsApp credentials first!');
        return;
    }
    
    // Update current credentials from form
    currentCredentials.whatsapp = {
        access_token: document.getElementById('whatsappToken').value,
        phone_number_id: document.getElementById('whatsappPhoneId').value,
        business_account_id: document.getElementById('whatsappBusinessId').value
    };
    
    const resultDiv = document.getElementById('whatsappTestResult');
    resultDiv.style.display = 'block';
    resultDiv.className = 'alert alert-info mt-3';
    resultDiv.innerHTML = '<strong>Testing WhatsApp API Connection...</strong><br>Connecting to Meta WhatsApp Business API...';
    
    // Real backend test - call your Laravel API endpoint
    fetch('/api/whatsapp/test-connection', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            access_token: currentCredentials.whatsapp.access_token,
            phone_number_id: currentCredentials.whatsapp.phone_number_id,
            business_account_id: currentCredentials.whatsapp.business_account_id
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.className = 'alert alert-success mt-3';
            resultDiv.innerHTML = `<strong>✅ Connection Successful!</strong><br>
                <strong>Phone Number:</strong> ${data.phone_number}<br>
                <strong>Business Name:</strong> ${data.business_name}<br>
                <strong>Status:</strong> ${data.status}`;
        } else {
            resultDiv.className = 'alert alert-danger mt-3';
            resultDiv.innerHTML = `<strong>❌ Connection Failed!</strong><br>
                <strong>Error:</strong> ${data.error}<br>
                <strong>Code:</strong> ${data.error_code}`;
        }
    })
    .catch(error => {
        resultDiv.className = 'alert alert-danger mt-3';
        resultDiv.innerHTML = `<strong>❌ Network Error!</strong><br>
            <strong>Error:</strong> ${error.message}<br>
            <strong>Check:</strong> Backend server, API endpoint, CORS settings`;
        console.error('WhatsApp API Test Error:', error);
    });
}

function testSmtpConnection() {
    if (!currentCredentials.email.smtp_host || !currentCredentials.email.smtp_username) {
        alert('Please enter SMTP credentials first!');
        return;
    }
    
    // Update current credentials from form
    currentCredentials.email = {
        smtp_host: document.getElementById('smtpHost').value,
        smtp_port: parseInt(document.getElementById('smtpPort').value),
        smtp_username: document.getElementById('smtpUsername').value,
        smtp_password: document.getElementById('smtpPassword').value,
        smtp_encryption: document.getElementById('smtpEncryption').value,
        from_address: document.getElementById('fromAddress').value,
        from_name: document.getElementById('fromName').value
    };
    
    const resultDiv = document.getElementById('emailTestResult');
    resultDiv.style.display = 'block';
    resultDiv.className = 'alert alert-info mt-3';
    resultDiv.innerHTML = '<strong>Testing SMTP Connection...</strong><br>Connecting to SMTP server and sending test email...';
    
    // Real backend test - call your Laravel API endpoint
    fetch('/api/email/test-connection', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            smtp_host: currentCredentials.email.smtp_host,
            smtp_port: currentCredentials.email.smtp_port,
            smtp_username: currentCredentials.email.smtp_username,
            smtp_password: currentCredentials.email.smtp_password,
            smtp_encryption: currentCredentials.email.smtp_encryption,
            from_address: currentCredentials.email.from_address,
            from_name: currentCredentials.email.from_name,
            test_email: currentCredentials.email.smtp_username // Send test email to yourself
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.className = 'alert alert-success mt-3';
            resultDiv.innerHTML = `<strong>✅ SMTP Test Successful!</strong><br>
                <strong>Server:</strong> ${data.server_info}<br>
                <strong>Test Email Sent:</strong> ${data.test_email_sent}<br>
                <strong>Message ID:</strong> ${data.message_id}<br>
                <strong>Check your inbox for the test email!</strong>`;
        } else {
            resultDiv.className = 'alert alert-danger mt-3';
            resultDiv.innerHTML = `<strong>❌ SMTP Test Failed!</strong><br>
                <strong>Error:</strong> ${data.error}<br>
                <strong>Code:</strong> ${data.error_code}<br>
                <strong>Details:</strong> ${data.details}`;
        }
    })
    .catch(error => {
        resultDiv.className = 'alert alert-danger mt-3';
        resultDiv.innerHTML = `<strong>❌ Network Error!</strong><br>
            <strong>Error:</strong> ${error.message}<br>
            <strong>Check:</strong> Backend server, API endpoint, CORS settings`;
        console.error('SMTP Test Error:', error);
    });
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    if (event.target === document.getElementById('modalContainer')) {
        closeModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeModal();
    }
});

// Backend testing functions
function testWhatsAppBackend() {
    const resultDiv = document.getElementById('whatsappBackendResult');
    resultDiv.style.display = 'block';
    resultDiv.className = 'alert alert-info';
    resultDiv.innerHTML = '<strong>Testing WhatsApp Backend...</strong><br>Checking API endpoint availability...';
    
    // Test if the backend endpoint exists
    fetch('/api/whatsapp/test-connection', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            test: true
        })
    })
    .then(response => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
    })
    .then(data => {
        resultDiv.className = 'alert alert-success';
        resultDiv.innerHTML = `<strong>✅ Backend Available!</strong><br>
            <strong>Status:</strong> ${data.message || 'WhatsApp API endpoint is working'}<br>
            <strong>Response Time:</strong> ${data.response_time || 'N/A'}`;
    })
    .catch(error => {
        resultDiv.className = 'alert alert-danger';
        resultDiv.innerHTML = `<strong>❌ Backend Not Available!</strong><br>
            <strong>Error:</strong> ${error.message}<br>
            <strong>Action Required:</strong> Implement the Laravel API endpoint<br>
            <strong>Route:</strong> /api/whatsapp/test-connection`;
        console.error('WhatsApp Backend Test Error:', error);
    });
}

function testEmailBackend() {
    const resultDiv = document.getElementById('emailBackendResult');
    resultDiv.style.display = 'block';
    resultDiv.className = 'alert alert-info';
    resultDiv.innerHTML = '<strong>Testing Email Backend...</strong><br>Checking SMTP endpoint availability...';
    
    // Test if the backend endpoint exists
    fetch('/api/email/test-connection', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            test: true
        })
    })
    .then(response => {
        if (response.ok) {
            return response.json();
        } else {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
    })
    .then(data => {
        resultDiv.className = 'alert alert-success';
        resultDiv.innerHTML = `<strong>✅ Backend Available!</strong><br>
            <strong>Status:</strong> ${data.message || 'Email API endpoint is working'}<br>
            <strong>Response Time:</strong> ${data.response_time || 'N/A'}`;
    })
    .catch(error => {
        resultDiv.className = 'alert alert-danger';
        resultDiv.innerHTML = `<strong>❌ Backend Not Available!</strong><br>
            <strong>Error:</strong> ${error.message}<br>
            <strong>Action Required:</strong> Implement the Laravel API endpoint<br>
            <strong>Route:</strong> /api/email/test-connection`;
        console.error('Email Backend Test Error:', error);
    });
}

// Form submission handling
document.getElementById('ticket-create-form').addEventListener('submit', function(e) {
    console.log('Form submission started...');
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Creating Ticket...';
    submitBtn.disabled = true;
    
    // Add loading class to form
    this.classList.add('loading');
    
    // Let the form submit naturally
    console.log('Form submitted successfully');
});

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    console.log('Page loaded, initializing...');
    
    // Load saved credentials
    loadSavedCredentials();
    
    // Initialize API sections based on current channel selection
    toggleApiSelection();
    
    // Check for success/error messages in URL parameters or session
    checkForMessages();
    
    console.log('Initialization complete');
});

// Check for success/error messages
function checkForMessages() {
    // Check URL parameters for messages
    const urlParams = new URLSearchParams(window.location.search);
    const success = urlParams.get('success');
    const error = urlParams.get('error');
    
    if (success) {
        showMessage('success', success);
    }
    if (error) {
        showMessage('error', error);
    }
}

// Show success/error messages
function showMessage(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const icon = type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-triangle';
    
    const alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    `;
    
    // Insert at the top of the page content
    const pageContent = document.querySelector('.container-fluid');
    pageContent.insertAdjacentHTML('afterbegin', alertHtml);
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
        const alert = pageContent.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>
@endpush
