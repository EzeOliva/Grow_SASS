@extends('layout.wrapper') @section('content')

<style>
/* Messages page - prevent overflow caused by global wrapper padding */
.page-wrapper { padding-bottom: 0 !important; }
</style>

<!-- Messages Page Container -->
<div class="messages-page-container">
    <!-- Hidden CSRF token for AJAX requests -->
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <!-- Left Sidebar - Beautiful Design -->
    <div class="messages-sidebar">
        <!-- Elegant Header -->
        <div class="sidebar-header">
            <div class="header-content">
                <div class="header-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <div class="header-text">
                    <h4>Messages</h4>
                    <p class="subtitle">Stay connected with your team</p>
                </div>
            </div>
            <div class="header-actions">
                <button class="btn-icon" title="New Message">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn-icon" title="More Options">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
            </div>
        </div>

        <!-- Search Bar - Modern Design -->
        <div class="search-container">
            <div class="search-wrapper">
                <div class="search-icon">
                    <i class="fas fa-search"></i>
                </div>
                <input type="text" 
                       class="search-input" 
                       id="messages_search" 
                       placeholder="Search conversations..." 
                       autocomplete="off">
                <button class="search-clear" id="search_clear" style="display: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Main Tabs - Beautiful Design -->
        <div class="main-tabs-container">
            <div class="main-tabs" id="main_tabs">
                <button class="tab-button active" data-tab="team">
                    <i class="fas fa-users"></i>
                    <span>Team</span>
                    <div class="tab-indicator"></div>
                </button>
                <button class="tab-button" data-tab="whatsapp">
                    <i class="fab fa-whatsapp"></i>
                    <span>WhatsApp</span>
                    <div class="tab-indicator"></div>
                </button>
            </div>
        </div>

        <!-- Sub Tabs - Elegant Design -->
        <div class="sub-tabs-container" id="sub_tabs">
            <div class="sub-tabs" id="sub_tabs_nav">
                <button class="sub-tab-button active" data-subtab="all-users">
                    <span>All Users</span>
                </button>
                <button class="sub-tab-button" data-subtab="online-users">
                    <span>Online</span>
                </button>
            </div>
        </div>

        <!-- Users List Container -->
        <div class="users-container scrollable-content">
            @include('pages.messages.components.left-panel')
        </div>
    </div>
    
    <!-- Right Content Area - Beautiful Design -->
    <div class="messages-content">
        <div id="messages-content-container">
            @if(isset($thread))
                <!-- Thread Content -->
                @include('pages.messages.components.chat-room', ['thread' => $thread])
            @else
                <!-- Welcome State - Beautiful Design -->
                @include('pages.messages.components.welcome-state')
            @endif
        </div>
    </div>
</div>

<!-- Modern CSS for Beautiful Messages Page -->
<style>
/* ===========================================
   MODERN MESSAGES PAGE DESIGN - 2024
   =========================================== */

/* Root Variables - Modern Design System */
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

/* ===========================================
   MAIN CONTAINER - MODERN LAYOUT
   =========================================== */

.messages-page-container {
    display: flex;
    height: calc(100vh - 80px);
    background: var(--bg-secondary);
    margin: 0;
    padding: 0;
    overflow: hidden;
    font-family: var(--font-family);
    position: relative;
}

/* ===========================================
   LEFT SIDEBAR - MODERN DESIGN
   =========================================== */

.messages-sidebar {
    width: 400px;
    min-width: 400px;
    background: var(--bg-primary);
    border-right: 1px solid var(--border-light);
    display: flex;
    flex-direction: column;
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    position: relative;
    z-index: var(--z-sticky);
}

/* ===========================================
   SIDEBAR HEADER - MODERN DESIGN
   =========================================== */

.sidebar-header {
    padding: var(--space-6);
    background: var(--primary-gradient);
    color: var(--text-inverse);
    position: relative;
    overflow: hidden;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -20%;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    border-radius: var(--radius-full);
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    50% { transform: translateY(-20px) rotate(180deg); }
}

.header-content {
    display: flex;
    align-items: center;
    gap: var(--space-4);
    position: relative;
    z-index: 2;
}

.header-icon {
    width: 56px;
    height: 56px;
    background: rgba(255,255,255,0.15);
    border-radius: var(--radius-lg);
    display: flex;
    align-items: center;
    justify-content: center;
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255,255,255,0.2);
    box-shadow: var(--shadow-md);
}

.header-icon i {
    font-size: 28px;
    color: var(--text-inverse);
}

.header-text h4 {
    margin: 0;
    font-size: var(--font-size-2xl);
    font-weight: 700;
    color: var(--text-inverse);
    letter-spacing: -0.025em;
}

.header-text .subtitle {
    margin: var(--space-1) 0 0 0;
    font-size: var(--font-size-sm);
    color: rgba(255,255,255,0.9);
    font-weight: 500;
    letter-spacing: 0.025em;
}

.header-actions {
    position: absolute;
    top: var(--space-6);
    right: var(--space-6);
    display: flex;
    gap: var(--space-2);
    z-index: 2;
}

.btn-icon {
    width: 44px;
    height: 44px;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.2);
    border-radius: var(--radius-lg);
    color: var(--text-inverse);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition-normal);
    backdrop-filter: blur(20px);
    box-shadow: var(--shadow-sm);
}

.btn-icon:hover {
    background: rgba(255,255,255,0.25);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* ===========================================
   SEARCH CONTAINER - MODERN DESIGN
   =========================================== */

.search-container {
    padding: var(--space-5) var(--space-6) var(--space-4);
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-light);
    position: relative;
}

.search-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.search-icon {
    position: absolute;
    left: var(--space-4);
    color: var(--text-muted);
    font-size: var(--font-size-sm);
    z-index: 2;
}

.search-input {
    width: 100%;
    height: 48px;
    padding: 0 var(--space-12) 0 var(--space-12);
    border: 2px solid var(--border-light);
    border-radius: var(--radius-xl);
    font-size: var(--font-size-sm);
    background: var(--gray-50);
    transition: var(--transition-normal);
    outline: none;
    font-weight: 500;
    color: var(--text-primary);
}

.search-input::placeholder {
    color: var(--text-muted);
    font-weight: 400;
}

.search-input:focus {
    border-color: var(--primary-color);
    background: var(--bg-primary);
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    transform: translateY(-1px);
}

.search-clear {
    position: absolute;
    right: var(--space-3);
    width: 24px;
    height: 24px;
    background: var(--gray-400);
    border: none;
    border-radius: var(--radius-full);
    color: var(--text-inverse);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: var(--font-size-xs);
    transition: var(--transition-fast);
    z-index: 2;
}

.search-clear:hover {
    background: var(--gray-500);
    transform: scale(1.1);
}

/* ===========================================
   MAIN TABS - MODERN DESIGN
   =========================================== */

.main-tabs-container {
    padding: 0 var(--space-6);
    background: var(--bg-primary);
    border-bottom: 1px solid var(--border-light);
}

.main-tabs {
    display: flex;
    gap: var(--space-1);
    padding: var(--space-2);
    background: var(--gray-100);
    border-radius: var(--radius-lg);
}

.tab-button {
    flex: 1;
    height: 44px;
    background: transparent;
    border: none;
    border-radius: var(--radius-md);
    color: var(--text-secondary);
    font-size: var(--font-size-sm);
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition-normal);
    display: flex;
    align-items: center;
    justify-content: center;
    gap: var(--space-2);
    position: relative;
    letter-spacing: 0.025em;
}

.tab-button:hover {
    color: var(--primary-color);
    background: rgba(59, 130, 246, 0.1);
    transform: translateY(-1px);
}

.tab-button.active {
    color: var(--primary-color);
    background: var(--bg-primary);
    box-shadow: var(--shadow-sm);
    transform: translateY(-1px);
}

.tab-button i {
    font-size: 16px;
}

.tab-indicator {
    position: absolute;
    bottom: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 0;
    height: 2px;
    background: var(--primary-color);
    transition: var(--transition);
}

.tab-button.active .tab-indicator {
    width: 60%;
}

/* Sub Tabs - Elegant Design */
.sub-tabs-container {
    padding: 16px 24px 0;
    background: var(--white);
}

.sub-tabs {
    display: flex;
    gap: 0;
    border-bottom: 1px solid var(--border-color);
}

.sub-tab-button {
    padding: 12px 24px;
    background: transparent;
    border: none;
    color: var(--text-secondary);
    font-size: 14px;
    font-weight: 500;
    cursor: pointer;
    transition: var(--transition);
    position: relative;
    border-bottom: 2px solid transparent;
}

.sub-tab-button:hover {
    color: var(--primary-color);
}

.sub-tab-button.active {
    color: var(--primary-color);
    border-bottom-color: var(--primary-color);
}

/* Users Container */
.users-container {
    flex: 1;
    overflow-y: auto;
    padding: 0;
    background: var(--white);
}

/* Scrollable Content */
.scrollable-content {
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: var(--border-color) transparent;
}

.scrollable-content::-webkit-scrollbar {
    width: 6px;
}

.scrollable-content::-webkit-scrollbar-track {
    background: transparent;
}

.scrollable-content::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 3px;
}

.scrollable-content::-webkit-scrollbar-thumb:hover {
    background: var(--text-muted);
}

/* ===========================================
   RIGHT CONTENT AREA - BEAUTIFUL DESIGN
   =========================================== */

/* ===========================================
   RIGHT CONTENT AREA - MODERN DESIGN
   =========================================== */

.messages-content {
    flex: 1;
    background: var(--bg-primary);
    display: flex;
    flex-direction: column;
    box-shadow: var(--shadow-lg);
    position: relative;
    overflow: hidden;
}

#messages-content-container {
    flex: 1;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* ===========================================
   CHAT ROOM STYLES
   =========================================== */

/* Chat Room Container */
.chat-room {
    display: flex;
    flex-direction: column;
    height: 100%;
    background: var(--white);
}

/* Chat Header */
.chat-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 24px;
    background: var(--white);
    border-bottom: 1px solid var(--border-color);
    box-shadow: var(--shadow-light);
    position: sticky;
    top: 0;
    z-index: 10;
}

.chat-contact-info {
    display: flex;
    align-items: center;
    gap: 16px;
}

.contact-avatar {
    position: relative;
}

.contact-avatar .avatar-placeholder {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--white);
    font-size: 20px;
    font-weight: 600;
    box-shadow: var(--shadow-light);
}

.contact-avatar .avatar-placeholder.whatsapp {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
}

.contact-avatar .avatar-placeholder.whatsapp i {
    font-size: 22px;
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
    gap: 4px;
}

.contact-name {
    font-size: 20px;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0;
}

.contact-meta {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.contact-role, .contact-phone {
    font-size: 14px;
    color: var(--text-secondary);
}

.contact-email, .contact-status {
    font-size: 12px;
    color: var(--text-muted);
}

.chat-actions {
    display: flex;
    gap: 8px;
}

.action-btn {
    width: 40px;
    height: 40px;
    background: var(--light-bg);
    border: none;
    border-radius: var(--radius-sm);
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
}

.action-btn:hover {
    background: var(--primary-light);
    color: var(--primary-color);
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

/* Message Composer */
.message-composer {
    background: var(--white);
    border-top: 1px solid var(--border-color);
    padding: 20px 24px;
    position: sticky;
    bottom: 0;
}

/* Channel Toggle */
.channel-toggle {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    padding: 16px;
    background: var(--light-bg);
    border-radius: var(--radius-md);
}

.toggle-label {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-secondary);
}

.toggle-buttons {
    display: flex;
    gap: 8px;
}

.toggle-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    background: transparent;
    border: 2px solid transparent;
    border-radius: var(--radius-sm);
    color: var(--text-secondary);
    font-size: 13px;
    font-weight: 500;
    cursor: pointer;
    transition: var(--transition);
}

.toggle-btn:hover {
    background: rgba(32, 174, 227, 0.1);
    color: var(--primary-color);
}

.toggle-btn.active {
    background: var(--primary-color);
    color: var(--white);
    border-color: var(--primary-color);
}

.toggle-btn i {
    font-size: 14px;
}

/* Message Input Area */
.message-input-area {
    position: relative;
}

.input-wrapper {
    display: flex;
    align-items: flex-end;
    gap: 12px;
    background: var(--light-bg);
    border: 2px solid transparent;
    border-radius: var(--radius-md);
    padding: 16px;
    transition: var(--transition);
}

.input-wrapper:focus-within {
    border-color: var(--primary-color);
    background: var(--white);
    box-shadow: 0 0 0 3px rgba(32, 174, 227, 0.1);
}

.input-actions {
    display: flex;
    gap: 8px;
}

.input-action-btn {
    width: 36px;
    height: 36px;
    background: transparent;
    border: none;
    border-radius: var(--radius-sm);
    color: var(--text-muted);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
}

.input-action-btn:hover {
    background: rgba(32, 174, 227, 0.1);
    color: var(--primary-color);
}

.message-input-container {
    flex: 1;
}

.message-input {
    width: 100%;
    border: none;
    background: transparent;
    resize: none;
    font-size: 14px;
    line-height: 1.5;
    color: var(--text-primary);
    outline: none;
    font-family: inherit;
}

.message-input::placeholder {
    color: var(--text-muted);
}

.input-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-top: 8px;
    font-size: 12px;
}

.message-options {
    display: flex;
    align-items: center;
}

.option-checkbox {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    color: var(--text-secondary);
}

.option-checkbox input {
    display: none;
}

.checkmark {
    width: 16px;
    height: 16px;
    border: 2px solid var(--border-color);
    border-radius: 3px;
    position: relative;
    transition: var(--transition);
}

.option-checkbox input:checked + .checkmark {
    background: var(--primary-color);
    border-color: var(--primary-color);
}

.option-checkbox input:checked + .checkmark::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: var(--white);
    font-size: 10px;
    font-weight: bold;
}

.char-counter {
    color: var(--text-muted);
}

.send-button {
    width: 44px;
        height: 44px;
    background: var(--primary-color);
    border: none;
    border-radius: var(--radius-md);
    color: var(--white);
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    font-size: 16px;
}

.send-button:hover:not(:disabled) {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: var(--shadow-medium);
}

.send-button:disabled {
    background: var(--text-muted);
    cursor: not-allowed;
    opacity: 0.6;
}

/* Emoji Picker */
.emoji-picker {
    position: absolute;
    bottom: 100%;
    left: 0;
    right: 0;
    background: var(--white);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-heavy);
    margin-bottom: 16px;
    z-index: 100;
}

.emoji-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px;
    border-bottom: 1px solid var(--border-color);
}

.emoji-header span {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-primary);
}

.close-emoji {
    width: 32px;
    height: 32px;
    background: transparent;
    border: none;
    border-radius: var(--radius-sm);
    color: var(--text-muted);
    cursor: pointer;
    transition: var(--transition);
}

.close-emoji:hover {
    background: var(--light-bg);
    color: var(--text-secondary);
}

.emoji-grid {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 8px;
    padding: 16px;
    max-height: 200px;
    overflow-y: auto;
}

.emoji {
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    cursor: pointer;
    border-radius: var(--radius-sm);
    transition: var(--transition);
}

.emoji:hover {
    background: var(--light-bg);
    transform: scale(1.1);
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
   RESPONSIVE DESIGN
   =========================================== */

@media (max-width: 1200px) {
    .messages-sidebar {
        width: 340px;
        min-width: 340px;
    }
}

/* ===========================================
   RESPONSIVE DESIGN - MODERN BREAKPOINTS
   =========================================== */

/* Large Desktop (1440px+) */
@media (min-width: 1440px) {
    .messages-sidebar {
        width: 450px;
        min-width: 450px;
    }
    
    .sidebar-header {
        padding: var(--space-8);
    }
    
    .search-container {
        padding: var(--space-6) var(--space-8) var(--space-5);
    }
}

/* Desktop (1024px - 1439px) */
@media (max-width: 1439px) and (min-width: 1024px) {
    .messages-sidebar {
        width: 380px;
        min-width: 380px;
    }
}

/* Tablet (768px - 1023px) */
@media (max-width: 1023px) and (min-width: 768px) {
    .messages-page-container {
        height: calc(100vh - 60px);
    }
    
    .messages-sidebar {
        width: 350px;
        min-width: 350px;
    }
    
    .sidebar-header {
        padding: var(--space-5);
    }
    
    .header-text h4 {
        font-size: var(--font-size-xl);
    }
    
    .search-container {
        padding: var(--space-4) var(--space-5);
    }
    
    .main-tabs-container {
        padding: 0 var(--space-5);
    }
}

/* Mobile Landscape (480px - 767px) */
@media (max-width: 767px) and (min-width: 480px) {
    .messages-page-container {
        flex-direction: column;
        height: calc(100vh - 60px);
    }
    
    .messages-sidebar {
        width: 100%;
        min-width: 100%;
        height: 50vh;
        max-height: 50vh;
        border-right: none;
        border-bottom: 1px solid var(--border-light);
    }
    
    .messages-content {
        height: 50vh;
        min-height: 50vh;
    }
    
    .sidebar-header {
        padding: var(--space-4);
    }
    
    .header-text h4 {
        font-size: var(--font-size-lg);
    }
    
    .search-container {
        padding: var(--space-3) var(--space-4);
    }
    
    .main-tabs-container {
        padding: 0 var(--space-4);
    }
    
    .chat-header {
        padding: var(--space-4) var(--space-5);
    }
    
    .contact-avatar .avatar-placeholder {
        width: 44px;
        height: 44px;
        font-size: 20px;
    }
    
    .contact-name {
        font-size: var(--font-size-lg);
    }
    
    .chat-messages {
        padding: var(--space-4) var(--space-5);
    }
    
    .message-composer {
        padding: var(--space-4) var(--space-5);
    }
}

/* Mobile Portrait (320px - 479px) */
@media (max-width: 479px) {
    .messages-page-container {
        flex-direction: column;
        height: calc(100vh - 50px);
    }
    
    .messages-sidebar {
        width: 100%;
        min-width: 100%;
        height: 45vh;
        max-height: 45vh;
        border-right: none;
        border-bottom: 1px solid var(--border-light);
    }
    
    .messages-content {
        height: 55vh;
        min-height: 55vh;
    }
    
    .sidebar-header {
        padding: var(--space-3);
    }
    
    .header-content {
        gap: var(--space-3);
    }
    
    .header-icon {
        width: 48px;
        height: 48px;
    }
    
    .header-icon i {
        font-size: 24px;
    }
    
    .header-text h4 {
        font-size: var(--font-size-lg);
    }
    
    .header-text .subtitle {
        font-size: var(--font-size-xs);
    }
    
    .header-actions {
        top: var(--space-3);
        right: var(--space-3);
    }
    
    .btn-icon {
        width: 40px;
        height: 40px;
    }
    
    .search-container {
        padding: var(--space-3);
    }
    
    .search-input {
        height: 44px;
        font-size: var(--font-size-base);
    }
    
    .main-tabs-container {
        padding: 0 var(--space-3);
    }
    
    .tab-button {
        height: 40px;
        font-size: var(--font-size-xs);
    }
    
    .chat-header {
        padding: var(--space-3) var(--space-4);
    }
    
    .chat-contact-info {
        gap: var(--space-3);
    }
    
    .contact-avatar .avatar-placeholder {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
    
    .contact-name {
        font-size: var(--font-size-base);
    }
    
    .contact-role {
        font-size: var(--font-size-xs);
    }
    
    .chat-messages {
        padding: var(--space-3) var(--space-4);
    }
    
    .message-composer {
        padding: var(--space-3) var(--space-4);
    }
    
    .composer-input-container {
        gap: var(--space-2);
    }
    
    .composer-btn {
        width: 36px;
        height: 36px;
    }
    
    .composer-input {
        font-size: var(--font-size-sm);
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

/* ===========================================
   MODERN ANIMATIONS & MICRO-INTERACTIONS
   =========================================== */

@keyframes fadeInUp {
    from { 
        opacity: 0; 
        transform: translateY(20px); 
    }
    to { 
        opacity: 1; 
        transform: translateY(0); 
    }
}

@keyframes slideInLeft {
    from { 
        transform: translateX(-30px); 
        opacity: 0; 
    }
    to { 
        transform: translateX(0); 
        opacity: 1; 
    }
}

@keyframes slideInRight {
    from { 
        transform: translateX(30px); 
        opacity: 0; 
    }
    to { 
        transform: translateX(0); 
        opacity: 1; 
    }
}

@keyframes pulse {
    0%, 100% { 
        transform: scale(1); 
    }
    50% { 
        transform: scale(1.05); 
    }
}

@keyframes shimmer {
    0% { 
        transform: translateX(-100%) translateY(-100%) rotate(45deg); 
    }
    100% { 
        transform: translateX(100%) translateY(100%) rotate(45deg); 
    }
}

@keyframes bounce {
    0%, 20%, 53%, 80%, 100% {
        transform: translate3d(0,0,0);
    }
    40%, 43% {
        transform: translate3d(0, -8px, 0);
    }
    70% {
        transform: translate3d(0, -4px, 0);
    }
    90% {
        transform: translate3d(0, -2px, 0);
    }
}

/* Page Load Animations */
.messages-sidebar {
    animation: slideInLeft 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

.messages-content {
    animation: slideInRight 0.6s cubic-bezier(0.4, 0, 0.2, 1);
}

/* User Item Animations */
.user-item {
    animation: fadeInUp 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    animation-fill-mode: both;
}

.user-item:nth-child(1) { animation-delay: 0.1s; }
.user-item:nth-child(2) { animation-delay: 0.15s; }
.user-item:nth-child(3) { animation-delay: 0.2s; }
.user-item:nth-child(4) { animation-delay: 0.25s; }
.user-item:nth-child(5) { animation-delay: 0.3s; }
.user-item:nth-child(6) { animation-delay: 0.35s; }
.user-item:nth-child(7) { animation-delay: 0.4s; }
.user-item:nth-child(8) { animation-delay: 0.45s; }
.user-item:nth-child(9) { animation-delay: 0.5s; }
.user-item:nth-child(10) { animation-delay: 0.55s; }

/* Hover Effects */
.user-item:hover .avatar-placeholder {
    animation: pulse 0.6s ease-in-out;
}

.btn-icon:hover {
    animation: bounce 0.6s ease-in-out;
}

/* Loading States */
.loading-shimmer {
    background: linear-gradient(90deg, var(--gray-200) 25%, var(--gray-100) 50%, var(--gray-200) 75%);
    background-size: 200% 100%;
    animation: shimmer 1.5s infinite;
}

/* Focus States */
.search-input:focus {
    animation: pulse 0.3s ease-in-out;
}

/* Success States */
.message-sent {
    animation: fadeInUp 0.3s ease-out;
}

/* Error States */
.error-shake {
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    10%, 30%, 50%, 70%, 90% { transform: translateX(-2px); }
    20%, 40%, 60%, 80% { transform: translateX(2px); }
}

/* ===========================================
   UTILITY CLASSES
   =========================================== */

.text-primary { color: var(--primary-color) !important; }
.text-secondary { color: var(--secondary-color) !important; }
.text-success { color: var(--success-color) !important; }
.text-warning { color: var(--warning-color) !important; }
.text-danger { color: var(--danger-color) !important; }

.bg-primary { background-color: var(--primary-color) !important; }
.bg-light { background-color: var(--light-bg) !important; }
.bg-white { background-color: var(--white) !important; }

.shadow-light { box-shadow: var(--shadow-light) !important; }
.shadow-medium { box-shadow: var(--shadow-medium) !important; }
.shadow-heavy { box-shadow: var(--shadow-heavy) !important; }

.rounded-sm { border-radius: var(--radius-sm) !important; }
.rounded-md { border-radius: var(--radius-md) !important; }
.rounded-lg { border-radius: var(--radius-lg) !important; }

.transition { transition: var(--transition) !important; }
</style>
@endsection

<!-- Messages Page JavaScript - Load after jQuery is ready -->
<script>
// Initialize NX namespace if it doesn't exist
if (typeof NX === 'undefined') {
    window.NX = {};
}

// Wait for jQuery to be available
function waitForJQuery() {
    if (typeof jQuery !== 'undefined') {
        loadMessagesJS();
    } else {
        setTimeout(waitForJQuery, 100);
    }
}

function loadMessagesJS() {
    // Load search functionality first
    const searchScript = document.createElement('script');
    searchScript.src = '/public/js/core/messages-search.js?v={{ config('system.versioning') }}';
    searchScript.onload = function() {
        // Load main messages script after search script loads
        const script = document.createElement('script');
        script.src = '/public/js/core/messages.js?v={{ config('system.versioning') }}';
        script.onload = function() {
            // Initialize functions after script loads
            if (typeof NX !== 'undefined') {
                // Functions are now available
                // Load initial chat history if there's a selected user or connection
                setTimeout(function() {
                    if (window.currentSelectedUser || window.currentSelectedConnection) {
                        NX.loadChatHistory(window.currentSelectedUser, window.currentSelectedConnection);
                    }
                }, 500);
            }
        };
        script.onerror = function() {
            alert('Failed to load messages functionality. Please refresh the page.');
        };
        document.head.appendChild(script);
    };
    searchScript.onerror = function() {
        alert('Failed to load search functionality. Please refresh the page.');
    };
    document.head.appendChild(searchScript);
}

// Start waiting for jQuery
waitForJQuery();

// Fallback function in case external JS fails
window.fallbackUserClick = function(userId, userName) {
    alert(`Fallback: User ${userName} (ID: ${userId}) you are full!`);
    
    // Simple content update for testing
    const contentContainer = document.getElementById('messages-content-container');
    if (contentContainer) {
        contentContainer.innerHTML = `
            <div class="chat-room" style="padding: 20px;">
                <h3>Chat with ${userName}</h3>
                <p>User ID: ${userId}</p>
                <p>This is a fallback chat room. The main JavaScript should handle this.</p>
            </div>
        `;
    }
};
</script>
