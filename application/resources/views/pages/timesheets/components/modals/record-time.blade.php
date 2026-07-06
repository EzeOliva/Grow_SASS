<div class="row">
    <div class="col-lg-12">



        @if(auth()->user()->is_admin && !is_numeric(request('task_id')))
        <div class="modal-selector">
            <!--select team member-->
            <div class="form-group row">
                <div class="col-sm-12 m-b-0">
                    <select class="select2-basic form-control form-control-sm select2-preselected" id="timesheet_user"
                        data-base-url="{{ url('/feed/users-projects?user_id=') }}" name="timesheet_user"
                        data-preselected="{{ auth()->user()->id }}">
                        @foreach(config('system.team_members') as $user)
                        <option value="{{ $user->id }}">{{ $user->full_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        @else
        <input type="hidden" name="timesheet_user" value="{{ auth()->user()->id }}">

        @endif

        <!--entry mode-->
        @if(is_numeric(request('task_id')))
        <input type="hidden" name="timer_entry_mode" value="task">
        @else
        <div class="form-group row m-b-12">
            <label
                class="col-12 text-left control-label col-form-label col-12 m-b-0 font-13 p-b-4">@lang('lang.type')</label>
            <div class="col-12">
                <select class="select2-basic form-control form-control-sm" id="timer_entry_mode" name="timer_entry_mode">
                    <option value="task">@lang('lang.task')</option>
                    <option value="client">@lang('lang.client')</option>
                </select>
            </div>
        </div>
        @endif

        <!--task entry mode-->
        <div id="timesheet-entry-mode-task">
            @if(is_numeric(request('task_id')))
            <input type="hidden" name="my_assigned_tasks" value="{{ request('task_id') }}">
            <input type="hidden" name="source" value="tasks">
            @else
            <div class="form-group row m-b-12">
                <label
                    class="col-12 text-left control-label col-form-label col-12 m-b-0 font-13 p-b-4">@lang('lang.my_projects')</label>
                <div class="col-12">
                    <select name="my_assigned_projects" id="my_assigned_projects" placeholder="project"
                        data-user-id="{{ auth()->user()->id }}"
                        class="projects_my_tasks_toggle form-control form-control-sm js-select2-basic-search-modal select2-hidden-accessible"
                        data-task-dropdown="my_assigned_tasks"
                        data-ajax--url="{{ url('/feed/users-projects?user_id='.auth()->user()->id) }}"></select>
                </div>
            </div>
            <div class="form-group row">
                <label
                    class="col-12 text-left control-label col-form-label col-12 m-b-0 font-13 p-b-4">@lang('lang.my_tasks')</label>
                <div class="col-12">
                    <select class="select2-basic form-control form-control-sm" id="my_assigned_tasks"
                        name="my_assigned_tasks" disabled>
                        <!--dynamic tasks lists-->
                    </select>
                </div>
            </div>
            <input type="hidden" name="source" value="timesheets">
            @endif

            <div class="form-group row dropdown-no-results-found hidden m-b-18" id="my_assigned_tasks_no_results">
                <div class="p-l-8 p-r-8">
                    <!--info tooltip-->
                    <span>@lang('lang.no_tasks_found')</span>
                    <span class="align-middle p-l-5 font-16" data-toggle="tooltip"
                        title="@lang('lang.no_tasks_assigned_to_you')" data-placement="top"><i
                            class="ti-info-alt font-13"></i></span>
                </div>
            </div>
        </div>

        <!--client entry mode-->
        <div id="timesheet-entry-mode-client" class="hidden">
            <div class="form-group row m-b-12">
                <label
                    class="col-12 text-left control-label col-form-label col-12 m-b-0 font-13 p-b-4">@lang('lang.client')</label>
                <div class="col-12">
                    <select class="clients_and_projects_toggle select2-basic form-control form-control-sm"
                        id="timesheet_clientid" name="timesheet_clientid" data-projects-dropdown="timesheet_projectid"
                        data-feed-request-type="clients_projects" disabled>
                        <option value="">@lang('lang.client')</option>
                        @foreach($clients ?? [] as $client)
                        <option value="{{ $client->client_id }}">{{ $client->client_company_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row m-b-12">
                <label
                    class="col-12 text-left control-label col-form-label col-12 m-b-0 font-13 p-b-4">@lang('lang.project') (opcional)</label>
                <div class="col-12">
                    <select class="select2-basic form-control form-control-sm" name="timesheet_projectid"
                        id="timesheet_projectid" disabled></select>
                </div>
            </div>
            <div class="form-group row m-b-12">
                <label
                    class="col-12 text-left control-label col-form-label col-12 m-b-0 font-13 p-b-4">@lang('lang.description')</label>
                <div class="col-12">
                    <textarea class="form-control form-control-sm" id="timer_notes" name="timer_notes" rows="3"
                        placeholder="@lang('lang.description')" disabled></textarea>
                </div>
            </div>
        </div>

        <!--timer date-->
        <div class="form-group row">
            <label
                class="col-12 text-left control-label col-form-label col-12 m-b-0 font-13 p-b-4">@lang('lang.date')</label>
            <div class="col-12">
                <input type="text" class="form-control  form-control-sm pickadate" disabled autocomplete="off"
                    name="timer_created_edit" id="manual_timer_created"
                    value="{{ runtimeDatepickerDate($estimate->bill_date ?? '') }}" autocomplete="off">
                <input class="mysql-date" type="hidden" name="timer_created" id="timer_created_edit"
                    value="{{ $estimate->bill_date ?? '' }}">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-6">
                <input type="number" class="form-control form-control-sm js-topnav-timer"
                    placeholder="@lang('lang.hrs')" name="manual_time_hours" id="manual_time_hours" disabled>
            </div>
            <div class="col-6">
                <input type="number" class="form-control form-control-sm js-topnav-timer"
                    placeholder="@lang('lang.mins')" name="manual_time_minutes" id="manual_time_minutes" disabled>
            </div>
        </div>
    </div>
</div>

@if(!is_numeric(request('task_id')))
<script>
    (function () {
        function toggleTimesheetEntryMode() {
            var mode = $("#timer_entry_mode").val() || 'task';

            if (typeof NX !== 'undefined' && typeof NX.recordTaskTimeModeToggle === 'function') {
                NX.recordTaskTimeModeToggle(mode);
                return;
            }

            var isClientMode = mode === 'client';
            $("#timesheet-entry-mode-task").toggleClass('hidden', isClientMode);
            $("#timesheet-entry-mode-client").toggleClass('hidden', !isClientMode);

            $("#timesheet_clientid, #timesheet_projectid, #timer_notes").prop('disabled', !isClientMode);
            $("#my_assigned_projects, #my_assigned_tasks").prop('disabled', isClientMode);

            if (isClientMode) {
                $("#manual_time_hours, #manual_time_minutes, #manual_timer_created, #commonModalSubmitButton").prop('disabled', false);
            }
        }

        $(document)
            .off('change.timesheet-entry-mode-fallback', '#timer_entry_mode')
            .on('change.timesheet-entry-mode-fallback', '#timer_entry_mode', toggleTimesheetEntryMode);

        setTimeout(toggleTimesheetEntryMode, 0);
    })();
</script>
@endif