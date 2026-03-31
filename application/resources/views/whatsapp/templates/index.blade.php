@extends('whatsapp.layouts.app')

@section('title', 'Quick Templates')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Quick Templates</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('whatsapp.dashboard') }}">WhatsApp</a></li>
                    <li class="breadcrumb-item active">Templates</li>
                </ul>
            </div>
            <div class="col-auto">
                                 <button class="btn btn-primary" data-toggle="modal" data-target="#createTemplateModal">
                     <i class="fas fa-plus"></i> New Template
                 </button>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Filter Templates</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="categoryFilter" onchange="filterTemplates()">
                                <option value="">All Categories</option>
                                <option value="greeting">Greeting</option>
                                <option value="closing">Closing</option>
                                <option value="support">Support</option>
                                <option value="sales">Sales</option>
                                <option value="technical">Technical</option>
                                <option value="general">General</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="statusFilter" onchange="filterTemplates()">
                                <option value="">All Statuses</option>
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Sort By</label>
                            <select class="form-select" id="sortFilter" onchange="filterTemplates()">
                                <option value="name">Name</option>
                                <option value="category">Category</option>
                                <option value="usage_count">Most Used</option>
                                <option value="created_at">Recently Created</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" class="form-control" id="searchFilter" placeholder="Search templates..." onkeyup="filterTemplates()">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Templates Grid -->
    <div class="row" id="templatesGrid">
        <!-- Templates will be loaded here via AJAX -->
    </div>

    <!-- Loading State -->
    <div id="loadingState" class="text-center py-5" style="display: none;">
        <div class="spinner-border text-primary" role="status">
            <span class="visually-hidden">Loading...</span>
        </div>
        <p class="mt-2">Loading templates...</p>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="text-center py-5" style="display: none;">
        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">No templates found</h5>
        <p class="text-muted">Create your first template to get started.</p>
                         <button class="btn btn-primary" data-toggle="modal" data-target="#createTemplateModal">
                     <i class="fas fa-plus"></i> Create Template
                 </button>
    </div>
</div>

<!-- Create Template Modal -->
<div class="modal fade" id="createTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
                         <div class="modal-header">
                 <h5 class="modal-title">Create New Template</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                     <i class="ti-close"></i>
                 </button>
             </div>
            <form id="createTemplateForm">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Template Name *</label>
                                <input type="text" class="form-control" name="name" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Category *</label>
                                <select class="form-select" name="category" required>
                                    <option value="">Select Category</option>
                                    <option value="greeting">Greeting</option>
                                    <option value="closing">Closing</option>
                                    <option value="support">Support</option>
                                    <option value="sales">Sales</option>
                                    <option value="technical">Technical</option>
                                    <option value="general">General</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Shortcut (Optional)</label>
                                <input type="text" class="form-control" name="shortcut" maxlength="50" placeholder="e.g., greeting1">
                                <small class="text-muted">Quick access code for agents</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" class="form-control" name="sort_order" value="0" min="0">
                                <small class="text-muted">Lower numbers appear first</small>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Template Content *</label>
                        <div class="composer-toolbar mb-2">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary" onclick="insertEmojiToTemplate(':)')" title="Smile">😊</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertEmojiToTemplate(':(')" title="Sad">😢</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertEmojiToTemplate(';)')" title="Wink">😉</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertEmojiToTemplate(':D')" title="Happy">😃</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertEmojiToTemplate('thumbsup')" title="Thumbs Up">👍</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertEmojiToTemplate('check')" title="Check">✅</button>
                            </div>
                            <div class="btn-group btn-group-sm ms-2" role="group">
                                <button type="button" class="btn btn-outline-secondary" onclick="formatTemplateText('bold')" title="Bold"><strong>B</strong></button>
                                <button type="button" class="btn btn-outline-secondary" onclick="formatTemplateText('italic')" title="Italic"><em>I</em></button>
                                <button type="button" class="btn btn-outline-secondary" onclick="formatTemplateText('code')" title="Code"><code>C</code></button>
                            </div>
                        </div>
                        <textarea class="form-control" name="content" rows="8" required maxlength="4000" 
                                  placeholder="Enter your template content here... Use :), :(, ;) for emojis. Use **bold**, *italic*, `code` for formatting."></textarea>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-muted">
                                <span id="templateCharCount">0</span>/4000 characters
                            </small>
                            <small class="text-muted">
                                <span id="templateWordCount">0</span> words
                            </small>
                        </div>
                    </div>
                </div>
                                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                     <button type="submit" class="btn btn-primary">Create Template</button>
                 </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Template Modal -->
<div class="modal fade" id="editTemplateModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
                         <div class="modal-header">
                 <h5 class="modal-title">Edit Template</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                     <i class="ti-close"></i>
                 </button>
             </div>
            <form id="editTemplateForm">
                @csrf
                @method('PUT')
                <input type="hidden" name="template_id" id="editTemplateId">
                <div class="modal-body">
                    <!-- Same form fields as create, but with edit IDs -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Template Name *</label>
                                <input type="text" class="form-control" name="name" id="editTemplateName" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Category *</label>
                                <select class="form-select" name="category" id="editTemplateCategory" required>
                                    <option value="">Select Category</option>
                                    <option value="greeting">Greeting</option>
                                    <option value="closing">Closing</option>
                                    <option value="support">Support</option>
                                    <option value="sales">Sales</option>
                                    <option value="technical">Technical</option>
                                    <option value="general">General</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Shortcut (Optional)</label>
                                <input type="text" class="form-control" name="shortcut" id="editTemplateShortcut" maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Sort Order</label>
                                <input type="number" class="form-control" name="sort_order" id="editTemplateSortOrder" min="0">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Template Content *</label>
                        <div class="composer-toolbar mb-2">
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-secondary" onclick="insertEmojiToEditTemplate(':)')" title="Smile">😊</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertEmojiToEditTemplate(':(')" title="Sad">😢</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertEmojiToEditTemplate(';)')" title="Wink">😉</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertEmojiToEditTemplate(':D')" title="Happy">😃</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertEmojiToEditTemplate('thumbsup')" title="Thumbs Up">👍</button>
                                <button type="button" class="btn btn-outline-secondary" onclick="insertEmojiToEditTemplate('check')" title="Check">✅</button>
                            </div>
                            <div class="btn-group btn-group-sm ms-2" role="group">
                                <button type="button" class="btn btn-outline-secondary" onclick="formatEditTemplateText('bold')" title="Bold"><strong>B</strong></button>
                                <button type="button" class="btn btn-outline-secondary" onclick="formatEditTemplateText('italic')" title="Italic"><em>I</em></button>
                                <button type="button" class="btn btn-outline-secondary" onclick="formatEditTemplateText('code')" title="Code"><code>C</code></button>
                            </div>
                        </div>
                        <textarea class="form-control" name="content" id="editTemplateContent" rows="8" required maxlength="4000"></textarea>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-muted">
                                <span id="editTemplateCharCount">0</span>/4000 characters
                            </small>
                            <small class="text-muted">
                                <span id="editTemplateWordCount">0</span> words
                            </small>
                        </div>
                    </div>
                </div>
                                 <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                     <button type="submit" class="btn btn-primary">Update Template</button>
                 </div>
            </form>
        </div>
    </div>
</div>

<!-- Template Preview Modal -->
<div class="modal fade" id="previewTemplateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
                         <div class="modal-header">
                 <h5 class="modal-title">Template Preview</h5>
                 <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                     <i class="ti-close"></i>
                 </button>
             </div>
            <div class="modal-body">
                <div id="templatePreviewContent"></div>
            </div>
                         <div class="modal-footer">
                 <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                 <button type="button" class="btn btn-primary" onclick="useTemplate()">Use This Template</button>
             </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
.composer-toolbar {
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.5rem;
    background-color: #f8f9fa;
}

.template-card {
    transition: transform 0.2s, box-shadow 0.2s;
    cursor: pointer;
}

.template-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.template-content {
    max-height: 100px;
    overflow: hidden;
    position: relative;
}

.template-content::after {
    content: '';
    position: absolute;
    bottom: 0;
    right: 0;
    width: 100%;
    height: 20px;
    background: linear-gradient(transparent, white);
}

.template-stats {
    font-size: 0.875rem;
}

.category-badge {
    font-size: 0.75rem;
}

.shortcut-badge {
    font-size: 0.75rem;
    background-color: #6c757d;
}
</style>
@endpush

@push('scripts')
<script>
let currentTemplates = [];
let selectedTemplate = null;

document.addEventListener('DOMContentLoaded', function() {
    loadTemplates();
    initializeFormHandlers();
});

function initializeFormHandlers() {
    // Create template form
    document.getElementById('createTemplateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        createTemplate();
    });

    // Edit template form
    document.getElementById('editTemplateForm').addEventListener('submit', function(e) {
        e.preventDefault();
        updateTemplate();
    });

    // Character and word count for create form
    document.querySelector('#createTemplateModal textarea[name="content"]').addEventListener('input', function() {
        updateCounts(this, 'templateCharCount', 'templateWordCount');
    });

    // Character and word count for edit form
    document.querySelector('#editTemplateModal textarea[name="content"]').addEventListener('input', function() {
        updateCounts(this, 'editTemplateCharCount', 'editTemplateWordCount');
    });
}

function updateCounts(textarea, charCountId, wordCountId) {
    const text = textarea.value;
    document.getElementById(charCountId).textContent = text.length;
    document.getElementById(wordCountId).textContent = text.trim().split(/\s+/).filter(word => word.length > 0).length;
}

function loadTemplates() {
    showLoading();
    
    const category = document.getElementById('categoryFilter').value;
    const status = document.getElementById('statusFilter').value;
    const sort = document.getElementById('sortFilter').value;
    const search = document.getElementById('searchFilter').value;
    
    fetch(`{{ route('whatsapp.messages.templates') }}?category=${category}&status=${status}&sort=${sort}&search=${search}`)
        .then(response => response.json())
        .then(templates => {
            currentTemplates = templates;
            renderTemplates(templates);
        })
        .catch(error => {
            console.error('Error loading templates:', error);
            showError('Error loading templates');
        });
}

function renderTemplates(templates) {
    const grid = document.getElementById('templatesGrid');
    
    if (templates.length === 0) {
        hideLoading();
        showEmpty();
        return;
    }
    
    hideEmpty();
    hideLoading();
    
    grid.innerHTML = templates.map(template => `
        <div class="col-xl-4 col-lg-6 col-md-6 col-12 mb-4">
            <div class="card template-card h-100">
                <div class="card-header d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="card-title mb-1">${template.name}</h6>
                        <span class="category-badge badge ${getCategoryBadgeClass(template.category)}">
                            ${template.category_text}
                        </span>
                        ${template.shortcut ? `<span class="shortcut-badge badge ms-1">/${template.shortcut}</span>` : ''}
                    </div>
                    <div class="dropdown">
                                                 <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-toggle="dropdown">
                             <i class="fas fa-ellipsis-v"></i>
                         </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="previewTemplate(${template.id})">
                                <i class="fas fa-eye"></i> Preview
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="editTemplate(${template.id})">
                                <i class="fas fa-edit"></i> Edit
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="toggleTemplateStatus(${template.id})">
                                <i class="fas fa-${template.is_active ? 'pause' : 'play'}"></i> ${template.is_active ? 'Deactivate' : 'Activate'}
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteTemplate(${template.id})">
                                <i class="fas fa-trash"></i> Delete
                            </a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="template-content mb-3">
                        ${template.preview}
                    </div>
                    <div class="template-stats d-flex justify-content-between text-muted">
                        <span><i class="fas fa-clock"></i> ${template.created_at}</span>
                        <span><i class="fas fa-chart-line"></i> ${template.usage_count} uses</span>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

function getCategoryBadgeClass(category) {
    const classes = {
        'greeting': 'bg-primary',
        'closing': 'bg-success',
        'support': 'bg-info',
        'sales': 'bg-warning',
        'technical': 'bg-secondary',
        'general': 'bg-dark'
    };
    return classes[category] || 'bg-secondary';
}

function filterTemplates() {
    loadTemplates();
}

function createTemplate() {
    const form = document.getElementById('createTemplateForm');
    const formData = new FormData(form);
    
    fetch(`{{ route('whatsapp.messages.create-template') }}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Template created successfully!');
            $('#createTemplateModal').modal('hide');
            form.reset();
            loadTemplates();
        } else {
            showAlert('error', data.message || 'Error creating template');
        }
    })
    .catch(error => {
        showAlert('error', 'Error creating template');
        console.error('Error:', error);
    });
}

function editTemplate(templateId) {
    const template = currentTemplates.find(t => t.id == templateId);
    if (!template) return;
    
    // Populate edit form
    document.getElementById('editTemplateId').value = template.id;
    document.getElementById('editTemplateName').value = template.name;
    document.getElementById('editTemplateCategory').value = template.category;
    document.getElementById('editTemplateShortcut').value = template.shortcut || '';
    document.getElementById('editTemplateSortOrder').value = template.sort_order;
    document.getElementById('editTemplateContent').value = template.content;
    
    // Update counts
    updateCounts(document.getElementById('editTemplateContent'), 'editTemplateCharCount', 'editTemplateWordCount');
    
            // Show modal
        const modal = $('#editTemplateModal');
        modal.modal('show');
}

function updateTemplate() {
    const form = document.getElementById('editTemplateForm');
    const formData = new FormData(form);
    const templateId = document.getElementById('editTemplateId').value;
    
    fetch(`/whatsapp/templates/${templateId}`, {
        method: 'PUT',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Template updated successfully!');
            $('#editTemplateModal').modal('hide');
            loadTemplates();
        } else {
            showAlert('error', data.message || 'Error updating template');
        }
    })
    .catch(error => {
        showAlert('error', 'Error updating template');
        console.error('Error:', error);
    });
}

function previewTemplate(templateId) {
    const template = currentTemplates.find(t => t.id == templateId);
    if (!template) return;
    
    selectedTemplate = template;
    document.getElementById('templatePreviewContent').innerHTML = `
        <div class="mb-3">
            <strong>Name:</strong> ${template.name}<br>
            <strong>Category:</strong> <span class="badge ${getCategoryBadgeClass(template.category)}">${template.category_text}</span><br>
            <strong>Shortcut:</strong> ${template.shortcut ? '/' + template.shortcut : 'None'}<br>
            <strong>Usage:</strong> ${template.usage_count} times
        </div>
        <div class="border rounded p-3 bg-light">
            <h6>Preview:</h6>
            <div>${template.content}</div>
        </div>
    `;
    
                    const modal = $('#previewTemplateModal');
        modal.modal('show');
}

function useTemplate() {
    if (selectedTemplate) {
        // TODO: Implement template usage - redirect to message composer
        showAlert('info', 'Template usage feature coming soon!');
        $('#previewTemplateModal').modal('hide');
    }
}

function toggleTemplateStatus(templateId) {
    if (confirm('Are you sure you want to change this template\'s status?')) {
        // TODO: Implement status toggle
        showAlert('info', 'Status toggle feature coming soon!');
    }
}

function deleteTemplate(templateId) {
    if (confirm('Are you sure you want to delete this template? This action cannot be undone.')) {
        // TODO: Implement template deletion
        showAlert('info', 'Template deletion feature coming soon!');
    }
}

// Emoji and formatting functions for create form
function insertEmojiToTemplate(shortcut) {
    insertEmojiToTextarea('#createTemplateModal textarea[name="content"]', shortcut);
}

function formatTemplateText(type) {
    formatTextareaText('#createTemplateModal textarea[name="content"]', type);
}

// Emoji and formatting functions for edit form
function insertEmojiToEditTemplate(shortcut) {
    insertEmojiToTextarea('#editTemplateModal textarea[name="content"]', shortcut);
}

function formatEditTemplateText(type) {
    formatTextareaText('#editTemplateModal textarea[name="content"]', type);
}

function insertEmojiToTextarea(selector, shortcut) {
    const textarea = document.querySelector(selector);
    const emojiMap = {
        ':)': '😊',
        ':(': '😢',
        ';)': '😉',
        ':D': '😃',
        'thumbsup': '👍',
        'check': '✅'
    };
    
    const emoji = emojiMap[shortcut] || shortcut;
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const text = textarea.value;
    
    textarea.value = text.substring(0, start) + emoji + text.substring(end);
    textarea.selectionStart = textarea.selectionEnd = start + emoji.length;
    textarea.focus();
    
    // Trigger input event to update counts
    textarea.dispatchEvent(new Event('input'));
}

function formatTextareaText(selector, type) {
    const textarea = document.querySelector(selector);
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textarea.value.substring(start, end);
    
    let formattedText = '';
    switch(type) {
        case 'bold':
            formattedText = `**${selectedText}**`;
            break;
        case 'italic':
            formattedText = `*${selectedText}*`;
            break;
        case 'code':
            formattedText = `\`${selectedText}\``;
            break;
    }
    
    textarea.value = textarea.value.substring(0, start) + formattedText + textarea.value.substring(end);
    textarea.selectionStart = start + formattedText.length;
    textarea.selectionEnd = start + formattedText.length;
    textarea.focus();
    
    // Trigger input event to update counts
    textarea.dispatchEvent(new Event('input'));
}

function showLoading() {
    document.getElementById('loadingState').style.display = 'block';
    document.getElementById('templatesGrid').innerHTML = '';
}

function hideLoading() {
    document.getElementById('loadingState').style.display = 'none';
}

function showEmpty() {
    document.getElementById('emptyState').style.display = 'block';
}

function hideEmpty() {
    document.getElementById('emptyState').style.display = 'none';
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.top = '20px';
    alertDiv.style.right = '20px';
    alertDiv.style.zIndex = '9999';
    alertDiv.innerHTML = `
        ${message}
                 <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
             <i class="ti-close"></i>
         </button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

function showError(message) {
    showAlert('error', message);
}
</script>
@endpush

