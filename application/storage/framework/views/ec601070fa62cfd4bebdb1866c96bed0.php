 <?php $__env->startSection('content'); ?>
<!-- main content -->
<div class="container-fluid saas-home p-l-30 p-r-30">

    <!-- top panel stats -->
    <?php echo $__env->make('landlord.home.components.panel-top-stats', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!-- income chart -->
    <?php echo $__env->make('landlord.home.components.panel-income-chart', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


        <!-- events timeline -->
        <?php echo $__env->make('landlord.home.components.panel-events', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


</div>
<!--main content -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make('landlord.layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tasklist/public_html/application/resources/views/landlord/home/wrapper.blade.php ENDPATH**/ ?>