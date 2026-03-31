<div class="team-general-alerts-analysis">
    <div class="card mb-3">
        <div class="card-header bg-warning text-dark">
            <h6><i class="fas fa-exclamation-triangle"></i> General Alerts</h6>
        </div>
        <div class="card-body">
            <h5>Overdue Tasks</h5>
            <ul>
                @forelse($overdueTasks as $task)
                    <li>{{ $task->task_title ?? $task->title }} <span class="text-muted">(Due: {{ $task->task_date_due ?? $task->due_date }})</span></li>
                @empty
                    <li class="text-muted">None</li>
                @endforelse
            </ul>
            <h5>No Tasks In Progress</h5>
            @if($noTasksInProgress)
                <div class="alert alert-warning">This member has no tasks in progress.</div>
            @else
                <div class="alert alert-success">This member has tasks in progress.</div>
            @endif
            <button class="btn btn-primary ai-analyze-btn mt-3" data-url="{{ route('team.analyze.ai.general_alerts', ['team_id' => $member->id]) }}">
                <i class="fas fa-magic"></i> Run AI Analysis
            </button>
            <div class="ai-analysis-result mt-3"></div>
        </div>
    </div>
</div> 