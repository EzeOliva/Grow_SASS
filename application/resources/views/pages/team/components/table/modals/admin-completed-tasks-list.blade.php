<div class="admin-completed-tasks-modal">
    <div class="m-b-15">
        <div class="font-weight-600">
            {{ trim(($member->first_name ?? '') . ' ' . ($member->last_name ?? '')) ?: '-' }}
        </div>
        <div class="text-muted font-13">
            Rol: {{ $member->role_name ?: '-' }} | Posicion: {{ $member->position ?: '-' }}
        </div>
    </div>

    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#admin-completed-week" role="tab">
                Ultimos 7 dias ({{ number_format(($completedLastWeekTasks ?? collect())->count()) }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#admin-completed-month" role="tab">
                Ultimos 30 dias ({{ number_format(($completedLastMonthTasks ?? collect())->count()) }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#admin-completed-60" role="tab">
                Ultimos 60 dias ({{ number_format(($completedLastSixtyDaysTasks ?? collect())->count()) }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#admin-completed-90" role="tab">
                Ultimos 90 dias ({{ number_format(($completedLastNinetyDaysTasks ?? collect())->count()) }})
            </a>
        </li>
    </ul>

    <div class="tab-content p-t-15">
        <div class="tab-pane active" id="admin-completed-week" role="tabpanel">
            @include('pages.team.components.table.modals.admin-completed-tasks-list-table', ['tasks' => $completedLastWeekTasks])
        </div>
        <div class="tab-pane" id="admin-completed-month" role="tabpanel">
            @include('pages.team.components.table.modals.admin-completed-tasks-list-table', ['tasks' => $completedLastMonthTasks])
        </div>
        <div class="tab-pane" id="admin-completed-60" role="tabpanel">
            @include('pages.team.components.table.modals.admin-completed-tasks-list-table', ['tasks' => $completedLastSixtyDaysTasks])
        </div>
        <div class="tab-pane" id="admin-completed-90" role="tabpanel">
            @include('pages.team.components.table.modals.admin-completed-tasks-list-table', ['tasks' => $completedLastNinetyDaysTasks])
        </div>
    </div>
</div>
