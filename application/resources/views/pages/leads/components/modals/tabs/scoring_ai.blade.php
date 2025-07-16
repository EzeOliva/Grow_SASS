<div class="card mb-3">
    <div class="card-header bg-warning text-white">
        <h6><i class="fas fa-robot"></i> Análisis de puntuación por IA</h6>
    </div>
    <div class="card-body">
        @if(!empty($aiAnalysisMarkdown))
            <div class="alert alert-success mb-0">
                <h6><i class="fas fa-check-circle"></i> Análisis IA Completo</h6>
                <div class="mt-3">
                    <div class="ai-analysis-content d-none">{{ $aiAnalysisMarkdown }}</div>
                    <div class="ai-analysis-html" style="font-size: 14px; line-height: 1.6;"></div>
                </div>
            </div>
        @elseif(!empty($aiAnalysisError))
            <div class="alert alert-danger mb-0">
                <h6><i class="fas fa-exclamation-triangle"></i> Análisis IA Fallido</h6>
                <p>{{ $aiAnalysisError }}</p>
            </div>
        @else
            <div class="alert alert-info mb-0">
                <i class="fas fa-spinner fa-spin"></i> Generando análisis...
            </div>
        @endif
    </div>
</div> 