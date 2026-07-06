<!--attachment-->
@if($event->event_item == 'attachment')
<div class="x-description"><a href="{{ url($event->event_item_content2) }}">{{ $event->event_item_content }}</a>
</div>
@endif

<!--comment-->
@if($event->event_item == 'comment')
<div class="x-description">{!! clean($event->event_item_content) !!}</div>
@endif

<!--note-->
@if($event->event_item == 'note')
<div class="x-description">
        @if($event->event_item_content != '')
        <div><strong>{!! clean($event->event_item_content) !!}</strong></div>
        @endif
        @if($event->event_item_content2 != '')
        <div>{!! clean($event->event_item_content2) !!}</div>
        @endif
</div>
@endif

<!--ai health summary-->
@if($event->event_item == 'ai_health_summary')
<div class="x-description" style="margin-top:10px;">
        <div style="background:#efe9fb; border:1px solid #d8c9f3; border-left:4px solid #a78bdb; border-radius:8px; padding:14px;">
                <div style="display:flex; align-items:center; margin-bottom:8px; color:#5b3f88; font-weight:600;">
                        <i class="fas fa-robot" style="margin-right:8px;"></i>
                        <span>Resumen generado con IA</span>
                </div>

                @if($event->event_item_content != '')
                <div style="font-weight:600; color:#4b386f; margin-bottom:8px;">{{ $event->event_item_content }}</div>
                @endif

                <div style="white-space: pre-line; color:#3f3f46; line-height:1.55;">{{ $event->event_item_content2 }}</div>
        </div>
</div>
@endif

<!--status-->
@if($event->event_item == 'status')
<div class="x-description"><strong>{{ cleanLang(__('lang.new_status')) }}:</strong>
        @if($event->event_parent_type == 'task')
        {{ taskStatusName($event->event_item_content) }}
        @elseif($event->event_parent_type == 'lead')
        {{ leadStatusName($event->event_item_content) }}
        @else
        {{ runtimeLang($event->event_item_content) }}
        @endif
</div>
@endif

<!--file-->
@if($event->event_item == 'file')
<div class="x-description"><a href="{{ url($event->event_item_content2) }}">{{ $event->event_item_content }}</a>
</div>
@endif

<!--task-->
@if($event->event_item == 'task')
<div class="x-description">
        <a
                href="{{ url('/tasks/v/'.$event->event_item_id.'/'.str_slug($event->event_parent_title)) }}">{{ $event->event_item_content }}</a>
</div>
@endif

<!--tickets-->
@if($event->event_item == 'ticket')
<div class="x-description"><a href="{{ url('tickets/'.$event->event_item_id) }}">{!! clean($event->event_item_content)
                !!}</a>
</div>
@endif

<!--invoice-->
@if($event->event_item == 'invoice')
<div class="x-description"><a href="{{ url('invoices/'.$event->event_item_id) }}">{!! clean($event->event_item_content)
                !!}</a>
</div>
@endif


<!--estimate-->
@if($event->event_item == 'estimate')
<div class="x-description"><a href="{{ url('estimates/'.$event->event_item_id) }}">{!! clean($event->event_item_content)
                !!}</a>
</div>
@endif

<!--project (..but do not show on project timeline)-->
@if($event->event_item == 'new_project' && request('timelineresource_type') != 'project')
<div class="x-description"><a
                href="{{ _url('projects/'.$event->event_parent_id) }}">{{ $event->event_parent_title }}</a>
</div>
@endif


<!--subscription-->
@if($event->event_item == 'subscription')
<div class="x-description"><a href="{{ url('subscriptions/'.$event->event_item_id) }}">
                {{ $event->event_item_content }}</a>
</div>
@endif


<!--proposal-->
@if($event->event_item == 'proposal')
<div class="x-description"><a href="{{ url('proposals/'. $event->event_item_id) }}">{{ $event->event_item_content }}</a>
</div>
@endif

<!--contract-->
@if($event->event_item == 'contract')
<div class="x-description"><a href="{{ url('contracts/'. $event->event_item_id) }}">{{ $event->event_item_content }}</a>
</div>
@endif