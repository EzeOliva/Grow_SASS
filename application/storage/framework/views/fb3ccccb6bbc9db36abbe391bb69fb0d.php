<div class="card-checklist" id="card-checklist">
    <div class="x-heading clearfix">
        <span class="pull-left"><i class="mdi mdi-checkbox-marked"></i><?php echo e(cleanLang(__('lang.checklist'))); ?></span>
        <span class="pull-right p-t-5" id="card-checklist-progress"><?php echo e($progress['completed']); ?></span>
    </div>
    <div class="progress" id="card-checklist-progress-container">
            <?php echo $__env->make('pages.task.components.progressbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    </div>
    <div class="x-content" id="card-checklists-container" data-progress-bar="hidden" data-type="form" data-ajax-type="post" data-form-id="card-checklists-container" data-url="<?php echo e(url('/tasks/update-checklist-positions')); ?>">
        <!--dynamic content here-->
    </div>
    <?php if($task->permission_edit_task): ?>
    <div class="x-action">
        <a href="javascript:void(0)" class="js-card-checklist-toggle" id="card-checklist-add-new"
            data-action-url="<?php echo e(urlResource('/tasks/'.$task->task_id.'/add-checklist')); ?>" data-toggle="new"><?php echo e(cleanLang(__('lang.add_new_item'))); ?></a>
    </div>
    <?php endif; ?>
</div><?php /**PATH /home/tasklist/public_html/application/resources/views/pages/task/components/checklists.blade.php ENDPATH**/ ?>