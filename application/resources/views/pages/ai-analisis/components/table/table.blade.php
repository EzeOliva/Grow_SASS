<div class="card count-{{ @count($analyses ?? []) }}" id="ai-analysis-table-wrapper">
    <div class="card-body">
        <div class="table-responsive">
            @if (@count($analyses ?? []) > 0)
            <table id="ai-analysis-table" class="table m-t-0 m-b-0 table-hover no-wrap contact-list" data-page-size="10">
                <thead>
                    <tr>
                        @if(config('visibility.analysis_col_checkboxes'))
                        <th class="list-checkbox-wrapper">
                            <!--checkbox masivos-->
                            <span class="list-checkboxes display-inline-block w-px-20">
                                <input type="checkbox" id="listcheckbox-analyses"
                                       class="listcheckbox-all filled-in chk-col-light-blue"
                                       data-actions-container-class="analysis-checkbox-actions-container"
                                       data-children-checkbox-class="listcheckbox-analyses">
                                <label for="listcheckbox-analyses"></label>
                            </span>
                        </th>
                        @endif
                        <th class="analysis_col_generated_by">{{ cleanLang(__('Generado por')) }}</th>
                        <th class="analysis_col_date_range">{{ cleanLang(__('Rango de Fechas')) }}</th>
                        <th class="analysis_col_topics">{{ cleanLang(__('Tópicos')) }}</th>
                        <th class="analysis_col_date">{{ cleanLang(__('Fecha Generado')) }}</th>
                        <th class="analysis_col_action"><a href="javascript:void(0)">{{ cleanLang(__('Acciones')) }}</a></th>
                    </tr>
                </thead>
                <tbody id="ai-analysis-td-container">
                    <!--ajax contenido acá-->
                    @include('pages.client.ai-analysis.components.table.ajax')
                    <!--ajax contenido acá-->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="20">
                            <!--botón cargar más-->
                            @include('misc.load-more-button')
                            <!--botón cargar más-->
                        </td>
                    </tr>
                </tfoot>
            </table>
            @endif
            @if (@count($analyses ?? []) == 0)
            <!--sin resultados-->
            @include('notifications.no-results-found')
            <!--sin resultados-->
            @endif
        </div>
    </div>
</div>
