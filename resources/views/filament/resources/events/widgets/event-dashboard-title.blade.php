<x-filament-widgets::widget class="sy-dashboard-title-widget">
    <div class="sy-dashboard-title">
        @if ($icon)
            <span class="sy-dashboard-title-icon">
                <x-filament::icon :icon="$icon" />
            </span>
        @endif

        <h2>{{ $title }}</h2>
    </div>
</x-filament-widgets::widget>
