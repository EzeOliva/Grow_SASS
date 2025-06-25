<!DOCTYPE html>
<html lang="en" class="app-admin">

<!--html header-->
<?php echo $__env->make('landlord.layout.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<!--html header-->

<body id="main-body"
    class="loggedin fix-header card-no-border fix-sidebar <?php echo e(runtimePreferenceLeftmenuPosition(auth()->user()->left_menu_position)); ?> <?php echo e($page['page'] ?? ''); ?>">
    <!--main wrapper-->
    <div id="main-wrapper">


        <!---------------------------------------------------------------------------------------
            [NEXTLOOP}
             always collapse left menu for small devices
            (NB: this code is in the correct place. It must run before menu is added to DOM)
         --------------------------------------------------------------------------------------->

        <!--top nav-->
        <?php echo $__env->make('landlord.layout.topnav', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php echo $__env->make('landlord.layout.leftmenu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <!--top nav-->


        <!--page wrapper-->
        <div class="page-wrapper <?php echo e(runtimeLeftInnerMenu(config('visibility.left_inner_menu'))); ?>">

            <!--left menu-->
            <?php if(config('visibility.left_inner_menu') != ''): ?>
            <div class="left-inner-menu" id="landlord-left-inner-menu">
                <!--settings menu-->
                <?php if(config('visibility.left_inner_menu') == 'settings'): ?>
                <?php echo $__env->make('landlord.settings.leftmenu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
                <!--frontend menu-->
                <?php if(config('visibility.left_inner_menu') == 'frontend'): ?>
                <?php echo $__env->make('landlord.frontend.leftmenu', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endif; ?>
            </div>
            <?php endif; ?>


            <!--overlay-->
            <div class="page-wrapper-overlay js-close-side-panels hidden" data-target=""></div>
            <!--overlay-->

            <!--preloader-->
            <div class="preloader">
                <div class="loader">
                    <div class="loader-loading"></div>
                </div>
            </div>
            <!--preloader-->


            <!-- main content -->
            <?php echo $__env->yieldContent('content'); ?>
            <!-- /#main content -->

        </div>
        <!--page wrapper-->
    </div>

    <!--common modals-->
    <?php echo $__env->make('modals.common-modal-wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('modals.actions-modal-wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>


    <!--js footer-->
    <?php echo $__env->make('landlord.layout.footerjs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</body>

</html><?php /**PATH /home/tasklist/public_html/application/resources/views/landlord/layout/wrapper.blade.php ENDPATH**/ ?>