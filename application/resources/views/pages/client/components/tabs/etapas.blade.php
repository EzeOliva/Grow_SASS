<div class="card m-b-20">
    <div class="card-body">
        <h5 class="card-title">Etapa actual</h5>

        <form id="client-stage-form" class="row">
            <div class="col-md-5">
                <label class="control-label">Etapa</label>
                <select class="form-control" id="client_stage_id" name="client_stage_id" required>
                    <option value="">Seleccione</option>
                    @foreach(($stages ?? collect([])) as $stage)
                        <option value="{{ $stage->client_stage_id }}" {{ (int)($client->client_stage_id ?? 0) === (int)$stage->client_stage_id ? 'selected' : '' }}>{{ $stage->client_stage_title }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-5">
                <label class="control-label">Detalle del cambio</label>
                <input type="text" class="form-control" id="change_detail" name="change_detail" placeholder="Opcional">
            </div>
            <div class="col-md-2 p-t-30 text-right">
                <button type="submit" id="client-stage-submit" class="btn btn-primary btn-block">Actualizar</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Historial de etapas</h5>
        <div class="table-responsive">
            <table class="table table-sm table-bordered" id="client-stage-history-table">
                <thead>
                    <tr>
                        <th>Fecha</th>
                        <th>Desde</th>
                        <th>Hacia</th>
                        <th>Detalle</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($history ?? collect([])) as $row)
                    <tr>
                        <td>{{ $row->changed_at ? \Carbon\Carbon::parse($row->changed_at)->format('d-m-Y H:i') : '' }}</td>
                        <td>{{ $row->from_stage_title ?? 'Sin etapa' }}</td>
                        <td>{{ $row->to_stage_title ?? '-' }}</td>
                        <td>{{ $row->change_detail ?: '-' }}</td>
                        <td>{{ trim(($row->first_name ?? '') . ' ' . ($row->last_name ?? '')) ?: '-' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">Sin cambios de etapa aún.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    (function () {
        function notify(type, message) {
            if (window.NX && typeof NX.notification === 'function') {
                NX.notification(type, message);
            } else if (window.$ && $.growl) {
                $.growl({ title: '', message: message }, { type: type === 'success' ? 'success' : 'danger' });
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

        $(document).off('submit.clientStageForm').on('submit.clientStageForm', '#client-stage-form', function (e) {
            e.preventDefault();

            var button = $('#client-stage-submit');
            if (button.prop('disabled')) {
                return;
            }

            var stageId = ($('#client_stage_id').val() || '').trim();
            if (!stageId) {
                notify('error', 'Selecciona una etapa');
                return;
            }

            button.prop('disabled', true).text('Guardando...');

            $.ajax({
                type: 'POST',
                url: '{{ url('/clients/' . $client->client_id . '/client-stage') }}',
                data: {
                    _token: '{{ csrf_token() }}',
                    client_stage_id: stageId,
                    change_detail: ($('#change_detail').val() || '').trim()
                },
                success: function (response) {
                    notify('success', response.message || 'Etapa actualizada');
                    $('#change_detail').val('');

                    if (response && response.item && response.item.client_id) {
                        var row = $('#client_' + response.item.client_id);
                        if (row.length) {
                            row.find('.js-client-stage-label').text(response.item.client_stage_title || '-');
                            row.find('.js-client-stage-select').val(String(response.item.client_stage_id || ''));
                        }
                    }

                    window.location.reload();
                },
                error: function (xhr) {
                    notify('error', parseError(xhr));
                },
                complete: function () {
                    button.prop('disabled', false).text('Actualizar');
                }
            });
        });
    })();
</script>
