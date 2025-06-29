<?php $__env->startSection('settings-page'); ?>
<!--settings-->
<form class="form">

    <h5><?php echo e(cleanLang(__('lang.general_settings'))); ?></h5>
    <div class="line"></div>
    <!--settings2_tasks_manage_dependencies-->
    <div class="form-group row">
        <label
            class="col-sm-12 col-lg-4 text-left control-label col-form-label"><?php echo app('translator')->get('lang.manage_task_dependencies'); ?></label>
        <div class="col-sm-12 col-lg-8">
            <select class="select2-basic form-control form-control-sm select2-preselected"
                id="settings2_tasks_manage_dependencies" name="settings2_tasks_manage_dependencies"
                data-preselected="<?php echo e(config('system.settings2_tasks_manage_dependencies')); ?>">
                <option value="admin-users"><?php echo app('translator')->get('lang.admin'); ?></option>
                <option value="super-users"><?php echo app('translator')->get('lang.admin'); ?> + <?php echo app('translator')->get('lang.project_manager'); ?></option>
                <option value="all-task-users"><?php echo app('translator')->get('lang.all_task_users'); ?></option>
            </select>
        </div>
    </div>

    <h5 class="m-t-50"><?php echo e(cleanLang(__('lang.kanban_board_settings'))); ?></h5>
    <div class="line"></div>
    <div class="p-b-20"><?php echo e(cleanLang(__('lang.kanban_card_front_settings_info'))); ?>.</div>

    <!--show project title-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label"><?php echo e(cleanLang(__('lang.project_title'))); ?></label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_tasks_kanban_project_title" name="settings_tasks_kanban_project_title"
                class="filled-in chk-col-light-blue"
                <?php echo e(runtimePrechecked($settings['settings_tasks_kanban_project_title'] ?? '')); ?>>
            <label for="settings_tasks_kanban_project_title"></label>
        </div>
    </div>

    <!--show client name-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label"><?php echo e(cleanLang(__('lang.client_name'))); ?></label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_tasks_kanban_client_name" name="settings_tasks_kanban_client_name"
                class="filled-in chk-col-light-blue"
                <?php echo e(runtimePrechecked($settings['settings_tasks_kanban_client_name'] ?? '')); ?>>
            <label for="settings_tasks_kanban_client_name"></label>
        </div>
    </div>

    <!--show date created-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label"><?php echo e(cleanLang(__('lang.date_created'))); ?></label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_tasks_kanban_date_created" name="settings_tasks_kanban_date_created"
                class="filled-in chk-col-light-blue"
                <?php echo e(runtimePrechecked($settings['settings_tasks_kanban_date_created'] ?? '')); ?>>
            <label for="settings_tasks_kanban_date_created"></label>
        </div>
    </div>

    <!--show due date-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label"><?php echo e(cleanLang(__('lang.due_date'))); ?></label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_tasks_kanban_date_due" name="settings_tasks_kanban_date_due"
                class="filled-in chk-col-light-blue"
                <?php echo e(runtimePrechecked($settings['settings_tasks_kanban_date_due'] ?? '')); ?>>
            <label for="settings_tasks_kanban_date_due"></label>
        </div>
    </div>
    <!--show start date-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label"><?php echo e(cleanLang(__('lang.start_date'))); ?></label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_tasks_kanban_date_start" name="settings_tasks_kanban_date_start"
                class="filled-in chk-col-light-blue"
                <?php echo e(runtimePrechecked($settings['settings_tasks_kanban_date_start'] ?? '')); ?>>
            <label for="settings_tasks_kanban_date_start"></label>
        </div>
    </div>

    <!--show milestone-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label"><?php echo e(cleanLang(__('lang.milestone'))); ?></label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_tasks_kanban_milestone" name="settings_tasks_kanban_milestone"
                class="filled-in chk-col-light-blue"
                <?php echo e(runtimePrechecked($settings['settings_tasks_kanban_milestone'] ?? '')); ?>>
            <label for="settings_tasks_kanban_milestone"></label>
        </div>
    </div>

    <!--show priority-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label"><?php echo e(cleanLang(__('lang.task_priority'))); ?></label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_tasks_kanban_priority" name="settings_tasks_kanban_priority"
                class="filled-in chk-col-light-blue"
                <?php echo e(runtimePrechecked($settings['settings_tasks_kanban_priority'] ?? '')); ?>>
            <label for="settings_tasks_kanban_priority"></label>
        </div>
    </div>
    <!--show client visibility-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label"><?php echo e(cleanLang(__('lang.client_visibility'))); ?></label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_tasks_kanban_client_visibility"
                name="settings_tasks_kanban_client_visibility" class="filled-in chk-col-light-blue"
                <?php echo e(runtimePrechecked($settings['settings_tasks_kanban_client_visibility'] ?? '')); ?>>
            <label for="settings_tasks_kanban_client_visibility"></label>
        </div>
    </div>

    <!--tags-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label"><?php echo app('translator')->get('lang.tags'); ?></label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_tasks_kanban_tags" name="settings_tasks_kanban_tags"
                class="filled-in chk-col-light-blue"
                <?php echo e(runtimePrechecked($settings['settings_tasks_kanban_tags'] ?? '')); ?>>
            <label for="settings_tasks_kanban_tags"></label>
        </div>
    </div>

    <!--reminders-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label"><?php echo app('translator')->get('lang.reminders'); ?></label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_tasks_kanban_reminder" name="settings_tasks_kanban_reminder"
                class="filled-in chk-col-light-blue"
                <?php echo e(runtimePrechecked($settings['settings_tasks_kanban_reminder'] ?? '')); ?>>
            <label for="settings_tasks_kanban_reminder"></label>
        </div>
    </div>

    <!--buttons-->
    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton"
            class="btn btn-rounded-x btn-danger waves-effect text-left js-ajax-ux-request" data-url="/settings/tasks"
            data-loading-target="" data-ajax-type="PUT" data-type="form"
            data-on-start-submit-button="disable"><?php echo e(cleanLang(__('lang.save_changes'))); ?></button>
    </div>
</form>

<?php if(config('system.settings_type') == 'standalone'): ?>
<!--[standalone] - settings documentation help-->
<a href="https://growcrm.io/documentation" target="_blank" class="btn btn-sm btn-info help-documentation"><i
        class="ti-info-alt"></i>
    <?php echo e(cleanLang(__('lang.help_documentation'))); ?>

</a>
<?php endif; ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('pages.settings.ajaxwrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/tasklist/public_html/application/resources/views/pages/settings/sections/tasks/page.blade.php ENDPATH**/ ?>