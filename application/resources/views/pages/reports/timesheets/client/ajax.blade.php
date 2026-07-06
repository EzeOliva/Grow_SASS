<!--rows-->
@foreach($timesheets as $timesheet)
<tr>

    <!--full_name-->
    <td>
        <a href="{{ url('/clients/'.$timesheet->client_id ) }}">{{ $timesheet->client_company_name ?? '---'  }}</a>
    </td>

    <!--collaborator-->
    <td>
        {{ $timesheet->first_name ?? '---' }} {{ $timesheet->last_name ?? '' }}
    </td>

    <!--project-->
    <td>
        @if(!empty($timesheet->project_id) && $timesheet->project_id > 0)
        <a href="{{ url('/projects/'.$timesheet->project_id ) }}">{{ $timesheet->project_title ?? '---' }}</a>
        @else
        <span class="text-muted">{{ cleanLang(__('lang.none')) }}</span>
        @endif
    </td>

    <!--sum_not_invoiced-->
    <td>
        {{ runtimeSecondsWholeHours($timesheet->sum_not_invoiced) }}:{{ runtimeSecondsWholeMinutesZero($timesheet->sum_not_invoiced) }}
    </td>


    <!--sum_invoiced-->
    <td>
        {{ runtimeSecondsWholeHours($timesheet->sum_invoiced) }}:{{ runtimeSecondsWholeMinutesZero($timesheet->sum_invoiced) }}
    </td>

    <!--sum_hours-->
    <td>
        {{ runtimeSecondsWholeHours($timesheet->sum_hours) }}:{{ runtimeSecondsWholeMinutesZero($timesheet->sum_hours) }}
    </td>

</tr>
@endforeach