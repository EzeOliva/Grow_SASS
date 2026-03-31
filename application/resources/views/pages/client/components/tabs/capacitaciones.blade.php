<div class="container py-4" id="client-capacitaciones-tab">
    <div class="card shadow-sm rounded-4 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0"><i class="fas fa-chalkboard-teacher text-primary mr-2"></i>Capacitaciones</h4>
            <small class="text-muted">Uso interno</small>
        </div>

        <form id="client-capacitaciones-form" class="mb-4" action="{{ url('clients/' . $client->client_id . '/client-capacitaciones') }}" method="POST">
            @csrf
            <input type="hidden" name="editing_capacitacion_id" id="editing_capacitacion_id" value="">
            <div class="form-row">
                <div class="col-md-3 mb-3">
                    <label class="control-label">Fecha</label>
                    <input type="date" name="capacitacion_date" class="form-control" required>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="control-label">Modalidad</label>
                    <select name="capacitacion_mode" class="form-control" required>
                        <option value="">Seleccionar</option>
                        <option value="meet">Meet</option>
                        <option value="onsite">Onsite</option>
                    </select>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="control-label">Participantes</label>
                    <input type="text" name="capacitacion_participants" class="form-control" placeholder="Ej: Ana, Pablo, Equipo de Ventas" required>
                </div>
            </div>

            <div class="form-row">
                <div class="col-md-6 mb-3">
                    <label class="control-label">Temas abordados</label>
                    <textarea name="capacitacion_topics" rows="4" class="form-control" required></textarea>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="control-label">Observación</label>
                    <textarea name="capacitacion_observations" rows="4" class="form-control"></textarea>
                </div>
            </div>
            <button type="submit" class="btn btn-primary" id="capacitacion-submit-btn">Guardar capacitación</button>
            <button type="button" class="btn btn-secondary" id="capacitacion-cancel-edit" style="display:none;">Cancelar edición</button>
        </form>

        <div id="client-capacitaciones-list">
            @if($capacitaciones->count())
                <div id="client-capacitaciones-accordion">
                    @foreach($capacitaciones as $capacitacion)
                        <div class="card border mb-2" id="capacitacion-item-{{ $capacitacion->client_capacitacion_id }}">
                            <div class="card-header bg-white" id="capacitacion-heading-{{ $capacitacion->client_capacitacion_id }}">
                                <a class="d-flex justify-content-between align-items-center text-dark" data-toggle="collapse"
                                    href="#capacitacion-collapse-{{ $capacitacion->client_capacitacion_id }}" role="button" aria-expanded="false"
                                    aria-controls="capacitacion-collapse-{{ $capacitacion->client_capacitacion_id }}">
                                    <span>
                                        <strong>{{ $capacitacion->capacitacion_date }}</strong>
                                        <span class="badge badge-info text-uppercase ml-2">{{ $capacitacion->capacitacion_mode }}</span>
                                    </span>
                                    <span class="text-muted small">{{ \Carbon\Carbon::parse($capacitacion->capacitacion_date)->format('d-m-Y') }} | Ver detalle</span>
                                </a>
                            </div>
                            <div id="capacitacion-collapse-{{ $capacitacion->client_capacitacion_id }}" class="collapse" data-parent="#client-capacitaciones-accordion">
                                <div class="card-body">
                                    <div class="mb-2"><strong>Participantes:</strong> {{ $capacitacion->capacitacion_participants }}</div>
                                    <div class="mb-2" style="white-space: pre-wrap;"><strong>Temas:</strong> {{ $capacitacion->capacitacion_topics }}</div>
                                    <div class="text-muted" style="white-space: pre-wrap;"><strong>Observación:</strong> {{ $capacitacion->capacitacion_observations }}</div>
                                    <div class="mt-3">
                                        <button type="button" class="btn btn-sm btn-outline-primary capacitacion-edit-btn"
                                            data-id="{{ $capacitacion->client_capacitacion_id }}"
                                            data-date="{{ $capacitacion->capacitacion_date }}"
                                            data-mode="{{ $capacitacion->capacitacion_mode }}"
                                            data-participants="{{ e($capacitacion->capacitacion_participants) }}"
                                            data-topics="{{ e($capacitacion->capacitacion_topics) }}"
                                            data-observations="{{ e($capacitacion->capacitacion_observations) }}">Editar</button>
                                        <button type="button" class="btn btn-sm btn-outline-danger capacitacion-delete-btn"
                                            data-id="{{ $capacitacion->client_capacitacion_id }}">Eliminar</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="alert alert-light mb-0" id="client-capacitaciones-empty">Aún no hay capacitaciones registradas.</div>
                <div id="client-capacitaciones-accordion" style="display:none;"></div>
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

    function prependCapacitacion(item) {
        var accordion = $('#client-capacitaciones-accordion');
        var emptyState = $('#client-capacitaciones-empty');
        var itemId = item.client_capacitacion_id;
        var collapseId = 'capacitacion-collapse-' + itemId;
        var headingId = 'capacitacion-heading-' + itemId;
        var mode = (item.capacitacion_mode || '').toUpperCase();

        if (emptyState.length) {
            emptyState.remove();
            accordion.show();
        }

        var html = '' +
            '<div class="card border mb-2" id="capacitacion-item-' + itemId + '">' +
                '<div class="card-header bg-white" id="' + headingId + '">' +
                    '<a class="d-flex justify-content-between align-items-center text-dark" data-toggle="collapse" href="#' + collapseId + '" role="button" aria-expanded="true" aria-controls="' + collapseId + '">' +
                        '<span><strong>' + escapeHtml(item.capacitacion_date) + '</strong><span class="badge badge-info text-uppercase ml-2">' + escapeHtml(mode) + '</span></span>' +
                        '<span class="text-muted small">' + escapeHtml(formatDateForDisplay(item.capacitacion_date)) + ' | Ver detalle</span>' +
                    '</a>' +
                '</div>' +
                '<div id="' + collapseId + '" class="collapse show" data-parent="#client-capacitaciones-accordion">' +
                    '<div class="card-body">' +
                        '<div class="mb-2"><strong>Participantes:</strong> ' + escapeHtml(item.capacitacion_participants) + '</div>' +
                        '<div class="mb-2" style="white-space: pre-wrap;"><strong>Temas:</strong> ' + escapeHtml(item.capacitacion_topics) + '</div>' +
                        '<div class="text-muted" style="white-space: pre-wrap;"><strong>Observación:</strong> ' + escapeHtml(item.capacitacion_observations) + '</div>' +
                        '<div class="mt-3">' +
                            '<button type="button" class="btn btn-sm btn-outline-primary capacitacion-edit-btn" data-id="' + itemId + '" data-date="' + escapeHtml(item.capacitacion_date) + '" data-mode="' + escapeHtml(item.capacitacion_mode) + '" data-participants="' + escapeHtml(item.capacitacion_participants) + '" data-topics="' + escapeHtml(item.capacitacion_topics) + '" data-observations="' + escapeHtml(item.capacitacion_observations) + '">Editar</button> ' +
                            '<button type="button" class="btn btn-sm btn-outline-danger capacitacion-delete-btn" data-id="' + itemId + '">Eliminar</button>' +
                        '</div>' +
                    '</div>' +
                '</div>' +
            '</div>';

        accordion.prepend(html);
    }

    function updateCapacitacionCard(item) {
        var itemSelector = '#capacitacion-item-' + item.client_capacitacion_id;
        var $item = $(itemSelector);
        var mode = (item.capacitacion_mode || '').toUpperCase();

        if (!$item.length) {
            prependCapacitacion(item);
            return;
        }

        $item.find('.card-header strong').first().text(item.capacitacion_date);
        $item.find('.card-header .badge').first().text(mode);
        $item.find('.text-muted.small').first().text(formatDateForDisplay(item.capacitacion_date) + ' | Ver detalle');

        var $bodyChildren = $item.find('.card-body').first().children();
        $bodyChildren.eq(0).html('<strong>Participantes:</strong> ' + escapeHtml(item.capacitacion_participants));
        $bodyChildren.eq(1).html('<strong>Temas:</strong> ' + escapeHtml(item.capacitacion_topics));
        $bodyChildren.eq(2).html('<strong>Observación:</strong> ' + escapeHtml(item.capacitacion_observations));

        $item.find('.capacitacion-edit-btn')
            .attr('data-date', item.capacitacion_date)
            .attr('data-mode', item.capacitacion_mode)
            .attr('data-participants', item.capacitacion_participants)
            .attr('data-topics', item.capacitacion_topics)
            .attr('data-observations', item.capacitacion_observations);
    }

    function resetCapacitacionForm() {
        $('#editing_capacitacion_id').val('');
        $('#client-capacitaciones-form')[0].reset();
        $('#capacitacion-submit-btn').text('Guardar capacitación');
        $('#capacitacion-cancel-edit').hide();
    }

    $(document).off('submit.clientCapacitaciones', '#client-capacitaciones-form').on('submit.clientCapacitaciones', '#client-capacitaciones-form', function (e) {
        e.preventDefault();

        var $form = $(this);
        var postUrl = $form.attr('action');
        var editingId = $('#editing_capacitacion_id').val();
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
                        updateCapacitacionCard(response.item);
                    } else {
                        prependCapacitacion(response.item);
                    }
                }
                resetCapacitacionForm();
                notifySuccess(response.message || (editingId ? 'Capacitación actualizada correctamente' : 'Capacitación creada correctamente'));
            },
            error: function (xhr) {
                if (typeof NXLABError === 'function') {
                    NXLABError(xhr);
                } else {
                    notifyError('No se pudo guardar la capacitación');
                }
            },
            complete: function () {
                $submitButton.prop('disabled', false).text(editingId ? 'Actualizar capacitación' : 'Guardar capacitación');
            }
        });
    });

    $(document).off('click.capacitacionEdit', '.capacitacion-edit-btn').on('click.capacitacionEdit', '.capacitacion-edit-btn', function () {
        $('#editing_capacitacion_id').val($(this).data('id'));
        $('#client-capacitaciones-form input[name="capacitacion_date"]').val($(this).attr('data-date'));
        $('#client-capacitaciones-form select[name="capacitacion_mode"]').val($(this).attr('data-mode'));
        $('#client-capacitaciones-form input[name="capacitacion_participants"]').val($(this).attr('data-participants'));
        $('#client-capacitaciones-form textarea[name="capacitacion_topics"]').val($(this).attr('data-topics'));
        $('#client-capacitaciones-form textarea[name="capacitacion_observations"]').val($(this).attr('data-observations'));
        $('#capacitacion-submit-btn').text('Actualizar capacitación');
        $('#capacitacion-cancel-edit').show();

        $('html, body').animate({
            scrollTop: $('#client-capacitaciones-form').offset().top - 120
        }, 250);
    });

    $(document).off('click.capacitacionCancel', '#capacitacion-cancel-edit').on('click.capacitacionCancel', '#capacitacion-cancel-edit', function () {
        resetCapacitacionForm();
    });

    $(document).off('click.capacitacionDelete', '.capacitacion-delete-btn').on('click.capacitacionDelete', '.capacitacion-delete-btn', function () {
        var capacitacionId = $(this).data('id');
        var token = $('#client-capacitaciones-form input[name="_token"]').val();

        if (!confirm('¿Eliminar esta capacitación?')) {
            return;
        }

        $.ajax({
            type: 'POST',
            url: '{{ url('clients/' . $client->client_id . '/client-capacitaciones') }}/' + capacitacionId,
            data: {
                _token: token,
                _method: 'DELETE'
            },
            success: function (response) {
                $('#capacitacion-item-' + capacitacionId).remove();
                if (!$('#client-capacitaciones-accordion').children().length) {
                    $('#client-capacitaciones-accordion').hide();
                    if (!$('#client-capacitaciones-empty').length) {
                        $('#client-capacitaciones-list').prepend('<div class="alert alert-light mb-0" id="client-capacitaciones-empty">Aún no hay capacitaciones registradas.</div>');
                    }
                }
                resetCapacitacionForm();
                notifySuccess(response.message || 'Capacitación eliminada correctamente');
            },
            error: function (xhr) {
                if (typeof NXLABError === 'function') {
                    NXLABError(xhr);
                } else {
                    notifyError('No se pudo eliminar la capacitación');
                }
            }
        });
    });
})();
</script>
