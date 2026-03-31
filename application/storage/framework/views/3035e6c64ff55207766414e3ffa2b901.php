<?php $__env->startSection('content'); ?>
<div class="container-fluid">
	<div class="row page-titles mb-4">
		<div class="col-md-6">
			<h3 class="text-dark fw-bold mb-2">
				<i class="fas fa-ticket-alt text-primary me-3"></i>WhatsApp Tickets
			</h3>
			<p class="text-muted fs-6 mb-0">Manage and track customer support tickets from WhatsApp conversations</p>
		</div>
		<div class="col-md-6 text-end">
			<a href="<?php echo e(route('whatsapp.tickets.create')); ?>" class="btn btn-primary btn-lg shadow-sm me-2">
				<i class="fas fa-plus-circle me-2"></i>Create Ticket
			</a>
			<a href="<?php echo e(route('whatsapp.dashboard')); ?>" class="btn btn-outline-info btn-lg shadow-sm">
				<i class="fas fa-chart-bar me-2"></i>Dashboard
			</a>
		</div>
	</div>

	<!-- Date Range & Filters -->
	<div class="card border-0 shadow-sm mb-4">
		<div class="card-body">
			<form method="GET" class="row g-3 align-items-end">
				<div class="col-md-2">
					<label for="start_date" class="form-label fw-semibold">Start Date</label>
					<input type="date" class="form-control" id="start_date" name="start_date" 
						value="<?php echo e($dateRange['start'] ?? date('Y-m-d', strtotime('-30 days'))); ?>">
				</div>
				<div class="col-md-2">
					<label for="end_date" class="form-label fw-semibold">End Date</label>
					<input type="date" class="form-control" id="end_date" name="end_date" 
						value="<?php echo e($dateRange['end'] ?? date('Y-m-d')); ?>">
				</div>
				<div class="col-md-2">
					<label for="status_filter" class="form-label fw-semibold">Status</label>
					<select class="form-control" id="status_filter" name="status">
						<option value="">All Statuses</option>
						<option value="open" <?php echo e(($filters['status'] ?? '') === 'open' ? 'selected' : ''); ?>>Open</option>
						<option value="in_progress" <?php echo e(($filters['status'] ?? '') === 'in_progress' ? 'selected' : ''); ?>>In Progress</option>
						<option value="on_hold" <?php echo e(($filters['status'] ?? '') === 'on_hold' ? 'selected' : ''); ?>>On Hold</option>
						<option value="closed" <?php echo e(($filters['status'] ?? '') === 'closed' ? 'selected' : ''); ?>>Closed</option>
					</select>
				</div>
				<div class="col-md-2">
					<label for="channel_filter" class="form-label fw-semibold">Channel</label>
					<select class="form-control" id="channel_filter" name="channel">
						<option value="">All Channels</option>
						<option value="whatsapp" <?php echo e(($filters['channel'] ?? '') === 'whatsapp' ? 'selected' : ''); ?>>WhatsApp</option>
						<option value="email" <?php echo e(($filters['channel'] ?? '') === 'email' ? 'selected' : ''); ?>>Email</option>
					</select>
				</div>
				<div class="col-md-2">
					<label for="agent_filter" class="form-label fw-semibold">Agent</label>
					<select class="form-control" id="agent_filter" name="agent_id">
						<option value="">All Agents</option>
						<?php if(isset($agents)): ?>
							<?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<option value="<?php echo e($agent->id); ?>" <?php echo e(($filters['agent_id'] ?? '') == $agent->id ? 'selected' : ''); ?>>
									<?php echo e($agent->name); ?>

								</option>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						<?php endif; ?>
					</select>
				</div>
				<div class="col-md-2">
					<button type="submit" class="btn btn-primary w-100">
						<i class="fas fa-filter me-2"></i>Filter
					</button>
				</div>
			</form>
		</div>
	</div>

	<!-- Debug Information (remove in production) -->
	<?php if(config('app.debug') || isset($debug_info)): ?>
	<div class="row mb-4">
		<div class="col-12">
			<div class="card border-warning">
				<div class="card-header bg-warning text-dark">
					<strong>Debug Information</strong>
				</div>
				<div class="card-body">
					<p><strong>Tickets Count:</strong> <?php echo e(isset($tickets) ? $tickets->count() : 'Not set'); ?></p>
					<p><strong>Tickets Total:</strong> <?php echo e(isset($tickets) && method_exists($tickets, 'total') ? $tickets->total() : 'Not set'); ?></p>
					<p><strong>KPIs:</strong> <?php echo e(isset($kpis) ? json_encode($kpis) : 'Not set'); ?></p>
					<p><strong>Agents Count:</strong> <?php echo e(isset($agents) ? $agents->count() : 'Not set'); ?></p>
					<?php if(isset($error)): ?>
						<p><strong>Error:</strong> <span class="text-danger"><?php echo e($error); ?></span></p>
					<?php endif; ?>
					<?php if(isset($debug_info)): ?>
						<hr>
						<h6>Additional Debug Info:</h6>
						<p><strong>Database:</strong> <?php echo e($debug_info['database'] ?? 'Not set'); ?></p>
						<p><strong>Table Exists:</strong> <?php echo e($debug_info['table_exists'] ? 'Yes' : 'No'); ?></p>
						<p><strong>Ticket Count:</strong> <?php echo e($debug_info['ticket_count'] ?? 'Not set'); ?></p>
						<p><strong>Raw Tickets:</strong> <pre><?php echo e(json_encode($debug_info['raw_tickets'] ?? [], JSON_PRETTY_PRINT)); ?></pre></p>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<!-- KPI Cards -->
	<?php if(isset($kpis)): ?>
	<div class="row mb-4">
		<div class="col-md-3 mb-3">
			<div class="card border-0 shadow-sm bg-gradient-primary text-white">
				<div class="card-body p-4">
					<div class="d-flex align-items-center">
						<div class="kpi-icon me-3">
							<i class="fas fa-ticket-alt fa-2x"></i>
						</div>
						<div>
							<h4 class="mb-1 fw-bold"><?php echo e($kpis['total_tickets'] ?? 0); ?></h4>
							<p class="mb-0 opacity-75">Total Tickets</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3 mb-3">
			<div class="card border-0 shadow-sm bg-gradient-warning text-white">
				<div class="card-body p-4">
					<div class="d-flex align-items-center">
						<div class="kpi-icon me-3">
							<i class="fas fa-clock fa-2x"></i>
						</div>
						<div>
							<h4 class="mb-1 fw-bold"><?php echo e($kpis['open_tickets'] ?? 0); ?></h4>
							<p class="mb-0 opacity-75">Open Tickets</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3 mb-3">
			<div class="card border-0 shadow-sm bg-gradient-info text-white">
				<div class="card-body p-4">
					<div class="d-flex align-items-center">
						<div class="kpi-icon me-3">
							<i class="fas fa-spinner fa-2x"></i>
						</div>
						<div>
							<h4 class="mb-1 fw-bold"><?php echo e($kpis['in_progress_tickets'] ?? 0); ?></h4>
							<p class="mb-0 opacity-75">In Progress</p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="col-md-3 mb-3">
			<div class="card border-0 shadow-sm bg-gradient-success text-white">
				<div class="card-body p-4">
					<div class="d-flex align-items-center">
						<div class="kpi-icon me-3">
							<i class="fas fa-check-circle fa-2x"></i>
						</div>
						<div>
							<h4 class="mb-1 fw-bold"><?php echo e($kpis['closed_tickets'] ?? 0); ?></h4>
							<p class="mb-0 opacity-75">Resolved</p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<div class="card border-0 shadow-sm">
		<div class="card-header bg-white border-0 py-3">
			<div class="row align-items-center">
				<div class="col-md-6">
					<h5 class="text-dark fw-semibold mb-0">
						<i class="fas fa-list-ul text-info me-2"></i>Ticket Management
					</h5>
				</div>
				<div class="col-md-6 text-end">
					<span class="badge bg-light text-dark fs-6 px-3 py-2">
						<i class="fas fa-info-circle me-1"></i><?php echo e(isset($tickets) ? $tickets->count() : 0); ?> Tickets
					</span>
				</div>
			</div>
		</div>
		<div class="card-body p-0">
			<?php if(isset($tickets) && $tickets->count()): ?>
				<div class="table-responsive">
					<table class="table table-hover mb-0">
						<thead class="table-light">
							<tr>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-ticket-alt me-2 text-primary"></i>Ticket Details
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-user me-2 text-info"></i>Contact
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-user-tie me-2 text-warning"></i>Agent
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-toggle-on me-2 text-success"></i>Status
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold">
									<i class="fas fa-clock me-2 text-secondary"></i>Created
								</th>
								<th class="border-0 py-3 px-4 text-dark fw-semibold text-center">
									<i class="fas fa-cogs me-2 text-dark"></i>Actions
								</th>
							</tr>
						</thead>
						<tbody>
							<?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<tr class="border-bottom">
									<td class="py-3 px-4">
										<div class="d-flex align-items-center">
											<div class="ticket-icon me-3">
												<i class="fas fa-ticket-alt fa-2x text-primary"></i>
											</div>
											<div>
												<h6 class="text-dark fw-semibold mb-1">
													#<?php echo e($ticket->id); ?> - <?php echo e(strlen($ticket->subject) > 50 ? substr($ticket->subject, 0, 50) . '...' : $ticket->subject); ?>

												</h6>
												<?php if($ticket->ticketType): ?>
													<small class="text-muted">
														<i class="fas fa-tag me-1"></i><?php echo e($ticket->ticketType->name); ?>

													</small>
												<?php endif; ?>
											</div>
										</div>
									</td>
									<td class="py-3 px-4">
										<?php if($ticket->contact): ?>
											<div class="d-flex align-items-center">
												<div class="contact-avatar me-2">
													<i class="fas fa-user fa-lg text-info"></i>
												</div>
												<div>
													<div class="fw-semibold"><?php echo e($ticket->contact_name ?? 'Unknown'); ?></div>
													<small class="text-muted"><?php echo e($ticket->contact_phone ?? 'No phone'); ?></small>
												</div>
											</div>
										<?php else: ?>
											<span class="text-muted">No contact</span>
										<?php endif; ?>
									</td>
									<td class="py-3 px-4">
										<?php if($ticket->agent): ?>
											<span class="badge bg-info bg-opacity-10 text-info fs-6 px-3 py-2">
												<i class="fas fa-user-tie me-1"></i><?php echo e($ticket->agent->first_name); ?> <?php echo e($ticket->agent->last_name); ?>

											</span>
										<?php else: ?>
											<span class="badge bg-secondary fs-6 px-3 py-2">
												<i class="fas fa-user-slash me-1"></i>Unassigned
											</span>
										<?php endif; ?>
									</td>
									<td class="py-3 px-4">
										<span class="badge bg-<?php echo e($ticket->status === 'open' ? 'warning' : ($ticket->status === 'in_progress' ? 'info' : 'success')); ?> fs-6 px-3 py-2">
											<i class="fas <?php echo e($ticket->status === 'open' ? 'fa-clock' : ($ticket->status === 'in_progress' ? 'fa-spinner' : 'fa-check-circle')); ?> me-1"></i>
											<?php echo e(ucfirst(str_replace('_', ' ', $ticket->status))); ?>

										</span>
									</td>
									<td class="py-3 px-4">
										<small class="text-muted">
											<i class="fas fa-clock me-1"></i>
											<?php echo e($ticket->created_at ? $ticket->created_at->diffForHumans() : 'Unknown'); ?>

										</small>
									</td>
									<td class="py-3 px-4 text-center">
										<div class="btn-group" role="group">
											<a href="<?php echo e(route('whatsapp.tickets.show', $ticket)); ?>" class="btn btn-outline-primary btn-sm me-2">
												<i class="fas fa-eye me-1"></i>View
											</a>
											<a href="<?php echo e(route('whatsapp.tickets.edit', $ticket)); ?>" class="btn btn-outline-warning btn-sm">
												<i class="fas fa-edit me-1"></i>Edit
											</a>
										</div>
									</td>
								</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
				</div>
				<?php if(method_exists($tickets, 'links')): ?>
					<div class="card-footer bg-white border-0 py-3">
						<?php echo e($tickets->links()); ?>

					</div>
				<?php endif; ?>
			<?php else: ?>
				<div class="text-center py-5">
					<div class="empty-state">
						<i class="fas fa-ticket-alt fa-4x text-muted mb-4"></i>
						<h5 class="text-dark fw-semibold mb-3">No Tickets Found</h5>
						<p class="text-muted fs-6 mb-4">
							<?php if(isset($error)): ?>
								<strong class="text-danger">Error:</strong> <?php echo e($error); ?>

							<?php else: ?>
								Start managing customer support by creating your first ticket.
							<?php endif; ?>
						</p>
						
						<?php if(isset($error)): ?>
							<div class="alert alert-warning mb-4">
								<h6 class="alert-heading">Troubleshooting Tips:</h6>
								<ul class="mb-0 text-start">
									<li>Check if the database connection is working</li>
									<li>Verify that the whatsapp_tickets table exists</li>
									<li>Run the seeder: <code>php artisan whatsapp:seed-tickets</code></li>
									<li>Check the Laravel logs for more details</li>
								</ul>
							</div>
						<?php endif; ?>
						
						<div class="d-flex gap-2 justify-content-center">
							<a href="<?php echo e(route('whatsapp.tickets.create')); ?>" class="btn btn-primary">
								<i class="fas fa-plus me-2"></i>Create First Ticket
							</a>
							<?php if(!isset($error)): ?>
								<a href="<?php echo e(route('whatsapp.dashboard')); ?>" class="btn btn-outline-secondary">
									<i class="fas fa-chart-bar me-2"></i>View Dashboard
								</a>
							<?php endif; ?>
						</div>
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

.ticket-icon, .contact-avatar {
	width: 48px;
	height: 48px;
	display: flex;
	align-items: center;
	justify-content: center;
	background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
	border-radius: 12px;
}

.kpi-icon {
	width: 60px;
	height: 60px;
	display: flex;
	align-items: center;
	justify-content: center;
	background: rgba(255, 255, 255, 0.2);
	border-radius: 12px;
}

.bg-gradient-primary {
	background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.bg-gradient-warning {
	background: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
}

.bg-gradient-info {
	background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
}

.bg-gradient-success {
	background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
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
</style>
<?php $__env->stopSection(); ?>



<?php echo $__env->make('layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\GrowSass\application\resources\views/whatsapp/tickets/index.blade.php ENDPATH**/ ?>