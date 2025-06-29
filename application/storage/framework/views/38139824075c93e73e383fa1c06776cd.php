<div class="card count-<?php echo e(@count($proposals ?? [])); ?>" id="proposals-table-wrapper">
    <div class="card-body">
        <div class="table-responsive list-table-wrapper">
            <?php if(@count($proposals ?? []) > 0): ?>
            <table id="proposals-list-table" class="table m-t-0 m-b-0 table-hover no-wrap proposal-list"
                data-page-size="10">
                <thead>
                    <tr>
                        <?php if(config('visibility.proposals_col_checkboxes')): ?>
                        <th class="list-checkbox-wrapper">
                            <!--list checkbox-->
                            <span class="list-checkboxes display-inline-block w-px-20">
                                <input type="checkbox" id="listcheckbox-proposals" name="listcheckbox-proposals"
                                    class="listcheckbox-all filled-in chk-col-light-blue"
                                    data-actions-container-class="proposals-checkbox-actions-container"
                                    data-children-checkbox-class="listcheckbox-proposals">
                                <label for="listcheckbox-proposals"></label>
                            </span>
                        </th>
                        <?php endif; ?>

                        <!--doc_id-->
                        <th class="col_doc_id"><a class="js-ajax-ux-request js-list-sorting" id="sort_doc_id"
                                href="javascript:void(0)"
                                data-url="<?php echo e(urlResource('/proposals?action=sort&orderby=doc_id&sortorder=asc')); ?>"><?php echo app('translator')->get('lang.id'); ?><span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>


                        <!--doc_date_start-->
                        <th class="col_doc_date_start"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_doc_date_start" href="javascript:void(0)"
                                data-url="<?php echo e(urlResource('/proposals?action=sort&orderby=doc_date_start&sortorder=asc')); ?>"><?php echo app('translator')->get('lang.date'); ?><span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>


                        <!--client-->
                        <?php if(config('visibility.col_client')): ?>
                        <th class="col_client"><a class="js-ajax-ux-request js-list-sorting" id="sort_client"
                                href="javascript:void(0)"
                                data-url="<?php echo e(urlResource('/proposals?action=sort&orderby=client&sortorder=asc')); ?>"><?php echo app('translator')->get('lang.proposed_to'); ?><span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <?php endif; ?>


                        <!--doc_title-->
                        <th class="col_doc_title"><a class="js-ajax-ux-request js-list-sorting" id="sort_doc_title"
                            href="javascript:void(0)"
                            data-url="<?php echo e(urlResource('/proposals?action=sort&orderby=doc_title&sortorder=asc')); ?>"><?php echo app('translator')->get('lang.title'); ?><span
                                class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                                
                        <!--value-->
                        <th class="col_value"><a class="js-ajax-ux-request js-list-sorting" id="sort_value"
                                href="javascript:void(0)"
                                data-url="<?php echo e(urlResource('/proposals?action=sort&orderby=value&sortorder=asc')); ?>"><?php echo app('translator')->get('lang.value'); ?><span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <!--created by-->
                        <?php if(config('visibility.col_created_by')): ?>
                        <th class="col_created_by"><a class="js-ajax-ux-request js-list-sorting" id="sort_created_by"
                                href="javascript:void(0)"
                                data-url="<?php echo e(urlResource('/proposals?action=sort&orderby=created_by&sortorder=asc')); ?>"><?php echo app('translator')->get('lang.created_by'); ?><span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>
                        <?php endif; ?>

                        <!--doc_date_end-->
                        <th class="col_doc_date_end"><a class="js-ajax-ux-request js-list-sorting"
                                id="sort_doc_date_end" href="javascript:void(0)"
                                data-url="<?php echo e(urlResource('/proposals?action=sort&orderby=doc_date_end&sortorder=asc')); ?>"><?php echo app('translator')->get('lang.valid_until'); ?><span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>


                        <!--status-->
                        <th class="col_doc_status"><a class="js-ajax-ux-request js-list-sorting" id="sort_doc_status"
                                href="javascript:void(0)"
                                data-url="<?php echo e(urlResource('/proposals?action=sort&orderby=doc_status&sortorder=asc')); ?>"><?php echo app('translator')->get('lang.status'); ?><span
                                    class="sorting-icons"><i class="ti-arrows-vertical"></i></span></a></th>

                        <!--actions-->
                        <?php if(config('visibility.proposals_col_action')): ?>
                        <th class="proposals_col_action"><a href="javascript:void(0)"><?php echo app('translator')->get('lang.action'); ?></a></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="proposals-td-container">
                    <!--ajax content here-->
                    <?php echo $__env->make('pages.proposals.components.table.ajax', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <!--ajax content here-->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="20">
                            <!--load more button-->
                            <?php echo $__env->make('misc.load-more-button', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                            <!--load more button-->
                        </td>
                    </tr>
                </tfoot>
            </table>
            <?php endif; ?> <?php if(@count($proposals ?? []) == 0): ?>
            <!--nothing found-->
            <?php echo $__env->make('notifications.no-results-found', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <!--nothing found-->
            <?php endif; ?>
        </div>
    </div>
</div><?php /**PATH /home/tasklist/public_html/application/resources/views/pages/proposals/components/table/table.blade.php ENDPATH**/ ?>