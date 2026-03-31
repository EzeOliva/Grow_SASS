<!-- Enhanced Users List Panel with WhatsApp Integration -->
<div class="users-panel">
    <!-- Debug Information -->
    <?php if(config('app.debug')): ?>
    <div class="debug-info" style="background: #f8f9fa; padding: 10px; margin: 10px; border-radius: 5px; font-size: 12px; color: #666;">
        <strong>Debug Info:</strong><br>
        Users Count: <?php echo e(isset($users) ? $users->count() : 'No users variable'); ?><br>
        WhatsApp Connections: <?php echo e(isset($whatsappConnections) ? $whatsappConnections->count() : 'No connections variable'); ?><br>
        <?php if(isset($users) && $users->count() > 0): ?>
            First User: <?php echo e(($users->first()->first_name ?? '') . ' ' . ($users->first()->last_name ?? '')); ?> (ID: <?php echo e($users->first()->id ?? 'No ID'); ?>)<br>
        <?php endif; ?>
        Auth User: <?php echo e(auth()->user()->name ?? 'No auth user'); ?> (ID: <?php echo e(auth()->user()->id ?? 'No ID'); ?>)
    </div>
    <?php endif; ?>

    <!-- Team Tab Content -->
    <div id="team-content" class="tab-content active">
        <!-- Users List -->
        <div class="users-list">
            <?php if(isset($users) && $users->count() > 0): ?>
                <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php if(auth()->check() && $user->id != auth()->user()->id): ?>
                    <div class="user-item" 
                         data-user-id="<?php echo e($user->id); ?>" 
                         data-user-name="<?php echo e(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')); ?>"
                         data-user-email="<?php echo e($user->email ?? 'No email'); ?>"
                         data-user-role="<?php echo e($user->type ?? 'User'); ?>"
                         data-user-position="<?php echo e(ucfirst($user->type ?? 'General')); ?>"
                         data-channel="internal"
                         >
                        
                        <div class="user-avatar">
                            <?php if(isset($user->avatar_filename) && $user->avatar_filename && isset($user->avatar_directory) && $user->avatar_directory): ?>
                                <img src="<?php echo e($user->avatar_directory); ?>/<?php echo e($user->avatar_filename); ?>" alt="<?php echo e(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')); ?>" class="avatar-img">
                            <?php else: ?>
                                <div class="avatar-placeholder">
                                    <?php echo e(strtoupper(substr($user->first_name ?? 'U', 0, 1))); ?>

                                </div>
                            <?php endif; ?>
                            <div class="status-indicator <?php echo e($user->status === 'active' ? 'online' : 'offline'); ?>"></div>
                        </div>
                        
                        <div class="user-details">
                            <div class="user-name"><?php echo e(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')); ?></div>
                            <div class="user-email"><?php echo e($user->email ?? 'No email'); ?></div>
                            <div class="user-role"><?php echo e(ucfirst($user->type ?? 'User')); ?> • <?php echo e(ucfirst($user->type ?? 'General')); ?></div>
                        </div>
                        
                        <div class="user-actions">
                            <div class="last-message-time">12:30</div>
                            <div class="unread-badge">2</div>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <!-- No Users Found -->
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="empty-text">
                        <h6>No Users Found</h6>
                        <p>
                            <?php if(isset($users)): ?>
                                No users are available in the system.
                            <?php else: ?>
                                Users data is not being loaded properly.
                            <?php endif; ?>
                        </p>
                        <div class="debug-details" style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 5px; font-size: 12px;">
                            <strong>Technical Details:</strong><br>
                            Users Variable: <?php echo e(isset($users) ? 'Set' : 'Not Set'); ?><br>
                            Users Count: <?php echo e(isset($users) ? $users->count() : 'N/A'); ?><br>
                            Auth Check: <?php echo e(auth()->check() ? 'Yes' : 'No'); ?><br>
                            Current User ID: <?php echo e(auth()->user()->id ?? 'None'); ?>

                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- WhatsApp Tab Content -->
    <div id="whatsapp-content" class="tab-content">
        <!-- WhatsApp Connections List -->
        <div class="whatsapp-connections-list">
            <?php if(isset($whatsappConnections) && $whatsappConnections->count() > 0): ?>
                <?php $__currentLoopData = $whatsappConnections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $connection): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="whatsapp-connection-item" 
                         data-connection-id="<?php echo e($connection->id); ?>" 
                         data-connection-name="<?php echo e($connection->connection_name); ?>"
                         data-connection-type="<?php echo e($connection->connection_type); ?>"
                         data-phone-number="<?php echo e($connection->phone_number); ?>"
                         data-status="<?php echo e($connection->status); ?>"
                         data-channel="whatsapp"
                         >
                        
                        <div class="connection-avatar">
                            <div class="avatar-placeholder whatsapp">
                                <i class="fab fa-whatsapp"></i>
                            </div>
                            <div class="status-indicator <?php echo e($connection->status === 'connected' ? 'connected' : 'disconnected'); ?>"></div>
                        </div>
                        
                        <div class="connection-details">
                            <div class="connection-name"><?php echo e($connection->connection_name); ?></div>
                            <div class="connection-phone"><?php echo e($connection->phone_number ?? 'No phone number'); ?></div>
                            <div class="connection-type"><?php echo e(ucfirst($connection->connection_type)); ?> • <?php echo e(ucfirst($connection->status)); ?></div>
                        </div>
                        
                        <div class="connection-actions">
                            <div class="connection-status">
                                <span class="status-badge <?php echo e($connection->status === 'connected' ? 'connected' : 'disconnected'); ?>">
                                    <?php echo e($connection->status === 'connected' ? 'Online' : 'Offline'); ?>

                                </span>
                            </div>
                            <?php if($connection->connection_type === 'baileys'): ?>
                                <button class="qr-btn" onclick="showQRCode(<?php echo e($connection->id); ?>)" title="Show QR Code">
                                    <i class="fas fa-qrcode"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php else: ?>
                <!-- No WhatsApp Connections Found -->
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div class="empty-text">
                        <h6>No WhatsApp Connections</h6>
                        <p>Set up your first WhatsApp connection to start messaging.</p>
                        <a href="<?php echo e(route('whatsapp.connections.create')); ?>" class="btn btn-primary btn-sm mt-3">
                            <i class="fas fa-plus me-2"></i>Add Connection
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- WhatsApp Recent Contacts (if available) -->
        <?php if(isset($whatsappTickets) && $whatsappTickets->count() > 0): ?>
        <div class="whatsapp-recent-contacts">
            <div class="section-header">
                <h6>Recent WhatsApp Contacts</h6>
            </div>
            <div class="recent-contacts-list">
                <?php $__currentLoopData = $whatsappTickets->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="contact-item" 
                         data-ticket-id="<?php echo e($ticket->id); ?>"
                         data-contact-name="<?php echo e($ticket->contact_name); ?>"
                         data-contact-phone="<?php echo e($ticket->contact_phone); ?>"
                         data-channel="whatsapp"
                         >
                        
                        <div class="contact-avatar">
                            <div class="avatar-placeholder whatsapp">
                                <i class="fas fa-user"></i>
                            </div>
                        </div>
                        
                        <div class="contact-details">
                            <div class="contact-name"><?php echo e($ticket->contact_name); ?></div>
                            <div class="contact-phone"><?php echo e($ticket->contact_phone); ?></div>
                            <div class="contact-status"><?php echo e(ucfirst($ticket->status)); ?> • <?php echo e($ticket->created_at->diffForHumans()); ?></div>
                        </div>
                        
                        <div class="contact-actions">
                            <div class="unread-badge"><?php echo e($ticket->unread_count ?? 0); ?></div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Simple CSS for Users Panel -->
<style>
.users-panel {
    padding: 0;
    background: var(--white);
}

.debug-info {
    border: 1px solid #dee2e6;
    font-family: monospace;
}

.users-list {
    padding: 0;
}

.user-item {
    display: flex;
    align-items: center;
    padding: var(--space-4) var(--space-6);
    border-bottom: 1px solid var(--border-light);
    position: relative;
    background: var(--bg-primary);
    cursor: pointer;
    transition: var(--transition-normal);
}

.user-item:hover {
    background-color: var(--gray-50);
    transform: translateX(2px);
}

.user-item.active {
    background-color: var(--primary-light);
    border-left: 4px solid var(--primary-color);
    box-shadow: var(--shadow-sm);
}

.whatsapp-connection-item {
    display: flex;
    cursor: pointer;
    transition: all 0.2s ease;
}

.whatsapp-connection-item:hover {
    background-color: rgba(32, 174, 227, 0.05);
}

.whatsapp-connection-item.active {
    background-color: rgba(32, 174, 227, 0.1);
    border-left: 3px solid var(--primary-color);
}

.contact-item {
    display: flex;
    cursor: pointer;
    transition: all 0.2s ease;
}

.contact-item:hover {
    background-color: rgba(32, 174, 227, 0.05);
}

.contact-item.active {
    background-color: rgba(32, 174, 227, 0.1);
    border-left: 3px solid var(--primary-color);
    align-items: center;
    padding: 16px 24px;
    border-bottom: 1px solid var(--border-color);
    position: relative;
    background: var(--white);
}

.user-item.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: var(--primary-color);
    border-radius: 0 2px 2px 0;
}

.user-avatar {
    position: relative;
    margin-right: 16px;
}

.avatar-placeholder {
    width: 52px;
    height: 52px;
    background: var(--primary-gradient);
    border-radius: var(--radius-full);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-inverse);
    font-size: 20px;
    font-weight: 700;
    box-shadow: var(--shadow-md);
    border: 3px solid var(--bg-primary);
}

.avatar-img {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    box-shadow: var(--shadow-light);
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

.status-indicator.offline {
    background: var(--text-muted);
}

.user-details {
    flex: 1;
    min-width: 0;
    margin-left: var(--space-4);
}

.user-name {
    font-size: var(--font-size-base);
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: var(--space-1);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    letter-spacing: -0.025em;
}

.user-email {
    font-size: var(--font-size-sm);
    color: var(--text-secondary);
    margin-bottom: var(--space-1);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 500;
}

.user-role {
    font-size: var(--font-size-xs);
    color: var(--text-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.user-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
    margin-left: 16px;
}

.last-message-time {
    font-size: 12px;
    color: var(--text-muted);
    font-weight: 500;
}

.unread-badge {
    background: var(--primary-color);
    color: var(--white);
    font-size: 11px;
    font-weight: 600;
    padding: 2px 6px;
    border-radius: 10px;
    min-width: 18px;
    text-align: center;
    line-height: 1.2;
}

.empty-state {
    padding: 48px 24px;
    text-align: center;
    color: var(--text-muted);
}

.empty-icon {
    font-size: 48px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.empty-text h6 {
    margin: 0 0 8px 0;
    font-size: 16px;
    font-weight: 600;
    color: var(--text-secondary);
}

.empty-text p {
    margin: 0;
    font-size: 14px;
    color: var(--text-muted);
}

.debug-details {
    text-align: left;
    font-family: monospace;
    font-size: 11px;
}

/* Tab Content */
.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

/* WhatsApp Connections Styles */
.whatsapp-connections-list {
    padding: 0;
}

.whatsapp-connection-item {
    display: flex;
    align-items: center;
    padding: 16px 24px;
    border-bottom: 1px solid var(--border-color);
    cursor: pointer;
    transition: var(--transition);
    position: relative;
    background: var(--white);
}

.whatsapp-connection-item:hover {
    background: var(--light-bg);
    transform: translateX(4px);
}

.whatsapp-connection-item.active {
    background: linear-gradient(135deg, rgba(37, 211, 102, 0.1) 0%, rgba(18, 140, 126, 0.05) 100%);
    border-left: 4px solid #25D366;
}

.connection-avatar {
    position: relative;
    margin-right: 16px;
}

.connection-avatar .avatar-placeholder.whatsapp {
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
}

.connection-avatar .avatar-placeholder.whatsapp i {
    font-size: 20px;
}

.connection-details {
    flex: 1;
    min-width: 0;
}

.connection-name {
    font-size: 16px;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.connection-phone {
    font-size: 14px;
    color: var(--text-secondary);
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.connection-type {
    font-size: 12px;
    color: var(--text-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.connection-actions {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 8px;
    margin-left: 16px;
}

.status-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge.connected {
    background: #25D366;
    color: white;
}

.status-badge.disconnected {
    background: var(--text-muted);
    color: white;
}

.qr-btn {
    width: 32px;
    height: 32px;
    background: var(--primary-color);
    border: none;
    border-radius: var(--radius-sm);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--transition);
    font-size: 14px;
}

.qr-btn:hover {
    background: var(--primary-dark);
    transform: scale(1.05);
}

/* WhatsApp Recent Contacts */
.whatsapp-recent-contacts {
    margin-top: 24px;
    padding: 0 24px;
}

.section-header {
    margin-bottom: 16px;
}

.section-header h6 {
    font-size: 14px;
    font-weight: 600;
    color: var(--text-secondary);
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.recent-contacts-list {
    padding: 0;
}

.contact-item {
    display: flex;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid var(--border-color);
    cursor: pointer;
    transition: var(--transition);
}

.contact-item:hover {
    background: var(--light-bg);
    margin: 0 -24px;
    padding: 12px 24px;
}

.contact-item:last-child {
    border-bottom: none;
}

.contact-avatar {
    margin-right: 12px;
}

.contact-avatar .avatar-placeholder.whatsapp {
    width: 36px;
    height: 36px;
    background: linear-gradient(135deg, #25D366 0%, #128C7E 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    font-weight: 600;
}

.contact-details {
    flex: 1;
    min-width: 0;
}

.contact-details .contact-name {
    font-size: 14px;
    font-weight: 500;
    color: var(--text-primary);
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.contact-details .contact-phone {
    font-size: 12px;
    color: var(--text-secondary);
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.contact-details .contact-status {
    font-size: 11px;
    color: var(--text-muted);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.contact-actions {
    margin-left: 12px;
}
</style><?php /**PATH E:\GrowSass\application\resources\views/pages/messages/components/left-panel.blade.php ENDPATH**/ ?>