<!--WIDGET NOTES: stats displayed on top of result tables and list pages-->
<div class="card-group table-stats-cards  <?php echo e(runtimePreferenceStatsPanelPosition(auth()->user()->stats_panel_position)); ?>" id="list-pages-stats-widget">
    <?php echo $__env->make('misc.list-pages-stats-content', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div><?php /**PATH /home/tasklist/public_html/application/resources/views/misc/list-pages-stats.blade.php ENDPATH**/ ?>