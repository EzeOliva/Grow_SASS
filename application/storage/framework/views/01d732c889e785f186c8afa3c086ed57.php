<?php $__env->startSection('content'); ?>
<div class="whatsapp-container">
    <!-- WhatsApp Header -->
    <div class="whatsapp-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1 class="whatsapp-title">
                        <i class="fab fa-whatsapp text-success me-2"></i>
                        WhatsApp Tickets
                    </h1>
                    <p class="whatsapp-subtitle mb-0">Manage customer conversations and support tickets</p>
                </div>
                <div class="col-md-6 text-end">
                    <div class="whatsapp-actions">
                        <a href="<?php echo e(route('whatsapp.tickets.create')); ?>" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>New Ticket
                        </a>
                        <a href="<?php echo e(route('whatsapp.connections.index')); ?>" class="btn btn-outline-secondary ms-2">
                            <i class="fas fa-cog me-2"></i>Connections
                        </a>
                        <a href="<?php echo e(route('whatsapp.dashboard')); ?>" class="btn btn-outline-info ms-2">
                            <i class="fas fa-chart-bar me-2"></i>Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="whatsapp-content">
        <?php echo $__env->yieldContent('whatsapp-content'); ?>
    </div>
</div>

<!-- WhatsApp Styles -->
<style>
.whatsapp-container {
    background: #f8f9fa;
    min-height: calc(100vh - 60px);
}

.whatsapp-header {
    background: #ffffff;
    color: var(--whatsapp-text);
    padding: 1rem 0;
    margin-bottom: 2rem;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
    border-bottom: 1px solid var(--whatsapp-border);
}

.whatsapp-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin: 0;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
}

.whatsapp-subtitle {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-top: 0.5rem;
}

.whatsapp-actions .btn {
    border-radius: 25px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.whatsapp-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.whatsapp-content {
    padding: 0 2rem 2rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .whatsapp-header {
        padding: 1.5rem 0;
        text-align: center;
    }
    
    .whatsapp-title {
        font-size: 2rem;
    }
    
    .whatsapp-actions {
        margin-top: 1rem;
    }
    
    .whatsapp-actions .btn {
        display: block;
        width: 100%;
        margin: 0.5rem 0;
    }
    
    .whatsapp-content {
        padding: 0 1rem 1rem;
    }
}

/* WhatsApp Theme Colors */
:root {
    --whatsapp-primary: #25d366; /* matches accent success in Modern */
    --whatsapp-secondary: #0f6d64; /* align with Modern primary tone */
    --whatsapp-light: #dcf8c6;
    --whatsapp-dark: #0b5a52;
    --whatsapp-gray: #f5f6f8; /* closer to Modern background */
    --whatsapp-text: #2b2f33;
    --whatsapp-border: #e6e8eb;
}

/* Custom Button Styles */
.btn-whatsapp {
    background: var(--whatsapp-primary);
    border-color: var(--whatsapp-primary);
    color: white;
}

.btn-whatsapp:hover {
    background: var(--whatsapp-secondary);
    border-color: var(--whatsapp-secondary);
    color: white;
}

.btn-outline-whatsapp {
    color: var(--whatsapp-primary);
    border-color: var(--whatsapp-primary);
}

.btn-outline-whatsapp:hover {
    background: var(--whatsapp-primary);
    border-color: var(--whatsapp-primary);
    color: white;
}

/* Card Styles */
.whatsapp-card {
    background: white;
    border-radius: 12px;
    border: 1px solid var(--whatsapp-border);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.whatsapp-card:hover {
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.whatsapp-card .card-header {
    background: #d9edeb;
    color: white;
    border-radius: 12px 12px 0 0;
    border: none;
    padding: 1.5rem;
}

.whatsapp-card .card-body {
    padding: 1.5rem;
}

/* Status Badges */
.status-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-open {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-in-progress {
    background: #d1ecf1;
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.status-closed {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

/* Priority Badges */
.priority-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.priority-urgent {
    background: #f8d7da;
    color: #721c24;
}

.priority-high {
    background: #f5c6cb;
    color: #721c24;
}

.priority-medium {
    background: #fff3cd;
    color: #856404;
}

.priority-low {
    background: #d1ecf1;
    color: #0c5460;
}

/* Channel Badges */
.channel-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
}

.channel-whatsapp {
    background: var(--whatsapp-light);
    color: var(--whatsapp-dark);
}

.channel-email {
    background: #e3f2fd;
    color: #1565c0;
}

/* Conversation Messages */
.message .message-content {
    border-radius: 14px;
    line-height: 1.5;
}

.message-client .message-content {
    background: #f8f9fa;
    border: 1px solid #e0e0e0;
}

.message-agent .message-content {
    background: var(--whatsapp-primary);
    color: #ffffff;
}

.message-agent .message-content .text-muted {
    color: rgba(255, 255, 255, 0.8) !important;
}

.message-attachments {
    margin-top: 8px;
}

.message-attachments .attachment-item a {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    color: inherit;
    padding: 4px 8px;
    border-radius: 6px;
    transition: background-color .2s ease;
}

.message-attachments .attachment-item a:hover {
    background: rgba(0,0,0,0.05);
}

/* Reply Composer */
.composer-toolbar {
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Emoji Picker */
.emoji-menu {
    position: absolute;
    z-index: 1050;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 10px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    width: 280px;
}

.emoji-grid {
    display: grid;
    grid-template-columns: repeat(8, 1fr);
    gap: 6px;
}

.emoji-item {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 28px;
    height: 28px;
    font-size: 18px;
    line-height: 1;
    border: none;
    background: transparent;
    border-radius: 6px;
    cursor: pointer;
}

.emoji-item:hover {
    background: #f3f4f6;
}

/* Templates Dropdown */
.templates-menu {
    max-height: 260px;
    overflow: auto;
    min-width: 360px;
}

/* Emoji Tabs */
.emoji-tabs {
    display: flex;
    align-items: center;
    gap: 6px;
    border-bottom: 1px solid #eee;
    padding-bottom: 6px;
    margin-bottom: 8px;
}

.emoji-tab {
    border: none;
    background: transparent;
    padding: 6px 8px;
    border-radius: 8px;
    font-size: 18px;
    line-height: 1;
    cursor: pointer;
}

.emoji-tab.active {
    background: #f3f4f6;
}

/* Showbox Animations */
.showbox-animate {
    opacity: 0;
}

.showbox-animate.slide-left.showbox-enter {
    animation: slideInLeft 600ms ease-out forwards;
}

.showbox-animate.slide-up.showbox-enter {
    animation: slideInUp 600ms ease-out forwards;
}

@keyframes slideInLeft {
    from { opacity: 0; transform: translateX(-18px); }
    to { opacity: 1; transform: translateX(0); }
}

@keyframes slideInUp {
    from { opacity: 0; transform: translateY(18px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Enhanced Textareas */
.nx-textarea {
    border-radius: 10px;
    border: 1px solid var(--whatsapp-border);
    background: #ffffff;
    transition: box-shadow .2s ease, border-color .2s ease;
    resize: none; /* we'll auto-resize via JS */
}

.nx-textarea:focus {
    outline: none;
    border-color: var(--whatsapp-secondary);
    box-shadow: 0 0 0 3px rgba(18, 140, 126, 0.15);
}

.nx-textarea-wrapper {
    position: relative;
}

.nx-textarea-counter {
    position: absolute;
    right: 10px;
    bottom: 6px;
    font-size: 12px;
    color: #999;
}
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    (function(){
        function enhanceTextareas(scope){
            var containers = (scope || document).querySelectorAll('.nx-textarea-wrapper');
            containers.forEach(function(cont){
                var ta = cont.querySelector('textarea.nx-textarea');
                if (!ta) return;
                var counter = cont.querySelector('.nx-textarea-counter');
                var max = parseInt(ta.getAttribute('maxlength') || '0', 10);

                function autoresize(){
                    ta.style.height = 'auto';
                    ta.style.height = (ta.scrollHeight + 2) + 'px';
                }

                function updateCounter(){
                    if (!counter) return;
                    var len = ta.value.length;
                    if (max > 0) {
                        counter.textContent = len + ' / ' + max;
                    } else {
                        counter.textContent = len + ' chars';
                    }
                }

                ta.addEventListener('input', function(){
                    autoresize();
                    updateCounter();
                });

                ta.addEventListener('keydown', function(e){
                    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter'){
                        // submit closest form
                        var form = ta.closest('form');
                        if (form) form.submit();
                    }
                });

                // initial
                autoresize();
                updateCounter();
            });
        }

        if (document.readyState === 'loading'){
            document.addEventListener('DOMContentLoaded', function(){ enhanceTextareas(); });
        } else {
            enhanceTextareas();
        }

        // expose for ajax updates if needed
        window.NXEnhanceWhatsAppTextareas = enhanceTextareas;
    })();
</script>
<!-- CKEditor 5 -->
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    (function(){
        var editors = {};
        function generateId(){ return 'ta-' + Math.random().toString(36).slice(2); }

        function initCKOnWhatsAppTextareas(scope){
            var root = scope || document;
            var container = root.querySelector('.whatsapp-content') || root;
            var textareas = container.querySelectorAll('textarea');
            textareas.forEach(function(ta){
                if (ta.dataset.ckInitialized === '1') return;
                // Skip if marked opt-out
                if (ta.classList.contains('js-no-richtext')) return;
                if (!ta.id) ta.id = generateId();

                ClassicEditor.create(ta, {
                    toolbar: [
                        'undo','redo','|','heading','|',
                        'bold','italic','underline','strikethrough','|',
                        'link','blockQuote','code','codeBlock','|',
                        'bulletedList','numberedList','outdent','indent'
                    ],
                    heading: {
                        options: [
                            { model: 'paragraph', title: 'Paragraph', class: 'ck-heading_paragraph' },
                            { model: 'heading2', view: 'h2', title: 'Heading 2', class: 'ck-heading_heading2' },
                            { model: 'heading3', view: 'h3', title: 'Heading 3', class: 'ck-heading_heading3' }
                        ]
                    }
                }).then(function(editor){
                    editors[ta.id] = editor;
                    ta.dataset.ckInitialized = '1';

                    // Ctrl+Enter submit
                    editor.keystrokes.set('Ctrl+Enter', function(){
                        var form = ta.closest('form');
                        if (form) form.submit();
                        return true;
                    });
                }).catch(function(err){
                    console.error('CKEditor init failed for', ta, err);
                });
            });
        }

        if (document.readyState === 'loading'){
            document.addEventListener('DOMContentLoaded', function(){ initCKOnWhatsAppTextareas(); });
        } else {
            initCKOnWhatsAppTextareas();
        }

        window.NXCKEditors = editors;
        window.NXInitWhatsAppCKEditors = initCKOnWhatsAppTextareas;
    })();
</script>

<script>
    (function(){
        function collectShowboxes(scope){
            var root = scope || document;
            var boxes = Array.prototype.slice.call(root.querySelectorAll('.whatsapp-card, .card, .status-badge, .priority-badge, .channel-badge'));
            // mark & set initial classes
            boxes.forEach(function(el, idx){
                if (el.dataset.showboxInitialized === '1') return;
                el.dataset.showboxInitialized = '1';
                el.classList.add('showbox-animate');
                // alternate flow types
                el.classList.add((idx % 2 === 0) ? 'slide-left' : 'slide-up');
            });
            // stagger animation start
            boxes.forEach(function(el, idx){
                setTimeout(function(){ el.classList.add('showbox-enter'); }, 80 * idx);
            });
        }

        if (document.readyState === 'loading'){
            document.addEventListener('DOMContentLoaded', function(){ collectShowboxes(); });
        } else {
            collectShowboxes();
        }

        // expose for dynamic content
        window.NXWhatsAppAnimateShowboxes = collectShowboxes;
    })();
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\GrowSass\application\resources\views/whatsapp/layouts/app.blade.php ENDPATH**/ ?>