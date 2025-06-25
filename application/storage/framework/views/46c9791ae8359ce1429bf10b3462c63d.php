<div class="x-heading p-t-10"><i class="mdi mdi-cached"></i><?php echo app('translator')->get('lang.recurring_settings'); ?></div>




<!--not recurring-->
<?php if($task->task_recurring =='no' && !request()->filled('recurring_action') ): ?>
<div class="x-no-result">
    <div class="alert alert-info m-t-40 m-b-40"><?php echo app('translator')->get('lang.task_is_not_recurring'); ?></div>
    <div class="text-center p-t-10">
        <button class="btn btn-info btn-sm ajax-request" data-loading-class="loading-before-centre"
            data-loading-target="card-tasks-left-panel"
            data-url="<?php echo e(urlResource('/tasks/'.$task->task_id.'/recurring-settings?source=modal&recurring_action=edit')); ?>"><?php echo app('translator')->get('lang.make_recurring'); ?></a>
    </div>
</div>
<?php endif; ?>

<!--recurring [show]-->
<?php if($task->task_recurring =='yes' && !request()->filled('recurring_action')): ?>
<div class="card-show-form-data" id="card-task-organisation">

    <!--repeat every-->
    <div class="form-data-row">
        <span class="x-data-title"><?php echo app('translator')->get('lang.repeat_every'); ?> - </span>
        <span class="x-data-content text">
            <?php echo e(runtimeIntervalPlural($task->task_recurring_duration, $task->task_recurring_period)); ?>

        </span>
    </div>

    <!--cycles-->
    <div class="form-data-row">
        <span class="x-data-title"><?php echo app('translator')->get('lang.cycles'); ?> - </span>
        <span class="x-data-content text">
            <?php if($task->task_recurring_cycles == 0): ?>
            <span><?php echo app('translator')->get('lang.infinite'); ?></span>
            <?php else: ?>
            <span><?php echo e($task->task_recurring_cycles); ?></span>
            <?php endif; ?>
        </span>
    </div>

    <!--first task data-->
    <div class="form-data-row">
        <span class="x-data-title"><?php echo app('translator')->get('lang.first_task_date'); ?> - </span>
        <span class="x-data-content text"><?php echo e(runtimeDate($task->task_recurring_next)); ?></span>
    </div>

    <!--copy checklists-->
    <div class="form-data-row">
        <span class="x-data-title"><?php echo app('translator')->get('lang.copy_checklists'); ?> - </span>
        <span class="x-data-content text"><?php echo e(runtimeLang($task->task_recurring_copy_checklists)); ?></span>
    </div>

    <!--copy files-->
    <div class="form-data-row">
        <span class="x-data-title"><?php echo app('translator')->get('lang.copy_files'); ?> - </span>
        <span class="x-data-content text"><?php echo e(runtimeLang($task->task_recurring_copy_files)); ?></span>
    </div>

    <!--automatically assign-->
    <div class="form-data-row">
        <span class="x-data-title"><?php echo app('translator')->get('lang.automatically_assign'); ?> - </span>
        <span class="x-data-content text"><?php echo e(runtimeLang($task->task_recurring_automatically_assign)); ?></span>
    </div>

    <!--edit button-->
    <div class="form-data-row-buttons">
        <button type="button" class="btn waves-effect waves-light btn-xs btn-info ajax-request"
            data-url="<?php echo e(urlResource('/tasks/'.$task->task_id.'/recurring-settings?source=modal&recurring_action=edit')); ?>"
            data-loading-class="loading-before-centre"
            data-loading-target="card-tasks-left-panel"><?php echo app('translator')->get('lang.edit'); ?></button>
    </div>
</div>
<?php endif; ?>

<!--recurring [edit]-->
<?php if(request('recurring_action') == 'edit'): ?>
<div class="p-t-40" id="task-modal-recurring-form">
    <?php echo $__env->make('pages.tasks.components.modals.recurring-settings', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <div class="text-right p-t-10">
        <!--stop-->
        <button class="btn btn-default btn-xs ajax-request" data-loading-class="loading-before-centre"
            data-loading-target="card-tasks-left-panel"
            data-url="<?php echo e(urlResource('/tasks/'.$task->task_id.'/stop-recurring?source=modal')); ?>"><?php echo app('translator')->get('lang.stop_recurring'); ?></button>
        <!--save changes-->
        <button class="btn btn-danger btn-xs ajax-request" data-type="form" data-form-id="task-modal-recurring-form"
            data-ajax-type="post" data-loading-class="loading-before-centre" data-loading-target="card-tasks-left-panel"
            data-url="<?php echo e(urlResource('/tasks/'.$task->task_id.'/recurring-settings?source=modal')); ?>"><?php echo app('translator')->get('lang.save_changes'); ?></button>
    </div>
</div>
<?php endif; ?><?php /**PATH /home/tasklist/public_html/application/resources/views/pages/task/components/recurring.blade.php ENDPATH**/ ?>