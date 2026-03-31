<div class="messages-right-panel">
    <!-- Chat Header -->
    <div class="chat-header">
            <div class="contact-info">
            <div class="contact-avatar">
                <div class="avatar-placeholder">U</div>
            </div>
            <div class="contact-details">
                <h5>Select a user or WhatsApp connection to start chatting</h5>
                <div class="contact-id">No selection</div>
                <div class="assigned-to">Choose from the left panel</div>
            </div>
            </div>
            <div class="contact-actions">
                <div class="tags-input">
                <input type="text" class="form-control" placeholder="Tags..." id="contact_tags">
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm" id="reopen_ticket_btn" style="display: none;">
                REABRIR
            </button>
        </div>
    </div>

    <!-- Chat Messages Container -->
    <div class="chat-messages" id="chat_messages">
        <!-- Welcome Placeholder -->
        <div class="chat-placeholder">
            <i class="fas fa-comments"></i>
            <h5>Welcome to Messages</h5>
            <p>Select a user from the left panel to start a conversation or choose a WhatsApp connection to manage messages.</p>
        </div>
        
        <!-- Messages Feed (Hidden by default) -->
        <div class="messages-feed" id="messages_feed" style="display: none;">
            <!-- Messages will be loaded here via AJAX -->
        </div>
    </div>

    <!-- Message Composer (Hidden by default) -->
    <div class="message-composer" style="display: none;">
        <!-- Channel Toggle -->
        <div class="channel-toggle">
            <span class="toggle-label">Channel: Internal</span>
            <div class="btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-sm active" data-channel="internal">
                    <input type="radio" name="channel" value="internal" checked> Internal
                    </label>
                <label class="btn btn-sm" data-channel="whatsapp">
                    <input type="radio" name="channel" value="whatsapp"> WhatsApp
                    </label>
            </div>
        </div>

        <!-- Message Input Area -->
        <div class="message-input-area">
            <div class="input-group">
                <div class="input-group-prepend">
                    <button type="button" class="btn btn-outline-secondary" id="emoji_btn">
                        <i class="far fa-smile"></i>
                    </button>
                    <button type="button" class="btn btn-outline-secondary" id="file_upload_btn">
                        <i class="fas fa-paperclip"></i>
                    </button>
                </div>
                <textarea class="form-control" id="message_input" 
                          placeholder="Type your message here..." 
                          rows="3"></textarea>
                <div class="input-group-append">
                    <div class="input-group-text">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="sign_message" checked>
                            <label class="form-check-label small" for="sign_message">
                                Firmar
                            </label>
                        </div>
                    </div>
                    <button type="button" class="btn btn-primary send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Emoji Picker (Hidden by default) -->
        <div class="emoji-picker" id="emoji_picker">
            <div class="emoji-grid">
                <span class="emoji">😀</span>
                <span class="emoji">😃</span>
                <span class="emoji">😄</span>
                <span class="emoji">😁</span>
                <span class="emoji">😆</span>
                <span class="emoji">😅</span>
                <span class="emoji">😂</span>
                <span class="emoji">🤣</span>
                <span class="emoji">😊</span>
                <span class="emoji">😇</span>
                <span class="emoji">🙂</span>
                <span class="emoji">🙃</span>
                <span class="emoji">😉</span>
                <span class="emoji">😌</span>
                <span class="emoji">😍</span>
                <span class="emoji">🥰</span>
                <span class="emoji">😘</span>
                <span class="emoji">😗</span>
                <span class="emoji">😙</span>
                <span class="emoji">😚</span>
                <span class="emoji">😋</span>
                <span class="emoji">😛</span>
                <span class="emoji">😝</span>
                <span class="emoji">😜</span>
                <span class="emoji">🤪</span>
                <span class="emoji">🤨</span>
                <span class="emoji">🧐</span>
                <span class="emoji">🤓</span>
                <span class="emoji">😎</span>
                <span class="emoji">🤩</span>
                <span class="emoji">🥳</span>
                <span class="emoji">😏</span>
                <span class="emoji">😒</span>
                <span class="emoji">😞</span>
                <span class="emoji">😔</span>
                <span class="emoji">😟</span>
                <span class="emoji">😕</span>
                <span class="emoji">🙁</span>
                <span class="emoji">☹️</span>
                <span class="emoji">😣</span>
                <span class="emoji">😖</span>
                <span class="emoji">😫</span>
                <span class="emoji">😩</span>
                <span class="emoji">🥺</span>
                <span class="emoji">😢</span>
                <span class="emoji">😭</span>
                <span class="emoji">😤</span>
                <span class="emoji">😠</span>
                <span class="emoji">😡</span>
                <span class="emoji">🤬</span>
                <span class="emoji">🤯</span>
                <span class="emoji">😳</span>
                <span class="emoji">🥵</span>
                <span class="emoji">🥶</span>
                <span class="emoji">😱</span>
                <span class="emoji">😨</span>
                <span class="emoji">😰</span>
                <span class="emoji">😥</span>
                <span class="emoji">😓</span>
                <span class="emoji">🤗</span>
                <span class="emoji">🤔</span>
                <span class="emoji">🤭</span>
                <span class="emoji">🤫</span>
                <span class="emoji">🤥</span>
                <span class="emoji">😶</span>
                <span class="emoji">😐</span>
                <span class="emoji">😑</span>
                <span class="emoji">😯</span>
                <span class="emoji">😦</span>
                <span class="emoji">😧</span>
                <span class="emoji">😮</span>
                <span class="emoji">😲</span>
                <span class="emoji">🥱</span>
                <span class="emoji">😴</span>
                <span class="emoji">🤤</span>
                <span class="emoji">😪</span>
                <span class="emoji">😵</span>
                <span class="emoji">🤐</span>
                <span class="emoji">🥴</span>
                <span class="emoji">🤢</span>
                <span class="emoji">🤮</span>
                <span class="emoji">🤧</span>
                <span class="emoji">😷</span>
                <span class="emoji">🤒</span>
                <span class="emoji">🤕</span>
                <span class="emoji">🤑</span>
                <span class="emoji">🤠</span>
                <span class="emoji">💩</span>
                <span class="emoji">👻</span>
                <span class="emoji">👽</span>
                <span class="emoji">👾</span>
                <span class="emoji">🤖</span>
                <span class="emoji">😺</span>
                <span class="emoji">😸</span>
                <span class="emoji">😹</span>
                <span class="emoji">😻</span>
                <span class="emoji">😼</span>
                <span class="emoji">😽</span>
                <span class="emoji">🙀</span>
                <span class="emoji">😿</span>
                <span class="emoji">😾</span>
            </div>
        </div>
    </div>

    <!-- Hidden form for message polling -->
    <form id="message_meta_container" style="display: none;">
        <input type="hidden" name="message_target" value="team">
        <input type="hidden" name="channel" value="internal">
        <input type="hidden" name="connection_id" value="">
        <input type="hidden" name="timestamp_submit_button" value="0">
    <span data-type="form" id="messages_polling_trigger" data-form-id="message_meta_container"
          data-ajax-type="post" data-progress-bar="hidden" data-url="{{ url('/messages/feed') }}">
    </span>
    </form>
</div>

<style>
/* Chat header styles */
.chat-header {
    background: #fff;
    border-bottom: 1px solid #e9ecef;
    padding: 1.25rem;
    flex-shrink: 0;
}

.chat-header .contact-info {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.contact-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    flex-shrink: 0;
}

.contact-avatar .avatar-placeholder {
    font-size: 20px;
    font-weight: 600;
    color: #6c757d;
}

.contact-details {
    flex: 1;
    min-width: 0;
}

.chat-header .contact-info h5 {
    font-size: 1.125rem;
    font-weight: 600;
    color: #212529;
    margin: 0 0 0.375rem 0;
    line-height: 1.3;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.chat-header .contact-info .contact-id {
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.chat-header .contact-info .assigned-to {
    color: #6c757d;
    font-size: 0.8125rem;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.chat-header .contact-actions {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    margin-top: 1rem;
}

.chat-header .tags-input {
    flex: 1;
    margin-right: 1rem;
}

.chat-header .tags-input .form-control {
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
    border-radius: 0.5rem;
    border: 1px solid #ced4da;
    transition: border-color 0.2s ease;
}

.chat-header .tags-input .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.chat-header .btn {
    font-size: 0.875rem;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 500;
    transition: all 0.2s ease;
}

.chat-header .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Chat messages styles */
.chat-messages {
    flex: 1;
    overflow-y: auto;
    padding: 1.25rem;
    background: #f8f9fa;
    position: relative;
}

.chat-placeholder {
    text-align: center;
    padding: 4rem 2rem;
    color: #6c757d;
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.chat-placeholder i {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    opacity: 0.3;
}

.chat-placeholder h5 {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: #495057;
}

.chat-placeholder p {
    font-size: 0.9375rem;
    margin: 0;
    opacity: 0.8;
    line-height: 1.5;
}

/* Message date separator */
.message-date-separator {
    text-align: center;
    margin: 1.5rem 0;
    color: #6c757d;
    font-size: 0.8125rem;
    font-weight: 600;
    position: relative;
}

.message-date-separator::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 0;
    right: 0;
    height: 1px;
    background: #dee2e6;
    z-index: 1;
}

.message-date-separator span {
    background: #f8f9fa;
    padding: 0.25rem 1rem;
    position: relative;
    z-index: 2;
}

/* Message items */
.message-item {
    margin-bottom: 1.25rem;
    display: flex;
    align-items: flex-start;
    animation: fadeInUp 0.3s ease;
}

.message-item.user-message {
    justify-content: flex-end;
}

.message-item .message-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    margin: 0 0.75rem;
    flex-shrink: 0;
    border: 2px solid #fff;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    background: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 14px;
    font-weight: 600;
}

.message-item .message-content {
    max-width: 70%;
    padding: 0.875rem 1.125rem;
    border-radius: 1.125rem;
    position: relative;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.message-item.user-message .message-content {
    background: #007bff;
    color: #fff;
    border-bottom-right-radius: 0.375rem;
}

.message-item.other-message .message-content {
    background: #fff;
    color: #212529;
    border: 1px solid #e9ecef;
    border-bottom-left-radius: 0.375rem;
}

.message-item .message-sender {
    font-size: 0.75rem;
    font-weight: 600;
    margin-bottom: 0.375rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.message-item.user-message .message-sender {
    color: rgba(255, 255, 255, 0.8);
}

.message-item .message-text {
    font-size: 0.875rem;
    line-height: 1.4;
    margin: 0;
    word-wrap: break-word;
}

.message-item .message-time {
    font-size: 0.6875rem;
    color: #6c757d;
    margin-top: 0.375rem;
    text-align: right;
    opacity: 0.8;
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 0.25rem;
}

.message-item.user-message .message-time {
    color: rgba(255, 255, 255, 0.8);
}

/* Message status indicators */
.message-status {
    display: flex;
    align-items: center;
    gap: 0.125rem;
}

.message-status .checkmark {
    font-size: 0.75rem;
    color: #28a745;
}

.message-status .checkmark.single {
    color: #28a745;
}

.message-status .checkmark.double {
    color: #28a745;
}

.message-status .checkmark.unread {
    color: #6c757d;
}

/* Message composer styles */
.message-composer {
    background: #fff;
    border-top: 1px solid #e9ecef;
    padding: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 -2px 8px rgba(0,0,0,0.1);
}

.channel-toggle {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 0.75rem;
    border: 1px solid #e9ecef;
    margin-bottom: 1.25rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

.channel-toggle .toggle-label {
    font-size: 0.9375rem;
    font-weight: 600;
    color: #495057;
    margin-right: 1.25rem;
}

.channel-toggle .btn-group-toggle .btn {
    font-size: 0.8125rem;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    border: 1px solid #e9ecef;
    background: #fff;
    color: #6c757d;
    transition: all 0.2s ease;
    font-weight: 500;
}

.channel-toggle .btn-group-toggle .btn:hover {
    background: #e9ecef;
    color: #495057;
    transform: translateY(-1px);
}

.channel-toggle .btn-group-toggle .btn.active {
    background: #28a745;
    color: #fff;
    border-color: #28a745;
    box-shadow: 0 2px 4px rgba(40, 167, 69, 0.3);
}

.message-input-area {
    display: flex;
    align-items: flex-end;
    gap: 0.75rem;
}

.message-input-area .input-group {
    flex: 1;
}

.message-input-area .form-control {
    border: 1px solid #e9ecef;
    border-radius: 0.75rem;
    padding: 0.875rem 1rem;
    font-size: 0.875rem;
    resize: none;
    transition: all 0.2s ease;
    min-height: 48px;
}

.message-input-area .form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    transform: translateY(-1px);
}

.message-input-area .btn {
    padding: 0.875rem;
    border-radius: 0.75rem;
    font-size: 1rem;
    border: 1px solid #e9ecef;
    background: #fff;
    color: #6c757d;
    transition: all 0.2s ease;
    min-width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.message-input-area .btn:hover {
    background: #f8f9fa;
    color: #495057;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.message-input-area .btn.send-btn {
    background: #007bff;
    color: #fff;
    border-color: #007bff;
    min-width: 56px;
    height: 48px;
}

.message-input-area .btn.send-btn:hover {
    background: #0056b3;
    border-color: #0056b3;
    box-shadow: 0 2px 8px rgba(0, 123, 255, 0.3);
}

/* Emoji picker styles */
.emoji-picker {
    position: absolute;
    bottom: 100%;
    left: 0;
    background: #fff;
    border: 1px solid #e9ecef;
    border-radius: 0.75rem;
    padding: 1rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1000;
    margin-bottom: 0.5rem;
    display: none;
}

.emoji-grid {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 0.5rem;
    max-width: 320px;
}

.emoji {
    font-size: 1.5rem;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 0.375rem;
    transition: all 0.2s ease;
    text-align: center;
}

.emoji:hover {
    background: #f8f9fa;
    transform: scale(1.1);
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .chat-header .contact-info {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.75rem;
    }
    
    .contact-avatar {
        width: 40px;
        height: 40px;
    }
    
    .contact-avatar .avatar-placeholder {
        font-size: 18px;
    }
    
    .chat-header .contact-actions {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }
    
    .chat-header .tags-input {
        margin-right: 0;
        margin-bottom: 0.5rem;
    }
    
    .message-item .message-content {
        max-width: 80%;
        padding: 0.75rem 1rem;
    }
    
    .message-input-area .form-control {
        padding: 0.75rem;
        font-size: 0.8125rem;
    }
    
    .message-input-area .btn {
        padding: 0.75rem;
        font-size: 0.9375rem;
        min-width: 44px;
        height: 44px;
    }
    
    .message-input-area .btn.send-btn {
        min-width: 52px;
        height: 44px;
    }
    
    .emoji-grid {
        grid-template-columns: repeat(6, 1fr);
        max-width: 280px;
    }
    
    .emoji {
        font-size: 1.25rem;
        padding: 0.1875rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Channel selector functionality
    const channelSelector = document.getElementById('channel_selector_wrapper');
    const quickTemplates = document.getElementById('quick_templates_wrapper');
    const whatsappQuickActions = document.getElementById('whatsapp_quick_actions');
    
    // Show channel selector when WhatsApp is selected
    document.addEventListener('click', function(e) {
        if (e.target.closest('.whatsapp-channel-link') || e.target.closest('.whatsapp-contact-link')) {
            channelSelector.style.display = 'block';
            quickTemplates.style.display = 'block';
            whatsappQuickActions.style.display = 'block';
            
            // Set WhatsApp as default channel
            document.getElementById('channel_whatsapp').checked = true;
            updateChannelSelection('whatsapp');
        } else if (e.target.closest('.messages-menu-link') && !e.target.closest('.whatsapp-channel-link') && !e.target.closest('.whatsapp-contact-link')) {
            channelSelector.style.display = 'none';
            quickTemplates.style.display = 'none';
            whatsappQuickActions.style.display = 'none';
            
            // Set internal as default channel
            updateChannelSelection('internal');
        }
    });

    // Channel selection change
    document.querySelectorAll('input[name="channel_selector"]').forEach(radio => {
        radio.addEventListener('change', function() {
            updateChannelSelection(this.value);
        });
    });

    function updateChannelSelection(channel) {
        document.querySelector('.tracking_channel').value = channel;
        document.getElementById('feed_container_channel').value = channel;
        
        if (channel === 'whatsapp') {
            whatsappQuickActions.style.display = 'block';
        } else {
            whatsappQuickActions.style.display = 'none';
        }
    }

    // Quick template functionality
    document.querySelectorAll('.quick-template-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const template = this.dataset.template;
            const textarea = document.getElementById('messaging_text_editor');
            textarea.value = template;
            textarea.focus();
        });
    });

    // Emoji picker functionality
    const emojiPickerBtn = document.getElementById('emoji_picker_btn');
    const emojiPicker = document.getElementById('emoji_picker');
    
    emojiPickerBtn.addEventListener('click', function() {
        emojiPicker.style.display = emojiPicker.style.display === 'none' ? 'block' : 'none';
    });

    document.querySelectorAll('.emoji').forEach(emoji => {
        emoji.addEventListener('click', function() {
            const emojiChar = this.dataset.emoji;
            const textarea = document.getElementById('messaging_text_editor');
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            const text = textarea.value;
            
            textarea.value = text.substring(0, start) + emojiChar + text.substring(end);
            textarea.selectionStart = textarea.selectionEnd = start + emojiChar.length;
            textarea.focus();
            
            emojiPicker.style.display = 'none';
        });
    });

    // Close emoji picker when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.emoji-picker-wrapper')) {
            emojiPicker.style.display = 'none';
        }
    });
});
</script>