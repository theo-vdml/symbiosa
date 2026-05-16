<script setup lang="ts">
    import { Head, Link } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';
    import AppButton from '@/components/AppButton.vue';
    import { archives } from '@/routes';
    import { CheckCircle, Clock, MapPin } from '@lucide/vue';

    defineProps<{
        events: Event[];
    }>();

    function getDateParts(dateString: string) {
        const date = new Date(dateString);
        const day = new Intl.DateTimeFormat('fr-BE', { day: '2-digit' }).format(
            date,
        );
        const month = new Intl.DateTimeFormat('fr-BE', { month: 'short' })
            .format(date)
            .replace('.', '')
            .toUpperCase();
        const year = date.getFullYear();
        return { day, month, year };
    }
</script>

<template>

    <Head title="Calendrier" />

    <Header />

    <div class="relative z-10 overflow-hidden rounded-b-[6rem] bg-black min-h-screen">
        <!-- Background Effects -->
        <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-black via-black to-black"></div>
        <div
            class="pointer-events-none absolute -top-32 left-1/2 h-115 w-[130%] -translate-x-1/2 rounded-full bg-[#06402B]/18 blur-[150px]">
        </div>
        <div class="pointer-events-none absolute inset-0 bg-[url('/noise.png')] opacity-[0.04] mix-blend-soft-light">
        </div>

        <main class="relative z-10 mx-auto max-w-6xl px-6 pt-34 pb-32 md:px-10">
            <header class="mb-20 space-y-4 text-center md:text-left">
                <p class="text-xs font-bold tracking-[0.35em] text-[#51A687] uppercase">
                    Calendrier
                </p>
                <h1 class="font-chillax text-5xl leading-[0.92] text-white md:text-7xl lg:text-8xl">
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

            <div v-if="events.length > 0" class="relative mt-20 px-4 md:px-0">
                <!-- Timeline Line -->
                <div
                    class="absolute top-0 bottom-0 left-1/2 z-1 hidden w-0.5 -translate-x-1/2 bg-linear-to-b from-transparent via-[#51A687]/60 via-10%  to-[#51A687]/20 md:block">
                </div>

                <div class="relative space-y-8 md:space-y-0">
                    <article v-for="(event, index) in events" :key="event.id" class="relative">
                        <!-- Timeline Node -->
                        <div class="absolute top-34 left-1/2 z-10 hidden -translate-x-1/2 -translate-y-1/2 md:block">
                            <div class="h-6 w-6 border-8 rounded-full bg-[#51A687] border-black"></div>
                        </div>

                        <div
                            class="grid grid-cols-1 items-start gap-12 rounded-[2.5rem] border border-white/10 bg-white/3 p-6 md:grid-cols-2 md:gap-24 md:border-0 md:bg-transparent md:p-0 md:py-24">
                            <!-- Poster Column -->
                            <div :class="index % 2 === 0 ? 'md:order-1' : 'md:order-2'" class="flex justify-center">
                                <div
                                    class="relative aspect-3/4 w-full overflow-hidden rounded-2xl border border-white/10 shadow-2xl md:max-w-md md:rounded-3xl">
                                    <img v-if="event.poster" :src="event.poster" :alt="event.title"
                                        class="absolute inset-0 h-full w-full object-cover" />

                                    <!-- Fallback 1: Background Image (Atmospheric but clear) -->
                                    <template v-else-if="event.background">
                                        <img :src="'/' + event.background" :alt="event.title"
                                            class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 hover:scale-105" />
                                        <div
                                            class="absolute inset-0 bg-linear-to-t from-black/60 via-black/10 to-transparent">
                                        </div>
                                    </template>

                                    <!-- Fallback 2: Abstract Branded Glow (Final fallback) -->
                                    <div v-else
                                        class="absolute inset-0 flex flex-col items-center justify-center bg-[#010806] text-center overflow-hidden">

                                        <!-- Animated Corner-to-Corner Light Sources -->
                                        <div class="absolute inset-0">
                                            <!-- Light Source 1 -->
                                            <div class="absolute h-0 w-0 animate-corner-path-1">
                                                <div class="absolute -translate-x-1/2 -translate-y-1/2">
                                                    <div class="h-96 w-96 rounded-full bg-[#51A687]/40 blur-[100px]">
                                                    </div>
                                                    <div
                                                        class="absolute top-1/2 left-1/2 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#51A687]/60 blur-[60px]">
                                                    </div>
                                                    <div
                                                        class="absolute top-1/2 left-1/2 h-32 w-32 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/30 blur-2xl">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Light Source 2 -->
                                            <div class="absolute h-0 w-0 animate-corner-path-2">
                                                <div class="absolute -translate-x-1/2 -translate-y-1/2">
                                                    <div class="h-96 w-96 rounded-full bg-[#51A687]/40 blur-[100px]">
                                                    </div>
                                                    <div
                                                        class="absolute top-1/2 left-1/2 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#51A687]/60 blur-[60px]">
                                                    </div>
                                                    <div
                                                        class="absolute top-1/2 left-1/2 h-32 w-32 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/30 blur-2xl">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div
                                            class="absolute inset-0 bg-[url('/noise.png')] opacity-40 mix-blend-soft-light">
                                        </div>

                                        <!-- Centered Title Overlay -->
                                        <div
                                            class="absolute inset-0 flex flex-col items-center justify-center text-center px-8">
                                            <span
                                                class="font-chillax text-4xl md:text-5xl text-white uppercase tracking-tighter leading-[0.9] drop-shadow-[0_0_40px_rgba(81,166,135,0.8)]">
                                                {{ event.title }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Info Column -->
                            <div :class="[index % 2 === 0 ? 'md:order-2 md:pl-12' : 'md:order-1 md:pr-12 md:text-right']"
                                class="flex flex-col px-2 pb-4 md:px-0 md:pb-0">
                                <!-- Header: Date + Title + Genres (Grouped for mobile) -->
                                <div class="flex flex-row items-start gap-6 md:flex-col"
                                    :class="index % 2 !== 0 ? 'md:items-end' : 'md:items-start'">
                                    <!-- Date Badge -->
                                    <div class="shrink-0">
                                        <div
                                            class="flex h-20 w-20 flex-col items-center justify-center rounded-2xl border border-[#51A687]/30 bg-[#51A687]/10 text-center backdrop-blur-md">
                                            <span class="font-chillax text-3xl leading-none text-white">
                                                {{ getDateParts(event.date).day }}
                                            </span>
                                            <span
                                                class="text-[10px] font-bold tracking-[0.25em] text-[#51A687] uppercase">
                                                {{ getDateParts(event.date).month }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col"
                                        :class="index % 2 !== 0 ? 'md:items-end' : 'md:items-start'">
                                        <h2
                                            class="mb-2 font-chillax text-3xl leading-[1.1] text-white md:mb-6 md:text-4xl lg:text-5xl">
                                            {{ event.title }}
                                        </h2>

                                        <!-- Music Styles -->
                                        <div v-if="event.genres && event.genres.length > 0"
                                            class="mb-4 flex flex-wrap gap-2 md:mb-8"
                                            :class="index % 2 !== 0 ? 'md:justify-end' : 'md:justify-start'">
                                            <span v-for="(genre, genreIndex) in event.genres" :key="genre.id"
                                                class="text-[10px] font-bold tracking-[0.3em] text-[#51A687] uppercase md:text-xs">
                                                {{ genre.name }}
                                                <span v-if="genreIndex < event.genres.length - 1"
                                                    class="ml-1 text-gray-700">/</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="mt-6 mb-8 flex items-center gap-3 text-gray-400 md:mt-0 md:mb-10" :class="index % 2 !== 0 ? 'md:justify-end' : ''
                                    ">
                                    <MapPin class="h-5 w-5" />
                                    <span class="text-lg font-medium tracking-wide uppercase">
                                        {{ event.city }}, {{ event.country }}
                                    </span>
                                </div>

                                <!-- CTAs -->
                                <div class="flex flex-col gap-6 sm:flex-row" :class="index % 2 !== 0
                                    ? 'md:flex-row-reverse'
                                    : 'md:flex-row'
                                    ">
                                    <AppButton :href="`/events/${event.slug}`" variant="outline" size="lg"
                                        class="w-full md:w-auto">
                                        Découvrir l'expérience
                                    </AppButton>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
                <div class="mt-32 flex flex-col items-center text-center bg-black z-2 relative">
                    <div
                        class="mb-6 flex h-12 w-12 items-center justify-center rounded-full bg-[#51A687]/10 text-[#51A687]">
                        <CheckCircle class="h-6 w-6" />
                    </div>
                    <h3 class="font-chillax text-2xl text-white">
                        C'est tout pour le moment !
                    </h3>
                    <p class="mt-3 max-w-sm text-sm text-gray-400">
                        Tu as vu tous nos événements à venir. En attendant les
                        prochaines annonces, replonge dans nos souvenirs.
                    </p>
                    <AppButton :href="archives.url()" variant="outline" size="md" class="mt-10">
                        Consulter les archives
                    </AppButton>
                </div>
            </div>

            <section v-else class="flex flex-col items-center justify-center py-16 text-center">
                <div class="mb-8 flex h-24 w-24 items-center justify-center rounded-full bg-white/5 text-gray-600">
                    <Clock class="h-10 w-10" />
                </div>
                <h2 class="font-chillax text-4xl text-white">
                    Aucun événement programmé
                </h2>
                <p class="mt-4 max-w-sm text-gray-400">
                    Nous préparons de nouvelles expériences. En attendant les
                    prochaines dates, découvre nos anciens événements.
                </p>
                <AppButton :href="archives.url()" variant="outline" size="md" class="mt-10">
                    Voir les archives
                </AppButton>
            </section>
        </main>
    </div>

    <Footer />
</template>

<style scoped>
@keyframes corner-path {

    0%,
    100% {
        top: 0%;
        left: 100%;
    }

    25% {
        top: 100%;
        left: 100%;
    }

    50% {
        top: 100%;
        left: 0%;
    }

    75% {
        top: 0%;
        left: 0%;
    }
}

.animate-corner-path-1 {
    animation: corner-path 30s linear infinite;
}

.animate-corner-path-2 {
    animation: corner-path 30s linear infinite;
    animation-delay: -15s;
}
</style>
