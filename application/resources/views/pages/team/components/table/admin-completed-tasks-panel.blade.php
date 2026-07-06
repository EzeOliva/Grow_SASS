<div class="m-b-20" id="admin-team-task-stats-panel">
    <div class="d-flex align-items-center justify-content-between">
        <h6 class="m-0 text-muted">Panel interno</h6>
        <a class="text-muted font-13" data-toggle="collapse" href="#admin-team-task-stats-collapse" role="button"
            aria-expanded="false" aria-controls="admin-team-task-stats-collapse">
            Ver productividad privada
        </a>
    </div>

    <div class="collapse m-t-10" id="admin-team-task-stats-collapse">
        <div class="border rounded p-15 bg-light">
            <div class="row m-b-10">
                <div class="col-md-6 col-lg-4">
                    <input type="text" id="admin-team-task-stats-search" class="form-control form-control-sm"
                        placeholder="Buscar por nombre, rol o posicion">
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-striped m-b-0" id="admin-team-task-stats-table">
                    <thead>
                        <tr>
                            <th>Miembro</th>
                            <th>Rol</th>
                            <th>Posicion</th>
                            <th class="text-right">Completadas (7 dias)</th>
                            <th class="text-right">Completadas (30 dias)</th>
                            <th class="text-right">Completadas (60 dias)</th>
                            <th class="text-right">Completadas (90 dias)</th>
                            <th class="text-right">Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($adminTeamTaskStats ?? collect()) as $stat)
                        <tr data-search="{{ strtolower(trim(($stat->first_name ?? '') . ' ' . ($stat->last_name ?? '') . ' ' . ($stat->role_name ?? '') . ' ' . ($stat->position ?? ''))) }}">
                            <td>{{ trim(($stat->first_name ?? '') . ' ' . ($stat->last_name ?? '')) ?: '-' }}</td>
                            <td>{{ $stat->role_name ?: '-' }}</td>
                            <td>{{ $stat->position ?: '-' }}</td>
                            <td class="text-right">{{ number_format((int) ($stat->completed_last_week ?? 0)) }}</td>
                            <td class="text-right">{{ number_format((int) ($stat->completed_last_month ?? 0)) }}</td>
                            <td class="text-right">{{ number_format((int) ($stat->completed_last_60_days ?? 0)) }}</td>
                            <td class="text-right">{{ number_format((int) ($stat->completed_last_90_days ?? 0)) }}</td>
                            <td class="text-right">
                                <button type="button"
                                    class="btn btn-xs btn-outline-info edit-add-modal-button js-ajax-ux-request reset-target-modal-form"
                                    data-toggle="modal" data-target="#commonModal"
                                    data-url="{{ urlResource('/team/' . ($stat->id ?? 0) . '/completed-tasks/modal') }}"
                                    data-loading-target="commonModalBody"
                                    data-modal-title="Tareas completadas - {{ trim(($stat->first_name ?? '') . ' ' . ($stat->last_name ?? '')) ?: 'Miembro' }}"
                                    data-modal-size="modal-xl"
                                    data-footer-visibility="hidden"
                                    data-action-ajax-loading-target="commonModalBody">
                                    Ver tareas
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Sin datos para mostrar.</td>
                        </tr>
                        @endforelse
                        <tr id="admin-team-task-stats-empty" style="display:none;">
                            <td colspan="8" class="text-center text-muted">No hay coincidencias para ese filtro.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    if (window.__teamAdminTaskStatsSearchBound) {
        return;
    }
    window.__teamAdminTaskStatsSearchBound = true;

    document.addEventListener('input', function (event) {
        if (!event.target || event.target.id !== 'admin-team-task-stats-search') {
            return;
        }

        var query = (event.target.value || '').toLowerCase().trim();
        var table = document.getElementById('admin-team-task-stats-table');
        if (!table) {
            return;
        }

        var rows = table.querySelectorAll('tbody tr[data-search]');
        var visible = 0;

        for (var i = 0; i < rows.length; i++) {
            var text = (rows[i].getAttribute('data-search') || '').toLowerCase();
            var match = !query || text.indexOf(query) !== -1;
            rows[i].style.display = match ? '' : 'none';
            if (match) {
                visible++;
            }
        }

        var emptyRow = document.getElementById('admin-team-task-stats-empty');
        if (emptyRow) {
            emptyRow.style.display = visible === 0 ? '' : 'none';
        }
    });
})();
</script>
