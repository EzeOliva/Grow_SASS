<div class="table-responsive">
    <table class="table table-sm table-striped m-b-0">
        <thead>
            <tr>
                <th style="width: 40%;">Tarea</th>
                <th style="width: 25%;">Proyecto</th>
                <th style="width: 15%;">Estado</th>
                <th style="width: 20%;">Actualizada</th>
            </tr>
        </thead>
        <tbody>
            @forelse(($tasks ?? collect()) as $task)
            <tr>
                <td>
                    <a href="{{ urlResource('/tasks/v/' . $task->task_id . '/' . str_slug($task->task_title ?? 'tarea')) }}" target="_blank">
                        {{ $task->task_title ?: ('Tarea #' . $task->task_id) }}
                    </a>
                </td>
                <td>
                    @if(!empty($task->project_id))
                    <a href="{{ url('/projects/' . $task->project_id) }}" target="_blank">
                        {{ $task->project_title ?: ('Proyecto #' . $task->project_id) }}
                    </a>
                    @else
                    -
                    @endif
                </td>
                <td>{{ $task->taskstatus_title ?: $task->task_status }}</td>
                <td>{{ runtimeDate($task->task_updated) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center text-muted">No hay tareas completadas en este periodo.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
