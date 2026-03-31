<div class="team-weekly-report-analysis">
    <div class="card mb-3">
        <div class="card-header bg-primary text-white">
            <h6><i class="fas fa-calendar-week"></i> Team Weekly Report</h6>
        </div>
        <div class="card-body">
            <h5>Completed Tasks (last week)</h5>
            <ul>
                @forelse($completedTasks as $task)
                    <li>{{ $task->task_title }} <span class="text-muted">({{ $task->task_updated }})</span></li>
                @empty
                    <li class="text-muted">None</li>
                @endforelse
            </ul>
            <h5>In Progress Tasks (last week)</h5>
            <ul>
                @forelse($inProgressTasks as $task)
                    <li>{{ $task->task_title }} <span class="text-muted">(Due: {{ $task->task_date_due }})</span></li>
                @empty
                    <li class="text-muted">None</li>
                @endforelse
            </ul>
            <h5>Overdue Tasks</h5>
            <ul>
                @forelse($overdueTasks as $task)
                    <li>{{ $task->task_title }} <span class="text-muted">(Due: {{ $task->task_date_due }})</span></li>
                @empty
                    <li class="text-muted">None</li>
                @endforelse
            </ul>
            <button class="btn btn-primary ai-analyze-btn mt-3" data-url="{{ route('team.analyze.ai.weekly_report', ['team_id' => $member->id]) }}">
                <i class="fas fa-magic"></i> Run AI Analysis
            </button>
            <div class="ai-analysis-result mt-3"></div>
        </div>
    </div>
</div> 