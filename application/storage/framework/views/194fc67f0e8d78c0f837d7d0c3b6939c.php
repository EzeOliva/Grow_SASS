 <?php $__env->startSection('content'); ?>

<!--javascript-->
<script src="public/js/core/messages.js?v=<?php echo e(config('system.versioning')); ?>"></script>

<!-- main content -->
<div class="container-fluid">
    <!-- .chat-row -->
    <div class="chat-main-box">

        <!-- .chat-left-panel -->
        <?php echo $__env->make('pages.messages.components.left-panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <!-- .chat-right-panel -->
        <?php echo $__env->make('pages.messages.components.right-panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


        <!-- file uplaod -->
        <?php echo $__env->make('pages.messages.components.file-upload', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    </div>
    <!-- .chat-right-panel -->
</div>
<!-- /.chat-row -->

</div>
<!--main content -->


<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tasklist/public_html/application/resources/views/pages/messages/wrapper.blade.php ENDPATH**/ ?>