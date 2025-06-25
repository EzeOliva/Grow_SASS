<!--ALL THIRD PART JAVASCRIPTS-->
<script src="public/vendor/js/vendor.footer.js?v=<?php echo e(config('system.versioning')); ?>"></script>

<!--nextloop.core.js-->
<script src="public/js/core/ajax.js?v=<?php echo e(config('system.versioning')); ?>"></script>

<!--MAIN JS - AT END-->
<script src="public/js/core/boot.js?v=<?php echo e(config('system.versioning')); ?>"></script>

<!--EVENTS-->
<script src="public/js/core/events.js?v=<?php echo e(config('system.versioning')); ?>"></script>

<!--CORE-->
<script src="public/js/core/app.js?v=<?php echo e(config('system.versioning')); ?>"></script>

<!--SAAS EVENTS-->
<script src="public/js/landlord/events.js?v=<?php echo e(config('system.versioning')); ?>"></script>
<script src="public/js/landlord/app.js?v=<?php echo e(config('system.versioning')); ?>"></script>


<!--bootstrap-timepicker-->
<script type="text/javascript" src="/public/vendor/js/bootstrap-timepicker/bootstrap-timepicker.js?v=<?php echo e(config('system.versioning')); ?>">
</script>

<!--calendaerfull js [v6.1.13]-->
<script src="/public/vendor/js/fullcalendar/index.global.min.js?v=<?php echo e(config('system.versioning')); ?>"></script>

<!--flash messages-->
<?php if(Session::has('success-notification')): ?>
<span id="js-trigger-session-message" data-type="success"
    data-message="<?php echo e(Session::get('success-notification')); ?>"></span>
<?php endif; ?>
<?php if(Session::has('error-notification')): ?>
<span id="js-trigger-session-message" data-type="warning"
    data-message="<?php echo e(Session::get('error-notification')); ?>"></span>
<?php endif; ?><?php /**PATH /home/tasklist/public_html/application/resources/views/landlord/layout/footerjs.blade.php ENDPATH**/ ?>