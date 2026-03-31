<?php $__env->startSection('whatsapp-content'); ?>
<div class="container-fluid">
    <!-- Success/Error Messages -->
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <!-- Ticket Header -->
    <div class="whatsapp-card mb-4">
        <div class="card-header">
            <h5 class="mb-1">
                <i class="fas fa-ticket-alt me-2"></i>
                Ticket #<?php echo e($ticket->id); ?>: <?php echo e($ticket->subject); ?>

            </h5>
            <div class="d-flex align-items-center gap-3">
                <span class="status-badge status-<?php echo e($ticket->status); ?>">
                    <?php echo e(ucfirst(str_replace('_', ' ', $ticket->status))); ?>

                </span>
                <span class="priority-badge priority-<?php echo e($ticket->priority); ?>">
                    <?php echo e(ucfirst($ticket->priority)); ?>

                </span>
                <span class="channel-badge channel-<?php echo e($ticket->channel); ?>">
                    <i class="fab fa-<?php echo e($ticket->channel == 'whatsapp' ? 'whatsapp' : 'envelope'); ?> me-1"></i>
                    <?php echo e(ucfirst($ticket->channel)); ?>

                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h6 class="fw-bold">Contact Information</h6>
                    <p><strong>Name:</strong> <?php echo e($ticket->contact_name); ?></p>
                    <p><strong>Email:</strong> <?php echo e($ticket->contact_email ?: 'Not provided'); ?></p>
                    <?php if($ticket->contact_phone): ?>
                        <p><strong>Phone:</strong> <?php echo e($ticket->contact_phone); ?></p>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold">Ticket Details</h6>
                    <p><strong>Opened:</strong> <?php echo e($ticket->opened_at ? $ticket->opened_at->format('M d, Y H:i') : 'Not set'); ?></p>
                    <p><strong>Agent:</strong> 
                        <?php if($ticket->agent && $ticket->agent->first_name): ?>
                            <?php echo e($ticket->agent->first_name); ?> <?php echo e($ticket->agent->last_name); ?>

                        <?php else: ?>
                            Unassigned
                        <?php endif; ?>
                    </p>
                    <?php if($ticket->ticketType): ?>
                        <p><strong>Type:</strong> <?php echo e($ticket->ticketType->name); ?></p>
                    <?php endif; ?>
                    <?php if($ticket->first_response_at): ?>
                        <p><strong>First Response:</strong> <?php echo e($ticket->first_response_at->format('M d, Y H:i')); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Messages -->
    <div class="whatsapp-card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-comments me-2"></i>
                Conversation
            </h5>
        </div>
        <div class="card-body">
            <?php $__currentLoopData = ($messages ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="message <?php echo e($message->isFromClient() ? 'message-client' : 'message-agent'); ?> mb-3">
                    <div class="message-content p-3 rounded">
                        <div class="message-header mb-2">
                            <strong>
                                <?php if($message->isFromClient()): ?>
                                    <?php echo e($message->sender_name); ?>

                                <?php else: ?>
                                    <?php echo e($message->sender ? $message->sender->first_name . ' ' . $message->sender->last_name : $message->sender_name); ?> (Agent)
                                <?php endif; ?>
                            </strong>
                            <small class="text-muted ms-2"><?php echo e($message->created_at->format('M d, Y H:i')); ?></small>
                        </div>
                        <div class="message-body">
                            <?php echo clean($message->body); ?>

                        </div>
                        <?php if(!empty($message->attachments)): ?>
                        <div class="message-attachments mt-2">
                            <?php $__currentLoopData = $message->attachments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $att): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="attachment-item mb-1">
                                    <a href="<?php echo e($att['path'] ?? '#'); ?>" target="_blank">
                                        <i class="fas fa-paperclip me-1"></i>
                                        <?php echo e($att['filename'] ?? 'file'); ?>

                                    </a>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <!-- Reply Form -->
    <?php if($ticket->status !== 'closed'): ?>
        <div class="whatsapp-card mt-4">
            <div class="card-body">
                <h6 class="fw-bold mb-3">Send Reply</h6>
                <form method="POST" action="<?php echo e(route('whatsapp.tickets.send-message', $ticket)); ?>" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <div class="composer-toolbar mb-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="btn-emoji-picker" title="Insert Emoji">
                                <i class="far fa-smile"></i>
                            </button>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="templatesDropdown" data-toggle="dropdown" aria-expanded="false">
                                    Quick Templates
                                </button>
                                <ul class="dropdown-menu templates-menu" aria-labelledby="templatesDropdown">
                                    <?php $__empty_1 = true; $__currentLoopData = ($canned_responses ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $canned): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <li>
                                            <a class="dropdown-item js-insert-template" href="#" data-template="<?php echo str_replace(["\n", "\r"], '', addslashes($canned->canned_message)); ?>">
                                                <div class="fw-bold"><?php echo e($canned->canned_title); ?></div>
                                                <small class="text-muted"><?php echo e($canned->category_name); ?></small>
                                            </a>
                                        </li>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <li class="px-3 py-2 text-muted">No templates found</li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                        <div class="nx-textarea-wrapper">
                            <textarea class="form-control nx-textarea" name="message" id="replyMessage" rows="4" maxlength="4000"
                                      placeholder="Type your message here..." required></textarea>
                            <div class="nx-textarea-counter"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Attachments</label>
                        <input class="form-control" type="file" id="attachmentsInput" name="attachments[]" multiple>
                        <div id="attachmentsPreview" class="row g-2 mt-2"></div>
                        <small class="text-muted">Max 10MB each. Images, documents, etc.</small>
                    </div>
                    <div class="mb-3">
                        <label for="channel" class="form-label">Channel</label>
                        <select class="form-select" name="channel" id="replyChannel" onchange="toggleReplyApiSelection()" required style="height: 35px; font-size: 15px;">
                            <option value="whatsapp" <?php echo e($ticket->channel == 'whatsapp' ? 'selected' : ''); ?>>WhatsApp</option>
                            <option value="email" <?php echo e($ticket->channel == 'email' ? 'selected' : ''); ?>>Email</option>
                        </select>
                    </div>

                    <!-- API Selection for Reply -->
                    <div class="mb-3" id="reply-api-selection" style="display: none;">
                        <label class="form-label">API Configuration</label>
                        <div class="row">
                            <div class="col-md-6">
                                <div id="whatsapp-api-reply" style="display: none;">
                                    <select class="form-select" name="whatsapp_api_type">
                                        <option value="cloud">WhatsApp Cloud API (Recommended)</option>
                                        <option value="legacy">Legacy WhatsApp API</option>
                                    </select>
                                </div>
                                <div id="email-api-reply" style="display: none;">
                                    <select class="form-select" name="email_api_type">
                                        <option value="enhanced">Enhanced Email Service (Recommended)</option>
                                        <option value="legacy">Legacy Email Service</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="replyTestResult" class="mt-3" style="display: none;"></div>
                    </div>

                    <button type="submit" class="btn btn-whatsapp">
                        <i class="fab fa-<?php echo e($ticket->channel == 'whatsapp' ? 'whatsapp' : 'envelope'); ?> me-2"></i>
                        Send Message
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
    (function(){
        var textarea = document.getElementById('replyMessage');
        if (!textarea) return;

        // basic emoji insert using a simple native picker where supported
        var btnEmoji = document.getElementById('btn-emoji-picker');
        if (btnEmoji) {
            btnEmoji.addEventListener('click', function(){
                // remove any existing emoji menu to prevent duplicates
                var existing = document.querySelector('.emoji-menu');
                if (existing && existing.parentNode) {
                    existing.parentNode.removeChild(existing);
                }
                // Emoji categories similar to EmojiCopy tabbed groups
                var categories = [
                    { key: 'smileys', icon: '😊', items: ['😀','😁','😂','🤣','😃','😄','😅','😊','🙂','🙃','😉','😇','🥰','😍','😘','😗','😙','😚','🤪','🤗','🤭','🤔','🤐','🙄','😏','😴','🤯'] },
                    { key: 'hands', icon: '👍', items: ['👍','👎','👌','🤌','🤏','✌️','🤟','🤘','🤙','👋','👏','🙌','👐','🙏','💪','🫶'] },
                    { key: 'hearts', icon: '❤️', items: ['❤️','🧡','💛','💚','💙','💜','🖤','🤍','🤎','💖','💗','💓','💞','💕','💘','💝','💟'] },
                    { key: 'symbols', icon: '✨', items: ['✨','🔥','💯','⭐','🌟','⚡','💡','❗','❓','✅','✔️','❌','🔔','🔕'] },
                    { key: 'office', icon: '📎', items: ['📌','📎','🖇️','📁','📄','📝','✏️','📅','📂','📤','📥'] }
                ];
                var menu = document.createElement('div');
                menu.className = 'emoji-menu';
                var tabs = document.createElement('div');
                tabs.className = 'emoji-tabs';
                var grid = document.createElement('div');
                grid.className = 'emoji-grid';

                function renderGrid(items){
                    grid.innerHTML = '';
                    items.forEach(function(e){
                        var b = document.createElement('button');
                        b.type = 'button';
                        b.className = 'emoji-item';
                        b.textContent = e;
                        b.addEventListener('click', function(){
                            insertContent(e);
                            if (menu && menu.parentNode) {
                                menu.parentNode.removeChild(menu);
                            }
                        });
                        grid.appendChild(b);
                    });
                }

                categories.forEach(function(cat, idx){
                    var t = document.createElement('button');
                    t.type = 'button';
                    t.className = 'emoji-tab' + (idx === 0 ? ' active' : '');
                    t.textContent = cat.icon;
                    t.addEventListener('click', function(){
                        tabs.querySelectorAll('.emoji-tab').forEach(function(x){ x.classList.remove('active'); });
                        t.classList.add('active');
                        renderGrid(cat.items);
                    });
                    tabs.appendChild(t);
                });

                menu.appendChild(tabs);
                renderGrid(categories[0].items);
                menu.appendChild(grid);
                var rect = btnEmoji.getBoundingClientRect();
                menu.style.left = (rect.left + window.scrollX) + 'px';
                menu.style.top = (rect.bottom + window.scrollY + 6) + 'px';
                document.body.appendChild(menu);
                document.addEventListener('click', function onDoc(e){
                    if (!menu.contains(e.target) && e.target !== btnEmoji){
                        if (menu && menu.parentNode) {
                            menu.parentNode.removeChild(menu);
                        }
                        document.removeEventListener('click', onDoc);
                    }
                });
            });
        }

        document.querySelectorAll('.js-insert-template').forEach(function(el){
            el.addEventListener('click', function(ev){
                ev.preventDefault();
                var html = this.getAttribute('data-template') || '';
                var text = html.replace(/<[^>]+>/g, '');
                insertContent(text);
            });
        });

        function insertContent(value){
            var ta = textarea;
            var editors = window.NXCKEditors || {};
            var editor = (ta && editors[ta.id]) ? editors[ta.id] : null;
            if (editor) {
                editor.model.change(function(writer){
                    var viewFragment = editor.data.processor.toView(value);
                    var modelFragment = editor.data.toModel(viewFragment);
                    editor.model.insertContent(modelFragment, editor.model.document.selection);
                });
                return;
            }
            if (!ta) return;
            var start = ta.selectionStart || 0;
            var end = ta.selectionEnd || 0;
            var before = ta.value.substring(0, start);
            var after = ta.value.substring(end, ta.value.length);
            ta.value = before + value + after;
            var pos = start + value.length;
            ta.selectionStart = ta.selectionEnd = pos;
            ta.focus();
        }

        // attachments preview
        var input = document.getElementById('attachmentsInput');
        var preview = document.getElementById('attachmentsPreview');
        if (input && preview){
            input.addEventListener('change', function(){
                preview.innerHTML = '';
                Array.prototype.slice.call(input.files || []).forEach(function(file){
                    var col = document.createElement('div');
                    col.className = 'col-auto';
                    var card = document.createElement('div');
                    card.className = 'border rounded p-2 d-flex align-items-center';
                    card.style.minWidth = '120px';
                    card.style.maxWidth = '180px';
                    var name = document.createElement('div');
                    name.className = 'small text-truncate';
                    name.style.maxWidth = '120px';
                    name.textContent = file.name;

                    if (file.type.startsWith('image/')){
                        var img = document.createElement('img');
                        img.className = 'rounded me-2';
                        img.style.width = '40px';
                        img.style.height = '40px';
                        img.style.objectFit = 'cover';
                        var reader = new FileReader();
                        reader.onload = function(e){ img.src = e.target.result; };
                        reader.readAsDataURL(file);
                        card.appendChild(img);
                        card.appendChild(name);
                    } else {
                        var icon = document.createElement('i');
                        icon.className = 'fas fa-paperclip me-2';
                        card.appendChild(icon);
                        card.appendChild(name);
                    }
                    col.appendChild(card);
                    preview.appendChild(col);
                });
            });
        }
    })();

    // Enhanced API functionality for replies
    let currentCredentials = {
        whatsapp: {},
        email: {}
    };

    function toggleReplyApiSelection() {
        const channel = document.getElementById('replyChannel').value;
        const apiSection = document.getElementById('reply-api-selection');
        const whatsappSection = document.getElementById('whatsapp-api-reply');
        const emailSection = document.getElementById('email-api-reply');
        
        if (channel === 'whatsapp') {
            apiSection.style.display = 'block';
            whatsappSection.style.display = 'block';
            emailSection.style.display = 'none';
        } else if (channel === 'email') {
            apiSection.style.display = 'block';
            whatsappSection.style.display = 'none';
            emailSection.style.display = 'block';
        } else {
            apiSection.style.display = 'none';
            whatsappSection.style.display = 'none';
            emailSection.style.display = 'none';
        }
    }

    function openWhatsAppCredentials() {
        loadSavedCredentials();
        $('#whatsappCredentialsModal').modal('show');
    }

    function openEmailCredentials() {
        loadSavedCredentials();
        $('#emailCredentialsModal').modal('show');
    }

    function testReplyConnection() {
        const channel = document.getElementById('replyChannel').value;
        if (channel === 'whatsapp') {
            testWhatsAppConnection();
        } else if (channel === 'email') {
            testSmtpConnection();
        }
    }

    function sendTestReply() {
        const channel = document.getElementById('replyChannel').value;
        const message = document.getElementById('replyMessage').value;
        
        if (!message.trim()) {
            alert('Please enter a message first!');
            return;
        }

        if (channel === 'whatsapp') {
            sendTestWhatsAppMessage();
        } else if (channel === 'email') {
            sendTestEmailMessage();
        }
    }

    function loadSavedCredentials() {
        const saved = localStorage.getItem('whatsapp_credentials');
        if (saved) {
            currentCredentials = JSON.parse(saved);
        }
    }

    async function testWhatsAppConnection() {
        if (!currentCredentials.whatsapp.access_token || !currentCredentials.whatsapp.phone_number_id) {
            alert('Please configure WhatsApp credentials first!');
            return;
        }

        try {
            const response = await fetch('/whatsapp/test/whatsapp-connection', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(currentCredentials.whatsapp)
            });
            
            const result = await response.json();
            showReplyTestResult(result, result.success ? 'success' : 'error');
        } catch (error) {
            showReplyTestResult({ error: error.message }, 'error');
        }
    }

    async function testSmtpConnection() {
        if (!currentCredentials.email.smtp_host || !currentCredentials.email.smtp_username) {
            alert('Please configure SMTP credentials first!');
            return;
        }

        try {
            const response = await fetch('/whatsapp/test/smtp-connection', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(currentCredentials.email)
            });
            
            const result = await response.json();
            showReplyTestResult(result, result.success ? 'success' : 'error');
        } catch (error) {
            showReplyTestResult({ error: error.message }, 'error');
        }
    }

    async function sendTestWhatsAppMessage() {
        if (!currentCredentials.whatsapp.access_token || !currentCredentials.whatsapp.phone_number_id) {
            alert('Please configure WhatsApp credentials first!');
            return;
        }

        const message = document.getElementById('replyMessage').value;
        const phoneNumber = '<?php echo e($ticket->contact_phone); ?>';

        try {
            const data = {
                ...currentCredentials.whatsapp,
                phone_number: phoneNumber,
                message: message
            };

            const response = await fetch('/whatsapp/test/whatsapp-message', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            showReplyTestResult(result, result.success ? 'success' : 'error');
        } catch (error) {
            showReplyTestResult({ error: error.message }, 'error');
        }
    }

    async function sendTestEmailMessage() {
        if (!currentCredentials.email.smtp_host || !currentCredentials.email.smtp_username) {
            alert('Please configure SMTP credentials first!');
            return;
        }

        const message = document.getElementById('replyMessage').value;
        const email = '<?php echo e($ticket->contact_email); ?>';

        if (!email) {
            alert('No email address available for this ticket!');
            return;
        }

        try {
            const data = {
                ...currentCredentials.email,
                email: email,
                subject: 'Test Reply - Ticket #<?php echo e($ticket->id); ?>',
                message: message
            };

            const response = await fetch('/whatsapp/test/email', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            
            const result = await response.json();
            showReplyTestResult(result, result.success ? 'success' : 'error');
        } catch (error) {
            showReplyTestResult({ error: error.message }, 'error');
        }
    }

    function showReplyTestResult(data, type) {
        const element = document.getElementById('replyTestResult');
        element.style.display = 'block';
        element.className = `alert alert-${type === 'success' ? 'success' : 'danger'}`;
        element.innerHTML = `<strong>${type === 'success' ? '✅ Success' : '❌ Error'}:</strong><br><pre>${JSON.stringify(data, null, 2)}</pre>`;
    }

    // Load credentials on page load
    document.addEventListener('DOMContentLoaded', function() {
        loadSavedCredentials();
        toggleReplyApiSelection();
    });
</script>

<!-- WhatsApp Credentials Modal -->
<div class="modal fade" id="whatsappCredentialsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fab fa-whatsapp me-2"></i>
                    Configure WhatsApp Cloud API Credentials
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="ti-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Access Token</label>
                            <input type="text" class="form-control" id="modalWhatsappToken" placeholder="EAA...">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Phone Number ID</label>
                            <input type="text" class="form-control" id="modalWhatsappPhoneId" placeholder="123456789">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Business Account ID</label>
                            <input type="text" class="form-control" id="modalWhatsappBusinessId" placeholder="123456789">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Test Connection</label>
                            <button type="button" class="btn btn-outline-success w-100" onclick="testWhatsAppConnection()">
                                <i class="fas fa-plug me-2"></i>Test Connection
                            </button>
                        </div>
                    </div>
                </div>
                <div id="whatsappTestResult" class="mt-3" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" onclick="saveWhatsAppCredentials()">
                    <i class="fas fa-save me-2"></i>Save Credentials
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Email Credentials Modal -->
<div class="modal fade" id="emailCredentialsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="fas fa-envelope me-2"></i>
                    Configure SMTP Credentials
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                    <i class="ti-close"></i>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">SMTP Host</label>
                            <input type="text" class="form-control" id="modalSmtpHost" placeholder="smtp.gmail.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">SMTP Port</label>
                            <input type="text" class="form-control" id="modalSmtpPort" value="587">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" id="modalSmtpUsername" placeholder="yourname@gmail.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" id="modalSmtpPassword" placeholder="your_app_password">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Encryption</label>
                            <select class="form-select" id="modalSmtpEncryption">
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Test Connection</label>
                            <button type="button" class="btn btn-outline-info w-100" onclick="testSmtpConnection()">
                                <i class="fas fa-plug me-2"></i>Test SMTP
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">From Address</label>
                            <input type="email" class="form-control" id="modalFromAddress" placeholder="yourname@gmail.com">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">From Name</label>
                            <input type="text" class="form-control" id="modalFromName" placeholder="Your Name">
                        </div>
                    </div>
                </div>
                <div id="emailTestResult" class="mt-3" style="display: none;"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-info" onclick="saveEmailCredentials()">
                    <i class="fas fa-save me-2"></i>Save Credentials
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Credential management functions
function saveWhatsAppCredentials() {
    currentCredentials.whatsapp = {
        access_token: document.getElementById('modalWhatsappToken').value,
        phone_number_id: document.getElementById('modalWhatsappPhoneId').value,
        business_account_id: document.getElementById('modalWhatsappBusinessId').value
    };
    
    localStorage.setItem('whatsapp_credentials', JSON.stringify(currentCredentials));
    $('#whatsappCredentialsModal').modal('hide');
    alert('WhatsApp credentials saved successfully!');
}

function saveEmailCredentials() {
    currentCredentials.email = {
        smtp_host: document.getElementById('modalSmtpHost').value,
        smtp_port: parseInt(document.getElementById('modalSmtpPort').value),
        smtp_username: document.getElementById('modalSmtpUsername').value,
        smtp_password: document.getElementById('modalSmtpPassword').value,
        smtp_encryption: document.getElementById('modalSmtpEncryption').value,
        from_address: document.getElementById('modalFromAddress').value,
        from_name: document.getElementById('modalFromName').value
    };
    
    localStorage.setItem('whatsapp_credentials', JSON.stringify(currentCredentials));
    $('#emailCredentialsModal').modal('hide');
    alert('Email credentials saved successfully!');
}
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('whatsapp.layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\GrowSass\application\resources\views/whatsapp/tickets/show.blade.php ENDPATH**/ ?>