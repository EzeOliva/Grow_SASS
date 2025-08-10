    <!--[dependency][lock-1] start-->
    @if (config('visibility.task_is_open'))
        <!----------Assigned----------->
        @if (config('visibility.tasks_card_assigned'))
            <div class="x-section">
                <div class="x-title">
                    <h6>{{ cleanLang(__('lang.assigned_users')) }}</h6>
                </div>
                <span id="task-assigned-container" class="">
                    @include('pages.task.components.assigned')
                </span>
                <!--user-->
                @if ($task->permission_assign_users)
                    <span
                        class="x-assigned-user x-assign-new js-card-settings-button-static card-task-assigned text-info"
                        data-container=".card-modal" tabindex="0" data-popover-content="card-task-team"
                        data-title="{{ cleanLang(__('lang.assign_users')) }}"><i class="mdi mdi-plus"></i></span>
                @endif
            </div>
        @else
            <!--spacer-->
            <div class="p-b-40"></div>
        @endif


        <!--show timer-->
        <div id="task-timer-container">
            @include('pages.task.components.timer')
        </div>


        <!----------settings----------->
        <div class="x-section">
            <div class="x-title">
                <h6>{{ cleanLang(__('lang.settings')) }}</h6>
            </div>
            <!--start date-->
            @if (config('visibility.tasks_standard_features'))
                <div class="x-element" id="task-start-date"><i class="mdi mdi-calendar-plus"></i>
                    <span>{{ cleanLang(__('lang.start_date')) }}:</span>
                    @if ($task->permission_edit_task)
                        <span class="x-highlight x-editable card-pickadate"
                            data-url="{{ urlResource('/tasks/' . $task->task_id . '/update-start-date/') }}"
                            data-type="form" data-progress-bar='hidden' data-form-id="task-start-date"
                            data-hidden-field="task_date_start" data-container="task-start-date-container"
                            data-ajax-type="post"
                            id="task-start-date-container">{{ runtimeDate($task->task_date_start) }}</span></span>
                        <input type="hidden" name="task_date_start" id="task_date_start">
                    @else
                        <span class="x-highlight">{{ runtimeDate($task->task_date_start) }}</span>
                    @endif
                </div>
            @endif
            <!--due date-->
            @if (config('visibility.tasks_standard_features'))
                <div class="x-element" id="task-due-date"><i class="mdi mdi-calendar-clock"></i>
                    <span>{{ cleanLang(__('lang.due_date')) }}:</span>
                    @if ($task->permission_edit_task)
                        <span class="x-highlight x-editable card-pickadate"
                            data-url="{{ urlResource('/tasks/' . $task->task_id . '/update-due-date/') }}"
                            data-type="form" data-progress-bar='hidden' data-form-id="task-due-date"
                            data-hidden-field="task_date_due" data-container="task-due-date-container"
                            data-ajax-type="post"
                            id="task-due-date-container">{{ runtimeDate($task->task_date_due) }}</span></span>
                        <input type="hidden" name="task_date_due" id="task_date_due">
                    @else
                        <span class="x-highlight">{{ runtimeDate($task->task_date_due) }}</span>
                    @endif
                </div>
            @endif
            <!--status-->
            <div class="x-element" id="card-task-status"><i class="mdi mdi-flag"></i>
                <span>{{ cleanLang(__('lang.status')) }}: </span>
                @if ($task->permission_edit_task)
                    <span class="x-highlight x-editable js-card-settings-button-static" data-container=".card-modal"
                        id="card-task-status-text" tabindex="0" data-popover-content="card-task-statuses"
                        data-offset="0 25%" data-status-id="{{ $task->taskstatus_id }}"
                        data-title="{{ cleanLang(__('lang.status')) }}">{{ runtimeLang($task->taskstatus_title) }}</strong></span>
                @else
                    <span class="x-highlight">{{ runtimeLang($task->taskstatus_title) }}</span>
                @endif
            </div>

            <!--priority-->
            <div class="x-element" id="card-task-priority"><i class="mdi mdi-flag"></i>
                <span>{{ cleanLang(__('lang.priority')) }}: </span>
                @if ($task->permission_edit_task)
                    <span class="x-highlight x-editable js-card-settings-button-static" data-container=".card-modal"
                        id="card-task-priority-text" tabindex="0" data-popover-content="card-task-priorities"
                        data-offset="0 25%" data-status-id="{{ $task->taskpriority_id }}"
                        data-title="{{ cleanLang(__('lang.priority')) }}">{{ runtimeLang($task->taskpriority_title) }}</strong></span>
                @else
                    <span class="x-highlight">{{ runtimeLang($task->taskpriority_title) }}</span>
                @endif
            </div>

            <!--short title-->
            @if(config('system.settings_tasks_short_title') == 'enabled')
            <div class="x-element" id="card-task-short-title"><i class="mdi mdi-tag-text"></i>
                <span>{{ cleanLang(__('lang.short_title')) }}: </span>
                @if ($task->permission_edit_task)
                    <span class="x-highlight x-editable js-card-settings-button-static" data-container=".card-modal"
                        id="card-task-short-title-text" tabindex="0" data-popover-content="card-task-short-titles"
                        data-offset="0 25%"
                        data-title="{{ cleanLang(__('lang.short_title')) }}">{{ $task->task_short_title ?: '--' }}</span>
                @else
                    <span class="x-highlight">{{ $task->task_short_title ?: '--' }}</span>
                @endif
            </div>
            @endif

            <!--start time / end time-->
            @if(config('system.settings_tasks_start_end_time') == 'enabled')
            <div class="x-element" id="card-task-times"><i class="mdi mdi-clock"></i>
                <span>{{ cleanLang(__('lang.start_time')) }} / {{ cleanLang(__('lang.end_time')) }}: </span>
                @if ($task->permission_edit_task)
                    <span class="x-highlight x-editable js-card-settings-button-static" data-container=".card-modal"
                        id="card-task-times-text" tabindex="0" data-popover-content="card-task-times-popover"
                        data-offset="0 25%"
                        data-title="{{ cleanLang(__('lang.start_time')) }} / {{ cleanLang(__('lang.end_time')) }}">
                        {{ $task->task_start_time ? \Carbon\Carbon::parse($task->task_start_time)->format('H:i') : '--' }}
                        /
                        {{ $task->task_end_time ? \Carbon\Carbon::parse($task->task_end_time)->format('H:i') : '--' }}
                    </span>
                @else
                    <span class="x-highlight">
                        {{ $task->task_start_time ? \Carbon\Carbon::parse($task->task_start_time)->format('H:i') : '--' }}
                        /
                        {{ $task->task_end_time ? \Carbon\Carbon::parse($task->task_end_time)->format('H:i') : '--' }}
                    </span>
                @endif
            </div>
            @endif

            <!--estimated time-->
            @if(config('system.settings_tasks_estimated_time') == 'enabled')
            <div class="x-element" id="card-task-estimated-time"><i class="mdi mdi-timer-sand"></i>
                <span>{{ cleanLang(__('lang.estimated_time')) }}: </span>
                @if ($task->permission_edit_task)
                    <span class="x-highlight x-editable js-card-settings-button-static" data-container=".card-modal"
                        id="card-task-estimated-time-text" tabindex="0"
                        data-popover-content="card-task-estimated-times" data-offset="0 25%"
                        data-title="{{ cleanLang(__('lang.estimated_time')) }}">{{ $task->task_estimated_time ?: '--' }}</span>
                @else
                    <span class="x-highlight">{{ $task->task_estimated_time ?: '--' }}</span>
                @endif
            </div>
            @endif

            <!--location-->
            @if(config('system.settings_tasks_location') == 'enabled')
            <div class="x-element" id="card-task-location"><i class="mdi mdi-map-marker"></i>
                <span>{{ cleanLang(__('lang.location')) }}: </span>
                @if ($task->permission_edit_task)
                    <span class="x-highlight x-editable js-card-settings-button-static" data-container=".card-modal"
                        id="card-task-location-text" tabindex="0" data-popover-content="card-task-locations"
                        data-offset="0 25%"
                        data-title="{{ cleanLang(__('lang.location')) }}">{{ $task->task_location ?: '--' }}</span>
                @else
                    <span class="x-highlight">{{ $task->task_location ?: '--' }}</span>
                @endif
            </div>
            @endif

            <!--task color-->
            @if(config('system.settings_tasks_color') == 'enabled')
            <div class="x-element" id="card-task-color"><i class="mdi mdi-palette"></i>
                <span>{{ cleanLang(__('lang.task_color')) }}: </span>
                @if ($task->permission_edit_task)
                    <span class="x-highlight x-editable js-card-settings-button-static" data-container=".card-modal"
                        id="card-task-color-text" tabindex="0" data-popover-content="card-task-colors"
                        data-offset="0 25%" data-title="{{ cleanLang(__('lang.task_color')) }}">
                        <span class="color-indicator"
                            style="background-color: {{ $task->task_color ?: '#007bff' }}; width: 16px; height: 16px; display: inline-block; border-radius: 3px; margin-right: 5px;"></span>
                        {{ $task->task_color ?: 'Default' }}
                    </span>
                @else
                    <span class="x-highlight">
                        <span class="color-indicator"
                            style="background-color: {{ $task->task_color ?: '#007bff' }}; width: 16px; height: 16px; display: inline-block; border-radius: 3px; margin-right: 5px;"></span>
                        {{ $task->task_color ?: 'Default' }}
                    </span>
                @endif
            </div>
            @endif

            <!--client visibility-->
            @if (auth()->user()->type == 'team')
                <div class="x-element" id="card-task-client-visibility"><i class="mdi mdi-eye"></i>
                    <span>{{ cleanLang(__('lang.client')) }}:</span>
                    @if ($task->permission_edit_task)
                        <span class="x-highlight x-editable js-card-settings-button-static"
                            data-container=".card-modal" id="card-task-client-visibility-text" tabindex="0"
                            data-popover-content="card-task-visibility"
                            data-title="{{ cleanLang(__('lang.client_visibility')) }}">{{ runtimeDBlang($task->task_client_visibility, 'task_client_visibility') }}</strong></span>
                        <input type="hidden" name="task_client_visibility" id="task_client_visibility">
                    @else
                        <span
                            class="x-highlight">{{ runtimeDBlang($task->task_client_visibility, 'task_client_visibility') }}</span>
                    @endif

                </div>
            @endif

            <!--reminder-->
            @if (config('visibility.modules.reminders') && $task->project_type == 'project')
                <div class="card-reminders-container" id="card-reminders-container">
                    @include('pages.reminders.cards.wrapper')
                </div>
            @endif


        </div>

        <!----------tags----------->
        <div class="card-tags-container" id="card-tags-container">
            @include('pages.task.components.tags')
        </div>
    @endif
    <!--[dependency][lock-1] end-->

    <!--[dependency][lock-2] start-->
    @if (config('visibility.task_is_locked'))
        <!--spacer-->
        <div class="p-t-15"></div>
    @endif
    <!--[dependency][lock-2] end-->


    <!--dependencies-->
    <div class="x-section">
        <div class="x-title">
            <h6>{{ cleanLang(__('lang.dependencies')) }}</h6>
        </div>
        @include('pages.task.dependency.wrapper')
    </div>


    <!--[dependency][lock-3] start-->
    @if (config('visibility.task_is_open'))

        <!----------actions----------->
        <div class="x-section">
            <div class="x-title">
                <h6>{{ cleanLang(__('lang.actions')) }}</h6>
            </div>

            <!--track if we have any actions-->
            @php $count_action = 0 ; @endphp

            <!--change milestone-->
            @if ($task->permission_edit_task && auth()->user()->type == 'team')
                <div class="x-element x-action js-card-settings-button-static" data-container=".card-modal"
                    id="card-task-milestone" tabindex="0" data-popover-content="card-task-milestones"
                    data-title="{{ cleanLang(__('lang.milestone')) }}"><i class="mdi mdi-redo-variant"></i>
                    <span class="x-highlight">{{ cleanLang(__('lang.change_milestone')) }}</strong></span>
                </div>
                @php $count_action ++ ; @endphp
            @endif

            <!--stop all timer-->
            @if ($task->permission_super_user && config('visibility.tasks_standard_features'))
                <div class="x-element x-action confirm-action-danger"
                    data-confirm-title="{{ cleanLang(__('lang.stop_all_timers')) }}"
                    data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                    data-url="{{ url('/') }}/tasks/timer/{{ $task->task_id }}/stopall?source=card"><i
                        class="mdi mdi-timer-off"></i>
                    <span class="x-highlight"
                        id="task-start-date">{{ cleanLang(__('lang.stop_all_timers')) }}</span></span>
                </div>
                @php $count_action ++ ; @endphp
            @endif


            <!--archive-->
            @if ($task->permission_edit_task && config('visibility.tasks_standard_features'))
                <div class="x-element x-action confirm-action-info  {{ runtimeActivateOrAchive('archive-button', $task->task_active_state) }} card_archive_button_{{ $task->task_id }}"
                    id="card_archive_button_{{ $task->task_id }}"
                    data-confirm-title="{{ cleanLang(__('lang.archive_task')) }}"
                    data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="PUT"
                    data-url="{{ url('/') }}/tasks/{{ $task->task_id }}/archive"><i
                        class="mdi mdi-archive"></i> <span class="x-highlight"
                        id="task-start-date">{{ cleanLang(__('lang.archive')) }}</span></span></div>
                @php $count_action ++ ; @endphp
            @endif

            <!--restore-->
            @if ($task->permission_edit_task && runtimeArchivingOptions())
                <div class="x-element x-action confirm-action-info  {{ runtimeActivateOrAchive('activate-button', $task->task_active_state) }} card_restore_button_{{ $task->task_id }}"
                    id="card_restore_button_{{ $task->task_id }}"
                    data-confirm-title="{{ cleanLang(__('lang.restore_task')) }}"
                    data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="PUT"
                    data-url="{{ url('/') }}/tasks/{{ $task->task_id }}/activate"><i
                        class="mdi mdi-archive"></i> <span class="x-highlight"
                        id="task-start-date">{{ cleanLang(__('lang.restore')) }}</span></span></div>
                @php $count_action ++ ; @endphp
            @endif

            <!--delete-->
            @if ($task->permission_delete_task && runtimeArchivingOptions())
                <div class="x-element x-action confirm-action-danger"
                    data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
                    data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}" data-ajax-type="DELETE"
                    data-url="{{ urlResource('/') }}/tasks/{{ $task->task_id }}"><i class="mdi mdi-delete"></i>
                    <span class="x-highlight" id="task-start-date">{{ cleanLang(__('lang.delete')) }}</span></span>
                </div>
                @php $count_action ++ ; @endphp
            @endif


            <!--no action available-->
            @if ($count_action == 0)
                <div class="x-element">
                    {{ cleanLang(__('lang.no_actions_available')) }}
                </div>
            @endif

        </div>

        <!----------meta infor----------->
        <div class="x-section">
            <div class="x-title">
                <h6>{{ cleanLang(__('lang.information')) }}</h6>
            </div>
            <div class="x-element x-action">
                <table class="table table-bordered table-sm">
                    <tbody>
                        <tr>
                            <td>{{ cleanLang(__('lang.task_id')) }}</td>
                            <td><strong>#{{ $task->task_id }}</strong></td>
                        </tr>
                        <tr>
                            <td>{{ cleanLang(__('lang.created_by')) }}</td>
                            <td><strong>{{ $task->first_name }} {{ $task->last_name }}</strong></td>
                        </tr>
                        <tr>
                            <td>{{ cleanLang(__('lang.date_created')) }}</td>
                            <td><strong>{{ runtimeDate($task->task_created) }}</strong></td>
                        </tr>
                        @if (auth()->user()->is_team)
                            <tr>
                                <td>{{ cleanLang(__('lang.total_time')) }}</td>
                                <td><strong><span
                                            id="task_timer_all_card_{{ $task->task_id }}">{!! clean(runtimeSecondsHumanReadable($task->sum_all_time, false)) !!}</span></strong>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ cleanLang(__('lang.time_invoiced')) }}</td>
                                <td><strong><span
                                            id="task_timer_all_card_{{ $task->task_id }}">{!! clean(runtimeSecondsHumanReadable($task->sum_invoiced_time, false)) !!}</span></strong>
                                </td>
                            </tr>
                            <tr>
                                <td>{{ cleanLang(__('lang.project')) }}</td>
                                <td><strong><a href="{{ urlResource('/projects/' . $task->task_projectid) }}"
                                            target="_blank">#{{ $task->project_id }}</a></strong>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!--just a spacer for dependencied-->
        <div class="p-b-100 p-t-100"></div>

    @endif
    <!--[dependency][lock-3] end-->




    <!-----------------------------popover dropdown elements------------------------------------>

    <!--task statuses - popover -->
    @if ($task->permission_participate)
        <div class="hidden" id="card-task-statuses">
            <ul class="list">
                @foreach (config('task_statuses') as $task_status)
                    <li class="card-tasks-update-status-link" data-button-text="card-task-status-text"
                        data-progress-bar='hidden'
                        data-url="{{ urlResource('/tasks/' . $task->task_id . '/update-status') }}" data-type="form"
                        data-value="{{ $task_status->taskstatus_id }}" data-form-id="--set-dynamically--"
                        data-ajax-type="post">
                        {{ runtimeLang($task_status->taskstatus_title) }}</li>
                @endforeach
            </ul>
            <input type="hidden" name="task_status" id="task_status">
            <input type="hidden" name="current_task_status_text" id="current_task_status_text">
        </div>
    @endif


    <!--task priorities - popover -->
    @if ($task->permission_participate)
        <div class="hidden" id="card-task-priorities">
            <ul class="list">
                @foreach (config('task_priorities') as $priority)
                    <li class="card-tasks-update-priority-link" data-button-text="card-task-priority-text"
                        data-progress-bar='hidden'
                        data-url="{{ urlResource('/tasks/' . $task->task_id . '/update-priority') }}"
                        data-type="form" data-value="{{ $priority->taskpriority_id }}"
                        data-form-id="--set-dynamically--" data-ajax-type="post">
                        {{ runtimeLang($priority->taskpriority_title) }}</li>
                @endforeach
            </ul>
            <input type="hidden" name="task_priority" id="task_priority">
            <input type="hidden" name="current_task_priority_text" id="current_task_priority_text">
        </div>
    @endif


    <!--task priority - popover-->
    @if ($task->permission_participate)
        <div class="hidden" id="card-task-priorities">
            <ul class="list">
                @foreach (config('settings.task_priority') as $key => $value)
                    <li class="card-tasks-update-priority-link" data-button-text="card-task-priority-text"
                        data-progress-bar='hidden'
                        data-url="{{ urlResource('/tasks/' . $task->task_id . '/update-priority') }}"
                        data-type="form" data-value="{{ $key }}" data-form-id="--set-dynamically--"
                        data-ajax-type="post">
                        {{ runtimeLang($key) }}</li>
                @endforeach
            </ul>
            <input type="hidden" name="task_priority" id="task_priority">
            <input type="hidden" name="current_task_priority_text" id="current_task_priority_text">
        </div>
    @endif

    <!--client visibility - popover-->
    @if ($task->permission_edit_task)
        <div class="hidden" id="card-task-visibility">
            <ul class="list">
                <li class="card-tasks-update-visibility-link" data-button-text="card-task-client-visibility-text"
                    data-progress-bar='hidden'
                    data-url="{{ urlResource('/tasks/' . $task->task_id . '/update-visibility') }}" data-type="form"
                    data-value="no" data-text="{{ cleanLang(__('lang.hidden')) }}"
                    data-form-id="card-task-client-visibility" data-ajax-type="post">
                    {{ cleanLang(__('lang.hidden')) }}
                </li>
                <li class="card-tasks-update-visibility-link" data-button-text="card-task-client-visibility-text"
                    data-progress-bar='hidden'
                    data-url="{{ urlResource('/tasks/' . $task->task_id . '/update-visibility') }}" data-type="form"
                    data-value="yes" data-text="{{ cleanLang(__('lang.visible')) }}"
                    data-form-id="card-task-client-visibility" data-ajax-type="post">
                    {{ cleanLang(__('lang.visible')) }}
                </li>
            </ul>
            <input type="hidden" name="task_client_visibility" id="task_client_visibility">
            <input type="hidden" name="current_task_client_visibility_text"
                id="current_task_client_visibility_text">
        </div>
    @endif

    <!--milestone - popover -->
    @if ($task->permission_edit_task)
        <div class="hidden" id="card-task-milestones">
            <div class="form-group m-t-10">
                <select class="custom-select col-12 form-control form-control-sm" id="task_milestoneid"
                    name="task_milestoneid">
                    @if (isset($milestones))
                        @foreach ($milestones as $milestone)
                            <option value="{{ $milestone->milestone_id }}">
                                {{ runtimeLang($milestone->milestone_title, 'task_milestone') }}</option>
                        @endforeach
                    @endif
                </select>
            </div>
            <div class="form-group text-right">
                <button type="button" class="btn btn-danger btn-sm" id="card-tasks-update-milestone-button"
                    data-progress-bar='hidden'
                    data-url="{{ urlResource('/tasks/' . $task->task_id . '/update-milestone') }}" data-type="form"
                    data-ajax-type="post" data-form-id="popover-body">
                    {{ cleanLang(__('lang.update')) }}
                </button>
            </div>
        </div>
    @endif


    <!--assign user-->
    <div class="hidden" id="card-task-team">
        <div class="card-assigned-popover-content">
            <div class="alert alert-info">Only users assigned to the project are shown in this list</div>
            <div class="line"></div>

            <!--staff users-->
            <h5>@lang('lang.team_members')</h5>
            @foreach ($project_assigned as $staff)
                <div class="form-check m-b-15">
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" name="assigned[{{ $staff->id }}]"
                            class="custom-control-input assigned_user_{{ $staff->id }}">
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description"><img
                                src="{{ getUsersAvatar($staff->avatar_directory, $staff->avatar_filename) }}"
                                class="img-circle avatar-xsmall"> {{ $staff->first_name }}
                            {{ $staff->last_name }}</span>
                    </label>
                </div>
            @endforeach

            <div class="line"></div>

            <!--client users-->
            <h5>@lang('lang.client_users')</h5>
            @foreach ($client_users as $staff)
                <div class="form-check m-b-15">
                    <label class="custom-control custom-checkbox">
                        <input type="checkbox" name="assigned[{{ $staff->id }}]"
                            class="custom-control-input assigned_user_{{ $staff->id }}">
                        <span class="custom-control-indicator"></span>
                        <span class="custom-control-description"><img
                                src="{{ getUsersAvatar($staff->avatar_directory, $staff->avatar_filename) }}"
                                class="img-circle avatar-xsmall"> {{ $staff->first_name }}
                            {{ $staff->last_name }}</span>
                    </label>
                </div>
            @endforeach

            <div class="form-group text-right">
                <button type="button" class="btn btn-danger btn-sm" id="card-tasks-update-assigned"
                    data-progress-bar='hidden'
                    data-url="{{ urlResource('/tasks/' . $task->task_id . '/update-assigned') }}" data-type="form"
                    data-ajax-type="post" data-form-id="popover-body">
                    {{ cleanLang(__('lang.update')) }}
                </button>
            </div>
        </div>
    </div>

    <!--short title - popover -->
    @if ($task->permission_edit_task)
        <div class="hidden" id="card-task-short-titles">
            <div class="popover-body">
                <div class="form-group m-t-10">
                    <input type="text" class="form-control form-control-sm" id="task_short_title"
                        name="task_short_title" value="{{ $task->task_short_title }}"
                        placeholder="{{ cleanLang(__('lang.short_title')) }}">
                </div>
                <div class="form-group text-right">
                    <button type="button" class="btn btn-danger btn-sm" id="card-tasks-update-short-title-button"
                        data-progress-bar='hidden'
                        data-url="{{ urlResource('/tasks/' . $task->task_id . '/update-short-title') }}"
                        data-type="form" data-ajax-type="post" data-form-id="popover-body">
                        {{ cleanLang(__('lang.update')) }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!--start time / end time - popover -->
    @if ($task->permission_edit_task)
        <div class="hidden" id="card-task-times-popover">
            <div class="popover-body">
                <div class="form-group m-t-10">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">{{ cleanLang(__('lang.start_time')) }}</label>
                            <input type="time" class="form-control form-control-sm" id="task_start_time"
                                name="task_start_time"
                                value="{{ $task->task_start_time ? \Carbon\Carbon::parse($task->task_start_time)->format('H:i') : '' }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ cleanLang(__('lang.end_time')) }}</label>
                            <input type="time" class="form-control form-control-sm" id="task_end_time"
                                name="task_end_time"
                                value="{{ $task->task_end_time ? \Carbon\Carbon::parse($task->task_end_time)->format('H:i') : '' }}">
                        </div>
                    </div>
                </div>
                <div class="form-group text-right">
                    <button type="button" class="btn btn-danger btn-sm" id="card-tasks-update-times-button"
                        data-progress-bar='hidden'
                        data-url="{{ urlResource('/tasks/' . $task->task_id . '/update-times') }}" data-type="form"
                        data-ajax-type="post" data-form-id="popover-body">
                        {{ cleanLang(__('lang.update')) }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!--estimated time - popover -->
    @if ($task->permission_edit_task)
        <div class="hidden" id="card-task-estimated-times">
            <div class="popover-body">
                <div class="form-group m-t-10">
                    <div class="row">
                        <div class="col-6">
                            <label class="form-label">{{ cleanLang(__('lang.value')) }}</label>
                            <input type="number" class="form-control form-control-sm" id="task_estimated_time_value"
                                name="task_estimated_time_value" min="0" step="0.5"
                                value="{{ $task->task_estimated_time ? explode('h', explode('d', explode('w', $task->task_estimated_time)[0])[0])[0] : '' }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label">{{ cleanLang(__('lang.unit')) }}</label>
                            <select class="form-control form-control-sm" id="task_estimated_time_unit"
                                name="task_estimated_time_unit">
                                <option value="h"
                                    {{ $task->task_estimated_time && strpos($task->task_estimated_time, 'h') !== false ? 'selected' : '' }}>
                                    Hours</option>
                                <option value="d"
                                    {{ $task->task_estimated_time && strpos($task->task_estimated_time, 'd') !== false ? 'selected' : '' }}>
                                    Days</option>
                                <option value="w"
                                    {{ $task->task_estimated_time && strpos($task->task_estimated_time, 'w') !== false ? 'selected' : '' }}>
                                    Weeks</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group text-right">
                    <button type="button" class="btn btn-danger btn-sm" id="card-tasks-update-estimated-time-button"
                        data-progress-bar='hidden'
                        data-url="{{ urlResource('/tasks/' . $task->task_id . '/update-estimated-time') }}"
                        data-type="form" data-ajax-type="post" data-form-id="popover-body">
                        {{ cleanLang(__('lang.update')) }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!--location - popover -->
    @if ($task->permission_edit_task)
        <div class="hidden" id="card-task-locations">
            <div class="popover-body">
                <div class="form-group m-t-10">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" id="task_location"
                            name="task_location" value="{{ $task->task_location }}"
                            placeholder="{{ cleanLang(__('lang.location')) }}">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="show-location-map-btn"
                                title="{{ cleanLang(__('lang.show_on_map')) }}">
                                <i class="mdi mdi-map-marker"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="form-group text-right">
                    <button type="button" class="btn btn-danger btn-sm" id="card-tasks-update-location-button"
                        data-progress-bar='hidden'
                        data-url="{{ urlResource('/tasks/' . $task->task_id . '/update-location') }}"
                        data-type="form" data-ajax-type="post" data-form-id="popover-body">
                        {{ cleanLang(__('lang.update')) }}
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!--task color - popover -->
    @if ($task->permission_edit_task)
        <style>
            .disabled-section {
                opacity: 0.6;
                pointer-events: none;
            }
            .disabled-section .form-control {
                background-color: #e9ecef;
                cursor: not-allowed;
            }
            
            /* Google Places Autocomplete Styling */
            .pac-container {
                z-index: 9999 !important;
                border-radius: 4px;
                border: 1px solid #ddd;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                font-family: inherit;
                font-size: 14px;
            }
            
            .pac-item {
                padding: 8px 12px;
                border-bottom: 1px solid #f0f0f0;
                cursor: pointer;
            }
            
            .pac-item:hover {
                background-color: #f8f9fa;
            }
            
            .pac-item:last-child {
                border-bottom: none;
            }
            
            .pac-item-query {
                font-weight: 500;
                color: #333;
            }
            
            .pac-matched {
                font-weight: bold;
                color: #007bff;
            }
            
            .pac-secondary-text {
                color: #666;
                font-size: 12px;
            }
            
            /* New PlaceAutocompleteElement Styling */
            .location-autocomplete-container {
                position: relative;
                width: 100%;
            }
            
            .location-autocomplete-container input {
                width: 100%;
                padding: 8px 12px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 14px;
            }
            
            .location-autocomplete-container input:focus {
                outline: none;
                border-color: #007bff;
                box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
            }
            
            /* Ensure autocomplete dropdown appears above other elements */
            .location-autocomplete-container .pac-container {
                z-index: 99999 !important;
            }
            
            /* Fallback Autocomplete Styling */
            .fallback-autocomplete-dropdown {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: white;
                border: 1px solid #ddd;
                border-top: none;
                border-radius: 0 0 4px 4px;
                max-height: 200px;
                overflow-y: auto;
                z-index: 9999;
                display: none;
                box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                font-family: inherit;
                font-size: 14px;
            }
            
            .fallback-suggestion-item {
                padding: 8px 12px;
                cursor: pointer;
                border-bottom: 1px solid #f0f0f0;
                transition: background-color 0.2s ease;
            }
            
            .fallback-suggestion-item:hover {
                background-color: #f8f9fa;
            }
            
            .fallback-suggestion-item:last-child {
                border-bottom: none;
            }
        </style>
        <div class="hidden" id="card-task-colors">
            <div class="popover-body">
                <!-- Custom color picker -->
                <div class="form-group m-t-10">
                    <label class="form-label">{{ cleanLang(__('lang.custom_color')) }}</label>
                    <input type="color" class="form-control form-control-sm task_color_custom_right"
                        id="task_color_custom_right" name="task_color_custom" value="{{ $task->task_color ?? '#007bff' }}"
                        style="height: 40px;">
                </div>
                
                <!-- Hidden input for final color value -->
                <input type="hidden" name="task_color" id="task_color_final_right" value="{{ $task->task_color ?? '#007bff' }}">
                
                <div class="form-group text-right">
                    <button type="button" class="btn btn-danger btn-sm" id="card-tasks-update-color-button"
                        data-progress-bar='hidden'
                        data-url="{{ urlResource('/tasks/' . $task->task_id . '/update-color') }}" data-type="form"
                        data-ajax-type="post" data-form-id="popover-body">
                        {{ cleanLang(__('lang.update')) }}
                    </button>
                </div>
            </div>
        </div>
    @endif
