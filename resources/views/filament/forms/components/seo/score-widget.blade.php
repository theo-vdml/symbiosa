<div class="space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">

    {{-- Header : Score Global version Filament Card Header --}}
    @php
        $score = $analysis['score'];
        $scoreColor =
            $score > 75
                ? 'text-success-600 dark:text-success-400'
                : ($score > 45
                    ? 'text-warning-600 dark:text-warning-400'
                    : 'text-danger-600 dark:text-danger-400');
        $metrics = $analysis['metrics'];
    @endphp

    <div class="flex items-center gap-x-4 border-b border-gray-100 pb-4 dark:border-gray-800">
        <div class="text-4xl font-black tracking-tight {{ $scoreColor }}">
            {{ $score }}<span class="text-lg font-medium text-gray-400 dark:text-gray-500">/100</span>
        </div>
        <div>
            <h4 class="text-sm font-semibold leading-6 text-gray-950 dark:text-white">Indexation & Métadonnées</h4>
            <p class="text-xs text-gray-550 dark:text-gray-400">Rapport d'audit technique de la conformité SEO de la
                page.</p>
        </div>
    </div>

    {{-- Body : Liste des métriques formatée façon Statut Rows de Filament --}}
    <div class="divide-y divide-gray-100 dark:divide-gray-800 text-sm">
        @foreach ($metrics as $key => $metric)
            @php
                // Mapping des couleurs et icônes aux standards Filament
                [$badgeColor, $icon] = match ($metric['status']) {
                    'good' => [
                        'bg-success-50 text-success-700 ring-success-600/10 dark:bg-success-500/10 dark:text-success-400 dark:ring-success-500/20',
                        'heroicon-m-check-circle',
                    ],
                    'warning' => [
                        'bg-warning-50 text-warning-700 ring-warning-600/10 dark:bg-warning-500/10 dark:text-warning-400 dark:ring-warning-500/20',
                        'heroicon-m-exclamation-triangle',
                    ],
                    'danger', 'empty' => [
                        'bg-danger-50 text-danger-700 ring-danger-600/10 dark:bg-danger-500/10 dark:text-danger-400 dark:ring-danger-500/20',
                        'heroicon-m-x-circle',
                    ],
                    default => [
                        'bg-gray-50 text-gray-600 ring-gray-500/10 dark:bg-gray-400/10 dark:text-gray-400 dark:ring-gray-400/20',
                        'heroicon-m-information-circle',
                    ],
                };
            @endphp

            <div
                class="flex flex-col gap-y-2 py-4 first:pt-0 last:pb-0 sm:flex-row sm:items-start sm:justify-between sm:gap-x-6">
                {{-- Données sémantiques à gauche --}}
                <div class="space-y-1 max-w-xl">
                    <div class="flex items-center gap-x-2">
                        <span class="font-semibold text-gray-950 dark:text-white">{{ $metric['label'] }}</span>

                        {{-- Indicateur de valeur (ex: 45 / 60 car.) --}}
                        <span
                            class="inline-flex items-center rounded-md bg-gray-50 px-2 py-0.5 text-xs font-medium text-gray-600 ring-1 ring-inset ring-gray-500/10 dark:bg-gray-800 dark:text-gray-450 dark:ring-gray-700">
                            {{ $metric['value'] }}
                        </span>
                    </div>
                    <p class="text-xs leading-relaxed text-gray-500 dark:text-gray-400">
                        {{ $metric['help'] }}
                    </p>
                </div>

                {{-- Badge de Statut Filament à droite --}}
                <div class="flex items-center sm:pt-0.5">
                    <span
                        class="inline-flex items-center gap-x-1.5 rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $badgeColor }}">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                            aria-hidden="true">
                            @if ($metric['status'] === 'good')
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            @elseif($metric['status'] === 'warning')
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                            @else
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            @endif
                        </svg>
                        {{ $metric['status'] === 'good' ? 'Conforme' : ($metric['status'] === 'warning' ? 'Alerte' : 'Critique') }}
                    </span>
                </div>
            </div>
        @endforeach
    </div>
</div>
