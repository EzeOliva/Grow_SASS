<?php
   $page['meta_title'] = __('lang.error_no_permission_for_resource');
?>

<?php $__env->startSection('content'); ?>
<!-- main content -->
<div class="container-fluid">
    <!-- page content -->
    <div class="row">
        <div class="col-12">
            <div class="permision-denied">
                <img src="<?php echo e(url('/')); ?>/public/images/permission-denied.png" alt="permission denied" /> 
                <div class="x-message"><h2><?php echo e(cleanLang(__('lang.error_no_permission_for_resource'))); ?></h2></div>
            </div>
        </div>
    </div>
    <!--page content -->
</div>
<!--main content -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make(Auth::user() ? 'layout.wrapper' : 'layout.wrapperplain', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\my_apache\application\resources\views/errors/403.blade.php ENDPATH**/ ?>