

<?php $__env->startSection('content'); ?>
<div class="container-fluid">

	<div class="row page-titles mb-4">
		<div class="col-md-6">
			<h3 class="text-dark fw-bold mb-2">
				<i class="fas fa-tags text-primary me-3"></i>WhatsApp Ticket Types
			</h3>
			<p class="text-muted fs-6 mb-0">Organize and categorize your support tickets with custom classifications</p>
		</div>
		<div class="col-md-6 text-end">
			<a href="<?php echo e(route('whatsapp.ticket-types.create')); ?>" class="btn btn-primary btn-lg shadow-sm">
				<i class="fas fa-plus-circle me-2"></i>Create New Type
			</a>
		</div>
	</div>

	<div class="card border-0 shadow-sm">
		<div class="card-header bg-white border-0 py-3">
			<div class="row align-items-center">
				<div class="col-md-6">
					<h5 class="text-dark fw-semibold mb-0">
						<i class="fas fa-list-ul text-info me-2"></i>Ticket Type List
					</h5>
				</div>
				<div class="col-md-6 text-end">
					<span class="badge bg-light text-dark fs-6 px-3 py-2">
						<i class="fas fa-info-circle me-1"></i><?php echo e(isset($ticketTypes) ? $ticketTypes->count() : 0); ?> Types
					</span>
				</div>
			</div>
		</div>
		<div class="card-body p-0">
			<?php if(isset($ticketTypes) && $ticketTypes->count()): ?>
				<div class="table-responsive">
					<table class="table table-hover mb-0">
						<thead class="table-light">
							<tr>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-tag me-2 text-primary"></i>Type Name
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-toggle-on me-2 text-success"></i>Status
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold text-center">
									<i class="fas fa-cogs me-2 text-info"></i>Actions
								</th>
							</tr>
						</thead>
						<tbody>
							<?php $__currentLoopData = $ticketTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr class="border-bottom">
									<td class="py-3 px-4">
										<div class="d-flex align-items-center">
											<div class="color-dot me-3" style="width: 16px; height: 16px; background-color: <?php echo e($type->color ?? '#6c757d'); ?>; border-radius: 50%; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);"></div>
											<div>
												<h6 class="text-dark fw-semibold mb-1"><?php echo e($type->name); ?></h6>
												<?php if($type->description): ?>
													<small class="text-muted"><?php echo e(strlen($type->description) > 60 ? substr($type->description, 0, 60) . '...' : $type->description); ?></small>
												<?php endif; ?>
											</div>
										</div>
									</td>
									<td class="py-3 px-4">
										<span class="badge <?php echo e($type->is_active ? 'bg-success' : 'bg-secondary'); ?> fs-6 px-3 py-2">
											<i class="fas <?php echo e($type->is_active ? 'fa-check-circle' : 'fa-times-circle'); ?> me-1"></i>
											<?php echo e($type->is_active ? 'Active' : 'Inactive'); ?>

										</span>
									</td>
									<td class="py-3 px-4 text-center">
										<div class="btn-group" role="group">
											<a href="<?php echo e(route('whatsapp.ticket-types.show', $type)); ?>" class="btn btn-outline-primary btn-sm me-2">
												<i class="fas fa-eye me-1"></i>View
											</a>
											<a href="<?php echo e(route('whatsapp.ticket-types.edit', $type)); ?>" class="btn btn-outline-warning btn-sm">
												<i class="fas fa-edit me-1"></i>Edit
											</a>
										</div>
									</td>
								</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
				</div>
				<?php if(method_exists($ticketTypes, 'links')): ?>
					<div class="card-footer bg-white border-0 py-3">
						<?php echo e($ticketTypes->links()); ?>

					</div>
				<?php endif; ?>
			<?php else: ?>
				<div class="text-center py-5">
					<div class="empty-state">
						<i class="fas fa-tags fa-4x text-muted mb-4"></i>
						<h5 class="text-dark fw-semibold mb-3">No Ticket Types Found</h5>
						<p class="text-muted fs-6 mb-4">Get started by creating your first ticket type to organize your support workflow.</p>
						<a href="<?php echo e(route('whatsapp.ticket-types.create')); ?>" class="btn btn-primary">
							<i class="fas fa-plus me-2"></i>Create First Type
						</a>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>

<style>
.page-titles h3 {
	font-size: 1.75rem;
	font-weight: 700;
}

.page-titles p {
	font-size: 1rem;
	line-height: 1.5;
}

.card {
	border-radius: 12px;
	overflow: hidden;
}

.card-header {
	border-bottom: 1px solid #e9ecef;
}

.table th {
	font-weight: 600;
	font-size: 0.9rem;
	text-transform: uppercase;
	letter-spacing: 0.5px;
}

.table td {
	vertical-align: middle;
}

.btn-lg {
	padding: 0.75rem 1.5rem;
	font-weight: 600;
	border-radius: 8px;
}

.btn-sm {
	padding: 0.5rem 1rem;
	font-weight: 500;
	border-radius: 6px;
}

.badge {
	font-weight: 500;
	border-radius: 6px;
}

.empty-state {
	padding: 2rem;
}

.color-dot {
	transition: transform 0.2s ease;
}

.color-dot:hover {
	transform: scale(1.2);
}

.table-hover tbody tr:hover {
	background-color: #f8f9fa;
	transform: translateY(-1px);
	box-shadow: 0 2px 8px rgba(0,0,0,0.1);
	transition: all 0.2s ease;
}
</style>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\GrowSass\application\resources\views/whatsapp/ticket-types/index.blade.php ENDPATH**/ ?>