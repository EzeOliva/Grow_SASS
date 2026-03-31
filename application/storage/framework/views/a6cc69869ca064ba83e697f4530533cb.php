

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
	<div class="row page-titles">
		<div class="col-md-6">
			<h4 class="mb-0">Edit Line Configuration</h4>
			<p class="text-muted mb-0">Modify line: <?php echo e($lineConfig->line_name); ?></p>
		</div>
		<div class="col-md-6 text-right">
			<a href="<?php echo e(route('whatsapp.line-configs.show', $lineConfig)); ?>" class="btn btn-secondary">
				<i class="fas fa-arrow-left me-2"></i>Back to Details
			</a>
		</div>
	</div>

	<div class="card">
		<div class="card-body">
			<form action="<?php echo e(route('whatsapp.line-configs.update', $lineConfig)); ?>" method="POST">
				<?php echo csrf_field(); ?>
				<?php echo method_field('PUT'); ?>
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="line_name" class="form-label">Line Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control <?php $__errorArgs = ['line_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
								id="line_name" name="line_name" value="<?php echo e(old('line_name', $lineConfig->line_name)); ?>" 
								placeholder="e.g., Support Line, Sales Line" required>
							<?php $__errorArgs = ['line_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
								<div class="invalid-feedback"><?php echo e($message); ?></div>
							<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="connection_id" class="form-label">Connection <span class="text-danger">*</span></label>
							<select class="form-control <?php $__errorArgs = ['connection_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="connection_id" name="connection_id" required>
								<option value="">Select connection</option>
								<?php $__currentLoopData = $connections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $connection): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($connection->id); ?>" <?php echo e(old('connection_id', $lineConfig->connection_id) == $connection->id ? 'selected' : ''); ?>>
										<?php echo e($connection->connection_name); ?> (<?php echo e($connection->connection_type); ?>)
									</option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</select>
							<?php $__errorArgs = ['connection_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
								<div class="invalid-feedback"><?php echo e($message); ?></div>
							<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
						</div>
					</div>
				</div>

				<div class="row mt-3">
					<div class="col-md-6">
						<div class="form-group">
							<label for="assignment_mode" class="form-label">Assignment Mode <span class="text-danger">*</span></label>
							<select class="form-control <?php $__errorArgs = ['assignment_mode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="assignment_mode" name="assignment_mode" required>
								<option value="manual" <?php echo e(old('assignment_mode', $lineConfig->assignment_mode) == 'manual' ? 'selected' : ''); ?>>Manual Assignment</option>
								<option value="auto_round_robin" <?php echo e(old('assignment_mode', $lineConfig->assignment_mode) == 'auto_round_robin' ? 'selected' : ''); ?>>Auto Round Robin</option>
								<option value="auto_load_balanced" <?php echo e(old('assignment_mode', $lineConfig->assignment_mode) == 'auto_load_balanced' ? 'selected' : ''); ?>>Auto Load Balanced</option>
							</select>
							<?php $__errorArgs = ['assignment_mode'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
								<div class="invalid-feedback"><?php echo e($message); ?></div>
							<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="auto_assign_enabled" class="form-label">Auto Assignment</label>
							<div class="form-check mt-2">
								<input type="checkbox" class="form-check-input" id="auto_assign_enabled" name="auto_assign_enabled" value="1" 
									<?php echo e(old('auto_assign_enabled', $lineConfig->auto_assign_enabled) ? 'checked' : ''); ?>>
								<label class="form-check-label" for="auto_assign_enabled">
									Enable automatic ticket assignment
								</label>
							</div>
						</div>
					</div>
				</div>

				<div class="row mt-3">
					<div class="col-md-6">
						<div class="form-group">
							<label for="auto_assign_agents" class="form-label">Auto-Assign Agents</label>
							<select class="form-control" id="auto_assign_agents" name="auto_assign_agents[]" multiple>
								<?php $__currentLoopData = $agents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $agent): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
									<option value="<?php echo e($agent->id); ?>" 
										<?php echo e(in_array($agent->id, old('auto_assign_agents', $lineConfig->auto_assign_agents ?? [])) ? 'selected' : ''); ?>>
										<?php echo e($agent->first_name); ?> <?php echo e($agent->last_name); ?>

									</option>
								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</select>
							<small class="form-text text-muted">Hold Ctrl/Cmd to select multiple agents</small>
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="inactivity_timeout_minutes" class="form-label">Inactivity Timeout (minutes)</label>
							<input type="number" class="form-control <?php $__errorArgs = ['inactivity_timeout_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
								id="inactivity_timeout_minutes" name="inactivity_timeout_minutes" 
								value="<?php echo e(old('inactivity_timeout_minutes', $lineConfig->inactivity_timeout_minutes)); ?>" 
								placeholder="1440 (24 hours)" min="1" max="10080">
							<?php $__errorArgs = ['inactivity_timeout_minutes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
								<div class="invalid-feedback"><?php echo e($message); ?></div>
							<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
							<small class="form-text text-muted">Minutes before auto-closing inactive tickets</small>
						</div>
					</div>
				</div>

				<div class="row mt-3">
					<div class="col-md-4">
						<div class="form-group">
							<label for="welcome_message" class="form-label">Welcome Message</label>
							<textarea class="form-control <?php $__errorArgs = ['welcome_message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
								id="welcome_message" name="welcome_message" rows="3" 
								placeholder="Message sent when accepting ticket"><?php echo e(old('welcome_message', $lineConfig->welcome_message)); ?></textarea>
							<?php $__errorArgs = ['welcome_message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
								<div class="invalid-feedback"><?php echo e($message); ?></div>
							<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
						</div>
					</div>
					
					<div class="col-md-4">
						<div class="form-group">
							<label for="closure_message" class="form-label">Closure Message</label>
							<textarea class="form-control <?php $__errorArgs = ['closure_message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
								id="closure_message" name="closure_message" rows="3" 
								placeholder="Message sent when closing ticket"><?php echo e(old('closure_message', $lineConfig->closure_message)); ?></textarea>
							<?php $__errorArgs = ['closure_message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
								<div class="invalid-feedback"><?php echo e($message); ?></div>
							<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
						</div>
					</div>
					
					<div class="col-md-4">
						<div class="form-group">
							<label for="inactivity_message" class="form-label">Inactivity Message</label>
							<textarea class="form-control <?php $__errorArgs = ['inactivity_message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
								id="inactivity_message" name="inactivity_message" rows="3" 
								placeholder="Message sent before auto-close"><?php echo e(old('inactivity_message', $lineConfig->inactivity_message)); ?></textarea>
							<?php $__errorArgs = ['inactivity_message'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
								<div class="invalid-feedback"><?php echo e($message); ?></div>
							<?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
						</div>
					</div>
				</div>

				<div class="row mt-4">
					<div class="col-12">
						<div class="form-group">
							<button type="submit" class="btn btn-primary">
								<i class="fas fa-save me-2"></i>Update Line Configuration
							</button>
							<a href="<?php echo e(route('whatsapp.line-configs.show', $lineConfig)); ?>" class="btn btn-secondary ms-2">Cancel</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layout.wrapper', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\GrowSass\application\resources\views/whatsapp/line-configs/edit.blade.php ENDPATH**/ ?>