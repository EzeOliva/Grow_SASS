
<div class="row">
    <div class="col-lg-12">

        @if(config('visibility.task_modal_project_option'))
        <!--project-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.project')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <select name="task_projectid" id="task_projectid"
                    class="projects_assigned_toggle projects_assigned_client_toggle form-control form-control-sm js-select2-basic-search-modal select2-hidden-accessible"
                    data-assigned-dropdown="assigned" data-client-assigned-dropdown="assigned-client"
                    data-ajax--url="{{ url('/') }}/feed/projects?ref=general"></select>
            </div>
        </div>
        @endif

        <!--title-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.title')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" id="task_title" name="task_title"
                    placeholder="">
            </div>
        </div>

        <!--short title-->
        @if(config('system.settings_tasks_short_title') == 'enabled')
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.short_title')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm" name="task_short_title" 
                       value="{{ $task->task_short_title ?? '' }}" 
                       placeholder="TM, TN, T1" required>
            </div>
        </div>
        @endif

        <!--start date-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.start_date')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <input type="text" class="form-control form-control-sm pickadate" name="task_start_date" 
                       autocomplete="off" placeholder="" value="{{ $task->task_start_date ?? '' }}" required>
                <input class="mysql-date" type="hidden" name="task_start_date" id="task_start_date" value="{{ $task->task_start_date ?? '' }}">
            </div>
        </div>

        @if(config('visibility.task_modal_milestone_option'))
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.milestone')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <select name="task_milestoneid" id="task_milestoneid"
                    class="select2-basic form-control form-control-sm">
                    @if(isset($milestones))
                    @foreach($milestones as $milestone)
                    <option value="{{ $milestone->milestone_id }}">
                        {{ runtimeLang($milestone->milestone_title, 'task_milestone') }}</option>
                    @endforeach
                    @endif
                    <!--/#users list-->
                </select>
            </div>
        </div>
        @endif

        <!--task status-->
        @if(request('status') != '')
        <input type="hidden" name="task_status" value="{{ request('status') }}">
        @else
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.status')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control form-control-sm" id="task_status" name="task_status">
                    @foreach($statuses as $status)
                    <option value="{{ $status->taskstatus_id }}">{{ runtimeLang($status->taskstatus_title) }}
                    </option>
                    @endforeach

                </select>
            </div>
        </div>
        @endif

        <!--task priority-->
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label required">{{ cleanLang(__('lang.priority')) }}*</label>
            <div class="col-sm-12 col-lg-9">
                <select class="select2-basic form-control form-control-sm" id="task_priority" name="task_priority">
                    @foreach($priorities as $priority)
                    <option value="{{ $priority->taskpriority_id }}">{{ $priority->taskpriority_title }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!--assigned [team users]-->
        @if(auth()->user()->role->role_assign_tasks == 'yes' && config('visibility.tasks_standard_features'))
        <div class="form-group row">
            <label
                class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.assign_users')) }}
                <a class="align-middle font-16 toggle-collapse" href="#assigning_info" role="button"><i
                        class="ti-info-alt text-themecontrast"></i></a></label>
            <div class="col-sm-12 col-lg-9">
                @if(config('visibility.projects_assigned_users'))
                <select name="assigned" id="assigned"
                    class="form-control form-control-sm select2-basic select2-multiple select2-tags select2-hidden-accessible"
                    multiple="multiple" tabindex="-1" aria-hidden="true">
                    @foreach(config('project.assigned') as $user)
                    @if($user->type =='team')
                    <option value="{{ $user->id}}">{{ $user->first_name }} {{ $user->last_name }}</option>
                    @endif
                    @endforeach
                </select>
                @else
                <select name="assigned" id="assigned"
                    class="form-control form-control-sm select2-basic select2-multiple select2-tags select2-hidden-accessible"
                    multiple="multiple" tabindex="-1" aria-hidden="true" disabled>
                </select>
                @endif
            </div>
        </div>
        <div class="collapse" id="assigning_info">
            <div class="alert alert-info">
                {{ cleanLang(__('lang.assigning_users_to_a_task_info')) }}
            </div>
        </div>
        @endif

        <!--assigned [client users]-->
        @if(auth()->user()->role->role_assign_tasks == 'yes' && config('visibility.tasks_standard_features'))
        <div class="form-group row">
            <label class="col-sm-12 col-lg-3 text-left control-label col-form-label">@lang('lang.assign_client')
                <a class="align-middle font-16 toggle-collapse" href="#assigning_client_info" role="button"><i
                        class="ti-info-alt text-themecontrast"></i></a></label>
            <div class="col-sm-12 col-lg-9">
                @if(config('visibility.projects_assigned_users'))
                <select name="assigned-client" id="assigned-client"
                    class="form-control form-control-sm select2-basic select2-multiple select2-tags select2-hidden-accessible"
                    multiple="multiple" tabindex="-1" aria-hidden="true">
                    @foreach(config('project.client_users') as $user)
                    <option value="{{ $user->id}}">{{ $user->first_name }} {{ $user->last_name }}</option>
                    @endforeach
                </select>
                @else
                <select name="assigned-client" id="assigned-client"
                    class="form-control form-control-sm select2-basic select2-multiple select2-tags select2-hidden-accessible"
                    multiple="multiple" tabindex="-1" aria-hidden="true" disabled>
                </select>
                @endif
            </div>
        </div>
        <div class="collapse" id="assigning_client_info">
            <div class="alert alert-info">
                @lang('lang.assign_client_info')
            </div>
        </div>
        @endif

        <!--CUSTOMER FIELDS [expanded]-->
        @if(config('system.settings_customfields_display_tasks') == 'expanded')
        @include('misc.customfields')
        @endif
        <!--/#CUSTOMER FIELDS [expanded]-->

        <div class="line"></div>
        <!--spacer-->
        <div class="spacer row">
            <div class="col-sm-12 col-lg-8">
                <span class="title">{{ cleanLang(__('lang.description')) }}</span class="title">
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="switch  text-right">
                    <label>
                        <input type="checkbox" name="show_more_settings_tasks" id="show_more_settings_tasks"
                            class="js-switch-toggle-hidden-content" data-target="task_description_section">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>
        </div>
        <!--spacer-->

        <!--description-->
        <div class="hidden" id="task_description_section">
            <div class="form-group row">
                <div class="col-sm-12">
                    <textarea id="project_description" name="task_description"
                        class="tinymce-textarea">{{ $task->task_description ?? '' }}</textarea>
                </div>
            </div>
        </div>

        <!--CUSTOMER FIELDS [collapsed]-->
        @if(config('system.settings_customfields_display_tasks') == 'toggled')
        <div class="spacer row">
            <div class="col-sm-12 col-lg-8">
                <span class="title">{{ cleanLang(__('lang.more_information')) }}</span class="title">
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="switch  text-right">
                    <label>
                        <input type="checkbox" name="add_client_option_other" id="add_client_option_other"
                            class="js-switch-toggle-hidden-content" data-target="leads_custom_fields_collaped">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>
        </div>
        <div id="leads_custom_fields_collaped" class="hidden">
            @if(config('app.application_demo_mode'))
            <!--DEMO INFO-->
            <div class="alert alert-info">
                <h5 class="text-info"><i class="sl-icon-info"></i> Demo Info</h5>
                These are custom fields. You can change them or <a
                    href="{{ url('app/settings/customfields/projects') }}">create your own.</a>
            </div>
            @endif

            @include('misc.customfields')
        </div>
        @endif
        <!--/#CUSTOMER FIELDS [collapsed]-->

        @if(config('visibility.task_modal_additional_options'))
        <!--spacer-->
        <div class="spacer row">
            <div class="col-sm-12 col-lg-8">
                <span class="title">{{ cleanLang(__('lang.options')) }}</span class="title">
            </div>
            <div class="col-sm-12 col-lg-4">
                <div class="switch  text-right">
                    <label>
                        <input type="checkbox" name="show_more_settings_tasks2" id="show_more_settings_tasks2"
                            class="js-switch-toggle-hidden-content" data-target="additional_information_section">
                        <span class="lever switch-col-light-blue"></span>
                    </label>
                </div>
            </div>
        </div>
        <!--spacer-->
        <!--option toggle-->
        <div class="hidden" id="additional_information_section">

            <!--start time / end time-->
            @if(config('system.settings_tasks_start_end_time') == 'enabled')
            <div class="form-group row">
                <div class="col-sm-12 col-lg-6">
                    <label class="text-left control-label col-form-label">{{ cleanLang(__('lang.start_time')) }}</label>
                    <input type="time" class="form-control form-control-sm" name="task_start_time" 
                           value="{{ $task->task_start_time ?? '' }}">
                </div>
                <div class="col-sm-12 col-lg-6">
                    <label class="text-left control-label col-form-label">{{ cleanLang(__('lang.end_time')) }}</label>
                    <input type="time" class="form-control form-control-sm" name="task_end_time" 
                           value="{{ $task->task_end_time ?? '' }}">
                </div>
            </div>
            @endif

            <!--estimated time-->
            @if(config('system.settings_tasks_estimated_time') == 'enabled')
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.estimated_time')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <div class="row">
                        <div class="col-sm-6">
                            <input type="number" class="form-control form-control-sm" name="task_estimated_time_value" 
                                   value="{{ isset($task->task_estimated_time) ? explode(' ', $task->task_estimated_time)[0] ?? '' : '' }}" 
                                   placeholder="1" min="0" step="0.5">
                        </div>
                        <div class="col-sm-6">
                            <select class="form-control form-control-sm" name="task_estimated_time_unit">
                                <option value="">{{ cleanLang(__('lang.select_unit')) }}</option>
                                <option value="h" {{ (isset($task->task_estimated_time) && str_contains($task->task_estimated_time, 'h')) ? 'selected' : '' }}>{{ cleanLang(__('lang.hours')) }}</option>
                                <option value="d" {{ (isset($task->task_estimated_time) && str_contains($task->task_estimated_time, 'd')) ? 'selected' : '' }}>{{ cleanLang(__('lang.days')) }}</option>
                                <option value="w" {{ (isset($task->task_estimated_time) && str_contains($task->task_estimated_time, 'w')) ? 'selected' : '' }}>{{ cleanLang(__('lang.weeks')) }}</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <!--location-->
            @if(config('system.settings_tasks_location') == 'enabled')
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.location')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" name="task_location" 
                               value="{{ $task->task_location ?? '' }}" 
                               placeholder="Enter location or Google Maps address"
                               id="task_location_input">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary btn-sm" id="show-location-map-modal-btn"
                                title="{{ cleanLang(__('lang.show_on_map')) }}">
                                <i class="mdi mdi-map-marker"></i>
                            </button>
                        </div>
                    </div>
                    <small class="form-text text-muted">{{ cleanLang(__('lang.location_help_text')) }}</small>
                </div>
            </div>
            @endif

            <!--task color-->
            @if(config('system.settings_tasks_color') == 'enabled')
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.task_color')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <div class="row">
                        <div class="col-sm-6">
                            <label class="form-label small">{{ cleanLang(__('lang.custom_color')) }}</label>
                            <input type="color" class="form-control form-control-sm" name="task_color_custom" 
                                   value="{{ $task->task_color ?? '#007bff' }}" 
                                   style="height: 38px;">
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label small">{{ cleanLang(__('lang.default_colors')) }}</label>
                            <select class="form-control form-control-sm" name="task_color_preset" id="task_color_preset">
                                <option value="">{{ cleanLang(__('lang.select_color')) }}</option>
                                <option value="#007bff" {{ (isset($task->task_color) && $task->task_color == '#007bff') ? 'selected' : '' }} style="background-color: #007bff; color: white;">{{ cleanLang(__('lang.blue')) }}</option>
                                <option value="#28a745" {{ (isset($task->task_color) && $task->task_color == '#28a745') ? 'selected' : '' }} style="background-color: #28a745; color: white;">{{ cleanLang(__('lang.green')) }}</option>
                                <option value="#ffc107" {{ (isset($task->task_color) && $task->task_color == '#ffc107') ? 'selected' : '' }} style="background-color: #ffc107; color: black;">{{ cleanLang(__('lang.yellow')) }}</option>
                                <option value="#dc3545" {{ (isset($task->task_color) && $task->task_color == '#dc3545') ? 'selected' : '' }} style="background-color: #dc3545; color: white;">{{ cleanLang(__('lang.red')) }}</option>
                                <option value="#6f42c1" {{ (isset($task->task_color) && $task->task_color == '#6f42c1') ? 'selected' : '' }} style="background-color: #6f42c1; color: white;">{{ cleanLang(__('lang.purple')) }}</option>
                                <option value="#fd7e14" {{ (isset($task->task_color) && $task->task_color == '#fd7e14') ? 'selected' : '' }} style="background-color: #fd7e14; color: white;">{{ cleanLang(__('lang.orange')) }}</option>
                                <option value="#20c997" {{ (isset($task->task_color) && $task->task_color == '#20c997') ? 'selected' : '' }} style="background-color: #20c997; color: white;">{{ cleanLang(__('lang.teal')) }}</option>
                                <option value="#6c757d" {{ (isset($task->task_color) && $task->task_color == '#6c757d') ? 'selected' : '' }} style="background-color: #6c757d; color: white;">{{ cleanLang(__('lang.gray')) }}</option>
                                <option value="#e83e8c" {{ (isset($task->task_color) && $task->task_color == '#e83e8c') ? 'selected' : '' }} style="background-color: #e83e8c; color: white;">{{ cleanLang(__('lang.pink')) }}</option>
                                <option value="#17a2b8" {{ (isset($task->task_color) && $task->task_color == '#17a2b8') ? 'selected' : '' }} style="background-color: #17a2b8; color: white;">{{ cleanLang(__('lang.cyan')) }}</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="task_color" id="task_color_final" value="{{ $task->task_color ?? '#007bff' }}">
                </div>
            </div>
            @endif

            <!--due date-->
            @if(config('visibility.tasks_standard_features'))
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.target_date')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <input type="text" class="form-control form-control-sm pickadate" name="task_date_due"
                        autocomplete="off" placeholder="">
                    <input class="mysql-date" type="hidden" name="task_date_due" id="task_date_due">
                </div>
            </div>
            @endif

            <!--tags-->
            <div class="form-group row">
                <label
                    class="col-sm-12 col-lg-3 text-left control-label col-form-label">{{ cleanLang(__('lang.tags')) }}</label>
                <div class="col-sm-12 col-lg-9">
                    <select name="tags" id="tags"
                        class="form-control form-control-sm select2-multiple {{ runtimeAllowUserTags() }} select2-hidden-accessible"
                        multiple="multiple" tabindex="-1" aria-hidden="true">
                        <!--array of selected tags-->
                        @if(isset($page['section']) && $page['section'] == 'edit')
                        @foreach($lead->tags as $tag)
                        @php $selected_tags[] = $tag->tag_title ; @endphp
                        @endforeach
                        @endif
                        <!--/#array of selected tags-->
                        @foreach($tags as $tag)
                        <option value="{{ $tag->tag_title }}"
                            {{ runtimePreselectedInArray($tag->tag_title ?? '', $selected_tags  ?? []) }}>
                            {{ $tag->tag_title }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!--[toggled] project options-->
            <div class="toggle_task_type add_task_toggle_container_project">
                <div class="form-group form-group-checkbox row">
                    <div class="col-12 p-t-5">
                        <div class="pull-left min-w-200">
                            <label>{{ cleanLang(__('lang.visible_to_client')) }}</label>
                        </div>
                        <div class="pull-left p-l-10">
                            @if(isset($page['section']) && $page['section'] == 'create')
                            <input type="checkbox" id="task_client_visibility" name="task_client_visibility"
                                {{ runtimeTasksDefaults('task_client_visibility') }}
                                class="filled-in chk-col-light-blue">
                            @endif
                            @if(isset($page['section']) && $page['section'] == 'edit')
                            <input type="checkbox" id="task_client_visibility" name="task_client_visibility"
                                class="filled-in chk-col-light-blue">
                            @endif
                            <label for="task_client_visibility"></label>
                        </div>
                    </div>
                </div>
                <div class="form-group form-group-checkbox row">
                    <div class="col-12 p-t-5">
                        <div class="pull-left min-w-200">
                            <label>{{ cleanLang(__('lang.billable')) }}</label>
                        </div>
                        <div class="pull-left p-l-10">
                            @if(isset($page['section']) && $page['section'] == 'create')
                            <input type="checkbox" id="task_billable" name="task_billable"
                                {{ runtimeTasksDefaults('task_billable') }} class="filled-in chk-col-light-blue">
                            @endif
                            @if(isset($page['section']) && $page['section'] == 'edit')
                            <input type="checkbox" id="task_billable" name="task_billable"
                                class="filled-in chk-col-light-blue">
                            @endif
                            <label for="task_billable"></label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--option toggle-->
        @endif


        <!--redirect to project-->
        @if(config('visibility.task_show_task_option'))
        <div class="line"></div>
        <div class="form-group form-group-checkbox row">
            <div class="col-12 text-left p-t-5">
                <input type="checkbox" id="show_after_adding" name="show_after_adding"
                    class="filled-in chk-col-light-blue" checked="checked">
                <label for="show_after_adding">{{ cleanLang(__('lang.show_task_after_adding')) }}</label>
            </div>
        </div>
        @endif

        <!--pass source-->
        <input type="hidden" name="source" value="{{ request('source') }}">
        <input type="hidden" name="ref" value="{{ request('ref') }}">

        <!--notes-->
        <div class="row">
            <div class="col-12">
                <div><small><strong>* {{ cleanLang(__('lang.required')) }}</strong></small></div>
            </div>
        </div>
    </div>
</div>

<style>
/* Google Places Autocomplete Styling for Modal */
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