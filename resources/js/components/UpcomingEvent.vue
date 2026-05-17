<script setup lang="ts">
    import AppButton from '@/components/AppButton.vue';
    import events from '@/routes/events';
    import { Calendar, MapPin } from '@lucide/vue';

    interface Props {
        event: Event;
    }

    defineProps<Props>();

    const getWeekday = (dateStr: string) => {
        const date = new Date(dateStr);
        return date.toLocaleDateString('fr-FR', { weekday: 'long' });
    };

    const getDateFormatted = (dateStr: string) => {
        const date = new Date(dateStr);
        return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
    };
</script>

<template>
    <section class="relative min-h-screen w-full overflow-hidden bg-black flex items-center py-24 lg:py-32">

        <!-- --- LAYER 0: SMART BACKGROUND --- -->
        <div class="absolute inset-0 z-0">
            <!-- Priority 1: Event Background -->
            <img v-if="event.background_url" :src="event.background_url" :srcset="event.background_responsive?.srcset"
                sizes="(max-width: 768px) 200vw, 100vw" class="h-full w-full object-cover" alt="" />

            <!-- Priority 2: Branded Deep Gradient -->
            <template v-else>
                <div class="h-full w-full bg-linear-to-b from-transparent via-[#51A687]/70 to-transparent"></div>
                <div class="absolute left-1/2 top-1/2 -translate-1/2 w-1/2 aspect-square bg-[#51A687]/70 blur-3xl">
                </div>
            </template>

            <!-- Overlays for Readability -->
            <div class="absolute inset-0 bg-black/60"></div>
            <div class="absolute inset-0 bg-linear-to-b from-black via-transparent to-black"></div>
            <div class="absolute inset-0 bg-radial-gradient from-transparent via-black/40 to-black/80"></div>
            <div class="absolute inset-0 bg-[url('/noise.png')] opacity-[0.05] mix-blend-soft-light"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-12 w-full">
            <div :class="[
                'flex flex-col items-center gap-16 lg:gap-32 text-center',
                event.poster_url ? 'lg:flex-row' : ''
            ]">
                <!-- Left: The Poster (Optional) -->
                <div v-if="event.poster_url" class="w-full lg:w-4/12 xl:w-5/12 shrink-0 flex justify-center">
                    <div class="relative group w-full max-w-sm lg:max-w-none">
                        <div
                            class="relative z-10 w-full aspect-3/4 overflow-hidden rounded-2xl border border-white/10 shadow-2xl">
                            <img :src="event.poster_url" :alt="event.title"
                                class="h-full w-full object-cover" />
                        </div>
                    </div>
                </div>

                <!-- Right: The Content -->
                <div :class="[
                    'w-full space-y-10 flex flex-col items-center',
                    event.poster_url ? 'lg:w-7/12' : 'max-w-4xl mx-auto'
                ]">
                    <div class="space-y-6 flex flex-col items-center w-full">
                        <!-- Header: City & Date -->
                        <div class="flex flex-col items-center gap-2">
                            <span class="text-white text-sm font-bold tracking-[0.4em] uppercase">
                                {{ event.city }}
                            </span>
                            <span class="text-[#51A687] text-sm font-bold tracking-[0.4em] uppercase">
                                {{ getWeekday(event.date) }} {{ getDateFormatted(event.date) }}
                            </span>
                        </div>

                        <h2 :class="[
                            'font-chillax text-white leading-[0.85] tracking-tight uppercase',
                            event.poster_url ? 'text-6xl md:text-8xl' : 'text-7xl md:text-9xl'
                        ]">
                            {{ event.title }}
                        </h2>

                        <!-- Event Description -->
                        <div v-if="event.description"
                            class="max-w-2xl text-white text-lg md:text-xl leading-relaxed font-light"
                            v-html="event.description">
                        </div>
                    </div>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row justify-center gap-6 pt-4 w-full">
                        <AppButton v-if="event.ticketing_status === 'open'" :href="events.ticketing(event.slug).url" variant="primary" size="lg"
                            class="w-full sm:w-auto border-[#51A687]/50 bg-[#51A687]/10 backdrop-blur-xl hover:bg-[#51A687]/20">
                            Réserver mes places
                        </AppButton>
                        <AppButton :href="events.show(event.slug).url" variant="primary" size="lg"
                            class="w-full sm:w-auto border-[#51A687]/50 bg-[#51A687]/10 backdrop-blur-xl hover:bg-[#51A687]/20">
                            Découvrir l'expérience
                        </AppButton>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
