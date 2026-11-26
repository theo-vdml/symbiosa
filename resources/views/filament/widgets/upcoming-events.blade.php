<x-filament-widgets::widget>
    <x-filament::section heading="Prochains événements" icon="heroicon-o-calendar-days">
        <x-slot name="afterHeader">
            <x-filament::link :href="$indexUrl" size="sm">Tous les événements</x-filament::link>
        </x-slot>

        @if ($events->isEmpty())
            <div class="sy-upcoming-empty">
                <p>Aucun événement à venir pour l'instant.</p>
                <x-filament::button tag="a" :href="$createUrl" icon="heroicon-m-plus">
                    Créer un événement
                </x-filament::button>
            </div>
        @else
            <ul class="sy-upcoming">
                @foreach ($events as $event)
                    <li>
                        <a href="{{ $event['url'] }}" class="sy-upcoming-item">
                            @if ($event['startsAt'])
                                <span class="sy-upcoming-date" aria-hidden="true">
                                    <b>{{ $event['startsAt']->format('d') }}</b>
                                    <small>{{ $event['startsAt']->translatedFormat('M') }}</small>
                                </span>
                            @endif

                            <span class="sy-upcoming-body">
                                <span class="sy-upcoming-title">{{ $event['title'] }}</span>
                                <span class="sy-upcoming-meta">
                                    {{ $event['startsAt']?->translatedFormat('l j F · H:i') }}
                                    @if ($event['city'])
                                        · {{ $event['city'] }}
                                    @endif
                                </span>
                            </span>

                            <span class="sy-upcoming-sales">
                                @if ($event['capacity'] > 0)
                                    <span class="sy-upcoming-sales-label">
                                        <span>{{ $event['sold'] }} / {{ $event['capacity'] }} billets</span>
                                        <span>{{ $event['percentage'] }} %</span>
                                    </span>
                                    <span class="sy-progress" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $event['percentage'] }}" aria-label="Billets vendus">
                                        <span style="width: {{ $event['percentage'] }}%"></span>
                                    </span>
                                @else
                                    <span class="sy-upcoming-sales-label"><span>Pas de billetterie</span></span>
                                @endif
                            </span>

                            <x-filament::badge :color="$event['statusColor']">{{ $event['statusLabel'] }}</x-filament::badge>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif
    </x-filament::section>
</x-filament-widgets::widget>
