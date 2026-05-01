@php
    $sponsorId = $getState();
    $sponsor = $sponsorId ? \App\Models\Sponsor::find($sponsorId) : null;
@endphp

<div class="w-full flex flex-col gap-2">
    {{-- Le Container principal avec hauteur fixe pour la cohérence --}}
    <div
        class="w-full h-32 flex items-center justify-center rounded-xl bg-gray-800 border border-gray-200 dark:border-white/10 overflow-hidden shadow-sm">
        @if ($sponsor)
            {{-- Logo affiché sur fond neutre --}}
            @if ($sponsor->logo)
                <img src="{{ asset('storage/' . $sponsor->logo) }}" alt="Logo"
                    class="h-24 w-auto max-w-[90%] object-contain drop-shadow-sm">
            @else
                <div class="flex flex-col items-center gap-1 opacity-20">
                    <x-heroicon-m-photo class="w-10 h-10" />
                    <span class="text-[10px] font-bold uppercase">Sans Logo</span>
                </div>
            @endif
        @else
            {{-- Empty State (même hauteur que la preview) --}}
            <div class="flex flex-col items-center gap-2 text-white">
                <x-heroicon-m-cursor-arrow-rays class="w-8 h-8 opacity-50" />
                <span class="text-xs font-medium italic">Sélectionnez un sponsor</span>
            </div>
        @endif
    </div>

    {{-- Barre d'infos Website (en bas à gauche) --}}
    <div class="flex items-center gap-2 min-h-6 px-1">
        @if ($sponsor)
            <x-heroicon-m-globe-alt class="w-4 h-4 text-gray-400" />
            @if ($sponsor->website)
                <a href="{{ $sponsor->website }}" target="_blank"
                    class="text-xs font-medium text-primary-600 hover:underline dark:text-primary-400 truncate">
                    {{ str($sponsor->website)->after('://')->limit(40) }}
                </a>
            @else
                <span class="text-xs font-medium text-gray-400 italic">Pas de site</span>
            @endif
        @endif
    </div>
</div>
