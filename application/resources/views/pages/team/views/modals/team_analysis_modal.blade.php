    <div class="modal-dialog modal-xl" id="basicModalContainer">
        <div class="modal-content">
            <div class="modal-header" id="basicModalHeader">
                <h3 class="modal-title">
                    <i class="fas fa-wand-magic-sparkles text-warning mr-2"></i>
                    <span>Análisis de Equipo con IA - {{ $team->full_name ?? 'Miembro del Equipo' }}</span>    </h3>
                <button type="button" class="close" data-dismiss="modal" id="basicModalCloseIcon">
                    <i class="ti-close"></i>
                </button>
            </div>
            <div class="modal-body" id="basicModalBody">
                <div class="container">
                    <!-- Analysis Type Tabs -->
                    <ul class="nav nav-tabs" id="aiAnalysisTabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active js-ajax-ux-request" id="weekly-report-tab" data-toggle="tab"
                                href="#analysis-content" role="tab"
                                data-url="{{ route('team.analyze.ai.base.weekly_report', ['team_id' => $team->id ?? '']) }}"
                                data-ajax-type="GET" data-loading-target="analysis-content"
                                data-loading-class="loading">
                                <i class="fas fa-calendar-week"></i> Reporte Semanal
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link js-ajax-ux-request" id="general-alerts-tab" data-toggle="tab"
                                href="#analysis-content" role="tab"
                                data-url="{{ route('team.analyze.ai.base.general_alerts', ['team_id' => $team->id ?? '']) }}"
                                data-ajax-type="GET" data-loading-target="analysis-content"
                                data-loading-class="loading">
                                <i class="fas fa-exclamation-triangle"></i> Alertas Generales
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link js-ajax-ux-request" id="productivity-tab" data-toggle="tab"
                                href="#analysis-content" role="tab"
                                data-url="{{ route('team.analyze.ai.base.productivity', ['team_id' => $team->id ?? '']) }}"
                                data-ajax-type="GET" data-loading-target="analysis-content"
                                data-loading-class="loading">
                                <i class="fas fa-chart-line"></i> Productividad
                            </a>
                        </li>
                    </ul>
                    <!-- Single Content Area -->
                    <div class="tab-content mt-3">
                        <div class="tab-pane show active" id="analysis-content" role="tabpanel">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">Cargando...</span>
                                </div>
                                <p class="mt-2">Analizando la actividad del miembro del equipo...</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- No inline JS here; all tab logic should be in global JS -->
