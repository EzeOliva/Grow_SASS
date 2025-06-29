<?php $__env->startSection('content'); ?>
<!-- main content -->
<div class="container-fluid">
    <!-- General errors and notifications -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="page-notification">
                        <img class="m-b-30" src="<?php echo e(url('/')); ?>/public/images/404.png" alt="404 - Not found" /> 
                        <h2  class="m-b-30 font-weight-200"> <?php echo e($error['message'] ?? ''); ?> </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--General errors and notifications -->
</div>
<!--main content -->
<?php $__env->stopSection(); ?>
<?php echo $__env->make(Auth::user() ? 'layout.wrapper' : 'layout.wrapperplain', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tasklist/public_html/application/resources/views/errors/409.blade.php ENDPATH**/ ?>