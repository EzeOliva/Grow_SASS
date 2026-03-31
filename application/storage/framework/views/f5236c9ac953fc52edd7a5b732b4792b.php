

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
	<div class="row page-titles mb-4">
		<div class="col-md-6">
			<h3 class="text-dark fw-bold mb-2">
				<i class="fas fa-route text-primary me-3"></i>WhatsApp Line Configurations
			</h3>
			<p class="text-muted fs-6 mb-0">Configure support lines and automate ticket assignment workflows</p>
		</div>
		<div class="col-md-6 text-end">
			<a href="<?php echo e(route('whatsapp.line-configs.create')); ?>" class="btn btn-primary btn-lg shadow-sm">
				<i class="fas fa-plus-circle me-2"></i>Create New Line
			</a>
		</div>
	</div>

	<div class="card border-0 shadow-sm">
		<div class="card-header bg-white border-0 py-3">
			<div class="row align-items-center">
				<div class="col-md-6">
					<h5 class="text-dark fw-semibold mb-0">
						<i class="fas fa-list-ul text-info me-2"></i>Line Management
					</h5>
				</div>
				<div class="col-md-6 text-end">
					<span class="badge bg-light text-dark fs-6 px-3 py-2">
						<i class="fas fa-info-circle me-1"></i><?php echo e(isset($lineConfigs) ? $lineConfigs->count() : 0); ?> Lines
					</span>
				</div>
			</div>
		</div>
		<div class="card-body p-0">
			<?php if(isset($lineConfigs) && $lineConfigs->count()): ?>
				<div class="table-responsive">
					<table class="table table-hover mb-0">
						<thead class="table-light">
							<tr>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-route me-2 text-primary"></i>Line Details
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-plug me-2 text-info"></i>Connection
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-cogs me-2 text-warning"></i>Assignment
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-toggle-on me-2 text-success"></i>Status
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold text-center">
									<i class="fas fa-tools me-2 text-secondary"></i>Actions
								</th>
							</tr>
						</thead>
						<tbody>
							<?php $__currentLoopData = $lineConfigs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lineConfig): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr class="border-bottom">
									<td class="py-3 px-4">
										<div class="d-flex align-items-center">
											<div class="line-icon me-3">
												<i class="fas fa-route fa-2x text-primary"></i>
											</div>
											<div>
												<h6 class="text-dark fw-semibold mb-1"><?php echo e($lineConfig->line_name); ?></h6>
												<small class="text-muted">
													<i class="fas fa-clock me-1"></i>
													<?php if($lineConfig->inactivity_timeout_minutes): ?>
														<?php echo e($lineConfig->inactivity_timeout_minutes); ?> min timeout
													<?php else: ?>
														No timeout set
													<?php endif; ?>
												</small>
											</div>
										</div>
									</td>
									<td class="py-3 px-4">
										<?php if($lineConfig->connection): ?>
											<span class="badge bg-info bg-opacity-10 text-info fs-6 px-3 py-2">
												<i class="fas fa-plug me-1"></i><?php echo e($lineConfig->connection->connection_name); ?>

											</span>
										<?php else: ?>
											<span class="badge bg-secondary fs-6 px-3 py-2">
												<i class="fas fa-exclamation-triangle me-1"></i>No Connection
											</span>
										<?php endif; ?>
									</td>
									<td class="py-3 px-4">
										<div class="d-flex flex-column">
											<span class="badge bg-warning bg-opacity-10 text-warning fs-6 px-3 py-2 mb-1">
												<i class="fas fa-cogs me-1"></i><?php echo e(ucfirst(str_replace('_', ' ', $lineConfig->assignment_mode))); ?>

											</span>
											<?php if($lineConfig->auto_assign_enabled): ?>
												<small class="text-success">
													<i class="fas fa-check-circle me-1"></i>Auto-assign enabled
												</small>
											<?php else: ?>
												<small class="text-muted">
													<i class="fas fa-times-circle me-1"></i>Manual assignment
												</small>
											<?php endif; ?>
										</div>
									</td>
									<td class="py-3 px-4">
										<span class="badge <?php echo e($lineConfig->is_active ? 'bg-success' : 'bg-secondary'); ?> fs-6 px-3 py-2">
											<i class="fas <?php echo e($lineConfig->is_active ? 'fa-check-circle' : 'fa-times-circle'); ?> me-1"></i>
											<?php echo e($lineConfig->is_active ? 'Active' : 'Inactive'); ?>

										</span>
									</td>
									<td class="py-3 px-4 text-center">
										<div class="btn-group" role="group">
											<a href="<?php echo e(route('whatsapp.line-configs.show', $lineConfig)); ?>" class="btn btn-outline-primary btn-sm me-2">
												<i class="fas fa-eye me-1"></i>View
											</a>
											<a href="<?php echo e(route('whatsapp.line-configs.edit', $lineConfig)); ?>" class="btn btn-outline-warning btn-sm">
												<i class="fas fa-edit me-1"></i>Edit
											</a>
										</div>
									</td>
								</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
				</div>
				<?php if(method_exists($lineConfigs, 'links')): ?>
					<div class="card-footer bg-white border-0 py-3">
						<?php echo e($lineConfigs->links()); ?>

					</div>
				<?php endif; ?>
			<?php else: ?>
				<div class="text-center py-5">
					<div class="empty-state">
						<i class="fas fa-route fa-4x text-muted mb-4"></i>
						<h5 class="text-dark fw-semibold mb-3">No Line Configurations Found</h5>
						<p class="text-muted fs-6 mb-4">Set up your first support line to start managing WhatsApp tickets efficiently.</p>
						<a href="<?php echo e(route('whatsapp.line-configs.create')); ?>" class="btn btn-primary">
							<i class="fas fa-plus me-2"></i>Create First Line
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

.line-icon {
	width: 48px;
	height: 48px;
	display: flex;
	align-items: center;
	justify-content: center;
	background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
	border-radius: 12px;
}

.table-hover tbody tr:hover {
	background-color: #f8f9fa;
	transform: translateY(-1px);
	box-shadow: 0 2px 8px rgba(0,0,0,0.1);
	transition: all 0.2s ease;
}

.bg-opacity-10 {
	background-color: rgba(13, 202, 240, 0.1) !important;
}

.bg-warning.bg-opacity-10 {
	background-color: rgba(255, 193, 7, 0.1) !important;
}
</style>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\GrowSass\application\resources\views/whatsapp/line-configs/index.blade.php ENDPATH**/ ?>