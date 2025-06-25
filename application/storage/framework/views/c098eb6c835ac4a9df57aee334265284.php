<!-- action buttons -->
<?php echo $__env->make('pages.tasks.components.misc.list-page-actions', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<!-- action buttons -->

<!--stats panel-->
<?php if(auth()->user()->is_team): ?>
<div id="tasks-stats-wrapper" class="stats-wrapper card-embed-fix">
    <?php if(@count($tasks ?? []) > 0): ?> <?php echo $__env->make('misc.list-pages-stats', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?> <?php endif; ?>
</div>
<?php endif; ?>
<!--stats panel-->

<!--tasks and kanban layouts-->
<?php if(auth()->user()->pref_view_tasks_layout =='list'): ?>
<div class="card-embed-fix  kanban-wrapper">
    <?php echo $__env->make('pages.tasks.components.table.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php else: ?>
<div class="card-embed-fix  kanban-wrapper">
    <?php echo $__env->make('pages.tasks.components.kanban.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<?php endif; ?>
<!--/#tasks and kanban layouts-->

<!--filter-->
<?php if(auth()->user()->is_team): ?>
<?php echo $__env->make('pages.tasks.components.misc.filter-tasks', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>
<!--filter-->

<!--task modal-->
<?php echo $__env->make('pages.task.modal', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php /**PATH /home/tasklist/public_html/application/resources/views/pages/tasks/tabswrapper.blade.php ENDPATH**/ ?>