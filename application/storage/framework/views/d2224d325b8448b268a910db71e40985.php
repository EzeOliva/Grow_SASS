<!DOCTYPE html>
<html lang="en" class="app-admin logged-out">

<!--html header-->
<?php echo $__env->make('landlord.layout.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<!--html header-->

<body class="<?php echo e($page['page'] ?? ''); ?>">
    <!--preloader-->
    <div class="preloader">
        <div class="loader">
            <div class="loader-loading"></div>
        </div>
    </div>
    <!--preloader-->

    <!--main content-->
    <div id="main-wrapper">
        <?php echo $__env->yieldContent('content'); ?>
    </div>
</body>

<?php echo $__env->make('landlord.layout.footerjs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</html><?php /**PATH /home/tasklist/public_html/application/resources/views/landlord/layout/wrapperplain.blade.php ENDPATH**/ ?>