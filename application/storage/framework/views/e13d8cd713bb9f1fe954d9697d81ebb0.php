<!--CRUMBS CONTAINER (RIGHT)-->
<div class="col-md-12  col-lg-7 p-b-9 align-self-center text-right list-page-actions-containers <?php echo e($page['list_page_actions_size'] ?? ''); ?> <?php echo e($page['list_page_container_class'] ?? ''); ?>"
    id="list-page-actions-container-customer">
    <div id="list-page-actions">
        <div class="btn-group">
            <button type="button" class="btn btn-info dropdown-toggle" data-toggle="dropdown" aria-haspopup="true"
                aria-expanded="false">
                Edit Customer
            </button>
            <!--edit account-->
            <div class="dropdown-menu" x-placement="top-start"
                style="position: absolute; transform: translate3d(0px, -197px, 0px); top: 0px; left: 0px; will-change: transform;">

                <!--edit account-->
                <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0);" data-toggle="modal" data-target="#commonModal"
                    data-url="<?php echo e(urlResource('/app-admin/customers/'.$customer->tenant_id.'/edit')); ?>"
                    data-loading-target="commonModalBody" data-modal-title="<?php echo e(cleanLang(__('lang.edit_account'))); ?>"
                    data-action-url="<?php echo e(urlResource('/app-admin/customers/'.$customer->tenant_id.'?ref=page')); ?>"
                    data-action-method="PUT" data-action-ajax-class=""
                    data-action-ajax-loading-target="customer-td-container"><?php echo app('translator')->get('lang.edit_account'); ?></a>

                <!--cancel subscription-->
                <?php if(config('visibility.has_subscription')): ?>
                <a class="dropdown-item confirm-action-danger" data-confirm-title="<?php echo app('translator')->get('lang.delete_subscription'); ?>"
                    href="javascript:void(0);" data-confirm-text="<?php echo app('translator')->get('lang.delete_subscription_info'); ?>" data-ajax-type="POST"
                    data-url="<?php echo e(urlResource('/app-admin/subscriptions/'.$subscription->subscription_id.'/cancel?ref=page')); ?>">
                    <?php echo app('translator')->get('lang.cancel_subscription'); ?>
                </a>
                <?php endif; ?>

                <!--delete subscription (same as cancel - needed - do not remove)-->
                <?php if(config('subscription_status') == 'awaiting-payment'): ?>
                <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                    data-modal-title="<?php echo app('translator')->get('lang.set_to_active'); ?>"
                    data-url="<?php echo e(urlResource('/app-admin/customers/'.$customer->tenant_id.'/set-active?ref=list')); ?>"
                    data-action-url="<?php echo e(urlResource('/app-admin/customers/'.$customer->tenant_id.'/set-active?ref=list')); ?>"
                    data-loading-target="commonModalBody" data-action-method="POST">
                    <?php echo app('translator')->get('lang.set_to_active'); ?></a>
                <?php endif; ?>


                <!--change subscription plan-->
                <?php if(in_array(config('subscription_status'), ['active', 'free-trial', 'awaiting-payment'])): ?>
                <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0);" data-toggle="modal" data-target="#commonModal"
                    data-url="<?php echo e(urlResource('/app-admin/subscriptions/'.$customer->tenant_id.'/create-edit-plan')); ?>"
                    data-loading-target="commonModalBody" data-modal-title="<?php echo app('translator')->get('lang.change_plan'); ?>"
                    data-action-url="<?php echo e(urlResource('/app-admin/subscriptions/'.$customer->tenant_id.'/create-edit-plan?ref=page')); ?>"
                    data-action-method="POST" data-action-ajax-class=""
                    data-action-ajax-loading-target="customer-td-container"><?php echo app('translator')->get('lang.change_plan'); ?></a>
                <?php endif; ?>

                <!--create subscription-->
                <?php if(!config('visibility.has_subscription')): ?>
                <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0);" data-toggle="modal" data-target="#commonModal"
                    data-url="<?php echo e(urlResource('/app-admin/subscriptions/'.$customer->tenant_id.'/create-edit-plan')); ?>"
                    data-loading-target="commonModalBody" data-modal-title="<?php echo app('translator')->get('lang.create_subscription'); ?>"
                    data-action-url="<?php echo e(urlResource('/app-admin/subscriptions/'.$customer->tenant_id.'/create-edit-plan?ref=page')); ?>"
                    data-action-method="POST" data-action-ajax-class=""
                    data-action-ajax-loading-target="customer-td-container"><?php echo app('translator')->get('lang.create_subscription'); ?></a>
                <?php endif; ?>


                <!--update password-->
                <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                    data-modal-title="<?php echo app('translator')->get('lang.update_password'); ?>"
                    data-url="<?php echo e(urlResource('/app-admin/customers/'.$customer->tenant_id.'/update-password?ref=list')); ?>"
                    data-action-url="<?php echo e(urlResource('/app-admin/customers/'.$customer->tenant_id.'/update-password?ref=list')); ?>"
                    data-loading-target="commonModalBody" data-action-method="POST">
                    <?php echo app('translator')->get('lang.update_password'); ?></a>

                <!--sync customers crm with subscription account details/settings (needed is there is a mismatch)-->
                <a class="dropdown-item edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                    href="javascript:void(0)" data-toggle="modal" data-target="#commonModal"
                    data-modal-title="<?php echo app('translator')->get('lang.sync_account'); ?>"
                    data-action-ajax-class="ajax-request"
                    data-url="<?php echo e(urlResource('/app-admin/customers/'.$customer->tenant_id.'/sync-account?ref=list')); ?>"
                    data-action-url="<?php echo e(urlResource('/app-admin/customers/'.$customer->tenant_id.'/sync-account?ref=list')); ?>"
                    data-loading-target="commonModalBody" data-action-method="POST">
                    <?php echo app('translator')->get('lang.sync_account'); ?></a>

                <!--cancel subscription-->
                <a class="dropdown-item confirm-action-danger" data-confirm-title="<?php echo app('translator')->get('lang.delete_account'); ?>"
                    data-confirm-text="<?php echo app('translator')->get('lang.are_you_sure'); ?>" data-ajax-type="DELETE"
                    data-url="<?php echo e(url('/app-admin/customers/'.$customer->tenant_id.'?source=page')); ?>">
                    <?php echo app('translator')->get('lang.delete_account'); ?>
                </a>

            </div>
        </div>
    </div>
</div>

<!--hidden actions-->
<?php $page['list_page_container_class'] = 'hidden' ; ?>

<!--payments-->
<?php echo $__env->make('landlord.payments.actions.page-actions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tasklist/public_html/application/resources/views/landlord/customer/actions/page-actions.blade.php ENDPATH**/ ?>