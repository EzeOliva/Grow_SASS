<?php $__currentLoopData = $events; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $event): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="profiletimeline">
    <div class="sl-item">
        <div class="sl-left">
            <?php if($event->event_creator_type =='customer'): ?>
            <img src="<?php echo e(url('/storage/avatars/system/default_avatar.jpg')); ?>" alt="user" class="img-circle" />
            <?php endif; ?>
            <?php if($event->event_creator_type =='admin'): ?>
            <img src="<?php echo e(getUsersAvatar($event->avatar_directory, $event->avatar_filename, $event->event_creatorid)); ?>"
                alt="user" class="img-circle" />
            <?php endif; ?>
            <?php if($event->event_creator_type =='system'): ?>
            <img src="<?php echo e(url('/storage/avatars/system/avatar.jpg')); ?>" alt="user" class="img-circle" />
            <?php endif; ?>
        </div>
        <div class="sl-right">
            <div>
                <?php if($event->event_creator_type =='customer'): ?>
                <?php echo e($event->tenant_name); ?>

                <?php endif; ?>
                <?php if($event->event_creator_type =='admin'): ?>
                <?php echo e($event->first_name); ?>

                <?php endif; ?>
                <?php if($event->event_creator_type =='system'): ?>
                <?php echo app('translator')->get('lang.system'); ?>
                <?php endif; ?>
                <span class="sl-date"><?php echo e(runtimeDateAgo($event->event_created)); ?></span>
                <!--new account created-->
                <?php if($event->event_type == 'account-created'): ?>
                <?php echo $__env->make('landlord.events.components.account_created', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <!--updated account-->
                <?php if($event->event_type == 'account-updated'): ?>
                <?php echo $__env->make('landlord.events.components.account_updated', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <!--changed plan-->
                <?php if($event->event_type == 'changed-plan'): ?>
                <?php echo $__env->make('landlord.events.components.changed_plan', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <!--created a subscription-->
                <?php if($event->event_type == 'created-subscription'): ?>
                <?php echo $__env->make('landlord.events.components.created_subscription', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <!--cancelled subscription-->
                <?php if($event->event_type == 'subscription-cancelled'): ?>
                <?php echo $__env->make('landlord.events.components.subscription_cancelled', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <!--paid subscription-->
                <?php if($event->event_type == 'subscription-paid'): ?>
                <?php echo $__env->make('landlord.events.components.subscription_paid', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <!--updated password-->
                <?php if($event->event_type == 'password-updated'): ?>
                <?php echo $__env->make('landlord.events.components.password_updated', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <!--synced account-->
                <?php if($event->event_type == 'account-synced'): ?>
                <?php echo $__env->make('landlord.events.components.account_synced', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <hr>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php /**PATH /home/tasklist/public_html/application/resources/views/landlord/events/event.blade.php ENDPATH**/ ?>