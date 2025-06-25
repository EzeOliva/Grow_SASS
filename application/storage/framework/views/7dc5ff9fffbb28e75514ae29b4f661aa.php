<div class="row">
    <!--PROJECTS PENDING-->
    <?php echo $__env->make('pages.home.team.widgets.first-row.projects-pending', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!--PROJECTS COMPLETED-->
    <?php echo $__env->make('pages.home.team.widgets.first-row.tasks-new', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!--INVOICES DUE-->
    <?php echo $__env->make('pages.home.team.widgets.first-row.tasks-inprogress', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <!--INVOICES OVERDUE-->
    <?php echo $__env->make('pages.home.team.widgets.first-row.tasks-feedback', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div><?php /**PATH /home/tasklist/public_html/application/resources/views/pages/home/team/widgets/first-row/wrapper.blade.php ENDPATH**/ ?>