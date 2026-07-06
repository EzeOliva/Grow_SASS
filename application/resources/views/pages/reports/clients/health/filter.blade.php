<div class="reports-list-page-filter-container">
    <form class="form-inline row gy-2 gx-3 align-items-center" id="reports-clients-stage-health-filter-form">

        <div class="form-group row">
            <select class="select2-basic form-control form-control-sm select2-preselected" id="health_period"
                style="width:180px;" name="health_period" data-preselected="{{ $selected_period ?? 'quarter' }}" data-width="resolve">
                <option></option>
                <optgroup label="Período">
                    <option value="month">Último mes</option>
                    <option value="quarter">Último trimestre</option>
                </optgroup>
            </select>
        </div>

        <div class="form-group row">
            <select class="select2-basic form-control form-control-sm select2-preselected" id="filter_client_stage_id"
                style="width:220px;" name="filter_client_stage_id" data-preselected="{{ $selected_stage ?? 'all' }}" data-width="resolve">
                <option></option>
                <optgroup label="Etapa">
                    <option value="all">Todas las etapas</option>
                    @foreach(($stage_options ?? []) as $stage)
                        <option value="{{ $stage->client_stage_id }}">{{ $stage->client_stage_title }}</option>
                    @endforeach
                </optgroup>
            </select>
        </div>

        <div class="col-auto">
            <input type="hidden" name="report-form" value="filter">
            <button type="submit" class="btn btn-info btn-sm waves-effect text-left ajax-request"
                data-url="{{ url('report/clients/health-by-stage?action=load') }}"
                data-loading-target="report-results-container" data-ajax-type="POST"
                data-form-id="reports-clients-stage-health-filter-form"
                data-on-start-submit-button="disable">Actualizar informe</button>
        </div>
    </form>
</div>
