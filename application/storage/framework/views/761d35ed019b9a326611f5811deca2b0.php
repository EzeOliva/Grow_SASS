<!--reminder-->
<?php if($payload['has_reminder']): ?>
<?php echo $__env->make('pages.reminders.cards.reminder-show', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php else: ?>
<?php echo $__env->make('pages.reminders.cards.reminder-add', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?><?php /**PATH /home/tasklist/public_html/application/resources/views/pages/reminders/cards/wrapper.blade.php ENDPATH**/ ?>