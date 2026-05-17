@php
    $artistId = $getState();
    $artist = $artistId ? \App\Models\Artist::with('genres')->find($artistId) : null;
    $performanceTime = $get('performance_time');
@endphp

<div class="w-full">
    @if ($artist)
        <div class="relative overflow-hidden group bg-[#052519] transition-all duration-500 rounded-tl-[2rem] rounded-br-[2rem] h-48 shadow-lg">
            @if($artist->portrait_url)
                <img src="{{ $artist->portrait_url }}" alt="{{ $artist->name }}"
                    class="absolute inset-0 h-full w-full object-cover opacity-80" />
            @endif

            <!-- Overlays matching Show.vue -->
            <div class="absolute inset-0 bg-[#51A687]/15 mix-blend-overlay transition-opacity"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-[#51A687]/50 via-transparent to-transparent opacity-60"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>

            <!-- Backlight Glow -->
            <div class="absolute -bottom-12 -left-12 h-32 w-32 rounded-full bg-[#51A687]/30 blur-[40px] opacity-40"></div>

            <!-- Genre Badges -->
            <div class="absolute top-4 right-4 flex flex-wrap gap-1 justify-end max-w-[70%]">
                @foreach($artist->genres as $genre)
                    <span class="rounded-full border border-white/20 bg-white/5 px-2 py-0.5 text-[8px] font-bold tracking-[0.1em] text-gray-200 uppercase backdrop-blur-md">
                        {{ $genre->name }}
                    </span>
                @endforeach
            </div>

            <!-- Artist Info -->
            <div class="absolute bottom-4 left-6 right-20 pointer-events-none">
                <h3 class="font-chillax text-white text-lg md:text-xl tracking-tighter leading-tight font-normal drop-shadow-md">
                    @if(str_contains($artist->name, ' b2b '))
                        @php
                            $parts = explode(' b2b ', $artist->name);
                        @endphp
                        <span>{{ $parts[0] }}</span>
                        <span class="text-xs lowercase font-semibold mx-1 align-middle">b2b</span>
                        <span>{{ $parts[1] }}</span>
                    @else
                        {{ $artist->name }}
                    @endif
                </h3>
            </div>

            <!-- Time Label -->
            @if($performanceTime)
                <div class="absolute bottom-4 right-6 text-right">
                    <p class="font-chillax text-sm md:text-base text-white/80 font-semibold tracking-widest">
                        {{ \Carbon\Carbon::parse($performanceTime)->format('H:i') }}
                    </p>
                </div>
            @endif
        </div>
    @else
        <div class="w-full h-48 flex flex-col items-center justify-center rounded-tl-[2rem] rounded-br-[2rem] bg-gray-800 border border-dashed border-gray-600 shadow-sm text-white/50">
            <x-heroicon-m-user-circle class="w-10 h-10 mb-2 opacity-50" />
            <span class="text-xs font-medium italic">Sélectionnez un artiste pour voir l'aperçu</span>
        </div>
    @endif
</div>
