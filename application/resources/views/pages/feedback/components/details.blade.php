{{-- Detalle de preguntas (sigla + pregunta + valor/rango) --}}
@php
  // textos por defecto cuando en BD no haya una pregunta
  $defaultQuestions = [
    'NPS' => '¿Qué tan probable es que nos recomiendes a un amigo o colega?',
    'CSAT'=> '¿Qué tan satisfecho/a estás con el servicio recibido?',
    'CES' => '¿Qué tan fácil fue resolver tu necesidad?',
    'TTR' => 'Tiempo de respuesta (horas)',
  ];
@endphp

<ul class="list-group list-group-flush mb-0">
@foreach($details as $d)
  @php
    $q        = $d->feedbackQuery;
    // sigla/código (elegí el que tengas en la tabla)
    $abbr     = $q->abbr ?? $q->code ?? $q->title ?? $q->name ?? '—';

    // intentamos distintos campos comunes para la pregunta; si no hay, usamos un default por sigla
    $question = $q->question
              ?? $q->description
              ?? $q->label
              ?? $q->text
              ?? $defaultQuestions[$abbr] ?? null;

    // rango numérico (default a 10 si viniera null)
    $range    = (int) ($q->range ?? 10);

    // valor de la respuesta
    $value    = (int) $d->value;
  @endphp

  <li class="list-group-item px-0">
    <div class="d-flex justify-content-between align-items-center">
      <div class="mr-3" style="min-width:0">
        <div class="font-weight-bold text-uppercase">{{ $abbr }}</div>
        @if($question)
          <div class="small text-muted text-truncate-2">{{ $question }}</div>
          <div class="small text-muted">Rango: 1–{{ $range }}</div>
        @else
          <div class="small text-muted">Rango: 1–{{ $range }}</div>
        @endif
      </div>

      <div class="text-right">
        <span class="h6 mb-0 font-weight-bold">{{ $value }}</span>
        <span class="text-muted">/ {{ $range }}</span>
      </div>
    </div>
  </li>
@endforeach
</ul>

<style>
/* ayuda a truncar el texto de la pregunta en 2 líneas */
.text-truncate-2{
  display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden;
}
</style>
