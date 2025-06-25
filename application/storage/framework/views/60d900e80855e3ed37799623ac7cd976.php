    <!-- Row -->
    <div class="row">
        <!-- Column -->
        <div class="col-12">
            <div class="card">
                <div class="card-body dynamic-content-container">

                    <h5 class="card-title m-b-30 align-self-center"><?php echo e(cleanLang(__('lang.events'))); ?></h5>

                    <div id="dynamic-content-container">
                        <!--dynamic events-->
                    </div>

                    <!--load more button-->
                    <?php echo $__env->make('landlord.misc.load-more-dynamic-button', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                </div>
            </div>
        </div>
        <!-- Column -->
    </div>
    <!-- Row -->


    <!--dynamically load timeline events-->
    <script src="public/js/landlord/dynamic/timeline.js?v=<?php echo e(config('system.versioning')); ?>"
        id="dynamic-load-timeline-events" data-loading-target="dynamic-content-container" data-progress-bar="hidden"
        data-url="<?php echo e(url('app-admin/events?ref-source=home')); ?>">
    </script><?php /**PATH /home/tasklist/public_html/application/resources/views/landlord/home/components/panel-events.blade.php ENDPATH**/ ?>