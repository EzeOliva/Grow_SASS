<div class="modal-dialog modal-md" id="basicModalContainer">
    @php
        $feedbackImpact = $feedbackImpact ?? [];
        $tasksCompleted = (int) ($feedbackImpact['tasks_completed'] ?? 0);
        $trainingsCount = (int) ($feedbackImpact['capacitaciones_count'] ?? 0);
        $expectationsMet = (int) ($feedbackImpact['expectations_fulfilled'] ?? 0);
        $meetingsCount = (int) ($feedbackImpact['minutas_count'] ?? 0);
        $hasImpactSummary = ($tasksCompleted + $trainingsCount + $expectationsMet + $meetingsCount) > 0;
    @endphp

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
                        @if($hasImpactSummary)
                            <div class="alert alert-info mb-3" style="background:#f3f9ff;border:1px solid #c8e4ff;color:#1f3d5a;">
                                <div class="small mb-2"><strong>En los últimos 3 meses trabajamos junto a ustedes en:</strong></div>
                                <div class="d-flex flex-wrap align-items-center">
                                    @if($tasksCompleted > 0)
                                        <span class="badge badge-pill badge-light border mr-2 mb-2">{{ $tasksCompleted }} tareas completadas</span>
                                    @endif
                                    @if($trainingsCount > 0)
                                        <span class="badge badge-pill badge-light border mr-2 mb-2">{{ $trainingsCount }} capacitaciones</span>
                                    @endif
                                    @if($expectationsMet > 0)
                                        <span class="badge badge-pill badge-light border mr-2 mb-2">{{ $expectationsMet }} expectativas cumplidas</span>
                                    @endif
                                    @if($meetingsCount > 0)
                                        <span class="badge badge-pill badge-light border mb-2">{{ $meetingsCount }} reuniones</span>
                                    @endif
                                </div>
                                <div class="small text-muted mb-0">Tu feedback nos ayuda a mejorar aún más.</div>
                            </div>
                        @endif

                          @foreach($feedbackQueries as $index => $item) 
                            <div class="form-group">
                                <div class="feedback-block feedback-query-answer">
                                    <div class="pb-3">
                                        <label><strong>{{$item->content}}</strong></label>
                                    </div>
                                    @if($item->type == 1)
                                        <div class="d-flex flex-wrap">
                                            @for($i = 1; $i <= $item->range; $i++)
                                                <button type="button" class="btn btn-outline-info m-1 feedback-mark-button" name="{{$item->feedback_query_id}}" data-question="{{$item->feedback_query_id}}" data-value="{{$i}}">{{$i}}</button>
                                            @endfor
                                        </div>
                                    @endif
                                    @if($item->type == 2)
                                        <div class="d-flex align-items-center editable feedback-stars p-2" data-question="{{$item->feedback_query_id}}">
                                            @for($i = 1; $i <= $item->range; $i++)
                                                    <i class="far editable feedback-star fa-star fa-lg mr-2" role="button" data-value="{{$i}}"></i>
                                            @endfor
                                        </div>
                                    @endif
                                    @if($item->type == 3)
                                        <select class="form-control mt-2" name="{{$item->feedback_query_id}}" data-question="{{$item->feedback_query_id}}">
                                            @for($i = 1; $i <= $item->range; $i++)
                                                <option value="{{$i}}">{{$i}}</button>
                                            @endfor
                                        </select>
                                    @endif
                                </div>
                            </div>
                          @endforeach
                    </div>
                  </div>
    
                <!-- Comentario -->
                <div class="form-group">
                    <label for="comment"><strong>{{ __('lang.comment') }}</strong> <small class="text-muted">({{ __('lang.optional') }})</small></label>
                    {{-- {{var_dump($feedbackQueries)}} --}}
                    <textarea class="form-control" id="comment" rows="3"></textarea>
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
