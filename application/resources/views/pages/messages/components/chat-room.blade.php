<!-- Beautiful Chat Room Component -->
<div class="chat-room" data-thread-id="{{ $thread['id'] }}" data-thread-type="{{ $thread['type'] }}" data-name="{{ $thread['name'] }}" data-company="{{ $thread['company'] ?? '' }}" data-tags="{{ isset($thread['tags']) && is_array($thread['tags']) ? implode(',', $thread['tags']) : '' }}" onclick="document.getElementById('emoji_picker').style.display = 'none'; document.getElementById('templates_menu').style.display = 'none';">
    <!-- Chat Header -->
    <div class="chat-header">
        <div class="chat-contact-info">
            <div class="contact-avatar">
                @if($thread['type'] === 'user')
                    <div class="avatar-placeholder">
                        {{ strtoupper(substr($thread['name'], 0, 1)) }}
                    </div>
                @else
                    <div class="avatar-placeholder whatsapp">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                @endif
                <div class="status-indicator {{ $thread['type'] === 'user' ? 'online' : 'connected' }}"></div>
            </div>
            <div class="contact-details">
                <h3 class="contact-name">{{ $thread['name'] }}</h3>
                <div class="contact-meta">
                    @if($thread['type'] === 'user')
                        <span class="contact-role">{{ $thread['role'] ?? 'User' }} • {{ $thread['department'] ?? 'General' }}</span>
                        <span class="contact-email">{{ $thread['email'] ?? '' }}</span>
                    @else
                        <span class="contact-phone">{{ $thread['phone_number'] ?? 'WhatsApp Connection' }}</span>
                        <span class="contact-status">Connected</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="chat-actions">
            <button class="action-btn" title="Search in chat">
                <i class="fas fa-search"></i>
            </button>
            <div class="action-menu" style="position: relative;">
                <button class="action-btn" title="More options" id="chat_header_more_btn" onclick="window.toggleChatHeaderQuickView(event)">
                    <i class="fas fa-ellipsis-vertical"></i>
                </button>
                <div id="chat_header_quick_view" class="quick-view-dropdown" style="display:none; position:absolute; right:0; top:44px; width:320px; background:#ffffff; border:1px solid var(--border-color); box-shadow: var(--shadow-medium); border-radius:8px; z-index:9999; padding:12px; pointer-events:auto;">
                    <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:8px;">
                        <strong style="font-size:14px; color:var(--text-primary);">Quick View</strong>
                        <button type="button" id="chat_header_close_quick_view" class="action-btn" style="width:28px; height:28px;" onclick="window.closeQuickView(event)">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <label style="font-size:12px; color:var(--text-muted);">Name</label>
                        <input type="text" id="quick_name_input" class="form-control" value="{{ $thread['name'] }}" style="height:34px; font-size:13px;">

                        @if(($thread['type'] ?? '') === 'user')
                            <label style="font-size:12px; color:var(--text-muted);">Company</label>
                            <input type="text" id="quick_company_input" class="form-control" value="{{ $thread['company'] ?? '' }}" style="height:34px; font-size:13px;">
                        @endif

                        <label style="font-size:12px; color:var(--text-muted);">Tags (comma separated)</label>
                        <input type="text" id="quick_tags_input" class="form-control" value="{{ isset($thread['tags']) && is_array($thread['tags']) ? implode(', ', $thread['tags']) : '' }}" style="height:34px; font-size:13px;">

                        <div style="display:flex; gap:8px; justify-content:flex-end; margin-top:4px;">
                            <button type="button" id="quick_view_cancel_btn" class="btn btn-light btn-sm" onclick="window.closeQuickView(event)">Cancel</button>
                            <button type="button" id="quick_view_save_btn" class="btn btn-primary btn-sm" onclick="window.saveQuickViewChanges(event)">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chat Messages Container -->
    <div class="chat-messages" id="chat_messages">
        <!-- Messages will be loaded here via JavaScript -->
        <!-- Loading states will be managed by JavaScript -->
    </div>

    <script>
        (function(){
            var moreBtn = document.getElementById('chat_header_more_btn');
            var dropdown = document.getElementById('chat_header_quick_view');
            var closeBtn = document.getElementById('chat_header_close_quick_view');
            var cancelBtn = document.getElementById('quick_view_cancel_btn');
            var saveBtn = document.getElementById('quick_view_save_btn');

            function toggleDropdown(forceState){
                if(!dropdown) return;
                var current = dropdown.style.display === 'none' ? 'none' : 'block';
                var next = (typeof forceState === 'string') ? forceState : (current === 'none' ? 'block' : 'none');
                dropdown.style.display = next;
            }

            if(moreBtn){
                moreBtn.addEventListener('click', function(e){
                    e.stopPropagation();
                    toggleDropdown();
                });
            }
            if(closeBtn){ closeBtn.addEventListener('click', function(){ toggleDropdown('none'); }); }
            if(cancelBtn){ cancelBtn.addEventListener('click', function(){ toggleDropdown('none'); }); }

            document.addEventListener('click', function(e){
                if(!dropdown) return;
                var clickOnToggle = moreBtn && (e.target === moreBtn || moreBtn.contains(e.target));
                if(!dropdown.contains(e.target) && !clickOnToggle){
                    dropdown.style.display = 'none';
                }
            });

            if(saveBtn){
                saveBtn.addEventListener('click', function(){
                    var room = document.querySelector('.chat-room');
                    if(!room) return;
                    var threadId = room.getAttribute('data-thread-id');
                    var threadType = room.getAttribute('data-thread-type');
                    var nameInput = document.getElementById('quick_name_input');
                    var companyInput = document.getElementById('quick_company_input');
                    var tagsInput = document.getElementById('quick_tags_input');

                    var payload = {
                        thread_id: threadId,
                        thread_type: threadType,
                        name: nameInput ? nameInput.value : '',
                        company: companyInput ? companyInput.value : '',
                        tags: tagsInput ? tagsInput.value : ''
                    };

                    var csrfEl = document.querySelector('input[name="_token"]');
                    var csrf = csrfEl ? csrfEl.value : '';

                    fetch('/messages/thread/update', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf,
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify(payload)
                    }).then(function(r){ return r.json(); })
                    .then(function(res){
                        if(res && res.success){
                            // update header name
                            var nameEl = document.querySelector('.contact-name');
                            if(nameEl && res.data && res.data.name){ nameEl.textContent = res.data.name; }
                            toggleDropdown('none');
                        } else {
                            alert((res && res.message) ? res.message : 'Failed to save');
                        }
                    }).catch(function(){
                        alert('Failed to save');
                    });
                });
            }
        })();
    </script>

            <!-- Message Composer -->
        <div class="message-composer">
            <!-- Channel Selector -->
            <div class="channel-selector">
                <label for="channel_dropdown">Channel:</label>
                <select id="channel_dropdown" class="channel-dropdown">
                    <option value="internal" {{ ($thread['channel'] ?? 'internal') === 'internal' ? 'selected' : '' }}>Internal</option>
                    <option value="whatsapp" {{ ($thread['channel'] ?? 'internal') === 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                    <option value="email" {{ ($thread['channel'] ?? 'internal') === 'email' ? 'selected' : '' }}>Email</option>
                </select>
            </div>

            <!-- Message Input Area -->
            <div class="message-input-area">
                <div id="reply_preview" style="display:none; margin: 0 0 8px 0; padding:8px 12px; border-left:3px solid #3b82f6; background:#f1f5f9; border-radius:6px;">
                    <div style="display:flex; align-items:center; justify-content:space-between; gap:8px;">
                        <div style="font-size:12px; color:#334155;">
                            Replying to: <span id="reply_preview_text" style="font-weight:600; color:#0f172a;"></span>
                        </div>
                        <button type="button" id="cancel_reply_btn" style="border:none; background:transparent; color:#64748b; cursor:pointer;" onclick="if(window.NX&&NX.cancelReply){NX.cancelReply();}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
                <div class="input-wrapper">
                    <div class="input-actions">
                        <button class="input-action-btn" id="file_upload_btn" title="Attach file" aria-label="Attach file" onclick="window.openFileUpload()">
                            <i class="fas fa-paperclip"></i>
                        </button>
                        <button class="input-action-btn" id="emoji_btn" title="Add emoji" aria-label="Add emoji" onclick="const picker = document.getElementById('emoji_picker'); if(picker.style.display === 'none') { picker.style.display = 'block'; window.populateEmojiGrid(); } else { picker.style.display = 'none'; }">
                            <i class="far fa-smile"></i>
                        </button>
                        <div class="templates-dropdown">
                            <button class="input-action-btn" id="templates_btn" title="Templates" aria-label="Message templates" onclick="const menu = document.getElementById('templates_menu'); if(menu.style.display === 'none') { menu.style.display = 'block'; } else { menu.style.display = 'none'; }">
                                <i class="fas fa-list"></i>
                            </button>
                            <div class="templates-menu" id="templates_menu" style="display: none;" onclick="event.stopPropagation();">
                                <div class="template-item" data-template="Hello! How can I help you today?" onclick="const input = document.getElementById('message_input'); const menu = document.getElementById('templates_menu'); input.value = this.getAttribute('data-template'); input.dispatchEvent(new Event('input')); menu.style.display = 'none';">Hello! How can I help you today?</div>
                                <div class="template-item" data-template="Thank you for your message. I'll get back to you soon." onclick="const input = document.getElementById('message_input'); const menu = document.getElementById('templates_menu'); input.value = this.getAttribute('data-template'); input.dispatchEvent(new Event('input')); menu.style.display = 'none';">Thank you for your message. I'll get back to you soon.</div>
                                <div class="template-item" data-template="Is there anything else you need assistance with?" onclick="const input = document.getElementById('message_input'); const menu = document.getElementById('templates_menu'); input.value = this.getAttribute('data-template'); input.dispatchEvent(new Event('input')); menu.style.display = 'none';">Is there anything else you need assistance with?</div>
                            </div>
                        </div>
                    </div>
                    <div class="message-input-container">
                        <input type="hidden" id="reply_to_message_id" value="">
                        <textarea class="message-input" 
                                    id="message_input" 
                                    placeholder="Write a message..." 
                                    rows="1"
                                    maxlength="1000"
                                    aria-label="Message text"
                                    oninput="const text = this.value.trim(); const sendBtn = document.getElementById('send_message_btn'); const charCount = document.getElementById('char_count'); if(charCount) charCount.textContent = this.value.length; if(sendBtn) sendBtn.disabled = text.length === 0;"></textarea>
                        <div class="input-footer">
                            <div class="message-options">
                                <label class="option-checkbox">
                                    <input type="checkbox" id="sign_message" checked>
                                    <span class="checkmark"></span>
                                    <span class="option-label">Sign message</span>
                                </label>
                            </div>
                            <div class="char-counter">
                                <span id="char_count">0</span>/1000
                            </div>
                        </div>
                    </div>
                    <button class="send-button" id="send_message_btn" disabled aria-label="Send message" onclick="event.stopPropagation(); event.preventDefault(); window.sendMessage();">
                        <i class="fas fa-paper-plane"></i>
                    </button>


                </div>
            </div>

                <!-- Emoji Picker will be dynamically generated by JavaScript -->
    </div>
</div>

<!-- Modern CSS for Chat Room -->
<style>
/* ===========================================
   MODERN CHAT ROOM DESIGN - 2024
   =========================================== */

/* CSS Variables for Chat Room */
:root {
    /* Primary Colors - Modern Blue Palette */
    --primary-color: #3b82f6;
    --primary-light: #dbeafe;
    --primary-dark: #1e40af;
    --primary-gradient: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
    
    /* Secondary Colors */
    --secondary-color: #64748b;
    --secondary-light: #f1f5f9;
    --secondary-dark: #334155;
    
    /* Status Colors - Modern Palette */
    --success-color: #10b981;
    --success-light: #d1fae5;
    --warning-color: #f59e0b;
    --warning-light: #fef3c7;
    --danger-color: #ef4444;
    --danger-light: #fee2e2;
    
    /* Neutral Colors - Modern Grays */
    --white: #ffffff;
    --gray-50: #f8fafc;
    --gray-100: #f1f5f9;
    --gray-200: #e2e8f0;
    --gray-300: #cbd5e1;
    --gray-400: #94a3b8;
    --gray-500: #64748b;
    --gray-600: #475569;
    --gray-700: #334155;
    --gray-800: #1e293b;
    --gray-900: #0f172a;
    
    /* Text Colors */
    --text-primary: #0f172a;
    --text-secondary: #475569;
    --text-muted: #94a3b8;
    --text-inverse: #ffffff;
    
    /* Background Colors */
    --bg-primary: #ffffff;
    --bg-secondary: #f8fafc;
    --bg-tertiary: #f1f5f9;
    --bg-overlay: rgba(15, 23, 42, 0.8);
    
    /* Border Colors */
    --border-light: #e2e8f0;
    --border-medium: #cbd5e1;
    --border-dark: #94a3b8;
    
    /* Shadows - Modern Elevation System */
    --shadow-xs: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-sm: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    --shadow-2xl: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    
    /* Border Radius - Modern Scale */
    --radius-xs: 4px;
    --radius-sm: 6px;
    --radius-md: 8px;
    --radius-lg: 12px;
    --radius-xl: 16px;
    --radius-2xl: 24px;
    --radius-full: 9999px;
    
    /* Spacing - Modern Scale */
    --space-1: 0.25rem;
    --space-2: 0.5rem;
    --space-3: 0.75rem;
    --space-4: 1rem;
    --space-5: 1.25rem;
    --space-6: 1.5rem;
    --space-8: 2rem;
    --space-10: 2.5rem;
    --space-12: 3rem;
    --space-16: 4rem;
    --space-20: 5rem;
    --space-24: 6rem;
    
    /* Typography */
    --font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    --font-size-xs: 0.75rem;
    --font-size-sm: 0.875rem;
    --font-size-base: 1rem;
    --font-size-lg: 1.125rem;
    --font-size-xl: 1.25rem;
    --font-size-2xl: 1.5rem;
    --font-size-3xl: 1.875rem;
    --font-size-4xl: 2.25rem;
    
    /* Transitions - Modern Easing */
    --transition-fast: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-normal: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    
    /* Z-Index Scale */
    --z-dropdown: 1000;
    --z-sticky: 1020;
    --z-fixed: 1030;
    --z-modal-backdrop: 1040;
    --z-modal: 1050;
    --z-popover: 1060;
    --z-tooltip: 1070;
}

.chat-room {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: var(--bg-primary);
    position: relative;
}

/* ===========================================
   CHAT HEADER - MODERN DESIGN
   =========================================== */

.chat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-5) var(--space-6);
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-light);
    box-shadow: var(--shadow-sm);
    position: sticky;
    top: 0;
    z-index: var(--z-sticky);
    backdrop-filter: blur(20px);
}

.chat-contact-info {
    display: flex;
    align-items: center;
    gap: var(--space-4);
}

.contact-avatar {
    position: relative;
}

.contact-avatar .avatar-placeholder {
    width: 56px;
    height: 56px;
    background: var(--primary-gradient);
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-inverse);
    font-size: 24px;
    font-weight: 700;
    box-shadow: var(--shadow-md);
    border: 3px solid var(--bg-primary);
}

.contact-avatar .avatar-placeholder.whatsapp {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
}

.contact-avatar .avatar-placeholder.whatsapp i {
    font-size: 26px;
}

.status-indicator {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 2px solid var(--white);
    box-shadow: var(--shadow-light);
}

.status-indicator.online {
    background: var(--success-color);
}

.status-indicator.connected {
    background: var(--success-color);
}

.contact-details {
    display: flex;
    flex-direction: column;
    gap: var(--space-1);
}

.contact-name {
    font-size: var(--font-size-xl);
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
    letter-spacing: -0.025em;
}

.contact-meta {
    display: flex;
    flex-direction: column;
    gap: var(--space-1);
}

.contact-role, .contact-phone {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    font-weight: 500;
}

.contact-email, .contact-status {
    font-size: var(--font-size-xs);
    color: var(--text-muted);
    font-weight: 500;
}

.chat-actions {
    display: flex;
    gap: var(--space-2);
}

.action-btn {
    width: 44px;
    height: 44px;
    background: var(--gray-100);
    border: none;
    border-radius: var(--radius-lg);
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition-normal);
    box-shadow: var(--shadow-xs);
}

.action-btn:hover {
    background: var(--primary-light);
    color: var(--primary-color);
    transform: translateY(-2px);
    box-shadow: var(--shadow-sm);
}

/* Chat Messages */
.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 24px;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    position: relative;
}

.messages-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    height: 200px;
    color: var(--text-muted);
}

.loading-spinner {
    font-size: 32px;
    margin-bottom: 16px;
    color: var(--primary-color);
}

.messages-loading p {
    margin: 0;
    font-size: 16px;
}

/* Message Groups */
.message-group {
    margin-bottom: 32px;
}

.message-date {
    text-align: center;
    margin-bottom: 24px;
    position: relative;
}

.message-date::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: var(--border-color);
    z-index: 1;
}

.message-date span {
    background: var(--white);
    padding: 8px 16px;
    border-radius: var(--radius-md);
    font-size: 12px;
    font-weight: 600;
    color: var(--text-secondary);
    border: 1px solid var(--border-color);
    position: relative;
    z-index: 2;
}

/* Individual Messages */
.message-item {
    display: flex;
    margin-bottom: 16px;
    animation: messageSlideIn 0.3s ease-out;
}

.message-item.sent {
    justify-content: flex-end;
}

.message-item.received {
    justify-content: flex-start;
}

.message-bubble {
    max-width: 70%;
    padding: 12px 16px;
    border-radius: var(--radius-md);
    position: relative;
    word-wrap: break-word;
}

.message-item.sent .message-bubble {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: var(--white);
    border-bottom-right-radius: 4px;
}

.message-item.received .message-bubble {
    background: var(--white);
    color: var(--text-primary);
    border: 1px solid var(--border-color);
    border-bottom-left-radius: 4px;
    box-shadow: var(--shadow-light);
}

.message-content {
    font-size: 14px;
    line-height: 1.5;
    margin-bottom: 8px;
}

.message-meta {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: 11px;
    opacity: 0.8;
}

.message-time {
    font-weight: 500;
}

.message-status {
    display: flex;
    align-items: center;
    gap: 4px;
}

.status-icon {
    font-size: 10px;
}

.status-icon.sent { color: rgba(255,255,255,0.7); }
.status-icon.delivered { color: rgba(255,255,255,0.8); }
.status-icon.read { color: rgba(255,255,255,0.9); }

/* ===========================================
   MESSAGE COMPOSER - MODERN DESIGN
   =========================================== */

.message-composer {
    background: var(--bg-primary);
    border-top: 1px solid var(--border-light);
    padding: var(--space-5) var(--space-6);
    position: sticky;
    bottom: 0;
    backdrop-filter: blur(20px);
}

/* ===========================================
   CHANNEL SELECTOR - MODERN DESIGN
   =========================================== */

.channel-selector {
    display: flex;
    align-items: center;
    gap: var(--space-3);
    margin-bottom: var(--space-5);
    padding: var(--space-4);
    background: var(--gray-50);
    border-radius: var(--radius-xl);
    border: 1px solid var(--border-light);
    /* Force modern styling */
    background-color: #f8fafc !important;
    border-radius: 16px !important;
    border: 1px solid #e2e8f0 !important;
    padding: 1rem !important;
    margin-bottom: 1.25rem !important;
}

.channel-selector label {
    font-size: var(--font-size-sm);
    font-weight: 600;
    color: var(--text-secondary);
    margin: 0;
    letter-spacing: 0.025em;
}

.channel-dropdown {
    padding: var(--space-2) var(--space-3);
    border: 2px solid var(--border-light);
    border-radius: var(--radius-lg);
    background: var(--bg-primary);
    color: var(--text-primary);
    font-size: var(--font-size-sm);
    font-weight: 500;
    cursor: pointer;
    transition: var(--transition-normal);
    box-shadow: var(--shadow-xs);
    /* Force modern styling */
    padding: 0.5rem 0.75rem !important;
    border: 2px solid #e2e8f0 !important;
    border-radius: 12px !important;
    background-color: #ffffff !important;
    color: #0f172a !important;
    font-size: 0.875rem !important;
    font-weight: 500 !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
}

.channel-dropdown:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    transform: translateY(-1px);
}

/* ===========================================
   TEMPLATES DROPDOWN - MODERN DESIGN
   =========================================== */

.templates-dropdown {
    position: relative;
}

.templates-menu {
    position: absolute;
    bottom: 100%;
    left: 0;
    background: var(--bg-primary);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-xl);
    box-shadow: var(--shadow-xl);
    min-width: 280px;
    max-width: 380px;
    z-index: var(--z-dropdown);
    margin-bottom: var(--space-2);
    backdrop-filter: blur(20px);
    overflow: hidden;
}

.template-item {
    padding: var(--space-3) var(--space-4);
    border-bottom: 1px solid var(--border-light);
    cursor: pointer;
    font-size: var(--font-size-sm);
    line-height: 1.5;
    transition: var(--transition-normal);
    color: var(--text-primary);
    font-weight: 500;
}

.template-item:hover {
    background: var(--gray-50);
    color: var(--primary-color);
    transform: translateX(4px);
}

.template-item:last-child {
    border-bottom: none;
}

/* ===========================================
   MESSAGE INPUT AREA - MODERN DESIGN
   =========================================== */

.message-input-area {
    position: relative;
}

.input-wrapper {
    display: flex;
    align-items: flex-end;
    gap: var(--space-3);
    background: var(--bg-primary);
    border: 2px solid var(--border-light);
    border-radius: var(--radius-2xl);
    padding: var(--space-4);
    transition: var(--transition-normal);
    min-height: 72px;
    box-shadow: var(--shadow-sm);
    position: relative;
    overflow: hidden;
    /* Force modern styling */
    background-color: #ffffff !important;
    border-color: #e2e8f0 !important;
    border-radius: 24px !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px -1px rgba(0, 0, 0, 0.1) !important;
}

.input-wrapper::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(59, 130, 246, 0.02) 0%, rgba(59, 130, 246, 0.05) 100%);
    opacity: 0;
    transition: var(--transition-normal);
    pointer-events: none;
}

.input-wrapper:focus-within {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1), var(--shadow-md);
    transform: translateY(-2px);
    /* Force focus styling */
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1) !important;
    transform: translateY(-2px) !important;
}

.input-wrapper:focus-within::before {
    opacity: 1;
}

.input-actions {
    display: flex;
    gap: var(--space-2);
    flex-shrink: 0;
    align-items: center;
}

.input-action-btn {
    width: 44px;
    height: 44px;
    background: var(--gray-100);
    border: none;
    border-radius: var(--radius-lg);
    color: var(--text-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition-normal);
    font-size: 18px;
    box-shadow: var(--shadow-xs);
    position: relative;
    overflow: hidden;
    /* Force modern styling */
    width: 44px !important;
    height: 44px !important;
    background-color: #f1f5f9 !important;
    border-radius: 12px !important;
    color: #94a3b8 !important;
    font-size: 18px !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
}

.input-action-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--primary-gradient);
    opacity: 0;
    transition: var(--transition-normal);
}

.input-action-btn:hover {
    background: var(--primary-light);
    color: var(--primary-color);
    transform: translateY(-2px) scale(1.05);
    box-shadow: var(--shadow-md);
}

.input-action-btn:hover::before {
    opacity: 0.1;
}

.input-action-btn:active {
    transform: translateY(0) scale(0.95);
}

.message-input-container {
    flex: 1;
    min-width: 0;
    display: flex;
    flex-direction: column;
    gap: var(--space-2);
}

.message-input {
    width: 100%;
    border: none;
    background: transparent;
    resize: none;
    font-size: var(--font-size-base);
    line-height: 1.6;
    color: var(--text-primary);
    outline: none;
    font-family: var(--font-family);
    min-height: 24px;
    max-height: 120px;
    overflow-y: auto;
    padding: 0;
    font-weight: 500;
    /* Force modern styling */
    font-size: 1rem !important;
    line-height: 1.6 !important;
    color: #0f172a !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
    font-weight: 500 !important;
}

.message-input::placeholder {
    color: var(--text-muted);
    font-weight: 400;
    font-style: normal;
}

/* ===========================================
   INPUT FOOTER - MODERN DESIGN
   =========================================== */

.input-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-size: var(--font-size-xs);
    margin-top: var(--space-2);
}

.message-options {
    display: flex;
    align-items: center;
}

.option-checkbox {
    display: flex;
    align-items: center;
    gap: var(--space-2);
    cursor: pointer;
    color: var(--text-secondary);
    font-weight: 500;
}

.option-checkbox input {
    display: none;
}

.checkmark {
    width: 18px;
    height: 18px;
    border: 2px solid var(--border-light);
    border-radius: var(--radius-sm);
    position: relative;
    transition: var(--transition-normal);
    background: var(--bg-primary);
}

.option-checkbox input:checked + .checkmark {
    background: var(--primary-color);
    border-color: var(--primary-color);
    box-shadow: var(--shadow-sm);
}

.option-checkbox input:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: var(--text-inverse);
    font-size: 12px;
    font-weight: 700;
}

.char-counter {
    color: var(--text-muted);
    font-weight: 500;
    font-size: var(--font-size-xs);
}

/* ===========================================
   SEND BUTTON - MODERN DESIGN
   =========================================== */

.send-button {
    width: 52px;
    height: 52px;
    background: var(--primary-gradient);
    border: none;
    border-radius: var(--radius-full);
    color: var(--text-inverse);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition-normal);
    font-size: 20px;
    flex-shrink: 0;
    box-shadow: var(--shadow-md);
    position: relative;
    overflow: hidden;
    /* Force modern styling */
    width: 52px !important;
    height: 52px !important;
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%) !important;
    border-radius: 9999px !important;
    color: #ffffff !important;
    font-size: 20px !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1) !important;
}

.send-button::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.2) 0%, rgba(255,255,255,0.1) 100%);
    opacity: 0;
    transition: var(--transition-normal);
}

.send-button:hover:not(:disabled) {
    transform: translateY(-3px) scale(1.05);
    box-shadow: var(--shadow-lg);
}

.send-button:hover:not(:disabled)::before {
    opacity: 1;
}

.send-button:active:not(:disabled) {
    transform: translateY(-1px) scale(0.95);
}

.send-button:disabled {
    background: var(--gray-300);
    cursor: not-allowed;
    opacity: 0.6;
    transform: none;
    box-shadow: var(--shadow-xs);
}

.send-button:disabled::before {
    display: none;
}

/* ===========================================
   EMOJI PICKER - MODERN DESIGN
   =========================================== */

.emoji-picker {
    position: absolute;
    bottom: 100%;
    left: 0;
    right: 0;
    background: var(--bg-primary);
    border: 2px solid var(--border-light);
    border-radius: var(--radius-2xl);
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.15),
        0 8px 16px rgba(0, 0, 0, 0.1),
        0 0 0 1px rgba(255, 255, 255, 0.8);
    margin-bottom: var(--space-4);
    z-index: var(--z-dropdown);
    backdrop-filter: blur(20px);
    overflow: hidden;
    min-height: 320px;
    max-height: 380px;
    animation: emojiPickerSlideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes emojiPickerSlideUp {
    from {
        opacity: 0;
        transform: translateY(20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.emoji-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-5);
    border-bottom: 2px solid var(--border-light);
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
}

.emoji-header span {
    font-size: var(--font-size-lg);
    font-weight: 700;
    color: var(--text-primary);
    letter-spacing: -0.025em;
}

.close-emoji {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--gray-100) 0%, var(--gray-200) 100%);
    border: 1px solid var(--border-light);
    border-radius: var(--radius-xl);
    color: var(--text-secondary);
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.close-emoji:hover {
    background: linear-gradient(135deg, var(--danger-light) 0%, #fecaca 100%);
    color: var(--danger-color);
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
}

.emoji-grid {
    display: grid;
    grid-template-columns: repeat(10, 1fr);
    gap: var(--space-2);
    padding: var(--space-4);
    max-height: 260px;
    overflow-y: auto;
}

.emoji {
    width: 42px;
    height: 42px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    cursor: pointer;
    border-radius: var(--radius-xl);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    background: transparent;
    border: 2px solid transparent;
}

.emoji::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--primary-gradient);
    opacity: 0;
    transition: var(--transition-normal);
}

.emoji:hover {
    transform: scale(1.15) translateY(-2px);
    border-color: var(--primary-color);
    box-shadow: 
        0 8px 20px rgba(59, 130, 246, 0.3),
        0 4px 8px rgba(0, 0, 0, 0.1);
}

.emoji:hover::before {
    opacity: 1;
}

.emoji:active {
    transform: scale(1.05) translateY(-1px);
    box-shadow: 
        0 4px 12px rgba(59, 130, 246, 0.4),
        0 2px 4px rgba(0, 0, 0, 0.1);
}

.emoji span {
    position: relative;
    z-index: 1;
    transition: transform 0.3s ease;
}

.emoji:hover span {
    transform: scale(1.1);
}

/* ===========================================
   EMOJI TABS - MODERN DESIGN
   =========================================== */

.emoji-tabs {
    display: flex;
    flex-wrap: wrap;
    gap: var(--space-1);
    padding: var(--space-3) var(--space-4);
    border-bottom: 1px solid var(--border-light);
    background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
    overflow-x: auto;
    scrollbar-width: none;
    -ms-overflow-style: none;
}

.emoji-tabs::-webkit-scrollbar {
    display: none;
}

.emoji-tab {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: var(--space-1);
    padding: var(--space-2) var(--space-3);
    background: transparent;
    border: 1px solid transparent;
    border-radius: var(--radius-lg);
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    min-width: 60px;
    flex-shrink: 0;
}

.emoji-tab:hover {
    background: var(--primary-light);
    border-color: var(--primary-color);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);
}

.emoji-tab.active {
    background: var(--primary-gradient);
    border-color: var(--primary-color);
    color: var(--text-inverse);
    box-shadow: 0 4px 16px rgba(59, 130, 246, 0.3);
}

.emoji-tab .tab-icon {
    font-size: 20px;
    line-height: 1;
}

.emoji-tab .tab-label {
    font-size: 10px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    line-height: 1;
}

.emoji-tab.active .tab-label {
    color: var(--text-inverse);
}

/* ===========================================
   EMOJI CONTENT AREA
   =========================================== */

.emoji-content {
    flex: 1;
    overflow: hidden;
}

.emoji-grid {
    display: grid;
    grid-template-columns: repeat(10, 1fr);
    gap: var(--space-2);
    padding: var(--space-4);
    max-height: 220px;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--primary-color) var(--gray-200);
}

.emoji-grid::-webkit-scrollbar {
    width: 6px;
}

.emoji-grid::-webkit-scrollbar-track {
    background: var(--gray-200);
    border-radius: 3px;
}

.emoji-grid::-webkit-scrollbar-thumb {
    background: var(--primary-color);
    border-radius: 3px;
}

.emoji-grid::-webkit-scrollbar-thumb:hover {
    background: var(--primary-dark);
}

/* Animations */
@keyframes messageSlideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ===========================================
   RESPONSIVE DESIGN - CHAT ROOM
   =========================================== */

/* Tablet (768px - 1023px) */
@media (max-width: 1023px) and (min-width: 768px) {
    .chat-header {
        padding: var(--space-4) var(--space-5);
    }
    
    .contact-avatar .avatar-placeholder {
        width: 52px;
        height: 52px;
        font-size: 22px;
    }
    
    .contact-name {
        font-size: var(--font-size-lg);
    }
    
    .message-composer {
        padding: var(--space-4) var(--space-5);
    }
    
    .input-wrapper {
        min-height: 68px;
    }
}

/* Mobile Landscape (480px - 767px) */
@media (max-width: 767px) and (min-width: 480px) {
    .chat-header {
        padding: var(--space-3) var(--space-4);
    }
    
    .contact-avatar .avatar-placeholder {
        width: 48px;
        height: 48px;
        font-size: 20px;
    }
    
    .contact-name {
        font-size: var(--font-size-base);
    }
    
    .contact-role, .contact-phone {
        font-size: var(--font-size-xs);
    }
    
    .action-btn {
        width: 40px;
        height: 40px;
    }
    
    .message-composer {
        padding: var(--space-3) var(--space-4);
    }
    
    .channel-selector {
        padding: var(--space-3);
        margin-bottom: var(--space-4);
    }
    
    .input-wrapper {
        padding: var(--space-3);
        gap: var(--space-2);
        min-height: 64px;
    }
    
    .input-action-btn {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }
    
    .send-button {
        width: 48px;
        height: 48px;
        font-size: 18px;
    }
    
    .message-input {
        font-size: var(--font-size-base); /* Prevents zoom on iOS */
    }
    
    .emoji-grid {
        grid-template-columns: repeat(6, 1fr);
        gap: var(--space-1);
    }
    
    .emoji {
        width: 36px;
        height: 36px;
        font-size: 20px;
    }
}

/* Mobile Portrait (320px - 479px) */
@media (max-width: 479px) {
    .chat-header {
        padding: var(--space-3);
    }
    
    .chat-contact-info {
        gap: var(--space-3);
    }
    
    .contact-avatar .avatar-placeholder {
        width: 44px;
        height: 44px;
        font-size: 18px;
    }
    
    .contact-name {
        font-size: var(--font-size-sm);
    }
    
    .contact-role, .contact-phone {
        font-size: var(--font-size-xs);
    }
    
    .contact-email, .contact-status {
        font-size: 10px;
    }
    
    .chat-actions {
        gap: var(--space-1);
    }
    
    .action-btn {
        width: 36px;
        height: 36px;
    }
    
    .message-composer {
        padding: var(--space-3);
    }
    
    .channel-selector {
        padding: var(--space-2);
        margin-bottom: var(--space-3);
        flex-direction: column;
        align-items: flex-start;
        gap: var(--space-2);
    }
    
    .channel-selector label {
        font-size: var(--font-size-xs);
    }
    
    .channel-dropdown {
        padding: var(--space-2);
        font-size: var(--font-size-xs);
        width: 100%;
    }
    
    .input-wrapper {
        padding: var(--space-2);
        gap: var(--space-1);
        min-height: 56px;
    }
    
    .input-actions {
        gap: var(--space-1);
    }
    
    .input-action-btn {
        width: 36px;
        height: 36px;
        font-size: 14px;
    }
    
    .send-button {
        width: 44px;
        height: 44px;
        font-size: 16px;
    }
    
    .message-input {
        font-size: var(--font-size-base);
    }
    
    .input-footer {
        margin-top: var(--space-1);
        font-size: 10px;
    }
    
    .char-counter {
        font-size: 10px;
    }
    
    .checkmark {
        width: 16px;
        height: 16px;
    }
    
    .option-checkbox input:checked + .checkmark::after {
        font-size: 10px;
    }
    
    .emoji-grid {
        grid-template-columns: repeat(5, 1fr);
        gap: var(--space-1);
        padding: var(--space-3);
    }
    
    .emoji {
        width: 32px;
        height: 32px;
        font-size: 18px;
    }
    
    .templates-menu {
        min-width: 240px;
        max-width: 300px;
    }
    
    .template-item {
        padding: var(--space-2) var(--space-3);
        font-size: var(--font-size-xs);
    }
}

/* Scrollbar Styling */
.chat-messages::-webkit-scrollbar {
    width: 6px;
}

.chat-messages::-webkit-scrollbar-track {
    background: transparent;
}

.chat-messages::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 3px;
}

.chat-messages::-webkit-scrollbar-thumb:hover {
    background: var(--text-muted);
}

/* File Attachments */
.file-attachments {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.file-attachment {
    display: flex;
    align-items: center;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: var(--radius-sm);
    padding: 12px;
    cursor: pointer;
    transition: var(--transition);
    max-width: 300px;
}

.message-item.received .file-attachment {
    background: var(--light-bg);
    border-color: var(--border-color);
}

.file-attachment:hover {
    background: rgba(255, 255, 255, 0.15);
    transform: translateY(-1px);
}

.message-item.received .file-attachment:hover {
    background: var(--primary-light);
}

.file-preview {
    margin-bottom: 8px;
}

.file-thumbnail {
    max-width: 200px;
    max-height: 150px;
    border-radius: var(--radius-sm);
    cursor: pointer;
    transition: var(--transition);
}

.file-thumbnail:hover {
    transform: scale(1.05);
}

.file-info {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
}

.file-icon {
    font-size: 24px;
    color: var(--primary-color);
    flex-shrink: 0;
}

.message-item.received .file-icon {
    color: var(--text-secondary);
}

.file-details {
    flex: 1;
    min-width: 0;
}

.file-name {
    font-size: 14px;
    font-weight: 500;
    color: var(--white);
    margin-bottom: 2px;
    word-break: break-word;
}

.message-item.received .file-name {
    color: var(--text-primary);
}

.file-size {
    font-size: 12px;
    color: rgba(255, 255, 255, 0.7);
}

.message-item.received .file-size {
    color: var(--text-muted);
}

.file-download {
    font-size: 16px;
    color: rgba(255, 255, 255, 0.7);
    cursor: pointer;
    transition: var(--transition);
    flex-shrink: 0;
}

.message-item.received .file-download {
    color: var(--text-muted);
}

.file-download:hover {
    color: var(--white);
    transform: scale(1.1);
}

.message-item.received .file-download:hover {
    color: var(--primary-color);
}

/* Active User State */
.user-item.active {
    background: var(--primary-light);
    border-left: 3px solid var(--primary-color);
}

.user-item.active .user-name {
    color: var(--primary-color);
    font-weight: 600;
}

/* Message Date Separators */
.message-date {
    text-align: center;
    margin: 24px 0 16px 0;
    position: relative;
}

.message-date::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: var(--border-color);
    z-index: 1;
}

.date-label {
    background: var(--white);
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 500;
    color: var(--text-muted);
    border: 1px solid var(--border-color);
    position: relative;
    z-index: 2;
}

/* Loading and Empty States */
.messages-loading {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    text-align: center;
    color: var(--text-muted);
}

.messages-loading p {
    margin: 0;
    font-size: 14px;
    font-weight: 500;
}

.messages-empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    text-align: center;
    color: var(--text-muted);
}

.messages-error-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 40px 20px;
    text-align: center;
    color: var(--text-muted);
}

/* Emoji Picker Styles */
.emoji-grid {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 4px;
    padding: 12px;
    max-height: 200px;
    overflow-y: auto;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.emoji {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    font-size: 18px;
    border-radius: 4px;
    cursor: pointer;
    transition: background-color 0.2s ease;
    user-select: none;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
}

.emoji:hover {
    background-color: #f0f0f0;
}

.emoji:active {
    background-color: #e0e0e0;
}

/* Ensure proper emoji font rendering */
.emoji, .emoji-grid {
    font-family: "Segoe UI Emoji", "Apple Color Emoji", "Noto Color Emoji", "Android Emoji", "EmojiSymbols", "EmojiOne Mozilla", "Twemoji Mozilla", "Segoe UI Symbol", Arial, sans-serif;
    font-feature-settings: "liga" 1, "dlig" 1;
    text-rendering: optimizeLegibility;
}

/* Emoji Picker Container */
.emoji-picker {
    position: absolute;
    bottom: 100%;
    left: 0;
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    z-index: 1000;
    min-width: 300px;
    max-width: 400px;
}

.emoji-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 16px;
    border-bottom: 1px solid #e0e0e0;
    background: #f8f9fa;
    border-radius: 8px 8px 0 0;
}

.emoji-header span {
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.close-emoji {
    background: none;
    border: none;
    color: #666;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.close-emoji:hover {
    background-color: #e0e0e0;
}

/* ===========================================
   COMPLETELY REDESIGNED MODERN INPUT - 2024
   =========================================== */

/* Modern Input Container - Beautiful Design */
.message-composer .input-wrapper {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
    border: 2px solid #e2e8f0 !important;
    border-radius: 28px !important;
    box-shadow: 
        0 4px 20px rgba(0, 0, 0, 0.08),
        0 1px 3px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.8) !important;
    padding: 16px 20px !important;
    min-height: 80px !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    position: relative !important;
    overflow: hidden !important;
    backdrop-filter: blur(20px) !important;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

/* Beautiful Focus State */
.message-composer .input-wrapper:focus-within {
    border-color: #3b82f6 !important;
    box-shadow: 
        0 0 0 4px rgba(59, 130, 246, 0.15),
        0 8px 32px rgba(59, 130, 246, 0.2),
        0 4px 20px rgba(0, 0, 0, 0.12),
        inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
    transform: translateY(-3px) scale(1.02) !important;
    background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%) !important;
}

/* Force modern input wrapper focus state */
.message-composer .input-wrapper:focus-within {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1), 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1) !important;
    transform: translateY(-2px) !important;
}

/* Beautiful Modern Action Buttons */
.message-composer .input-action-btn {
    width: 48px !important;
    height: 48px !important;
    background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
    border: 1px solid rgba(255, 255, 255, 0.8) !important;
    border-radius: 16px !important;
    color: #64748b !important;
    font-size: 20px !important;
    box-shadow: 
        0 2px 8px rgba(0, 0, 0, 0.06),
        0 1px 3px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    position: relative !important;
    overflow: hidden !important;
}

/* Beautiful Action Button Hover Effects */
.message-composer .input-action-btn:hover {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%) !important;
    color: #3b82f6 !important;
    transform: translateY(-2px) scale(1.05) !important;
    box-shadow: 
        0 4px 16px rgba(59, 130, 246, 0.2),
        0 2px 8px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.9) !important;
    border-color: rgba(59, 130, 246, 0.3) !important;
}

/* Action Button Active State */
.message-composer .input-action-btn:active {
    transform: translateY(0) scale(0.98) !important;
    box-shadow: 
        0 2px 8px rgba(59, 130, 246, 0.3),
        0 1px 3px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.8) !important;
}

/* Force modern action button hover */
.message-composer .input-action-btn:hover {
    background-color: #dbeafe !important;
    color: #3b82f6 !important;
    transform: translateY(-2px) scale(1.05) !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -2px rgba(0, 0, 0, 0.1) !important;
}

/* Stunning Modern Send Button */
.message-composer .send-button {
    width: 56px !important;
    height: 56px !important;
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 50%, #1d4ed8 100%) !important;
    border: 2px solid rgba(255, 255, 255, 0.2) !important;
    border-radius: 20px !important;
    color: #ffffff !important;
    font-size: 22px !important;
    box-shadow: 
        0 6px 20px rgba(59, 130, 246, 0.4),
        0 2px 8px rgba(0, 0, 0, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.3) !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1) !important;
    flex-shrink: 0 !important;
    position: relative !important;
    overflow: hidden !important;
}

/* Send Button Shimmer Effect */
.message-composer .send-button::before {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: -100% !important;
    width: 100% !important;
    height: 100% !important;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent) !important;
    transition: left 0.6s ease !important;
}

.message-composer .send-button:hover::before {
    left: 100% !important;
}

/* Beautiful Send Button Hover Effects */
.message-composer .send-button:hover:not(:disabled) {
    background: linear-gradient(135deg, #2563eb 0%, #1e40af 50%, #1e3a8a 100%) !important;
    transform: translateY(-4px) scale(1.08) !important;
    box-shadow: 
        0 12px 32px rgba(59, 130, 246, 0.5),
        0 4px 16px rgba(0, 0, 0, 0.15),
        inset 0 1px 0 rgba(255, 255, 255, 0.4) !important;
    border-color: rgba(255, 255, 255, 0.3) !important;
}

/* Force modern send button disabled */
.message-composer .send-button:disabled {
    background: #cbd5e1 !important;
    cursor: not-allowed !important;
    opacity: 0.6 !important;
    transform: none !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
}

/* Beautiful Modern Message Input */
.message-composer .message-input {
    font-size: 16px !important;
    line-height: 1.5 !important;
    color: #1e293b !important;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
    font-weight: 500 !important;
    border: none !important;
    background: transparent !important;
    outline: none !important;
    resize: none !important;
    min-height: 28px !important;
    max-height: 120px !important;
    overflow-y: auto !important;
    padding: 8px 0 !important;
    width: 100% !important;
    letter-spacing: 0.01em !important;
    transition: all 0.3s ease !important;
}

/* Beautiful Input Focus State */
.message-composer .message-input:focus {
    color: #0f172a !important;
    font-weight: 600 !important;
}

/* Beautiful Modern Placeholder */
.message-composer .message-input::placeholder {
    color: #94a3b8 !important;
    font-weight: 500 !important;
    font-style: normal !important;
    letter-spacing: 0.01em !important;
    transition: all 0.3s ease !important;
}

/* Placeholder Focus State */
.message-composer .message-input:focus::placeholder {
    color: #cbd5e1 !important;
    transform: translateY(-1px) !important;
}

/* Beautiful Modern Channel Selector */
.message-composer .channel-selector {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%) !important;
    border-radius: 20px !important;
    border: 1px solid #e2e8f0 !important;
    padding: 16px 20px !important;
    margin-bottom: 20px !important;
    display: flex !important;
    align-items: center !important;
    gap: 12px !important;
    box-shadow: 
        0 2px 8px rgba(0, 0, 0, 0.04),
        0 1px 3px rgba(0, 0, 0, 0.08) !important;
    transition: all 0.3s ease !important;
}

/* Channel Selector Hover Effect */
.message-composer .channel-selector:hover {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%) !important;
    border-color: #bae6fd !important;
    box-shadow: 
        0 4px 12px rgba(59, 130, 246, 0.1),
        0 2px 8px rgba(0, 0, 0, 0.06) !important;
    transform: translateY(-1px) !important;
}

/* Beautiful Modern Channel Dropdown */
.message-composer .channel-dropdown {
    padding: 10px 16px !important;
    border: 2px solid #e2e8f0 !important;
    border-radius: 14px !important;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
    color: #1e293b !important;
    font-size: 14px !important;
    font-weight: 600 !important;
    box-shadow: 
        0 2px 8px rgba(0, 0, 0, 0.06),
        0 1px 3px rgba(0, 0, 0, 0.1) !important;
    cursor: pointer !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    letter-spacing: 0.01em !important;
}

/* Channel Dropdown Hover Effect */
.message-composer .channel-dropdown:hover {
    border-color: #3b82f6 !important;
    background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%) !important;
    box-shadow: 
        0 4px 12px rgba(59, 130, 246, 0.15),
        0 2px 8px rgba(0, 0, 0, 0.08) !important;
    transform: translateY(-1px) !important;
}

/* Beautiful Channel Dropdown Focus State */
.message-composer .channel-dropdown:focus {
    outline: none !important;
    border-color: #3b82f6 !important;
    background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%) !important;
    box-shadow: 
        0 0 0 4px rgba(59, 130, 246, 0.15),
        0 4px 16px rgba(59, 130, 246, 0.2),
        0 2px 8px rgba(0, 0, 0, 0.1) !important;
    transform: translateY(-2px) !important;
}

/* Beautiful Modern Message Composer */
.message-composer {
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
    border-top: 1px solid #e2e8f0 !important;
    padding: 24px 32px !important;
    position: sticky !important;
    bottom: 0 !important;
    backdrop-filter: blur(20px) !important;
    box-shadow: 
        0 -4px 20px rgba(0, 0, 0, 0.05),
        0 -1px 3px rgba(0, 0, 0, 0.1) !important;
}

/* Beautiful Modern Input Footer */
.message-composer .input-footer {
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    font-size: 12px !important;
    margin-top: 8px !important;
    padding: 0 4px !important;
}

/* Beautiful Modern Character Counter */
.message-composer .char-counter {
    color: #94a3b8 !important;
    font-weight: 600 !important;
    font-size: 11px !important;
    letter-spacing: 0.05em !important;
    transition: all 0.3s ease !important;
}

/* Character Counter Warning State */
.message-composer .char-counter.warning {
    color: #f59e0b !important;
    font-weight: 700 !important;
}

/* Character Counter Error State */
.message-composer .char-counter.error {
    color: #ef4444 !important;
    font-weight: 700 !important;
}

/* Beautiful Modern Checkbox */
.message-composer .checkmark {
    width: 20px !important;
    height: 20px !important;
    border: 2px solid #e2e8f0 !important;
    border-radius: 8px !important;
    background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%) !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    box-shadow: 
        0 2px 4px rgba(0, 0, 0, 0.04),
        0 1px 2px rgba(0, 0, 0, 0.08) !important;
    position: relative !important;
}

.message-composer .option-checkbox input:checked + .checkmark {
    background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%) !important;
    border-color: #3b82f6 !important;
    box-shadow: 
        0 4px 12px rgba(59, 130, 246, 0.3),
        0 2px 8px rgba(0, 0, 0, 0.1) !important;
    transform: scale(1.05) !important;
}

.message-composer .option-checkbox input:checked + .checkmark::after {
    content: '✓' !important;
    position: absolute !important;
    top: 50% !important;
    left: 50% !important;
    transform: translate(-50%, -50%) !important;
    color: #ffffff !important;
    font-size: 14px !important;
    font-weight: 700 !important;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2) !important;
}
</style>
