<div class="row">
    <div class="col-lg-12">

        <!-- Fecha desde -->
        <div class="form-group row">
            <label class="col-sm-12 text-left control-label col-form-label required">
                {{ cleanLang(__('Desde')) }}*
            </label>
            <div class="col-sm-12">
                <input type="date" class="form-control form-control-sm" name="start_date" id="start_date" required>
            </div>
        </div>

        <!-- Fecha hasta -->
        <div class="form-group row">
            <label class="col-sm-12 text-left control-label col-form-label required">
                {{ cleanLang(__('Hasta')) }}*
            </label>
            <div class="col-sm-12">
                <input type="date" class="form-control form-control-sm" name="end_date" id="end_date" required>
            </div>
        </div>

        <!-- Tópicos -->
        <div class="form-group row">
            <label class="col-sm-12 text-left control-label col-form-label required">
                {{ cleanLang(__('Tópicos a analizar')) }}*
            </label>
            <div class="col-sm-12">
                <div class="form-check">
                    <label><input type="checkbox" name="topics[]" value="facturacion"> Facturación</label><br>
                    <label><input type="checkbox" name="topics[]" value="proyectos"> Proyectos</label><br>
                    <label><input type="checkbox" name="topics[]" value="comentarios"> Comentarios</label><br>
                    <label><input type="checkbox" name="topics[]" value="encuestas"> Encuestas</label><br>
                    <label><input type="checkbox" name="topics[]" value="mensajes"> Mensajes Internos</label>
                </div>
            </div>
        </div>

        <!-- Enviar -->
        <div class="form-group row">
            <div class="col-sm-12">
                <button type="button" class="btn btn-success" id="btn-generate-analysis">
                    <i class="ti-bar-chart"></i> {{ cleanLang(__('Generar Análisis')) }}
                </button>
            </div>
        </div>

        <!-- Placeholder para errores o validaciones -->
        <div class="row">
            <div class="col-12">
                <div><small><strong>* {{ cleanLang(__('lang.required')) }}</strong></small></div>
            </div>
        </div>

    </div>
</div>
