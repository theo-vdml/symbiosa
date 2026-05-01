<script setup lang="ts">
    import AppButton from '@/components/AppButton.vue';

    interface Props {
        eventTitle: string;
        eventDate: string;
        eventDay: string;
        eventCity: string;
        eventCountry: string;
        eventImage: string;
        ticketLink: string;
        moreInfoLink: string;
    }

    defineProps<Props>();
</script>

<template>
    <section class="relative min-h-[100vh] w-full overflow-hidden bg-black flex items-center py-24 lg:py-32">
        <!-- Hero Background -->
        <!-- <img src="/background_test_2.png" class="absolute inset-0 h-full w-full object-cover" alt="" /> -->

        <!-- Stronger Multi-layered Overlays -->
        <div class="absolute inset-0 bg-black/60"></div> <!-- Increased base tint -->
        <div class="absolute inset-0 bg-linear-to-b from-black via-transparent to-black"></div>
        <!-- Vertical Vignette -->
        <div class="absolute inset-0 bg-radial-gradient from-transparent via-black/40 to-black/80"></div>

        <div
            class="relative z-10 max-w-7xl mx-auto px-6 lg:px-12 flex flex-col lg:flex-row items-center gap-16 lg:gap-32">

            <!-- Left: The Poster (Visual) -->
            <div class="w-full lg:w-5/12 flex justify-center">
                <div class="relative group max-w-sm lg:max-w-none transition-transform duration-500 hover:scale-[1.02]">
                    <!-- Enhanced Glow -->
                    <div
                        class="absolute -inset-4 bg-[#51A687]/30 blur-3xl opacity-20 group-hover:opacity-60 transition-opacity duration-700">
                    </div>
                    <!-- Outer Border/Shadow ring -->
                    <div class="absolute inset-0 rounded-2xl shadow-[0_0_80px_rgba(0,0,0,0.8)] ring-1 ring-white/20">
                    </div>
                    <img :src="eventImage" :alt="eventTitle"
                        class="relative z-10 rounded-2xl border border-white/10 w-full h-auto object-cover shadow-2xl" />
                </div>
            </div>

            <!-- Right: The Content (Exact Hero Style) -->
            <div class="w-full lg:w-7/12 space-y-12">
                <div class="space-y-8">
                    <div class="flex items-center gap-4">
                        <div class="h-px w-8 bg-[#51A687]"></div>
                        <span class="text-[#51A687] text-sm font-bold tracking-[0.3em] uppercase">
                            Coming Soon
                        </span>
                    </div>

                    <h2 class="font-chillax text-6xl md:text-8xl text-white leading-[0.85] tracking-tight uppercase">
                        {{ eventTitle }}
                    </h2>

                    <!-- Info Grid: Mirrored from Events/Show -->
                    <div class="grid grid-cols-2 gap-12 pt-8 max-w-2xl">
                        <!-- Date Column -->
                        <div class="flex flex-col items-start gap-4 group">
                            <div
                                class="h-12 w-12 flex items-center justify-center rounded-full bg-white/5 border border-white/10 text-[#51A687]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-medium tracking-[0.2em] text-white/50 uppercase">{{ eventDay
                                    }}</span>
                                <span class="text-2xl font-chillax text-white uppercase leading-tight">
                                    {{ eventDate }}
                                </span>
                            </div>
                        </div>

                        <!-- Location Column -->
                        <div class="flex flex-col items-start gap-4 group">
                            <div
                                class="h-12 w-12 flex items-center justify-center rounded-full bg-white/5 border border-white/10 text-[#51A687]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-2xl font-chillax text-white uppercase leading-tight">
                                    {{ eventCity }}
                                </span>
                                <span class="text-xs font-medium tracking-[0.2em] text-white/50 uppercase">{{
                                    eventCountry }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTAs -->
                <div class="flex flex-col sm:flex-row gap-6">
                    <AppButton :href="ticketLink" variant="primary" size="lg"
                        class="w-full sm:w-auto shadow-2xl shadow-[#51A687]/20 border-[#51A687]/50 bg-[#51A687]/10 backdrop-blur-xl hover:bg-[#51A687]/20">
                        Réserver mes places
                    </AppButton>
                    <AppButton :href="moreInfoLink" variant="outline" size="lg" class="w-full sm:w-auto">
                        Découvrir l'expérience
                    </AppButton>
                </div>
            </div>
        </div>
    </section>
</template>
