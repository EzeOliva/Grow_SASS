

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="row page-titles">
        <div class="col-md-6">
            <div class="d-flex align-items-center">
                <div class="mr-3">
                    <i class="fab fa-whatsapp fa-2x text-success"></i>
                </div>
                <div>
                    <h4 class="mb-0">Edit Connection</h4>
                    <p class="text-muted mb-0">Update connection settings</p>
                </div>
            </div>
        </div>
        <div class="col-md-6 text-right">
            <a href="<?php echo e(route('whatsapp.connections.show', $connection)); ?>" class="btn btn-outline-info">
                <i class="fas fa-eye me-2"></i>View Details
            </a>
            <a href="<?php echo e(route('whatsapp.connections.index')); ?>" class="btn btn-outline-secondary ms-2">
                <i class="fas fa-arrow-left me-2"></i>Back to Connections
            </a>
        </div>
    </div>

    <!-- Connection Edit Form -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Edit Connection: <?php echo e($connection->connection_name); ?>

                    </h5>
                </div>
                <div class="card-body">
                    <form action="<?php echo e(route('whatsapp.connections.update', $connection)); ?>" method="POST" id="connection-edit-form">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PUT'); ?>
                        
                        <!-- Basic Information -->
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Basic Information
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="connection_name" class="control-label required">
                                        Connection Name <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control <?php $__errorArgs = ['connection_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="connection_name" 
                                           name="connection_name" 
                                           value="<?php echo e(old('connection_name', $connection->connection_name)); ?>"
                                           placeholder="e.g., Main WhatsApp Business"
                                           required>
                                    <?php $__errorArgs = ['connection_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="connection_type" class="control-label required">
                                        Connection Type <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select <?php $__errorArgs = ['connection_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                            id="connection_type" 
                                            name="connection_type" 
                                            required>
                                        <option value="">Select connection type</option>
                                        <option value="baileys" <?php echo e(old('connection_type', $connection->connection_type) == 'baileys' ? 'selected' : ''); ?>>Baileys (WhatsApp Web)</option>
                                        <option value="twilio" <?php echo e(old('connection_type', $connection->connection_type) == 'twilio' ? 'selected' : ''); ?>>Twilio</option>
                                        <option value="360dialog" <?php echo e(old('connection_type', $connection->connection_type) == '360dialog' ? 'selected' : ''); ?>>360dialog</option>
                                        <option value="gupshup" <?php echo e(old('connection_type', $connection->connection_type) == 'gupshup' ? 'selected' : ''); ?>>Gupshup</option>
                                    </select>
                                    <?php $__errorArgs = ['connection_type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone_number" class="control-label">
                                        Phone Number
                                    </label>
                                    <input type="text" 
                                           class="form-control <?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="phone_number" 
                                           name="phone_number" 
                                           value="<?php echo e(old('phone_number', $connection->phone_number)); ?>"
                                           placeholder="e.g., +1234567890">
                                    <?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Connection Type Specific Fields -->
                        <div id="baileys-fields" class="connection-type-fields" style="display: none;">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h6 class="text-info mb-3">
                                        <i class="fas fa-qrcode me-2"></i>Baileys Configuration
                                    </h6>
                                    <div class="alert alert-info">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Baileys uses WhatsApp Web QR code pairing. No additional configuration needed.
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="twilio-fields" class="connection-type-fields" style="display: none;">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fab fa-twilio me-2"></i>Twilio Configuration
                                    </h6>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="twilio_account_sid" class="control-label">
                                            Account SID
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="twilio_account_sid" 
                                               name="connection_data[account_sid]" 
                                               value="<?php echo e(old('connection_data.account_sid', $connection->connection_data['account_sid'] ?? '')); ?>"
                                               placeholder="Enter your Twilio Account SID">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="twilio_auth_token" class="control-label">
                                            Auth Token
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="twilio_auth_token" 
                                               name="connection_data[auth_token]" 
                                               value="<?php echo e(old('connection_data.auth_token', $connection->connection_data['auth_token'] ?? '')); ?>"
                                               placeholder="Enter your Twilio Auth Token">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="360dialog-fields" class="connection-type-fields" style="display: none;">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h6 class="text-success mb-3">
                                        <i class="fas fa-dialog me-2"></i>360dialog Configuration
                                    </h6>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="360dialog_api_key" class="control-label">
                                            API Key
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="360dialog_api_key" 
                                               name="connection_data[api_key]" 
                                               value="<?php echo e(old('connection_data.api_key', $connection->connection_data['api_key'] ?? '')); ?>"
                                               placeholder="Enter your 360dialog API Key">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="360dialog_webhook_secret" class="control-label">
                                            Webhook Secret
                                        </label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="360dialog_webhook_secret" 
                                               name="connection_data[webhook_secret]" 
                                               value="<?php echo e(old('connection_data.webhook_secret', $connection->connection_data['webhook_secret'] ?? '')); ?>"
                                               placeholder="Enter your webhook secret">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="gupshup-fields" class="connection-type-fields" style="display: none;">
                            <div class="row mt-4">
                                <div class="col-md-12">
                                    <h6 class="text-warning mb-3">
                                        <i class="fas fa-gupshup me-2"></i>Gupshup Configuration
                                    </h6>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gupshup_api_key" class="control-label">
                                            API Key
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="gupshup_api_key" 
                                               name="connection_data[api_key]" 
                                               value="<?php echo e(old('connection_data.api_key', $connection->connection_data['api_key'] ?? '')); ?>"
                                               placeholder="Enter your Gupshup API Key">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="gupshup_app_name" class="control-label">
                                            App Name
                                        </label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="gupshup_app_name" 
                                               name="connection_data[app_name]" 
                                               value="<?php echo e(old('connection_data.app_name', $connection->connection_data['app_name'] ?? '')); ?>"
                                               placeholder="Enter your app name">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Webhook Configuration -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <h6 class="text-primary mb-3">
                                    <i class="fas fa-webhook me-2"></i>Webhook Configuration (Optional)
                                </h6>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="webhook_url" class="control-label">
                                        Webhook URL
                                    </label>
                                    <input type="url" 
                                           class="form-control <?php $__errorArgs = ['webhook_config.webhook_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="webhook_url" 
                                           name="webhook_config[webhook_url]" 
                                           value="<?php echo e(old('webhook_config.webhook_url', $connection->webhook_config['webhook_url'] ?? '')); ?>"
                                           placeholder="https://yourdomain.com/webhook">
                                    <?php $__errorArgs = ['webhook_config.webhook_url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="webhook_secret" class="control-label">
                                        Webhook Secret
                                    </label>
                                    <input type="password" 
                                           class="form-control <?php $__errorArgs = ['webhook_config.webhook_secret'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                           id="webhook_secret" 
                                           name="webhook_config[webhook_secret]" 
                                           value="<?php echo e(old('webhook_config.webhook_secret', $connection->webhook_config['webhook_secret'] ?? '')); ?>"
                                           placeholder="Enter webhook secret for security">
                                    <?php $__errorArgs = ['webhook_config.webhook_secret'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Form Actions -->
                        <div class="row mt-4">
                            <div class="col-md-12">
                                <hr class="my-4">
                                <div class="d-flex justify-content-between align-items-center">
                                    <a href="<?php echo e(route('whatsapp.connections.show', $connection)); ?>" class="btn btn-outline-secondary">
                                        <i class="fas fa-check me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save me-2"></i>Update Connection
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

<!-- Custom Styles -->
<style>
.required {
    font-weight: 500;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-control, .form-select {
    border-radius: 6px;
    border: 1px solid #e0e0e0;
    transition: all 0.3s ease;
    height: 35px;
    font-size: 15px;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-control.is-invalid, .form-select.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #dc3545;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border-radius: 0.5rem;
}

.card-header {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-bottom: 1px solid #dee2e6;
    border-radius: 0.5rem 0.5rem 0 0;
}

.btn {
    border-radius: 6px;
    padding: 0.5rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
}

.text-primary {
    color: #007bff !important;
}

.page-titles {
    margin-bottom: 2rem;
}

.fa-2x {
    font-size: 2em;
}

.connection-type-fields {
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1.5rem;
    margin-top: 1rem;
}

@media (max-width: 768px) {
    .page-titles .text-right {
        text-align: left !important;
        margin-top: 1rem;
    }
    
    .d-flex.justify-content-between {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn {
        width: 100%;
    }
}
</style>

<!-- JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const connectionTypeSelect = document.getElementById('connection_type');
    const connectionTypeFields = document.querySelectorAll('.connection-type-fields');
    
    // Show/hide connection type specific fields
    function toggleConnectionTypeFields() {
        const selectedType = connectionTypeSelect.value;
        
        // Hide all fields first
        connectionTypeFields.forEach(field => {
            field.style.display = 'none';
        });
        
        // Show fields for selected type
        if (selectedType) {
            const targetField = document.getElementById(selectedType + '-fields');
            if (targetField) {
                targetField.style.display = 'block';
            }
        }
    }
    
    // Initial setup
    toggleConnectionTypeFields();
    
    // Listen for changes
    connectionTypeSelect.addEventListener('change', toggleConnectionTypeFields);
    
    // Form validation
    const form = document.getElementById('connection-edit-form');
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const requiredFields = form.querySelectorAll('[required]');
        
        requiredFields.forEach(function(field) {
            if (!field.value.trim()) {
                field.classList.add('is-invalid');
                isValid = false;
            } else {
                field.classList.remove('is-invalid');
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            // Scroll to first error
            const firstError = form.querySelector('.is-invalid');
            if (firstError) {
                firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }
    });
    
    // Real-time validation
    const inputs = form.querySelectorAll('input, select');
    inputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            if (this.hasAttribute('required') && !this.value.trim()) {
                this.classList.add('is-invalid');
            } else {
                this.classList.remove('is-invalid');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.classList.contains('is-invalid')) {
                this.classList.remove('is-invalid');
            }
        });
    });
});
</script>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\GrowSass\application\resources\views/whatsapp/connections/edit.blade.php ENDPATH**/ ?>