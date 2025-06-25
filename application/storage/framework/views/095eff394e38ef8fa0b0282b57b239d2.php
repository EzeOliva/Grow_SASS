 <?php $__env->startSection('content'); ?>
<!-- main content -->
<div class="container-fluid">

    <!--page heading-->
    <div class="row page-titles">

        <!-- bread crumbs -->
        <?php echo $__env->make('landlord.misc.crumbs', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <!-- bread crumbs -->

        <!-- action buttons -->
        <?php echo $__env->make('landlord.customer.actions.page-actions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <!-- action buttons -->

    </div>
    <!--page heading-->


    <!-- Row -->
    <div class="row">
        <!-- Column -->
        <div class="col-lg-4 col-xlg-3 col-md-5">
            <div class="card">
                <div class="card-body">
                    <center class="m-t-30">
                        <h3 class="card-title m-t-10 m-b-10"><?php echo e($customer->tenant_name); ?></h3>
                        <h5 class="card-subtitle"><a href="https://<?php echo e($customer->domain); ?>"
                                target="_blank"><?php echo e($customer->domain); ?></a></h5>
                        <div class="row text-center justify-content-md-center">
                            <span
                                class="label label-lg <?php echo e(runtimeCustomerStatusColors($customer->tenant_status)); ?> words-uppercase-first"
                                id="customer_account_status"><?php echo e(runtimeCustomerStatusLang($customer->tenant_status)); ?></span>
                        </div>
                    </center>
                </div>
                <div>
                    <hr class="m-t-15 m-b-15">
                </div>
                <div class="card-body p-t-0">
                    <small class="text-muted"><?php echo app('translator')->get('lang.email'); ?></small>
                    <h6><?php echo e($customer->tenant_email); ?></h6>
                    <small class="text-muted p-t-30 db"><?php echo app('translator')->get('lang.customer_signup_date'); ?></small>
                    <h6><?php echo e(runtimeDate($customer->tenant_created)); ?></h6>
                    <div class="m-l--25 m-r--25">
                        <hr>
                    </div>
                    <small class="text-muted p-t-10 db"><?php echo app('translator')->get('lang.package'); ?></small>
                    <h6><?php echo e($customer->package_name ?? '---'); ?></h6>
                    <small class="text-muted p-t-30 db"><?php echo app('translator')->get('lang.package_type'); ?></small>
                    <h6>
                        <?php if($customer->subscription_type == 'free'): ?>
                        <?php echo app('translator')->get('lang.free_package'); ?>
                        <?php elseif($customer->subscription_type == 'paid'): ?>
                        <?php echo app('translator')->get('lang.paid_package'); ?>
                        <?php else: ?>
                        ---
                        <?php endif; ?>
                    </h6>
                    <small class="text-muted p-t-30 db"><?php echo app('translator')->get('lang.subscription_start_date'); ?></small>
                    <h6><?php echo e(runtimeDate($customer->subscription_created)); ?></h6>
                    <?php if($customer->subscription_type == 'paid'): ?>
                    <small class="text-muted p-t-30 db"><?php echo app('translator')->get('lang.free_trial'); ?></small>
                    <h6>
                        <?php if($customer->subscription_status == 'free-trial'): ?>
                        <span class="uc-words"><?php echo app('translator')->get('lang.yes'); ?></span>
                        <?php else: ?>
                        <span class="uc-words"><?php echo app('translator')->get('lang.no'); ?></span>
                        <?php endif; ?>
                    </h6>
                    <?php if($customer->subscription_status == 'free-trial'): ?>
                    <small class="text-muted p-t-30 db"><?php echo app('translator')->get('lang.trial_end_date'); ?></small>
                    <h6>
                        <span class="uc-words"><?php echo e(runtimeDate($customer->subscription_trial_end)); ?></span>
                    </h6>
                    <?php endif; ?>
                    <?php endif; ?>
                    <small class="text-muted p-t-30 db"><?php echo app('translator')->get('lang.database_name'); ?></small>
                    <h6><?php echo e($customer->database); ?></h6>
                </div>
            </div>
        </div>
        <!-- Column -->
        <!-- Column -->
        <div class="col-lg-8 col-xlg-9 col-md-7">
            <div class="card">
                <!-- MENU -->
                <ul class="nav nav-tabs profile-tab" role="tablist">
                    <!--timeline-->
                    <li class="nav-item"> <a class="nav-link active cursor-pointer ajax-request" data-toggle="tab"
                            data-url="<?php echo e(url('app-admin/events?ref-source=customer&ref=customer&source=ext&action=load&event_customer_id='.$customer->tenant_id)); ?>"
                            data-loading-target="dynamic-content-container" role="tab"><?php echo app('translator')->get('lang.timeline'); ?></a>
                    </li>
                    <!--subscription-->
                    <li class="nav-item"> <a class="nav-link cursor-pointer ajax-request" data-toggle="tab"
                            data-url="<?php echo e(url('app-admin/customers/'.$customer->tenant_id.'/subscription?source=ext')); ?>"
                            data-loading-target="dynamic-content-container" role="tab"><?php echo app('translator')->get('lang.subscription'); ?></a>
                    </li>
                    <!--payments-->
                    <li class="nav-item dropdown <?php echo e($page['tabmenu_more'] ?? ''); ?>">
                        <a class="nav-link dropdown-toggle  tabs-menu-item" data-loading-class="loading-tabs"
                            data-toggle="dropdown" href="javascript:void(0)" role="button" aria-haspopup="true"
                            id="tabs-menu-billing" aria-expanded="false">
                            <span class="hidden-xs-down"><?php echo e(cleanLang(__('lang.payments'))); ?></span>
                        </a>
                        <div class="dropdown-menu" x-placement="bottom-start" id="fx-topnav-dropdown">

                            <!--online-->
                            <a class="dropdown-item tabs-menu-item   js-dynamic-url js-ajax-ux-request <?php echo e($page['tabmenu_tickets'] ?? ''); ?>"
                                id="tabs-menu-tickets" data-toggle="tab" data-loading-class="loading-tabs"
                                data-loading-target="dynamic-content-container"
                                data-url="<?php echo e(url('app-admin/payments?source=ext&payment_tenant_id='.$customer->tenant_id)); ?>"
                                href="javascript:void(0);" role="tab"><?php echo e(cleanLang(__('lang.online'))); ?></a>

                            <!--offline-->
                            <a class="dropdown-item tabs-menu-item   js-dynamic-url js-ajax-ux-request <?php echo e($page['tabmenu_tickets'] ?? ''); ?>"
                                id="tabs-menu-tickets" data-toggle="tab" data-loading-class="loading-tabs"
                                data-loading-target="dynamic-content-container"
                                data-url="<?php echo e(url('app-admin/offline-payments?source=ext&proof_tenant_id='.$customer->tenant_id)); ?>"
                                href="javascript:void(0);" role="tab"><?php echo e(cleanLang(__('lang.offline'))); ?></a>
                        </div>
                    </li>

                    <!--email settings-->
                    <li class="nav-item"> <a class="nav-link cursor-pointer ajax-request" data-toggle="tab"
                            data-url="<?php echo e(url('app-admin/customers/'.$customer->tenant_id.'/email?source=page')); ?>"
                            data-loading-target="dynamic-content-container" role="tab"><?php echo app('translator')->get('lang.email_settings'); ?></a>
                    </li>
                </ul>
                <!-- Tab panes -->
                <div class="tab-content">
                    <div class="tab-pane active" id="home" role="tabpanel">
                        <div class="card-body dynamic-content-container">
                            <div id="dynamic-content-container">
                                <!--dynamic events-->
                            </div>
                            <!--load more button-->
                            <?php echo $__env->make('landlord.misc.load-more-dynamic-button', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Column -->
    </div>
    <!-- Row -->

</div>
<!--main content -->

<!--dynamically load timeline events-->
<script src="public/js/landlord/dynamic/timeline.js?v=<?php echo e(config('system.versioning')); ?>"
    id="dynamic-load-timeline-events" data-loading-target="customer-content-container" data-progress-bar="hidden"
    data-url="<?php echo e(url('app-admin/events?ref-source=customer&source=customer&event_customer_id='.$customer->tenant_id)); ?>">
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('landlord.layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tasklist/public_html/application/resources/views/landlord/customer/wrapper.blade.php ENDPATH**/ ?>