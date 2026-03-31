@extends('layout.wrapper')

@section('content')
<div class="container-fluid">
	<div class="row page-titles">
		<div class="col-md-6">
			<h4 class="mb-0">New Line Configuration</h4>
			<p class="text-muted mb-0">Configure assignment & automation</p>
		</div>
		<div class="col-md-6 text-right">
			<a href="{{ route('whatsapp.line-configs.index') }}" class="btn btn-outline-secondary">
				<i class="fas fa-arrow-left me-2"></i>Back
			</a>
		</div>
	</div>

	<div class="card">
		<div class="card-body">
			<form method="POST" action="{{ route('whatsapp.line-configs.store') }}">
				@csrf
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label required">Connection</label>
							<select name="connection_id" class="form-select" required>
								<option value="">Select a connection</option>
								@foreach($connections as $connection)
									<option value="{{ $connection->id }}">{{ $connection->name }} ({{ $connection->phone_number }})</option>
								@endforeach
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label required">Line Name</label>
							<input name="line_name" class="form-control" required />
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label required">Assignment Mode</label>
							<select name="assignment_mode" class="form-select" required>
								<option value="manual">Manual</option>
								<option value="auto_round_robin">Auto (Round Robin)</option>
								<option value="auto_load_balanced">Auto (Load Balanced)</option>
							</select>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Auto-assign Enabled</label>
							<div class="form-check">
								<input type="checkbox" name="auto_assign_enabled" class="form-check-input" value="1" />
								<label class="form-check-label">Enable automatic assignment</label>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Welcome Message</label>
							<textarea name="welcome_message" class="form-control" rows="3"></textarea>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Closure Message</label>
							<textarea name="closure_message" class="form-control" rows="3"></textarea>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Inactivity Message</label>
							<textarea name="inactivity_message" class="form-control" rows="3"></textarea>
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Auto-close After (minutes)</label>
							<input type="number" name="inactivity_timeout_minutes" class="form-control" min="1" />
						</div>
					</div>
				</div>

				<div class="d-flex justify-content-between mt-3">
					<a href="{{ route('whatsapp.line-configs.index') }}" class="btn btn-outline-secondary">
						<i class="fas fa-check me-2"></i>Cancel
					</a>
					<button class="btn btn-primary" type="submit">
						<i class="fas fa-save me-2"></i>Create
					</button>
				</div>
			</form>
		</div>
	</div>
</div>
@endsection


