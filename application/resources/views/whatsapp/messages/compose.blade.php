@extends('whatsapp.layouts.app')

@section('title', 'Compose Message')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col">
                <h3 class="page-title">Compose Message</h3>
                <ul class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('whatsapp.dashboard') }}">WhatsApp</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('whatsapp.tickets.show', $ticket) }}">Ticket #{{ $ticket->id }}</a></li>
                    <li class="breadcrumb-item active">Compose</li>
                </ul>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Message Composer -->
        <div class="col-xl-8 col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">New Message</h5>
                </div>
                <div class="card-body">
                    <form id="messageForm" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Channel Selection -->
                        <div class="mb-3">
                            <label class="form-label">Channel</label>
                            <div class="btn-group w-100" role="group">
                                <input type="radio" class="btn-check" name="channel" id="whatsapp" value="whatsapp" checked>
                                <label class="btn btn-outline-primary" for="whatsapp">
                                    <i class="fab fa-whatsapp"></i> WhatsApp
                                </label>
                                
                                <input type="radio" class="btn-check" name="channel" id="email" value="email">
                                <label class="btn btn-outline-primary" for="email">
                                    <i class="fas fa-envelope"></i> Email
                                </label>
                            </div>
                        </div>

                        <!-- Message Content -->
                        <div class="mb-3">
                            <label class="form-label">Message Content</label>
                            <div class="composer-toolbar mb-2">
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="insertEmoji(':)')" title="Smile">😊</button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="insertEmoji(':(')" title="Sad">😢</button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="insertEmoji(';)')" title="Wink">😉</button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="insertEmoji(':D')" title="Happy">😃</button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="insertEmoji('thumbsup')" title="Thumbs Up">👍</button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="insertEmoji('check')" title="Check">✅</button>
                                </div>
                                <div class="btn-group btn-group-sm ms-2" role="group">
                                    <button type="button" class="btn btn-outline-secondary" onclick="formatText('bold')" title="Bold"><strong>B</strong></button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="formatText('italic')" title="Italic"><em>I</em></button>
                                    <button type="button" class="btn btn-outline-secondary" onclick="formatText('code')" title="Code"><code>C</code></button>
                                </div>
                            </div>
                            <textarea class="form-control" id="messageContent" name="message" rows="8" 
                                      placeholder="Type your message here... Use :), :(, ;) for emojis. Use **bold**, *italic*, `code` for formatting."
                                      maxlength="4000"></textarea>
                            <div class="d-flex justify-content-between mt-2">
                                <small class="text-muted">
                                    <span id="charCount">0</span>/4000 characters
                                </div>
                                <small class="text-muted">
                                    <span id="wordCount">0</span> words
                                </small>
                            </div>
                        </div>

                        <!-- Quick Templates -->
                        <div class="mb-3">
                            <label class="form-label">Quick Templates</label>
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <select class="form-select" id="templateCategory" onchange="loadTemplates()">
                                        <option value="">All Categories</option>
                                        <option value="greeting">Greeting</option>
                                        <option value="closing">Closing</option>
                                        <option value="support">Support</option>
                                        <option value="sales">Sales</option>
                                        <option value="technical">Technical</option>
                                        <option value="general">General</option>
                                    </select>
                                </div>
                                <div class="col-md-9">
                                    <select class="form-select" id="templateSelect" onchange="insertTemplate()">
                                        <option value="">Select a template...</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Attachments -->
                        <div class="mb-3">
                            <label class="form-label">Attachments</label>
                            <div class="input-group">
                                <input type="file" class="form-control" id="attachments" name="attachments[]" multiple accept="image/*,video/*,audio/*,application/pdf,.doc,.docx,.txt">
                                <button class="btn btn-outline-secondary" type="button" onclick="clearAttachments()">Clear</button>
                            </div>
                            <div id="attachmentPreview" class="mt-2"></div>
                            <small class="text-muted">Max file size: 10MB. Supported: Images, Videos, Audio, PDF, Documents</small>
                        </div>

                        <!-- Reply to Message -->
                        @if(isset($replyToMessage))
                        <div class="mb-3">
                            <label class="form-label">Replying to:</label>
                            <div class="alert alert-info">
                                <strong>{{ $replyToMessage->sender_name }}</strong> 
                                <small class="text-muted">{{ $replyToMessage->created_at->diffForHumans() }}</small>
                                <p class="mb-0 mt-1">{{ $replyToMessage->body }}</p>
                            </div>
                            <input type="hidden" name="reply_to_message_id" value="{{ $replyToMessage->id }}">
                        </div>
                        @endif

                        <!-- Send Button -->
                        <div class="d-flex justify-content-between">
                            <button type="button" class="btn btn-secondary" onclick="saveAsDraft()">
                                <i class="fas fa-save"></i> Save as Draft
                            </button>
                            <button type="submit" class="btn btn-primary" id="sendButton">
                                <i class="fas fa-paper-plane"></i> Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4 col-12">
            <!-- Ticket Info -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Ticket Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Contact:</strong><br>
                        {{ $ticket->contact_name }}
                        @if($ticket->contact_email)
                            <br><small class="text-muted">{{ $ticket->contact_email }}</small>
                        @endif
                        @if($ticket->contact_phone)
                            <br><small class="text-muted">{{ $ticket->contact_phone }}</small>
                        @endif
                    </div>
                    <div class="mb-3">
                        <strong>Subject:</strong><br>
                        {{ $ticket->subject ?? 'No subject' }}
                    </div>
                    <div class="mb-3">
                        <strong>Status:</strong>
                        <span class="badge bg-{{ $ticket->status === 'open' ? 'danger' : ($ticket->status === 'in_progress' ? 'warning' : 'success') }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <strong>Priority:</strong>
                        <span class="badge bg-{{ $ticket->priority === 'urgent' ? 'danger' : ($ticket->priority === 'high' ? 'warning' : 'info') }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </div>
                    @if($ticket->tags && is_array($ticket->tags))
                    <div class="mb-3">
                        <strong>Tags:</strong><br>
                        @foreach($ticket->tags as $tag)
                            <span class="badge bg-secondary me-1">{{ $tag }}</span>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            <!-- Recent Messages -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Recent Messages</h5>
                </div>
                <div class="card-body">
                    <div class="message-thread">
                        @forelse($ticket->messages()->latest()->take(5)->get() as $message)
                        <div class="message-item mb-2 {{ $message->sender_type === 'agent' ? 'text-end' : '' }}">
                            <div class="message-bubble {{ $message->sender_type === 'agent' ? 'bg-primary text-white' : 'bg-light' }}">
                                <small class="d-block">{{ $message->sender_name }}</small>
                                <div class="message-content">{{ $message->body }}</div>
                                <small class="d-block mt-1">{{ $message->created_at->format('H:i') }}</small>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted text-center">No messages yet</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-success" onclick="closeTicket()">
                            <i class="fas fa-check"></i> Close Ticket
                        </button>
                        <button class="btn btn-outline-warning" onclick="putOnHold()">
                            <i class="fas fa-pause"></i> Put On Hold
                        </button>
                        <button class="btn btn-outline-info" onclick="assignToMe()">
                            <i class="fas fa-user"></i> Assign to Me
                        </button>
                    </div>
                </div>
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

.message-thread {
    max-height: 300px;
    overflow-y: auto;
}

.message-item {
    margin-bottom: 0.5rem;
}

.message-bubble {
    display: inline-block;
    padding: 0.5rem 0.75rem;
    border-radius: 1rem;
    max-width: 80%;
    word-wrap: break-word;
}

.message-content {
    margin: 0.25rem 0;
}

#attachmentPreview {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.attachment-item {
    position: relative;
    border: 1px solid #dee2e6;
    border-radius: 0.375rem;
    padding: 0.5rem;
    background-color: #f8f9fa;
}

.attachment-remove {
    position: absolute;
    top: -0.5rem;
    right: -0.5rem;
    background: #dc3545;
    color: white;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 12px;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    initializeComposer();
    loadTemplates();
});

function initializeComposer() {
    const textarea = document.getElementById('messageContent');
    
    // Character and word count
    textarea.addEventListener('input', function() {
        const text = this.value;
        document.getElementById('charCount').textContent = text.length;
        document.getElementById('wordCount').textContent = text.trim().split(/\s+/).filter(word => word.length > 0).length;
    });

    // Handle form submission
    document.getElementById('messageForm').addEventListener('submit', function(e) {
        e.preventDefault();
        sendMessage();
    });

    // Handle file attachments
    document.getElementById('attachments').addEventListener('change', function(e) {
        handleAttachments(e.target.files);
    });
}

function insertEmoji(shortcut) {
    const textarea = document.getElementById('messageContent');
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
}

function formatText(type) {
    const textarea = document.getElementById('messageContent');
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
}

function loadTemplates() {
    const category = document.getElementById('templateCategory').value;
    const select = document.getElementById('templateSelect');
    
    fetch(`{{ route('whatsapp.messages.templates') }}?category=${category}`)
        .then(response => response.json())
        .then(templates => {
            select.innerHTML = '<option value="">Select a template...</option>';
            templates.forEach(template => {
                const option = document.createElement('option');
                option.value = template.id;
                option.textContent = template.name;
                select.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading templates:', error);
        });
}

function insertTemplate() {
    const select = document.getElementById('templateSelect');
    const templateId = select.value;
    
    if (!templateId) return;
    
    fetch(`{{ route('whatsapp.messages.templates') }}?category=all`)
        .then(response => response.json())
        .then(templates => {
            const template = templates.find(t => t.id == templateId);
            if (template) {
                document.getElementById('messageContent').value = template.content;
                select.value = '';
            }
        });
}

function handleAttachments(files) {
    const preview = document.getElementById('attachmentPreview');
    preview.innerHTML = '';
    
    Array.from(files).forEach((file, index) => {
        const item = document.createElement('div');
        item.className = 'attachment-item';
        
        if (file.type.startsWith('image/')) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            img.style.maxWidth = '100px';
            img.style.maxHeight = '100px';
            item.appendChild(img);
        } else {
            const icon = document.createElement('i');
            icon.className = 'fas fa-file fa-2x text-muted';
            item.appendChild(icon);
        }
        
        const name = document.createElement('div');
        name.className = 'mt-1';
        name.style.fontSize = '12px';
        name.textContent = file.name.length > 20 ? file.name.substring(0, 20) + '...' : file.name;
        item.appendChild(name);
        
        const remove = document.createElement('div');
        remove.className = 'attachment-remove';
        remove.innerHTML = '×';
        remove.onclick = () => removeAttachment(index);
        item.appendChild(remove);
        
        preview.appendChild(item);
    });
}

function removeAttachment(index) {
    const input = document.getElementById('attachments');
    const dt = new DataTransfer();
    const { files } = input;
    
    for (let i = 0; i < files.length; i++) {
        if (i !== index) {
            dt.items.add(files[i]);
        }
    }
    
    input.files = dt.files;
    handleAttachments(input.files);
}

function clearAttachments() {
    document.getElementById('attachments').value = '';
    document.getElementById('attachmentPreview').innerHTML = '';
}

function sendMessage() {
    const form = document.getElementById('messageForm');
    const formData = new FormData(form);
    const sendButton = document.getElementById('sendButton');
    
    sendButton.disabled = true;
    sendButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    
    fetch(`{{ route('whatsapp.messages.send', $ticket) }}`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Message sent successfully!');
            setTimeout(() => {
                window.location.href = `{{ route('whatsapp.tickets.show', $ticket) }}`;
            }, 1500);
        } else {
            showAlert('error', data.message || 'Error sending message');
        }
    })
    .catch(error => {
        showAlert('error', 'Error sending message');
        console.error('Error:', error);
    })
    .finally(() => {
        sendButton.disabled = false;
        sendButton.innerHTML = '<i class="fas fa-paper-plane"></i> Send Message';
    });
}

function saveAsDraft() {
    // TODO: Implement draft saving
    showAlert('info', 'Draft saving coming soon!');
}

function closeTicket() {
    if (confirm('Are you sure you want to close this ticket?')) {
        fetch(`{{ route('whatsapp.tickets.close', $ticket) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function putOnHold() {
    if (confirm('Put this ticket on hold?')) {
        fetch(`{{ route('whatsapp.tickets.put-on-hold', $ticket) }}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
}

function assignToMe() {
    fetch(`{{ route('whatsapp.tickets.assign', $ticket) }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            agent_id: {{ auth()->id() }}
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    });
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
</script>
@endpush

