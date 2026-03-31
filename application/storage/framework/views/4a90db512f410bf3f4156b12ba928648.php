<!-- This file must NOT be formatted -->
<style>
    :root {
        --calendar-type-event-background: <?php echo e(config('system.settings2_calendar_events_colour')); ?>;

        --calendar-type-project-background: <?php echo e(config('system.settings2_calendar_projects_colour')); ?>;

        --calendar-type-task-background: <?php echo e(config('system.settings2_calendar_tasks_colour')); ?>;

        --calendar-fc-daygrid-dot-event-background: <?php echo e(config('system.settings2_calendar_events_colour')); ?>;

        --calendar-fc-daygrid-dot-event-contrast: color-mix(in srgb, var(--calendar-fc-daygrid-dot-event-background) 70%, black);
    }

    /* Messages Page Responsive Styles */
    .messages-left-panel {
        height: 100%;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .messages-left-panel .search-section {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    .messages-left-panel .search-section .input-group {
        box-shadow: none;
    }

    .messages-left-panel .search-section .form-control {
        border: 1px solid #e9ecef;
        font-size: 1rem;
        padding: 0.75rem 1rem;
    }

    .messages-left-panel .search-section .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .messages-left-panel .nav-tabs-wrapper {
        background: #fff;
    }

    .messages-left-panel .nav-tabs {
        border-bottom: 1px solid #e9ecef;
    }

    .messages-left-panel .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 500;
        padding: 1rem 1.5rem;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }

    .messages-left-panel .nav-tabs .nav-link:hover {
        color: #495057;
        background: #f8f9fa;
    }

    .messages-left-panel .nav-tabs .nav-link.active {
        color: #007bff;
        background: #fff;
        border-bottom: 2px solid #007bff;
    }

    .messages-left-panel .tab-content {
        flex: 1;
        overflow: hidden;
    }

    .messages-left-panel .tab-pane {
        height: 100%;
        overflow: hidden;
    }

    .messages-left-panel .contacts-list {
        height: 100%;
        overflow-y: auto;
    }

    .messages-left-panel .contact-section {
        margin-bottom: 1rem;
    }

    .messages-left-panel .section-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    .messages-left-panel .section-header h6 {
        font-size: 0.9rem;
        font-weight: 600;
        color: #6c757d;
    }

    .messages-left-panel .contact-item {
        border-bottom: 1px solid #f8f9fa;
        transition: all 0.2s ease;
    }

    .messages-left-panel .contact-item:hover {
        background: #f8f9fa;
    }

    .messages-left-panel .contact-item.active {
        background: #e3f2fd;
        border-left: 3px solid #007bff;
    }

    .messages-left-panel .contact-item a {
        text-decoration: none;
        color: inherit;
        display: block;
        padding: 1rem;
        transition: all 0.2s ease;
    }

    .messages-left-panel .contact-item a:hover {
        text-decoration: none;
        color: inherit;
    }

    .messages-left-panel .contact-avatar {
        position: relative;
        flex-shrink: 0;
    }

    .messages-left-panel .contact-avatar img {
        border: 2px solid #e9ecef;
        transition: border-color 0.2s ease;
    }

    .messages-left-panel .contact-item.active .contact-avatar img {
        border-color: #007bff;
    }

    .messages-left-panel .status-indicator {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
    }

    .messages-left-panel .status-indicator.online {
        background: #28a745;
    }

    .messages-left-panel .status-indicator.offline {
        background: #6c757d;
    }

    .messages-left-panel .status-indicator.away {
        background: #ffc107;
    }

    .messages-left-panel .connection-status-indicator {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        border: 2px solid #fff;
    }

    .messages-left-panel .connection-status-indicator.connected {
        background: #28a745;
    }

    .messages-left-panel .connection-status-indicator.disconnected {
        background: #dc3545;
    }

    .messages-left-panel .contact-info h6 {
        font-size: 1rem;
        font-weight: 600;
        color: #212529;
        margin-bottom: 0.25rem;
    }

    .messages-left-panel .contact-info small {
        font-size: 0.85rem;
        color: #6c757d;
    }

    .messages-left-panel .messages_counter {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    /* Right Panel Styles */
    .messages-right-panel {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .messages-right-panel .chat-header {
        background: #fff;
        border-bottom: 1px solid #e9ecef;
        flex-shrink: 0;
    }

    .messages-right-panel .chat-header h6 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #212529;
    }

    .messages-right-panel .chat-header small {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .messages-right-panel .chat-messages-container {
        flex: 1;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .messages-right-panel .chat-placeholder {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        background: #f8f9fa;
    }

    .messages-right-panel .chat-placeholder h5 {
        font-size: 1.25rem;
        font-weight: 500;
        color: #6c757d;
    }

    .messages-right-panel .chat-placeholder p {
        font-size: 1rem;
        color: #6c757d;
    }

    .messages-right-panel .messages-feed {
        flex: 1;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .messages-right-panel .messages-header {
        background: #fff;
        border-bottom: 1px solid #e9ecef;
        flex-shrink: 0;
    }

    .messages-right-panel .messages-header h6 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #212529;
    }

    .messages-right-panel .messages-header small {
        font-size: 0.9rem;
        color: #6c757d;
    }

    .messages-right-panel .messages-list {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        background: #f8f9fa;
    }

    .messages-right-panel .message-composition {
        background: #fff;
        border-top: 1px solid #e9ecef;
        flex-shrink: 0;
    }

    .messages-right-panel .channel-selector-wrapper {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 0.5rem;
        border: 1px solid #e9ecef;
    }

    .messages-right-panel .channel-selector-wrapper label {
        font-size: 0.95rem;
        font-weight: 600;
        color: #495057;
    }

    .messages-right-panel .channel-selector-wrapper .btn-group-lg .btn {
        font-size: 0.95rem;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
    }

    .messages-right-panel .quick-templates-wrapper {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 0.5rem;
        border: 1px solid #e9ecef;
    }

    .messages-right-panel .quick-templates-wrapper label {
        font-size: 0.95rem;
        font-weight: 600;
        color: #495057;
    }

    .messages-right-panel .templates-list .btn {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        margin: 0.25rem;
        border-radius: 1.5rem;
    }

    .messages-right-panel .message-input-wrapper {
        margin-top: 1rem;
    }

    .messages-right-panel .message-input-wrapper .input-group-lg .form-control {
        font-size: 1rem;
        padding: 1rem;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
    }

    .messages-right-panel .message-input-wrapper .input-group-lg .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .messages-right-panel .message-input-wrapper .btn {
        font-size: 1rem;
        padding: 1rem 1.5rem;
        border-radius: 0.5rem;
    }

    .messages-right-panel .emoji-picker {
        position: absolute;
        bottom: 100%;
        left: 0;
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        padding: 1rem;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        z-index: 1000;
        margin-bottom: 0.5rem;
    }

    .messages-right-panel .emoji-grid {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 0.5rem;
        max-width: 320px;
    }

    .messages-right-panel .emoji {
        font-size: 1.5rem;
        cursor: pointer;
        padding: 0.25rem;
        border-radius: 0.25rem;
        transition: background-color 0.2s ease;
        text-align: center;
    }

    .messages-right-panel .emoji:hover {
        background: #f8f9fa;
    }

    .messages-right-panel .whatsapp-quick-actions {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 0.5rem;
        border: 1px solid #e9ecef;
    }

    .messages-right-panel .whatsapp-quick-actions label {
        font-size: 0.95rem;
        font-weight: 600;
        color: #495057;
    }

    .messages-right-panel .whatsapp-quick-actions .btn {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        margin: 0.25rem;
        border-radius: 1.5rem;
    }

    /* File Upload Styles */
    .messages-file-upload-wrapper {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 90%;
        max-width: 600px;
        max-height: 90vh;
        overflow-y: auto;
        z-index: 1050;
        background: #fff;
        border-radius: 0.5rem;
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.3);
    }

    .messages-file-upload-wrapper .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
    }

    .messages-file-upload-wrapper .card-header h6 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #212529;
    }

    .messages-file-upload-wrapper .upload-area {
        border: 2px dashed #e9ecef;
        border-radius: 0.5rem;
        background: #f8f9fa;
        transition: all 0.2s ease;
    }

    .messages-file-upload-wrapper .upload-area:hover {
        border-color: #007bff;
        background: #e3f2fd;
    }

    .messages-file-upload-wrapper .upload-area .dz-message h5 {
        font-size: 1.25rem;
        font-weight: 500;
        color: #6c757d;
    }

    .messages-file-upload-wrapper .upload-area .dz-message p {
        font-size: 1rem;
        color: #6c757d;
    }

    .messages-file-upload-wrapper .upload-area .btn {
        font-size: 1rem;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
    }

    .messages-file-upload-wrapper .upload-options label {
        font-size: 0.95rem;
        font-weight: 600;
        color: #495057;
    }

    .messages-file-upload-wrapper .upload-options .form-control {
        font-size: 0.95rem;
        padding: 0.75rem;
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
    }

    .messages-file-upload-wrapper .upload-actions .btn {
        font-size: 1rem;
        padding: 0.75rem 1.5rem;
        border-radius: 0.5rem;
    }

    /* Responsive Design */
    @media (max-width: 1199.98px) {
        .messages-left-panel .nav-tabs .nav-link {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }
        
        .messages-right-panel .channel-selector-wrapper .btn-group-lg .btn {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 991.98px) {
        .messages-left-panel .contact-item a {
            padding: 0.75rem;
        }
        
        .messages-left-panel .contact-avatar img {
            width: 40px;
            height: 40px;
        }
        
        .messages-right-panel .message-input-wrapper .input-group-lg .form-control {
            padding: 0.75rem;
            font-size: 0.95rem;
        }
        
        .messages-right-panel .message-input-wrapper .btn {
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
        }
    }

    @media (max-width: 767.98px) {
        .messages-left-panel .nav-tabs .nav-link {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }
        
        .messages-left-panel .contact-item a {
            padding: 0.5rem;
        }
        
        .messages-left-panel .contact-avatar img {
            width: 36px;
            height: 36px;
        }
        
        .messages-left-panel .contact-info h6 {
            font-size: 0.9rem;
        }
        
        .messages-left-panel .contact-info small {
            font-size: 0.8rem;
        }
        
        .messages-right-panel .channel-selector-wrapper {
            padding: 0.75rem;
        }
        
        .messages-right-panel .channel-selector-wrapper .btn-group-lg .btn {
            padding: 0.5rem 0.75rem;
            font-size: 0.85rem;
        }
        
        .messages-right-panel .quick-templates-wrapper {
            padding: 0.75rem;
        }
        
        .messages-right-panel .templates-list .btn {
            font-size: 0.8rem;
            padding: 0.4rem 0.8rem;
            margin: 0.2rem;
        }
        
        .messages-right-panel .message-input-wrapper .input-group-lg .form-control {
            padding: 0.5rem;
            font-size: 0.9rem;
        }
        
        .messages-right-panel .message-input-wrapper .btn {
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
        }
        
        .messages-right-panel .emoji-grid {
            grid-template-columns: repeat(6, 1fr);
            max-width: 280px;
        }
        
        .messages-right-panel .emoji {
            font-size: 1.25rem;
        }
        
        .messages-file-upload-wrapper {
            width: 95%;
            margin: 1rem;
        }
    }

    @media (max-width: 575.98px) {
        .messages-left-panel .nav-tabs .nav-link {
            padding: 0.4rem 0.5rem;
            font-size: 0.8rem;
        }
        
        .messages-left-panel .contact-item a {
            padding: 0.4rem;
        }
        
        .messages-left-panel .contact-avatar img {
            width: 32px;
            height: 32px;
        }
        
        .messages-left-panel .contact-info h6 {
            font-size: 0.85rem;
        }
        
        .messages-left-panel .contact-info small {
            font-size: 0.75rem;
        }
        
        .messages-right-panel .channel-selector-wrapper {
            padding: 0.5rem;
        }
        
        .messages-right-panel .channel-selector-wrapper .btn-group-lg .btn {
            padding: 0.4rem 0.6rem;
            font-size: 0.8rem;
        }
        
        .messages-right-panel .quick-templates-wrapper {
            padding: 0.5rem;
        }
        
        .messages-right-panel .templates-list .btn {
            font-size: 0.75rem;
            padding: 0.3rem 0.6rem;
            margin: 0.15rem;
        }
        
        .messages-right-panel .message-input-wrapper .input-group-lg .form-control {
            padding: 0.4rem;
            font-size: 0.85rem;
        }
        
        .messages-right-panel .message-input-wrapper .btn {
            padding: 0.4rem 0.6rem;
            font-size: 0.85rem;
        }
        
        .messages-right-panel .emoji-grid {
            grid-template-columns: repeat(5, 1fr);
            max-width: 240px;
        }
        
        .messages-right-panel .emoji {
            font-size: 1.1rem;
        }
    }

    /* Existing styles continue below */
    .star i {
        color: #ccc;
        font-size: 24px;
        cursor: pointer;
    }

    .star i.active {
        color: gold;
    }

    .feedback-block {
        border: 1px solid #5badff28;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        background-color: #dee0e231
    }

    .star-rating .fa-star {
        font-size: 1.5rem;
        cursor: pointer;
        color: #ccc;
    }

    .star-rating .selected {
        color: #ffc107;
    }

    .btn-group-toggle label {
        cursor: pointer;
    }

    #questions-scrollable {
        max-height: 55vh;
        overflow-y: auto;
        margin-bottom: 1.5rem;
        padding-right: 10px;
        padding-left: 10px;
        border: 1px solid rgba(238, 238, 238, 0.164);
        border-radius: 8px;
        background: inherit;
        scroll-behavior: smooth;
    }

    /* Chrome, Edge, Safari */
    #questions-scrollable::-webkit-scrollbar {
        width: 10px;
        background: #f1f1f1;
        border-radius: 8px;
    }

    #questions-scrollable::-webkit-scrollbar-thumb {
        background: #b3b3b3;
        border-radius: 8px;
        border: 2px solid #fafbfc;
        transition: background 0.3s;
    }

    #questions-scrollable::-webkit-scrollbar-thumb:hover {
        background: #888;
    }

    /* Firefox */
    #questions-scrollable {
        scrollbar-width: thin;
        scrollbar-color: #b3b3b3 #f1f1f1;
    }

    button.feedback-mark-button {
        /* background-color: inherit; */
        /* color: inherit; */
    }

    .score-badge {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background-color: #068a48c4;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.3rem;
        border: 2px solid currentColor;
        /* just to have a visible outline */
    }

    .feedback-mark-button {
        border-radius: 30px;
        min-width: 40px;
        height: 40px;
        font-weight: bold;
        transition: all 0.2s ease;
        border: 2px solid #17a2b8;
    }

    .feedback-mark-button:hover {
        background-color: #17a2b8;
        color: white;
    }

    .feedback-mark-button.active {
        background-color: #17a2b8;
        color: white;
        box-shadow: 0 0 8px rgba(23, 162, 184, 0.6);
    }

    .feedback-stars.editable i {
        color: #ddd;
        cursor: pointer;
        transition: color 0.2s;
    }

    .feedback-stars.editable i.active,
    .feedback-stars.editable i:hover,
    .feedback-stars.editable i.fas,
    .feedback-stars.editable i.hovered {
        color: #ffc107 !important;
        transform: scale(1.1);
    }

    .feedback-stars.editable {
        user-select: none;
    }

    select.form-control {
        max-width: 200px;
        border-radius: 0.5rem;
    }
    .customer-success-block {
        background-color: #8b8b8b17 !important;
    }
    .customer-success-block i.fas {
        font-size: 1.8rem;
    }

    .small-star {
        font-size: 0.8rem !important;
    }

    .h-scroll-wrapper {
    overflow-x: auto;
    white-space: nowrap;
  }

  .feedback-block {
    display: block;
    min-width: 350px;
    max-width: 100%;
    vertical-align: top;
    white-space: normal;
    border-right: 1px solid #ddd;
    padding-right: 10px;
    margin-right: 10px;
  }

  .small-stars.feedback-stars i {
    font-size: 14px;
  }

  /* Optional: Hide default scrollbar in Chrome */
  .h-scroll-wrapper::-webkit-scrollbar {
    height: 6px;
  }

  .h-scroll-wrapper::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.2);
    border-radius: 3px;
  }

  .feedback-scroll-area {
    max-height: 400px; /* adjust height as needed */
    overflow-y: auto;
    padding-right: 8px; /* space for scrollbar */
  }

  .feedback-scroll-area::-webkit-scrollbar {
    width: 6px;
  }

  .feedback-scroll-area::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.2);
    border-radius: 3px;
  }

  .analyze-ai-tab-card {
      border-left: 5px solid #e74c3c;
      border-radius: 10px;
      padding: 20px;
      background-color: #fff;
      box-shadow: 0 2px 6px rgba(0,0,0,0.05);
    }
    .nav-tabs .nav-link.active {
      border: none;
      border-bottom: 2px solid #007bff;
    }
    .nav-tabs .nav-link {
      border: none;
      color: #333;
      font-weight: 500;
    }
</style>
<?php /**PATH E:\GrowSass\application\resources\views/layout/dynamic-css.blade.php ENDPATH**/ ?>