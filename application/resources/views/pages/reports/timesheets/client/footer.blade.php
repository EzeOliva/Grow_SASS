<!--totals-->
<tr class="report-results-table-totals">
    <td colspan="3">@lang('lang.page_totals')</td>


    <!--sum_not_invoiced-->
    <td>
        {{ runtimeSecondsWholeHours($totals['sum_not_invoiced']) }}:{{ runtimeSecondsWholeMinutesZero($totals['sum_not_invoiced']) }}
    </td>

    <!--sum_invoiced-->
    <td>
        {{ runtimeSecondsWholeHours($totals['sum_invoiced']) }}:{{ runtimeSecondsWholeMinutesZero($totals['sum_invoiced']) }}
    </td>

    <!--sum_hours-->
    <td>
        {{ runtimeSecondsWholeHours($totals['sum_hours']) }}:{{ runtimeSecondsWholeMinutesZero($totals['sum_hours']) }}
    </td>

</tr>

<!--pagination-->
<tr>
    <td class="pagination-container" data-tableexport-display="none" colspan="6">
        <div class="pagination">
            {{ $timesheets->links('pages.reports.components.misc.pagination') }}
        </div>
    </td>
</tr>