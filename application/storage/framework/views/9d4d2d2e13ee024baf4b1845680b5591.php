<!--GENERAL FEED-->
<?php if(in_array(request('ref-source'), ['home','events']) || !request()->filled('ref-source')): ?>
<div class="m-t-7">
    <?php echo app('translator')->get('lang.event_paid_subscription'); ?>
</div>
<!--optional - payload-->
<div class="m-t-3">
    <?php echo e($event->event_payload_2); ?>

</div>
<!--optional - payload-->
<div class="m-t-3">
    <a href="<?php echo e(url('app-admin/customer').$event->event_customer_id); ?>" target="_blank"><?php echo e($event->event_payload_1); ?></a>
</div>
<?php endif; ?>

<!--CUSTOMER PAGE-->
<?php if(in_array(request('ref-source'), ['customer'])): ?>
<!--title-->
<div class="m-t-7">
    <?php echo app('translator')->get('lang.event_paid_subscription'); ?>
</div>
<!--optional - payload-->
<div class="m-t-3">
    <?php echo e($event->event_payload_2); ?>

</div>
<?php endif; ?><?php /**PATH /home/tasklist/public_html/application/resources/views/landlord/events/components/subscription_paid.blade.php ENDPATH**/ ?>