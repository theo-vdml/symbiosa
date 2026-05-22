<script setup lang="ts">
    interface Props {
        artists: any[];
    }

    defineProps<Props>();

    const getPerformanceTime = (time: string) => {
        return time.substring(0, 5).replace(':', 'h');
    };
</script>

<template>
    <div v-if="artists && artists.length" class="space-y-12">
        <h2 class="font-chillax text-4xl text-white">Line-up</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <component :is="artist.website ? 'a' : 'div'" v-for="(artist, index) in artists" :key="artist.name"
                :href="artist.website" :target="artist.website ? '_blank' : undefined"
                :rel="artist.website ? 'noopener noreferrer' : undefined" :class="[
                    'relative overflow-hidden group bg-[#052519] transition-all duration-500',
                    'rounded-tl-[3rem] rounded-br-[3rem]',
                    artist.website ? 'cursor-pointer' : 'cursor-default',
                    (artists.length % 2 !== 0 && index === 0) ||
                        (artists.length % 2 === 0 && (index === 0 || index === 1))
                        ? 'md:col-span-2 h-80 md:h-96' : 'h-80'
                ]">
                <!-- Artist Image - Clean and visible -->
                <img :src="artist.portrait_url" :alt="artist.name"
                    class="absolute inset-0 h-full w-full object-cover transition-all duration-700 group-hover:scale-105" />

                <!-- Luminous Overlays - More vibrant and light -->
                <div class="absolute inset-0 bg-[#51A687]/15 mix-blend-overlay transition-opacity">
                </div>
                <div
                    class="absolute inset-0 bg-linear-to-t from-[#51A687]/50 via-transparent to-transparent opacity-60 group-hover:opacity-30 transition-opacity">
                </div>
                <div class="absolute inset-0 bg-linear-to-t from-black/60  to-transparent">
                </div>

                <!-- Backlight Glow -->
                <div
                    class="absolute -bottom-12 -left-12 h-48 w-48 rounded-full bg-[#51A687]/30 blur-[60px] opacity-40 group-hover:opacity-100 transition-all duration-700">
                </div>

                <!-- Genre Badges (Strict Hero Style) -->
                <div class="absolute top-6 right-8 flex flex-wrap gap-2 justify-end max-w-[70%]">
                    <span v-for="genre in artist.genres" :key="genre.name"
                        class="rounded-full border border-white/20 bg-white/5 px-4 py-1.5 text-[10px] font-bold tracking-[0.25em] text-gray-200 uppercase backdrop-blur-md">
                        {{ genre.name }}
                    </span>
                </div>

                <!-- Artist Info -->
                <div class="absolute bottom-8 left-8 right-32 pointer-events-none">
                    <h3 :class="[
                        'font-chillax text-white tracking-tighter leading-tight font-normal drop-shadow-md',
                        (artists.length % 2 !== 0 && index === 0) ||
                            (artists.length % 2 === 0 && (index === 0 || index === 1))
                            ? 'text-4xl md:text-6xl' : 'text-2xl md:text-4xl'
                    ]">
                        <template v-if="artist.name.includes(' b2b ')">
                            <span>{{ artist.name.split(' b2b ')[0] }}</span>
                            <span class="text-xl md:text-3xl lowercase  font-semibold mx-4 align-middle">b2b</span>
                            <span>{{ artist.name.split(' b2b ')[1] }}</span>
                        </template>
                        <template v-else>
                            {{ artist.name }}
                        </template>
                    </h3>
                </div>

                <!-- Time Label -->
                <div v-if="artist.pivot.performance_time" class="absolute bottom-8 right-8 text-right">
                    <p
                        class="font-chillax text-xl md:text-2xl text-white/80 font-semibold tracking-widest group-hover:text-white transition-colors">
                        {{ getPerformanceTime(artist.pivot.performance_time) }}
                    </p>
                </div>
            </component>
        </div>
    </div>
</template>
