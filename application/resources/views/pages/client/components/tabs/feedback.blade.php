<div id="feedbackList">
    {{-- Title of this tab --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="font-weight-bold mb-0">{{__('lang.customer_feedback')}}</h5>
        @if(auth()->user()->is_team)
        <button type="button" class="btn btn-sm btn-outline-primary" id="btnCopyFeedbackLink" data-client-id="{{ $client->client_id }}">
            <i class="fas fa-link mr-1"></i> Copiar link de feedback
        </button>
        @endif
    </div>
    <p class="mb-3 text-light small">{{ __('lang.feedback_subtitle') }}</p>
  @forelse ($feedbacks as $index => $fb)
      <div class="feedback-block" style="cursor: pointer;" data-feedback-id="{{ $fb->feedback_id }}" title="Click para ver desglose">
          <div class="d-flex justify-content-between flex-wrap align-items-center mb-2">
              <div class="d-flex align-items-center" style="flex-wrap: wrap; flex: 1; min-width: 0;">
                  <div class="score-badge ml-3 mr-3" style="min-width: 50px; text-align: center;">
                      {{ number_format($fb->total_marks, 1) }}
                  </div>
                  <div style="flex: 1; min-width: 0;">
                      <div class="text-muted small">{{ $fb->feedback_date_human ?? \Carbon\Carbon::parse($fb->feedback_date)->diffForHumans() }}</div>
                      <div class="font-weight-bold text-break">"{{ $fb->comment }}"</div>
                  </div>
              </div>

              <div class="action-area ml-2 mt-2 mt-md-0">
                  <div class="feedback-stars text-right">
                      @php
                          $stars = $fb->total_marks / 2;
                          $fullStars = floor($stars) + (($stars % 1 >= 0.75) ? 1 : 0);
                          $hasHalf = ($stars % 1 >= 0.25 && $stars % 1 < 0.75);
                          $emptyStars = 5 - $fullStars - ($hasHalf ? 1 : 0);
                      @endphp

                      @for ($i = 0; $i < $fullStars; $i++)
                          <i class="fas fa-star text-warning"></i>
                      @endfor
                      @if ($hasHalf)
                          <i class="fas fa-star-half-alt text-warning"></i>
                      @endif
                      @for ($i = 0; $i < $emptyStars; $i++)
                          <i class="far fa-star text-warning"></i>
                      @endfor
                  </div>
              </div>
          </div>
      </div>
  @empty
      <div class="feedback-block alert-danger">
          {{ __('lang.no_feedback_available') }}
      </div>
  @endforelse
</div>

@if(auth()->user()->is_team)
<script>
$(document).off('click', '#btnCopyFeedbackLink').on('click', '#btnCopyFeedbackLink', function() {
    var btn = $(this);
    var clientId = btn.data('client-id');
    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i> Generando...');
    $.ajax({
        url: '/feedback/generate-token/' + clientId,
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(response) {
            if (response.success && response.url) {
                navigator.clipboard.writeText(response.url).then(function() {
                    btn.html('<i class="fas fa-check mr-1"></i> Link copiado!').removeClass('btn-outline-primary').addClass('btn-success');
                    setTimeout(function() {
                        btn.html('<i class="fas fa-link mr-1"></i> Copiar link de feedback').removeClass('btn-success').addClass('btn-outline-primary').prop('disabled', false);
                    }, 3000);
                });
            }
        },
        error: function() {
            btn.html('<i class="fas fa-times mr-1"></i> Error').prop('disabled', false);
        }
    });
});

// Feedback detail breakdown on click
$(document).off('click', '.feedback-block[data-feedback-id]').on('click', '.feedback-block[data-feedback-id]', function() {
    var $block = $(this);
    var feedbackId = $block.data('feedback-id');
    var $details = $block.find('.feedback-details-container');

    // Toggle: if already showing, hide it
    if ($details.length) {
        $details.slideToggle(200);
        return;
    }

    // Load details via AJAX
    $.ajax({
        url: '/feedback/' + feedbackId + '/details',
        type: 'GET',
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        success: function(response) {
            if (response.success && response.html) {
                $block.append('<div class="feedback-details-container mt-2 pt-2 border-top">' + response.html + '</div>');
            }
        }
    });
});
</script>
@endif
