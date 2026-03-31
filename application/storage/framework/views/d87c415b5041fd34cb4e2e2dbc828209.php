

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
	<div class="row page-titles">
		<div class="col-md-6">
			<h4 class="mb-0"><?php echo e($lineConfig->line_name); ?></h4>
			<p class="text-muted mb-0">Line Configuration Details</p>
		</div>
		<div class="col-md-6 text-right">
			<a href="<?php echo e(route('whatsapp.line-configs.edit', $lineConfig)); ?>" class="btn btn-primary">
				<i class="fas fa-edit me-2"></i>Edit
			</a>
			<a href="<?php echo e(route('whatsapp.line-configs.index')); ?>" class="btn btn-secondary">
				<i class="fas fa-arrow-left me-2"></i>Back to List
			</a>
		</div>
	</div>

	<div class="row">
		<div class="col-md-4">
			<div class="card">
				<div class="card-header">
					<h5 class="card-title mb-0">Line Information</h5>
				</div>
				<div class="card-body">
					<div class="mb-3">
						<small class="text-muted">Line Name</small>
						<div class="fw-bold"><?php echo e($lineConfig->line_name); ?></div>
					</div>
					
					<div class="mb-3">
						<small class="text-muted">Connection</small>
						<div>
							<?php if($lineConfig->connection): ?>
								<a href="<?php echo e(route('whatsapp.connections.show', $lineConfig->connection)); ?>" class="text-decoration-none">
									<?php echo e($lineConfig->connection->connection_name); ?>

								</a>
							<?php else: ?>
								<span class="text-muted">No connection</span>
							<?php endif; ?>
						</div>
					</div>
					
					<div class="mb-3">
						<small class="text-muted">Assignment Mode</small>
						<div>
							<span class="badge bg-info"><?php echo e(ucfirst(str_replace('_', ' ', $lineConfig->assignment_mode))); ?></span>
						</div>
					</div>
					
					<div class="mb-3">
						<small class="text-muted">Auto Assignment</small>
						<div>
							<span class="badge <?php echo e($lineConfig->auto_assign_enabled ? 'bg-success' : 'bg-secondary'); ?>">
								<?php echo e($lineConfig->auto_assign_enabled ? 'Enabled' : 'Disabled'); ?>

							</span>
						</div>
					</div>
					
					<div class="mb-3">
						<small class="text-muted">Status</small>
						<div>
							<span class="badge <?php echo e($lineConfig->is_active ? 'bg-success' : 'bg-secondary'); ?>">
								<?php echo e($lineConfig->is_active ? 'Active' : 'Inactive'); ?>

							</span>
						</div>
					</div>
					
					<?php if($lineConfig->inactivity_timeout_minutes): ?>
						<div class="mb-3">
							<small class="text-muted">Inactivity Timeout</small>
							<div><?php echo e($lineConfig->inactivity_timeout_minutes); ?> minutes</div>
						</div>
					<?php endif; ?>
					
					<?php if($lineConfig->auto_assign_agents && count($lineConfig->auto_assign_agents) > 0): ?>
						<hr>
						<small class="text-muted">Auto-Assign Agents</small>
						<div>
							<?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<span class="badge bg-light text-dark me-1"><?php echo e($agent->first_name); ?> <?php echo e($agent->last_name); ?></span>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		
		<div class="col-md-8">
			<div class="card">
				<div class="card-header">
					<h5 class="card-title mb-0">Recent Tickets</h5>
				</div>
				<div class="card-body">
					<?php if($recentTickets && $recentTickets->count()): ?>
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>ID</th>
										<th>Subject</th>
										<th>Status</th>
										<th>Agent</th>
										<th>Created</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php $__currentLoopData = $recentTickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
										<tr>
											<td>#<?php echo e($ticket->id); ?></td>
											<td>
												<a href="<?php echo e(route('whatsapp.tickets.show', $ticket)); ?>" class="text-decoration-none">
													<?php echo e(strlen($ticket->subject) > 50 ? substr($ticket->subject, 0, 50) . '...' : $ticket->subject); ?>

												</a>
											</td>
											<td>
												<span class="badge bg-<?php echo e($ticket->status === 'open' ? 'warning' : ($ticket->status === 'in_progress' ? 'info' : 'success')); ?>">
													<?php echo e(ucfirst(str_replace('_', ' ', $ticket->status))); ?>

												</span>
											</td>
											<td>
												<?php if($ticket->agent): ?>
													<?php echo e($ticket->agent->first_name); ?> <?php echo e($ticket->agent->last_name); ?>

												<?php else: ?>
													<span class="text-muted">Unassigned</span>
												<?php endif; ?>
											</td>
											<td><?php echo e($ticket->created_at->format('M j, Y')); ?></td>
											<td>
												<a href="<?php echo e(route('whatsapp.tickets.show', $ticket)); ?>" class="btn btn-sm btn-outline-primary">View</a>
											</td>
										</tr>
									<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
								</tbody>
							</table>
						</div>
					<?php else: ?>
						<div class="text-center text-muted py-4">
							<i class="fas fa-ticket-alt fa-3x mb-3"></i>
							<p>No tickets found for this line yet.</p>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\GrowSass\application\resources\views/whatsapp/line-configs/show.blade.php ENDPATH**/ ?>