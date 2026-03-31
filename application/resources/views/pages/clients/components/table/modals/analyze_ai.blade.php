<div class="modal-dialog modal-lg" id="basicModalContainer">
  <div class="modal-content">
    <div class="modal-header" id="basicModalHeader">
      <h3 class="modal-title"><i class="fa-solid fa-wand-magic-sparkles"></i><span>Informe de Salud del Cliente - {{ $client->client_company_name ?? cleanLang(__('lang.client')) }}</span></h3>
      <button type="button" class="close" data-dismiss="modal" id="basicModalCloseIcon">
          <i class="ti-close"></i>
      </button>
    </div>
    <div class="modal-body" id="basicModalBody">
      <div class="container">
          <!-- Health Report Tabs -->
        <ul class="nav nav-tabs" id="aiAnalysisTabs" role="tablist">
          <li class="nav-item">
            <a class="nav-link active js-ajax-ux-request" id="week-tab" data-toggle="tab" href="#analysis-content" role="tab"
              data-url="{{ route('clients.analyze.ai.health', ['client' => $client->client_id, 'period' => 'week']) }}"
               data-ajax-type="GET"
               data-loading-target="analysis-content"
               data-loading-class="loading">
              Última semana
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-ajax-ux-request" id="month-tab" data-toggle="tab" href="#analysis-content" role="tab"
              data-url="{{ route('clients.analyze.ai.health', ['client' => $client->client_id, 'period' => 'month']) }}"
               data-ajax-type="GET"
               data-loading-target="analysis-content"
               data-loading-class="loading">
              Último mes
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-ajax-ux-request" id="quarter-tab" data-toggle="tab" href="#analysis-content" role="tab"
              data-url="{{ route('clients.analyze.ai.health', ['client' => $client->client_id, 'period' => 'quarter']) }}"
               data-ajax-type="GET"
               data-loading-target="analysis-content"
               data-loading-class="loading">
              Último trimestre
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link js-ajax-ux-request" id="meeting-prep-tab" data-toggle="tab" href="#analysis-content" role="tab"
              data-url="{{ route('clients.analyze.ai.meeting.prep', ['client' => $client->client_id]) }}"
               data-ajax-type="GET"
               data-loading-target="analysis-content"
               data-loading-class="loading">
              Preparemos una reunión
            </a>
          </li>
        </ul>
        <!-- Single Content Area -->
        <div class="tab-content mt-3">
          <div class="tab-pane fade show active" id="analysis-content" role="tabpanel">
            <div class="text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="sr-only">{{ cleanLang(__('lang.loading')) }}</span>
              </div>
              <p class="mt-2">Cargando informe de salud del cliente...</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<script>
$(document).ready(function() {
    // Load initial week health report
    var initialTab = $('#week-tab');
    if (initialTab.length > 0) {
        nxAjaxUxRequest(initialTab);
    }
    // Handle tab clicks using framework
    $('#aiAnalysisTabs a[data-toggle="tab"]').on('click', function (e) {
        e.preventDefault();
        var $tab = $(this);
        // Remove active class from all tabs
        $('#aiAnalysisTabs a').removeClass('active');
        // Add active class to clicked tab
        $tab.addClass('active');
        // Show loading state in content area
        $('#analysis-content').html(`
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">${cleanLang(__('lang.loading'))}</span>
                </div>
                  <p class="mt-2">Cargando informe...</p>
            </div>
        `);
        // Trigger AJAX request
        nxAjaxUxRequest($tab);
    });
});
</script> 