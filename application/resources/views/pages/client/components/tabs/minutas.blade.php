<div class="container py-4" id="client-minutas-tab">
    <div class="card shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="fas fa-file-alt text-primary mr-2"></i>Minutas</h4>
            <small class="text-muted">Uso interno</small>
        </div>

        <form id="client-minutas-form" class="mb-4" action="{{ url('clients/' . $client->client_id . '/client-minutas') }}" method="POST">
            @csrf
            <input type="hidden" name="editing_minuta_id" id="editing_minuta_id" value="">
            <div class="form-row">
                <div class="col-md-3 mb-3">
                    <label class="control-label">Fecha</label>
                    <input type="date" name="minuta_date" class="form-control" required>
                </div>
                <div class="col-md-9 mb-3">
                    <label class="control-label">Detalle</label>
                    <textarea name="minuta_detail" rows="5" class="form-control" placeholder="Pegá acá el texto completo de la minuta" required></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" id="minuta-submit-btn">Guardar minuta</button>
            <button type="button" class="btn btn-secondary" id="minuta-cancel-edit" style="display:none;">Cancelar edición</button>
        </form>

        <div id="client-minutas-list">
            @if($minutas->count())
                <div id="client-minutas-accordion">
                    @foreach($minutas as $minuta)
                        <div class="card border mb-2" id="minuta-item-{{ $minuta->client_minuta_id }}">
                            <div class="card-header bg-white" id="minuta-heading-{{ $minuta->client_minuta_id }}">
                                <a class="d-flex justify-content-between align-items-center text-dark" data-toggle="collapse"
                                    href="#minuta-collapse-{{ $minuta->client_minuta_id }}" role="button" aria-expanded="false"
                                    aria-controls="minuta-collapse-{{ $minuta->client_minuta_id }}">
                                    <span class="font-weight-bold">{{ $minuta->minuta_date }}</span>
                                    <span class="text-muted small">{{ \Carbon\Carbon::parse($minuta->minuta_date)->format('d-m-Y') }} | Ver detalle</span>
                                </a>
                            </div>
                            <div id="minuta-collapse-{{ $minuta->client_minuta_id }}" class="collapse" data-parent="#client-minutas-accordion">
                                <div class="card-body">
                                    <div class="text-muted" style="white-space: pre-wrap;">{{ $minuta->minuta_detail }}</div>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary minuta-edit-btn"
                                            data-id="{{ $minuta->client_minuta_id }}"
                                            data-date="{{ $minuta->minuta_date }}"
                                            data-detail="{{ e($minuta->minuta_detail) }}">Editar</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger minuta-delete-btn"
                                            data-id="{{ $minuta->client_minuta_id }}">Eliminar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-light mb-0" id="client-minutas-empty">Aún no hay minutas registradas.</div>
                <div id="client-minutas-accordion" style="display:none;"></div>
            @endif
        </div>
    </div>
</div>

<script>
(function () {
    function escapeHtml(text) {
        return $('<div>').text(text || '').html();
    }

    function formatDateForDisplay(dateStr) {
        if (!dateStr || dateStr.indexOf('-') === -1) {
            return dateStr || '';
        }
        var parts = dateStr.split('-');
        if (parts.length !== 3) {
            return dateStr;
        }
        return parts[2] + '-' + parts[1] + '-' + parts[0];
    }

    function notifySuccess(message) {
        if (window.NX && typeof NX.notification === 'function') {
            NX.notification({ type: 'success', message: message });
            return;
        }
        if (window.Swal && typeof Swal.fire === 'function') {
            Swal.fire({ icon: 'success', text: message, toast: true, timer: 2200, showConfirmButton: false, position: 'top-end' });
            return;
        }
        alert(message);
    }

    function notifyError(message) {
        if (window.NX && typeof NX.notification === 'function') {
            NX.notification({ type: 'error', message: message });
            return;
        }
        if (window.Swal && typeof Swal.fire === 'function') {
            Swal.fire({ icon: 'error', text: message, toast: true, timer: 2600, showConfirmButton: false, position: 'top-end' });
            return;
        }
        alert(message);
    }

    function prependMinuta(item) {
        var accordion = $('#client-minutas-accordion');
        var emptyState = $('#client-minutas-empty');
        var minutaId = item.client_minuta_id;
        var collapseId = 'minuta-collapse-' + minutaId;
        var headingId = 'minuta-heading-' + minutaId;

        if (emptyState.length) {
            emptyState.remove();
            accordion.show();
        }

        var html = '' +
            '<div class="card border mb-2" id="minuta-item-' + minutaId + '">' +
                '<div class="card-header bg-white" id="' + headingId + '">' +
                    '<a class="d-flex justify-content-between align-items-center text-dark" data-toggle="collapse" href="#' + collapseId + '" role="button" aria-expanded="true" aria-controls="' + collapseId + '">' +
                        '<span class="font-weight-bold">' + escapeHtml(item.minuta_date) + '</span>' +
                        '<span class="text-muted small">' + escapeHtml(formatDateForDisplay(item.minuta_date)) + ' | Ver detalle</span>' +
                    '</a>' +
                '</div>' +
                '<div id="' + collapseId + '" class="collapse show" data-parent="#client-minutas-accordion">' +
                    '<div class="card-body">' +
                        '<div class="text-muted" style="white-space: pre-wrap;">' + escapeHtml(item.minuta_detail) + '</div>' +
                        '<div class="mt-3">' +
                            '<button type="button" class="btn btn-sm btn-outline-primary minuta-edit-btn" data-id="' + minutaId + '" data-date="' + escapeHtml(item.minuta_date) + '" data-detail="' + escapeHtml(item.minuta_detail) + '">Editar</button> ' +
                            '<button type="button" class="btn btn-sm btn-outline-danger minuta-delete-btn" data-id="' + minutaId + '">Eliminar</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

        accordion.prepend(html);
    }

    function updateMinutaCard(item) {
        var itemSelector = '#minuta-item-' + item.client_minuta_id;
        var $item = $(itemSelector);

        if (!$item.length) {
            prependMinuta(item);
            return;
        }

        $item.find('.font-weight-bold').first().text(item.minuta_date);
        $item.find('.text-muted.small').first().text(formatDateForDisplay(item.minuta_date) + ' | Ver detalle');
        $item.find('.card-body .text-muted').first().text(item.minuta_detail);

        $item.find('.minuta-edit-btn')
            .attr('data-date', item.minuta_date)
            .attr('data-detail', item.minuta_detail);
    }

    function resetMinutaForm() {
        $('#editing_minuta_id').val('');
        $('#client-minutas-form')[0].reset();
        $('#minuta-submit-btn').text('Guardar minuta');
        $('#minuta-cancel-edit').hide();
    }

    $(document).off('submit.clientMinutas', '#client-minutas-form').on('submit.clientMinutas', '#client-minutas-form', function (e) {
        e.preventDefault();

        var $form = $(this);
        var postUrl = $form.attr('action');
        var editingId = $('#editing_minuta_id').val();
        var $submitButton = $form.find('button[type="submit"]');

        if ($submitButton.prop('disabled')) {
            return;
        }

        $submitButton.prop('disabled', true).text('Guardando...');

        var ajaxType = 'POST';
        var formData = $form.serializeArray();

        if (editingId) {
            postUrl = postUrl + '/' + editingId;
            formData.push({ name: '_method', value: 'PUT' });
        }

        $.ajax({
            type: ajaxType,
            url: postUrl,
            data: $.param(formData),
            success: function (response) {
                if (response && response.item) {
                    if (editingId) {
                        updateMinutaCard(response.item);
                    } else {
                        prependMinuta(response.item);
                    }
                }
                resetMinutaForm();
                notifySuccess(response.message || (editingId ? 'Minuta actualizada correctamente' : 'Minuta creada correctamente'));
            },
            error: function (xhr) {
                if (typeof NXLABError === 'function') {
                    NXLABError(xhr);
                } else {
                    notifyError('No se pudo guardar la minuta');
                }
            },
            complete: function () {
                $submitButton.prop('disabled', false).text(editingId ? 'Actualizar minuta' : 'Guardar minuta');
            }
        });
    });

    $(document).off('click.minutaEdit', '.minuta-edit-btn').on('click.minutaEdit', '.minuta-edit-btn', function () {
        var id = $(this).data('id');
        var date = $(this).attr('data-date');
        var detail = $(this).attr('data-detail');

        $('#editing_minuta_id').val(id);
        $('#client-minutas-form input[name="minuta_date"]').val(date);
        $('#client-minutas-form textarea[name="minuta_detail"]').val(detail);
        $('#minuta-submit-btn').text('Actualizar minuta');
        $('#minuta-cancel-edit').show();

        $('html, body').animate({
            scrollTop: $('#client-minutas-form').offset().top - 120
        }, 250);
    });

    $(document).off('click.minutaCancel', '#minuta-cancel-edit').on('click.minutaCancel', '#minuta-cancel-edit', function () {
        resetMinutaForm();
    });

    $(document).off('click.minutaDelete', '.minuta-delete-btn').on('click.minutaDelete', '.minuta-delete-btn', function () {
        var minutaId = $(this).data('id');
        var token = $('#client-minutas-form input[name="_token"]').val();

        if (!confirm('¿Eliminar esta minuta?')) {
            return;
        }

        $.ajax({
            type: 'POST',
            url: '{{ url('clients/' . $client->client_id . '/client-minutas') }}/' + minutaId,
            data: {
                _token: token,
                _method: 'DELETE'
            },
            success: function (response) {
                $('#minuta-item-' + minutaId).remove();
                if (!$('#client-minutas-accordion').children().length) {
                    $('#client-minutas-accordion').hide();
                    if (!$('#client-minutas-empty').length) {
                        $('#client-minutas-list').prepend('<div class="alert alert-light mb-0" id="client-minutas-empty">Aún no hay minutas registradas.</div>');
                    }
                }
                resetMinutaForm();
                notifySuccess(response.message || 'Minuta eliminada correctamente');
            },
            error: function (xhr) {
                if (typeof NXLABError === 'function') {
                    NXLABError(xhr);
                } else {
                    notifyError('No se pudo eliminar la minuta');
                }
            }
        });
    });
})();
</script>
