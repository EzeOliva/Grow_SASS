 <?php $__env->startSection('content'); ?>
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <?php echo $__env->make('pages.project.components.misc.crumbs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <?php echo $__env->make('pages.project.components.misc.actions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    </div>
    <!--page heading-->

    <!--topnav-->
    <?php echo $__env->make('pages.project.components.misc.topnav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <!--topnav-->

    <!-- page content -->
    <div class="row m-t-10" id="projects-tab-single-screen">
        <!--dynamic ajax section-->
        <div class="col-lg-12">
            <div class="card min-h-300">
                <div class="tab-content">
                    <div class="tab-pane active ext-ajax-container" id="projects_ajaxtab" role="tabpanel">
                        <div class="card-body tab-body tab-body-embedded" id="embed-content-container">
                            <!--dynamic content here-->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--page content -->

</div>
<!--page content -->
</div>
<!--main content -->
<!--ajax tab loading-->


<!--dynamically load comments-->
<span class="hidden" id="dynamic-project-content" data-loading-target="embed-content-container"
    data-url="<?php echo e($page['dynamic_url'] ?? ''); ?>">placeholder</span>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tasklist/public_html/application/resources/views/pages/project/dynamic.blade.php ENDPATH**/ ?>