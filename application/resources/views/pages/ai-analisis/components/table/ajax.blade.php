@foreach($analyses as $analysis)
<!--each row-->
<tr id="analysis_{{ $analysis->id }}">
    @if(config('visibility.analysis_col_checkboxes'))
    <td class="analysis_col_checkbox checkitem" id="analysis_col_checkbox_{{ $analysis->id }}">
        <span class="list-checkboxes display-inline-block w-px-20">
            <input type="checkbox" id="listcheckbox-analyses-{{ $analysis->id }}" name="ids[{{ $analysis->id }}]"
                class="listcheckbox listcheckbox-analyses filled-in chk-col-light-blue"
                data-actions-container-class="analysis-checkbox-actions-container">
            <label for="listcheckbox-analyses-{{ $analysis->id }}"></label>
        </span>
    </td>
    @endif

    <!-- Generado por -->
    <td class="analysis_col_generated_by">
        <img src="{{ getUsersAvatar($analysis->user->avatar_directory, $analysis->user->avatar_filename) }}" alt="user"
            class="img-circle avatar-xsmall">
        {{ $analysis->user->first_name ?? runtimeUnkownUser() }}
    </td>

    <!-- Rango de fechas -->
    <td class="analysis_col_date_range">
        {{ runtimeDate($analysis->start_date) }} - {{ runtimeDate($analysis->end_date) }}
    </td>

    <!-- Tópicos -->
    <td class="analysis_col_topics">
        @if (!empty($analysis->topics))
            @foreach (explode(',', $analysis->topics) as $topic)
                <span class="label label-outline-default">{{ ucfirst(trim($topic)) }}</span>
            @endforeach
        @else
            <span>---</span>
        @endif
    </td>

    <!-- Fecha de análisis -->
    <td class="analysis_col_date">
        {{ runtimeDate($analysis->created_at) }}
    </td>

    <!-- Acciones -->
    <td class="analysis_col_action actions_column">
        <span class="list-table-action dropdown font-size-inherit">
            <a href="javascript:void(0)" title="{{ cleanLang(__('lang.view')) }}"
                class="data-toggle-action-tooltip btn btn-outline-info btn-circle btn-sm show-modal-button js-ajax-ux-request"
                data-toggle="modal"
                data-url="{{ url('/') }}/ai-analysis/{{ $analysis->id }}"
                data-target="#plainModal"
                data-loading-target="plainModalBody"
                data-modal-title="Análisis IA">
                <i class="ti-new-window"></i>
            </a>

            @if($analysis->permission_edit_delete)
            <button type="button" title="{{ cleanLang(__('lang.delete')) }}"
                class="data-toggle-action-tooltip btn btn-outline-danger btn-circle btn-sm confirm-action-danger"
                data-confirm-title="{{ cleanLang(__('lang.delete_item')) }}"
                data-confirm-text="{{ cleanLang(__('lang.are_you_sure')) }}"
                data-ajax-type="DELETE"
                data-url="{{ url('/') }}/ai-analysis/{{ $analysis->id }}">
                <i class="sl-icon-trash"></i>
            </button>
            @endif
        </span>
    </td>
</tr>
@endforeach
