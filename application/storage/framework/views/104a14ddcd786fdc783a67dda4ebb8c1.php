<!--main table view-->
<?php echo $__env->make('pages.tasks.components.kanban.kanban', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<!--Update Card Poistion (team only)-->
<?php if(auth()->user()->is_team || config('visibility.tasks_participate')): ?>
<span id="js-tasks-kanban-wrapper" class="hidden" data-position="<?php echo e(url('tasks/update-position')); ?>">placeholder</script>
<?php endif; ?>
<?php /**PATH /home/tasklist/public_html/application/resources/views/pages/tasks/components/kanban/wrapper.blade.php ENDPATH**/ ?>