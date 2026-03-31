<div class="messages-file-upload-wrapper" id="messages_file_upload_wrapper" style="display: none;">
    <div class="card">
        <div class="card-header bg-light">
            <div class="d-flex align-items-center justify-content-between">
                <h6 class="mb-0 font-weight-bold">
                    <i class="fas fa-paperclip mr-2"></i>@lang('lang.file_upload')
                </h6>
                <button type="button" class="btn btn-sm btn-outline-secondary" id="messages_file_upload_close_button">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        
        <div class="card-body">
            <!-- Upload Area -->
            <div class="upload-area mb-4">
                <div class="dropzone dz-clickable" id="fileupload_messages">
                    <div class="dz-default dz-message text-center p-5">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                        <h5 class="text-muted">@lang('lang.drag_drop_files_here')</h5>
                        <p class="text-muted mb-3">@lang('lang.or_click_to_select_files')</p>
                        <button type="button" class="btn btn-outline-primary btn-lg">
                            <i class="fas fa-folder-open mr-2"></i>@lang('lang.browse_files')
                        </button>
                    </div>
                </div>
            </div>

            <!-- File List Preview -->
            <div class="file-preview mb-4" id="file_preview" style="display: none;">
                <h6 class="font-weight-bold mb-3">@lang('lang.selected_files'):</h6>
                <div class="selected-files" id="selected_files">
                    <!-- Dynamic file previews will be added here -->
                </div>
            </div>

            <!-- Upload Options -->
            <div class="upload-options mb-4">
                <div class="row">
                    <div class="col-md-6">
                        <label class="font-weight-bold">@lang('lang.upload_to'):</label>
                        <select class="form-control" id="upload_target">
                            <option value="current_chat">@lang('lang.current_chat')</option>
                            <option value="new_chat">@lang('lang.new_chat')</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="font-weight-bold">@lang('lang.file_type'):</label>
                        <select class="form-control" id="file_type">
                            <option value="all">@lang('lang.all_files')</option>
                            <option value="images">@lang('lang.images')</option>
                            <option value="documents">@lang('lang.documents')</option>
                            <option value="videos">@lang('lang.videos')</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="upload-actions">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="upload-info">
                        <small class="text-muted">
                            <i class="fas fa-info-circle mr-1"></i>@lang('lang.max_file_size'): 10MB
                        </small>
                    </div>
                    <div class="upload-buttons">
                        <button type="button" class="btn btn-outline-secondary btn-lg mr-2" id="clear_files_btn">
                            <i class="fas fa-trash mr-1"></i>@lang('lang.clear_all')
                        </button>
                        <button type="button" class="btn btn-primary btn-lg edit-add-modal-button js-ajax-ux-request" 
                                data-type="form"
                                data-form-id="messages_file_upload_wrapper" 
                                data-ajax-type="post" 
                                data-loading-target="fileupload_messages"
                                data-url="{{ url('messages/fileupload') }}">
                            <i class="fas fa-paper-plane mr-2"></i>@lang('lang.send_files')
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hidden Fields -->
    <div id="file_upload_meta" style="display: none;">
        <input type="hidden" name="message_source" value="{{ auth()->user()->unique_id }}">
        <input type="hidden" class="tracking_message_target" name="message_target" value="team">
        <input type="hidden" class="tracking_timestamp" name="timestamp" id="timestamp_submit_button" value="">
        <input type="hidden" class="tracking_channel" name="channel" value="internal">
        <input type="hidden" class="tracking_connection_id" name="connection_id" value="">
        <input type="hidden" class="tracking_contact_id" name="contact_id" value="">
    </div>
</div>