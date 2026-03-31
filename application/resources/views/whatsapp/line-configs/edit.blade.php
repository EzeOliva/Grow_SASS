@extends('layout.wrapper')

@section('content')
<div class="container-fluid">
	<div class="row page-titles">
		<div class="col-md-6">
			<h4 class="mb-0">Edit Line Configuration</h4>
			<p class="text-muted mb-0">Modify line: {{ $lineConfig->line_name }}</p>
		</div>
		<div class="col-md-6 text-right">
			<a href="{{ route('whatsapp.line-configs.show', $lineConfig) }}" class="btn btn-secondary">
				<i class="fas fa-arrow-left me-2"></i>Back to Details
			</a>
		</div>
	</div>

	<div class="card">
		<div class="card-body">
			<form action="{{ route('whatsapp.line-configs.update', $lineConfig) }}" method="POST">
				@csrf
				@method('PUT')
				
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label for="line_name" class="form-label">Line Name <span class="text-danger">*</span></label>
							<input type="text" class="form-control @error('line_name') is-invalid @enderror" 
								id="line_name" name="line_name" value="{{ old('line_name', $lineConfig->line_name) }}" 
								placeholder="e.g., Support Line, Sales Line" required>
							@error('line_name')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="connection_id" class="form-label">Connection <span class="text-danger">*</span></label>
							<select class="form-control @error('connection_id') is-invalid @enderror" id="connection_id" name="connection_id" required>
								<option value="">Select connection</option>
								@foreach($connections as $connection)
									<option value="{{ $connection->id }}" {{ old('connection_id', $lineConfig->connection_id) == $connection->id ? 'selected' : '' }}>
										{{ $connection->connection_name }} ({{ $connection->connection_type }})
									</option>
								@endforeach
							</select>
							@error('connection_id')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
				</div>

				<div class="row mt-3">
					<div class="col-md-6">
						<div class="form-group">
							<label for="assignment_mode" class="form-label">Assignment Mode <span class="text-danger">*</span></label>
							<select class="form-control @error('assignment_mode') is-invalid @enderror" id="assignment_mode" name="assignment_mode" required>
								<option value="manual" {{ old('assignment_mode', $lineConfig->assignment_mode) == 'manual' ? 'selected' : '' }}>Manual Assignment</option>
								<option value="auto_round_robin" {{ old('assignment_mode', $lineConfig->assignment_mode) == 'auto_round_robin' ? 'selected' : '' }}>Auto Round Robin</option>
								<option value="auto_load_balanced" {{ old('assignment_mode', $lineConfig->assignment_mode) == 'auto_load_balanced' ? 'selected' : '' }}>Auto Load Balanced</option>
							</select>
							@error('assignment_mode')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="auto_assign_enabled" class="form-label">Auto Assignment</label>
							<div class="form-check mt-2">
								<input type="checkbox" class="form-check-input" id="auto_assign_enabled" name="auto_assign_enabled" value="1" 
									{{ old('auto_assign_enabled', $lineConfig->auto_assign_enabled) ? 'checked' : '' }}>
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
								@foreach($agents as $agent)
									<option value="{{ $agent->id }}" 
										{{ in_array($agent->id, old('auto_assign_agents', $lineConfig->auto_assign_agents ?? [])) ? 'selected' : '' }}>
										{{ $agent->first_name }} {{ $agent->last_name }}
									</option>
								@endforeach
							</select>
							<small class="form-text text-muted">Hold Ctrl/Cmd to select multiple agents</small>
						</div>
					</div>
					
					<div class="col-md-6">
						<div class="form-group">
							<label for="inactivity_timeout_minutes" class="form-label">Inactivity Timeout (minutes)</label>
							<input type="number" class="form-control @error('inactivity_timeout_minutes') is-invalid @enderror" 
								id="inactivity_timeout_minutes" name="inactivity_timeout_minutes" 
								value="{{ old('inactivity_timeout_minutes', $lineConfig->inactivity_timeout_minutes) }}" 
								placeholder="1440 (24 hours)" min="1" max="10080">
							@error('inactivity_timeout_minutes')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
							<small class="form-text text-muted">Minutes before auto-closing inactive tickets</small>
						</div>
					</div>
				</div>

				<div class="row mt-3">
					<div class="col-md-4">
						<div class="form-group">
							<label for="welcome_message" class="form-label">Welcome Message</label>
							<textarea class="form-control @error('welcome_message') is-invalid @enderror" 
								id="welcome_message" name="welcome_message" rows="3" 
								placeholder="Message sent when accepting ticket">{{ old('welcome_message', $lineConfig->welcome_message) }}</textarea>
							@error('welcome_message')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
					
					<div class="col-md-4">
						<div class="form-group">
							<label for="closure_message" class="form-label">Closure Message</label>
							<textarea class="form-control @error('closure_message') is-invalid @enderror" 
								id="closure_message" name="closure_message" rows="3" 
								placeholder="Message sent when closing ticket">{{ old('closure_message', $lineConfig->closure_message) }}</textarea>
							@error('closure_message')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
					
					<div class="col-md-4">
						<div class="form-group">
							<label for="inactivity_message" class="form-label">Inactivity Message</label>
							<textarea class="form-control @error('inactivity_message') is-invalid @enderror" 
								id="inactivity_message" name="inactivity_message" rows="3" 
								placeholder="Message sent before auto-close">{{ old('inactivity_message', $lineConfig->inactivity_message) }}</textarea>
							@error('inactivity_message')
								<div class="invalid-feedback">{{ $message }}</div>
							@enderror
						</div>
					</div>
				</div>

				<div class="row mt-4">
					<div class="col-12">
						<div class="form-group">
							<button type="submit" class="btn btn-primary">
								<i class="fas fa-save me-2"></i>Update Line Configuration
							</button>
							<a href="{{ route('whatsapp.line-configs.show', $lineConfig) }}" class="btn btn-secondary ms-2">Cancel</a>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection
