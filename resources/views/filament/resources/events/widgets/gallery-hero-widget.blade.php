<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col md:flex-row items-center justify-between gap-6">

            <!-- Contenu textuel -->
            <div class="flex-1 space-y-2">
                <h2 class="text-xl font-bold text-gray-950 dark:text-white">
                    Galerie de l'événement
                </h2>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Gérez la galerie d'images qui sera affichée dans la section archives du site une fois l'événement
                    terminé.
                </p>

                <p class="text-sm font-medium text-primary-600 dark:text-primary-400 mt-2">
                    Utilisez le bouton d'upload pour importer jusqu'à 50 images simultanément.
                </p>
            </div>

            <!-- Compteur -->
            <div
                class="flex flex-col items-center justify-center p-6 bg-gray-50 dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-800 min-w-37.5">
                <span class="text-4xl font-black text-gray-900 dark:text-white leading-none">
                    {{ $record->media()->where('collection_name', 'gallery')->count() }}
                </span>
                <span class="text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400 mt-2">
                    Images
                </span>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
