<script setup lang="ts">
    import { Head } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';
    import EventListItem from '@/components/Events/EventListItem.vue';
    import EventEmptyState from '@/components/Events/EventEmptyState.vue';
    import EventListEnd from '@/components/Events/EventListEnd.vue';
    import EventPoster from '@/components/Events/EventPoster.vue';
    import EventDetails from '@/components/Events/EventDetails.vue';

    const props = defineProps<{
        events: Event[];
    }>();
</script>

<template>

    <Head title="Calendrier" />

    <Header />

    <div class="relative z-10 overflow-hidden rounded-b-[3rem] lg:rounded-b-[6rem] bg-black min-h-screen">
        <!-- Background Effects -->
        <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-black via-black to-black"></div>
        <div
            class="pointer-events-none absolute -top-32 left-1/2 h-115 w-[130%] -translate-x-1/2 rounded-full bg-[#06402B]/18 blur-[150px]">
        </div>
        <div class="pointer-events-none absolute inset-0 bg-[url('/noise.png')] opacity-[0.04] mix-blend-soft-light">
        </div>

        <main class="relative z-10 mx-auto max-w-6xl px-4 pt-34 pb-32 md:px-10">
            <header class="mb-16 md:mb-20 space-y-4 text-center md:text-left">
                <p class="text-xs font-bold tracking-[0.35em] text-[#51A687] uppercase">
                    Calendrier
                </p>
                <h1 class="font-chillax text-4xl leading-[0.92] text-white md:text-7xl lg:text-8xl">
                    Tous les événements <br class="hidden md:block" />
                    à venir
                </h1>
                <p class="max-w-2xl text-sm text-gray-300 md:text-base">
                    Découvrez les prochaines expériences Symbiosa.
                    <br class="hidden md:block" />
                    Chaque événement est une immersion unique dans l'univers
                    électronique.
                </p>
            </header>

            <div v-if="events.length > 0" class="relative mt-20 md:px-0">
                <!-- Timeline Line -->
                <div
                    class="absolute top-0 bottom-0 left-1/2 z-1 hidden w-0.5 -translate-x-1/2 bg-linear-to-b from-transparent via-[#51A687]/60 via-10%  to-[#51A687]/20 md:block">
                </div>

                <!-- Events List -->
                <div class="relative space-y-16 md:space-y-0">
                    <article v-for="(event, index) in events" :key="event.id"
                        class="relative border-b border-white/5 pb-16 last:border-0 md:border-0 md:pb-0">
                        <!-- Timeline Node -->
                        <div class="absolute top-34 left-1/2 z-10 hidden -translate-x-1/2 -translate-y-1/2 md:block">
                            <div class="h-6 w-6 border-8 rounded-full bg-[#51A687] border-black"></div>
                        </div>

                        <div class="grid grid-cols-1 items-start gap-10 md:grid-cols-2 md:gap-24 md:py-24">
                            <!-- Poster Column -->
                            <div :class="index % 2 === 0 ? 'md:order-1' : 'md:order-2'" class="flex justify-center">
                                <EventPoster :title="event.title" :poster-url="event.poster_url"
                                    :background-url="event.background_url"
                                    :background-responsive="event.background_responsive" />
                            </div>

                            <!-- Info Column -->
                            <EventDetails :event="event" :is-even="index % 2 === 0"
                                :class="index % 2 === 0 ? 'md:order-2' : 'md:order-1'" />
                        </div>
                    </article>
                </div>

                <EventListEnd />
            </div>

            <EventEmptyState v-else />
        </main>
    </div>

    <Footer />
</template>
