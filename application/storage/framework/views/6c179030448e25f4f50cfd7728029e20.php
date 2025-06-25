<!--CRUMBS CONTAINER (LEFT)-->
<div class="col-md-12 <?php echo e(runtimeCrumbsColumnSize($page['crumbs_col_size'] ?? '')); ?> align-self-center <?php echo e($page['crumbs_special_class'] ?? ''); ?>"
    id="breadcrumbs">
    <h3 class="text-themecolor"><?php echo e($page['heading']); ?></h3>
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><?php echo e(cleanLang(__('lang.app'))); ?></li>
        <?php if(isset($page['crumbs'])): ?>
        <?php $__currentLoopData = $page['crumbs']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $title): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="breadcrumb-item <?php if($loop->last): ?> active active-bread-crumb <?php endif; ?>"><?php echo e($title ?? ''); ?></li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </ol>
</div><?php /**PATH /home/tasklist/public_html/application/resources/views/landlord/misc/crumbs.blade.php ENDPATH**/ ?>