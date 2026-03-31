<?php
   $page['meta_title'] = __('lang.error_session_timeout');
?>

<?php $__env->startSection('content'); ?>
<!-- main content -->
<div class="container-fluid">
    <!-- page content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="page-notification">
                        <h2 class="font-weight-200"><?php echo e(cleanLang(__('lang.error_session_timeout'))); ?>

                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--page content -->
</div>
<!--main content -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make(Auth::user() ? 'layout.wrapper' : 'layout.wrapperplain', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\GrowSass\application\resources\views/errors/419.blade.php ENDPATH**/ ?>