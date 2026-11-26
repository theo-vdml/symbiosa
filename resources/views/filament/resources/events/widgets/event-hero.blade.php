@php
    $startsAt = $event->start_at;
    $endsAt = $event->end_at;
@endphp

<x-filament-widgets::widget class="sy-event-hero-widget">
    <section
        @class(['sy-event-hero', 'sy-event-hero-has-image' => filled($backgroundUrl)])
        @if (filled($backgroundUrl)) style="--sy-hero-image: url('{{ $backgroundUrl }}')" @endif
    >
        <div class="sy-event-hero-inner">
            <div class="sy-event-hero-main">
                <p class="sy-event-hero-eyebrow">
                    @if ($startsAt)
                        <span>{{ $startsAt->translatedFormat('Y') }}</span>
                    @endif
                    @foreach ($genres as $genre)
                        <span>{{ $genre }}</span>
                    @endforeach
                </p>

                <h2 class="sy-event-hero-title">{{ $event->title }}</h2>

                <div class="sy-event-hero-meta">
                    @if ($startsAt)
                        <span>
                            <x-filament::icon icon="heroicon-m-calendar-days" />
                            {{ str($startsAt->translatedFormat('l j F Y'))->ucfirst() }}
                            · {{ $startsAt->format('H:i') }}@if ($endsAt) → {{ $endsAt->format('H:i') }}@endif
                        </span>
                    @endif

                    @if (filled($event->address) || filled($event->city))
                        <span>
                            <x-filament::icon icon="heroicon-m-map-pin" />
                            {{ collect([$event->address, $event->city])->filter()->join(', ') }}
                        </span>
                    @endif
                </div>

                <div class="sy-event-hero-badges">
                    <x-filament::badge :color="$statusColor">{{ $statusLabel }}</x-filament::badge>
                    <x-filament::badge :color="$ticketing[1]">{{ $ticketing[0] }}</x-filament::badge>
                </div>
            </div>

            <div class="sy-event-hero-side">
                @if ($startsAt && $endsAt?->isFuture() !== false)
                    <div
                        class="sy-countdown"
                        x-data="{
                            start: {{ $startsAt->getTimestamp() * 1000 }},
                            end: {{ ($endsAt ?? $startsAt)->getTimestamp() * 1000 }},
                            now: Date.now(),
                            init() { setInterval(() => (this.now = Date.now()), 30000) },
                            get left() { return Math.max(0, this.start - this.now) },
                            get days() { return Math.floor(this.left / 86400000) },
                            get hours() { return Math.floor((this.left % 86400000) / 3600000) },
                            get minutes() { return Math.floor((this.left % 3600000) / 60000) },
                            get live() { return this.now >= this.start && this.now < this.end },
                            pad(n) { return String(n).padStart(2, '0') },
                        }"
                    >
                        <template x-if="live">
                            <div class="sy-countdown-live"><span class="sy-dot"></span> En cours</div>
                        </template>
                        <template x-if="! live">
                            <div class="sy-countdown-cells">
                                <div><b x-text="days">{{ max(0, (int) now()->diffInDays($startsAt, false)) }}</b><small>jours</small></div>
                                <div><b x-text="pad(hours)">--</b><small>h</small></div>
                                <div><b x-text="pad(minutes)">--</b><small>min</small></div>
                            </div>
                        </template>
                    </div>
                @elseif ($endsAt?->isPast())
                    <div class="sy-countdown"><div class="sy-countdown-live sy-countdown-done">Terminé</div></div>
                @endif

                <div class="sy-event-hero-actions">
                    @if ($publicUrl)
                        <x-filament::button
                            tag="a"
                            :href="$publicUrl"
                            target="_blank"
                            color="gray"
                            icon="heroicon-m-arrow-top-right-on-square"
                            class="sy-btn-glass"
                        >
                            Voir la page
                        </x-filament::button>
                    @endif

                    <x-filament::button tag="a" :href="$editUrl" icon="heroicon-m-pencil-square">
                        Modifier
                    </x-filament::button>
                </div>
            </div>
        </div>
    </section>
</x-filament-widgets::widget>
