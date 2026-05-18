<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex items-center gap-x-3">
            @if ($icon)
                <x-filament::icon :icon="$icon" class="h-6 w-6 text-gray-500 dark:text-gray-400" />
            @endif

            <h2 class="text-xl font-bold tracking-tight text-gray-950 dark:text-white">
                {{ $title }}
            </h2>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
