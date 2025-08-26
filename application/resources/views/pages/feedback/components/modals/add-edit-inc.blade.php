<div class="modal-dialog modal-xl modal-dialog-scrollable" id="basicModalContainer">

    <form action="" method="post" id="feedbackForm" class="form-horizontal">
        <div class="modal-content">
            <div class="modal-header" id="basicModalHeader">
                <h2 class="mb-4 text-center"><i class="fa-regular fa-star-half-stroke mr-1"></i>{{ __('lang.customer_feedback') }}</h2>
                <button type="button" class="close" data-dismiss="modal"
                    id="basicModalCloseIcon">
                    <i class="ti-close"></i>
                </button>
            </div>
            <div class="modal-body min-h-200" id="basicModalBody">
                <!-- Comentario -->
                <div id="questions-scrollable">
                    <div id="questions-container">
                          @foreach($feedbackQueries as $index => $item) 
                            <div class="form-group">
                                <div class="feedback-block feedback-query-answer"
                                    data-feedback-query-id="{{$item->feedback_query_id}}">
                                    <div class="pb-3">
                                        <label><strong>{{$item->content}}</strong></label>
                                    </div>

                                    {{-- type 1: botones 1..N --}}
                                    @if($item->type == 1)
                                    <div class="d-flex flex-wrap">
                                        @for($i = 1; $i <= $item->range; $i++)
                                        <button type="button"
                                                class="btn btn-outline-info m-1 feedback-mark-button"
                                                data-question="{{$item->feedback_query_id}}"
                                                data-value="{{$i}}">{{$i}}</button>
                                        @endfor
                                    </div>
                                    @endif

                                    {{-- type 2: estrellas --}}
                                    @if($item->type == 2)
                                    <div class="d-flex align-items-center editable feedback-stars p-2"
                                        data-question="{{$item->feedback_query_id}}">
                                        @for($i = 1; $i <= $item->range; $i++)
                                        <i class="far editable feedback-star fa-star fa-lg mr-2" role="button" data-value="{{$i}}"></i>
                                        @endfor
                                    </div>
                                    @endif

                                    {{-- type 3: select --}}
                                    @if($item->type == 3)
                                    <select class="form-control mt-2"
                                            data-question="{{$item->feedback_query_id}}">
                                        @for($i = 1; $i <= $item->range; $i++)
                                        <option value="{{$i}}">{{$i}}</option>
                                        @endfor
                                    </select>
                                    @endif
                                </div>

                            </div>
                          @endforeach
                    </div>
                  </div>
    
                <!-- Comentario -->
                 <hr/>
                <div class="form-group"> 
                    <label for="comment">
                        <strong>{{ __('lang.comment') }}</strong><br/>
                        <small class="text-muted">Contanos cómo te ayudamos estos ultimos meses y dejános una reseña 🤗</small><br/>
                        <small id="ai-hint" class="text-muted hidden text-info">Sugerencia generada automáticamente ✨ (100% editable)</small>
                    </label>

                    <textarea
                        class="form-control"
                        id="comment"
                        rows="10"
                        required
                        placeholder="Escribí tu reseña aquí…"
                        oninvalid="this.setCustomValidity('Este campo es obligatorio. Contanos cómo te ayudamos este trimestre 😊')"
                        oninput="this.setCustomValidity('')"
                    ></textarea>
                    
                </div>

            </div>
            <div class="modal-footer" id="basicModalFooter">
                    <button type="submit" class="btn btn-primary btn-block">{{ __('lang.send') }}</button>
                    {{-- <button type="button" id="basicModalCloseButton" class="btn btn-rounded-x btn-secondary waves-effect text-left" data-dismiss="modal">{{ cleanLang(__('lang.close')) }}</button>
                    <button type="submit" id="basicModalSubmitButton"
                        class="btn btn-rounded-x btn-danger waves-effect text-left basicModalSubmitButton" data-url="" data-loading-target=""
                        data-ajax-type="POST" data-on-start-submit-button="disable">{{ cleanLang(__('lang.submit')) }}</button> --}}
            </div>
        </div>
    </form>
</div>

<script>
(function() {
  let timer = null, lastSuggestion = '';
  const minAnswered = 2; // esperá al menos 2 respuestas

  // Helper: guarda el valor elegido en el wrapper .feedback-block
  function setAnswerValue(questionId, value) {
    const block = document.querySelector('.feedback-block.feedback-query-answer[data-feedback-query-id="'+questionId+'"]');
    if (block) {
      block.dataset.value = String(value);
    }
  }

  // Colecciona respuestas desde los wrappers que ya tengan data-value
  function collectDetails() {
    const details = [];
    document.querySelectorAll('.feedback-block.feedback-query-answer[data-feedback-query-id]')
      .forEach(block => {
        const id = parseInt(block.dataset.feedbackQueryId);
        const val = parseInt(block.dataset.value || '');
        if (!isNaN(id) && !isNaN(val)) {
          details.push({ feedback_query_id: id, value: val });
        }
      });
    return details;
  }

  function requestSuggestion() {
    const details = collectDetails();
    if (details.length < minAnswered) return; // evita textos pobres

    clearTimeout(timer);
    timer = setTimeout(function() {
      fetch(`{{ route('feedbacks.suggest-review', ['client' => $client->client_id ?? auth()->user()->clientid]) }}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ details })
      })
      .then(r => r.json())
      .then(({ suggestion }) => {
        lastSuggestion = suggestion || '';
        const ta = document.getElementById('comment');
        const userTouched = ta.dataset.touched === '1';
        if (!userTouched && (ta.value.trim() === '' || ta.value === ta.dataset.lastAuto)) {
          ta.value = lastSuggestion;
          ta.dataset.lastAuto = lastSuggestion;
          document.getElementById('ai-hint')?.classList.remove('hidden');
        }
      })
      .catch(() => {});
    }, 400); // debounce
  }

  // === Listeners según tus 3 tipos ===

  // Type 1: botones 1..N
  document.querySelectorAll('.feedback-mark-button').forEach(btn => {
    btn.addEventListener('click', function() {
      const qid = parseInt(this.dataset.question);
      const val = parseInt(this.dataset.value);
      // pinta visual (opcional): desactivar hermanos y activar este
      this.parentElement.querySelectorAll('.feedback-mark-button').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      setAnswerValue(qid, val);
      requestSuggestion();
    });
  });

  // Type 2: estrellas
  document.querySelectorAll('.feedback-stars').forEach(container => {
    container.addEventListener('click', function(e) {
      const star = e.target.closest('.feedback-star');
      if (!star) return;
      const qid = parseInt(this.dataset.question);
      const val = parseInt(star.dataset.value);
      // pinta estrellas (far -> fas hasta val)
      this.querySelectorAll('.feedback-star').forEach(i => {
        const v = parseInt(i.dataset.value);
        i.classList.toggle('fas', v <= val);
        i.classList.toggle('far', v > val);
      });
      setAnswerValue(qid, val);
      requestSuggestion();
    });
  });

  // Type 3: select
  document.querySelectorAll('select[data-question]').forEach(sel => {
    sel.addEventListener('change', function() {
      setAnswerValue(parseInt(this.dataset.question), parseInt(this.value));
      requestSuggestion();
    });
  });

  // Textarea: marca que el usuario ya escribió para no sobreescribir
  const ta = document.getElementById('comment');
  ta.addEventListener('input', () => ta.dataset.touched = '1');

  // Botón para reusar sugerencia si la borra
  document.getElementById('use-ai')?.addEventListener('click', function() {
    ta.value = lastSuggestion;
    ta.dataset.touched = '1';
  });
})();
</script>
<style>.hidden{display:none}</style>

