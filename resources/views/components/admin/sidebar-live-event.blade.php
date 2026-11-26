<a
    href="{{ $url() }}"
    @class(['sy-live-event', 'sy-live-event-open' => $event->ticketing_status === 'open'])
    x-show="$store.sidebar.isOpen"
>
    <span class="sy-live-event-head">
        <span class="sy-live-event-dot" aria-hidden="true"></span>
        <span class="sy-live-event-status">{{ $statusLabel() }}</span>
    </span>

    <span class="sy-live-event-title">{{ $event->title }}</span>

    <span class="sy-live-event-meta">
        {{ $event->start_at?->translatedFormat('D j M') }}
        @if ($capacity > 0)
            · {{ $sold }} / {{ $capacity }} billets
        @endif
    </span>

    @if ($capacity > 0)
        <span class="sy-live-event-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $percentage() }}" aria-label="Billets vendus">
            <span style="width: {{ $percentage() }}%"></span>
        </span>
    @endif
</a>
