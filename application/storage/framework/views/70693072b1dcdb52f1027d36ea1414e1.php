<div class="card count-<?php echo e(@count($customers)); ?>" id="customer-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            <?php if(@count($customers) > 0): ?>
            <table id="customer-list-table" class="table m-t-0 m-b-0 table-hover no-wrap tenant-list"
                data-page-size="10">
                <thead>
                    <tr>
                        <!--tenant_id-->
                        <th class="tenants_col_tenant_id"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_tenant_id" href="javascript:void(0)"
                                data-url="<?php echo e(urlResource('/app-admin/customers?action=sort&orderby=tenant_id&sortorder=asc')); ?>"><?php echo app('translator')->get('lang.id'); ?><span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--tenant_name-->
                        <th class="tenants_col_tenant_name"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_tenant_name" href="javascript:void(0)"
                                data-url="<?php echo e(urlResource('/app-admin/customers?action=sort&orderby=tenant_name&sortorder=asc')); ?>"><?php echo app('translator')->get('lang.name'); ?><span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--tenant_created-->
                        <th class="tenants_col_tenant_created"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_tenant_created" href="javascript:void(0)"
                                data-url="<?php echo e(urlResource('/app-admin/customers?action=sort&orderby=tenant_created&sortorder=asc')); ?>"><?php echo app('translator')->get('lang.created'); ?><span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--domain-->
                        <th class="tenants_col_domain"><a class="js-ajax-ux-request js-list-sorting" id="sort_domain"
                                href="javascript:void(0)"
                                data-url="<?php echo e(urlResource('/app-admin/customers?action=sort&orderby=domain&sortorder=asc')); ?>"><?php echo app('translator')->get('lang.account_url'); ?><span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--package_name-->
                        <th class="tenants_col_package_name"><a class="js-ajax-ux-request js-list-sorting" id="sort_domain"
                                href="javascript:void(0)"
                                data-url="<?php echo e(urlResource('/app-admin/customers?action=sort&orderby=package_name&sortorder=asc')); ?>"><?php echo app('translator')->get('lang.package'); ?><span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--tenant_package_type-->
                        <th class="tenants_col_tenant_package_type"><a class="js-ajax-ux-request js-list-sorting" id="sort_domain"
                                href="javascript:void(0)"
                                data-url="<?php echo e(urlResource('/app-admin/customers?action=sort&orderby=tenant_package_type&sortorder=asc')); ?>"><?php echo app('translator')->get('lang.type'); ?><span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--tenant_status-->
                        <th class="tenants_col_tenant_status"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_tenant_status" href="javascript:void(0)"
                                data-url="<?php echo e(urlResource('/app-admin/customers?action=sort&orderby=tenant_status&sortorder=asc')); ?>"><?php echo app('translator')->get('lang.status'); ?><span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <!--actions-->
                        <th class="tenants_col_action"><a href="javascript:void(0)"><?php echo app('translator')->get('lang.action'); ?></a></th>
                    </tr>
                </thead>
                <tbody id="customer-td-container">
                    <!--ajax content here-->
                    <?php echo $__env->make('landlord.customers.table.ajax', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <!--ajax content here-->
                </tbody>
                <tbody class="border-0">
                    <tr>
                        <td colspan="20">
                            <!--load more button-->
                            <?php echo $__env->make('misc.load-more-button', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <!--load more button-->
                        </td>
                    </tr>
                </tbody>
            </table>
            <?php endif; ?> <?php if(@count($customers) == 0): ?>
            <!--nothing found-->
            <?php echo $__env->make('notifications.no-results-found', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <!--nothing found-->
            <?php endif; ?>
        </div>
    </div>
</div><?php /**PATH /home/tasklist/public_html/application/resources/views/landlord/customers/table/table.blade.php ENDPATH**/ ?>