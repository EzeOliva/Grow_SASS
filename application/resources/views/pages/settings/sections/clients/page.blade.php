@extends('pages.settings.ajaxwrapper')
@section('settings-page')
<!--settings-->
<form class="form">
    <!--allow registration-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label">{{ cleanLang(__('lang.allow_customers_to_signup')) }}</label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_clients_registration" name="settings_clients_registration"
                class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_clients_registration'] ?? '') }}>
            <label for="settings_clients_registration"></label>
        </div>
    </div>

    <!--allow clients to login-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label">{{ cleanLang(__('lang.allow_clients_to_login')) }}</label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_clients_app_login" name="settings_clients_app_login"
                class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_clients_app_login'] ?? '') }}>
            <label for="settings_clients_app_login"></label>
        </div>
    </div>


    <!--enable shipping address-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label">{{ cleanLang(__('lang.enable_shipping_address')) }}</label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_clients_shipping_address" name="settings_clients_shipping_address"
                class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_clients_shipping_address'] ?? '') }}>
            <label for="settings_clients_shipping_address"></label>
        </div>
    </div>

    <!--disable emails-->
    <div class="form-group form-group-checkbox row">
        <label class="col-4 col-form-label">{{ cleanLang(__('lang.disable_all_client_emails')) }}</label>
        <div class="col-8 p-t-5">
            <input type="checkbox" id="settings_clients_disable_email_delivery"
                name="settings_clients_disable_email_delivery" class="filled-in chk-col-light-blue"
                {{ runtimePrechecked($settings['settings_clients_disable_email_delivery'] ?? '') }}>
            <label for="settings_clients_disable_email_delivery"></label>
        </div>
    </div>

    <!--importing settings-->
    <h5 class="p-t-20">{{ cleanLang(__('lang.importing_clients_settings')) }}</h5>
    <div class="line"></div>

    <div class="modal-selector m-t-5 m-l-0 m-r-0">
        
        <h6 class="m-b-20">@lang('lang.avoid_duplicates') <span class="align-middle text-info font-16"
                data-toggle="tooltip" title="@lang('lang.avoid_duplicates_info')" data-placement="top"><i
                    class="ti-info-alt"></i></span></h6>


        <!--settings2_importing_clients_duplicates_company-->
        <div class="form-group form-group-checkbox row">
            <label class="col-4 col-form-label text-left">@lang('lang.company_name')</label>
            <div class="col-8 text-left p-t-5">
                <input type="checkbox" id="settings2_importing_clients_duplicates_company"
                    name="settings2_importing_clients_duplicates_company" class="filled-in chk-col-light-blue"
                    {{ runtimePrechecked($settings2->settings2_importing_clients_duplicates_company ?? '') }}>
                <label class="p-l-30" for="settings2_importing_clients_duplicates_company"></label>
            </div>
        </div>


        <!--settings2_importing_clients_duplicates_email-->
        <div class="form-group form-group-checkbox row">
            <label class="col-4 col-form-label text-left">@lang('lang.email')</label>
            <div class="col-8 text-left p-t-5">
                <input type="checkbox" id="settings2_importing_clients_duplicates_email"
                    name="settings2_importing_clients_duplicates_email" class="filled-in chk-col-light-blue"
                    {{ runtimePrechecked($settings2->settings2_importing_clients_duplicates_email ?? '') }}>
                <label class="p-l-30" for="settings2_importing_clients_duplicates_email"></label>
            </div>
        </div>


        <!--settings2_importing_clients_duplicates_telephone-->
        <div class="form-group form-group-checkbox row">
            <label class="col-4 col-form-label text-left">@lang('lang.telephone')</label>
            <div class="col-8 text-left p-t-5">
                <input type="checkbox" id="settings2_importing_clients_duplicates_telephone"
                    name="settings2_importing_clients_duplicates_telephone" class="filled-in chk-col-light-blue"
                    {{ runtimePrechecked($settings2->settings2_importing_clients_duplicates_telephone ?? '') }}>
                <label class="p-l-30" for="settings2_importing_clients_duplicates_telephone"></label>
            </div>
        </div>

    </div>

    <h5 class="p-t-20">Pipeline de clientes (por tenant)</h5>
    <div class="line"></div>

    <div class="alert alert-info m-b-20">
        Configura aquí las etapas secuenciales del pipeline. El orden se toma por el campo "Posición".
    </div>

    <div class="table-responsive m-b-20">
        <table class="table table-sm table-bordered" id="client-stages-table">
            <thead>
                <tr>
                    <th>Etapa</th>
                    <th>Descripción / propósito</th>
                    <th width="120">Posición</th>
                    <th width="120">Activa</th>
                    <th width="180">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse(($stages ?? collect([])) as $stage)
                <tr>
                    <td>
                        <input type="text" class="form-control form-control-sm js-stage-title" value="{{ $stage->client_stage_title }}">
                    </td>
                    <td>
                        <textarea class="form-control form-control-sm js-stage-description" rows="2" placeholder="Qué significa esta etapa y su objetivo">{{ $stage->client_stage_description ?? '' }}</textarea>
                    </td>
                    <td>
                        <input type="number" min="1" class="form-control form-control-sm js-stage-position" value="{{ (int) $stage->client_stage_position }}">
                    </td>
                    <td class="text-center">
                        <input type="checkbox" class="filled-in chk-col-light-blue js-stage-active" id="stage-active-{{ $stage->client_stage_id }}" {{ ($stage->client_stage_active ?? 'yes') === 'yes' ? 'checked' : '' }}>
                        <label for="stage-active-{{ $stage->client_stage_id }}"></label>
                    </td>
                    <td>
                        <button type="button" class="btn btn-sm btn-primary js-client-stage-save" data-id="{{ $stage->client_stage_id }}">Guardar</button>
                        <button type="button" class="btn btn-sm btn-danger js-client-stage-delete" data-id="{{ $stage->client_stage_id }}">Eliminar</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center text-muted">No hay etapas configuradas.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="card card-body m-b-20">
        <h6>Nueva etapa</h6>
        <div class="row">
            <div class="col-md-4">
                <label class="control-label">Nombre</label>
                <input type="text" id="new-client-stage-title" class="form-control" placeholder="Ej. Onboarding">
            </div>
            <div class="col-md-4">
                <label class="control-label">Descripción / propósito</label>
                <textarea id="new-client-stage-description" class="form-control" rows="2" placeholder="Explica qué se busca en esta etapa"></textarea>
            </div>
            <div class="col-md-2">
                <label class="control-label">Posición</label>
                <input type="number" min="1" id="new-client-stage-position" class="form-control" value="1">
            </div>
            <div class="col-md-2 p-t-30">
                <input type="checkbox" id="new-client-stage-active" class="filled-in chk-col-light-blue" checked>
                <label for="new-client-stage-active">Activa</label>
            </div>
            <div class="col-md-2 p-t-25 text-right">
                <button type="button" class="btn btn-success" id="btn-client-stage-create">Agregar etapa</button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            function notify(type, message) {
                if (window.NX && typeof NX.notification === 'function') {
                    NX.notification(type, message);
                } else if (window.$ && $.growl) {
                    $.growl({
                        title: '',
                        message: message
                    }, {
                        type: type === 'success' ? 'success' : 'danger'
                    });
                } else {
                    alert(message);
                }
            }

            function parseError(xhr) {
                if (!xhr) {
                    return 'Error inesperado';
                }
                if (xhr.responseJSON && xhr.responseJSON.notification && xhr.responseJSON.notification.value) {
                    return xhr.responseJSON.notification.value;
                }
                return xhr.responseText || 'Error inesperado';
            }

            function reloadSettingsClients() {
                window.location.reload();
            }

            $(document).off('click.clientStageCreate').on('click.clientStageCreate', '#btn-client-stage-create', function () {
                var title = ($('#new-client-stage-title').val() || '').trim();
                var description = ($('#new-client-stage-description').val() || '').trim();
                var position = parseInt($('#new-client-stage-position').val(), 10) || 1;
                var active = $('#new-client-stage-active').is(':checked');

                if (!title) {
                    notify('error', 'El nombre de la etapa es obligatorio');
                    return;
                }
                if (!description) {
                    notify('error', 'La descripción de la etapa es obligatoria');
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: '/settings/clients/stages',
                    data: {
                        _token: '{{ csrf_token() }}',
                        client_stage_title: title,
                        client_stage_description: description,
                        client_stage_position: position,
                        client_stage_active: active ? 'on' : 'off'
                    },
                    success: function () {
                        notify('success', 'Etapa creada');
                        reloadSettingsClients();
                    },
                    error: function (xhr) {
                        notify('error', parseError(xhr));
                    }
                });
            });

            $(document).off('click.clientStageSave').on('click.clientStageSave', '.js-client-stage-save', function () {
                var button = $(this);
                var row = button.closest('tr');
                var id = button.data('id');
                var title = (row.find('.js-stage-title').val() || '').trim();
                var description = (row.find('.js-stage-description').val() || '').trim();
                var position = parseInt(row.find('.js-stage-position').val(), 10) || 1;
                var active = row.find('.js-stage-active').is(':checked');

                if (!title) {
                    notify('error', 'El nombre de la etapa es obligatorio');
                    return;
                }
                if (!description) {
                    notify('error', 'La descripción de la etapa es obligatoria');
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: '/settings/clients/stages/' + id,
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'PUT',
                        client_stage_title: title,
                        client_stage_description: description,
                        client_stage_position: position,
                        client_stage_active: active ? 'on' : 'off'
                    },
                    success: function () {
                        notify('success', 'Etapa actualizada');
                        reloadSettingsClients();
                    },
                    error: function (xhr) {
                        notify('error', parseError(xhr));
                    }
                });
            });

            $(document).off('click.clientStageDelete').on('click.clientStageDelete', '.js-client-stage-delete', function () {
                var id = $(this).data('id');
                if (!confirm('¿Eliminar esta etapa?')) {
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: '/settings/clients/stages/' + id,
                    data: {
                        _token: '{{ csrf_token() }}',
                        _method: 'DELETE'
                    },
                    success: function () {
                        notify('success', 'Etapa eliminada');
                        reloadSettingsClients();
                    },
                    error: function (xhr) {
                        notify('error', parseError(xhr));
                    }
                });
            });
        })();
    </script>

    @if(config('system.settings_type') == 'standalone')
    <!--[standalone] - settings documentation help-->
    <div>
        <a href="https://growcrm.io/documentation" target="_blank" class="btn btn-sm btn-info help-documentation"><i
                class="ti-info-alt"></i>
            {{ cleanLang(__('lang.help_documentation')) }}</a>
    </div>
    @endif

    <!--buttons-->
    <div class="text-right">
        <button type="submit" id="commonModalSubmitButton"
            class="btn btn-rounded-x btn-danger waves-effect text-left js-ajax-ux-request" data-url="/settings/clients"
            data-loading-target="" data-ajax-type="PUT" data-type="form"
            data-on-start-submit-button="disable">{{ cleanLang(__('lang.save_changes')) }}</button>
    </div>
</form>
@endsection