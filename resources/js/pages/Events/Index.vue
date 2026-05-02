<script setup lang="ts">
    import { Head, Link } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';
    import AppButton from '@/components/AppButton.vue';
    import { archives } from '@/routes';

    interface Event {
        id: number;
        title: string;
        date: string;
        start_time: string;
        end_time: string;
        city: string;
        country: string;
        address: string;
        dress_code: string;
        minimum_age: number;
        description: string;
        background: string;
        poster: string;
        created_at: string;
        updated_at: string;
        faq: {
            question: string;
            answer: string;
        }[];
        genres?: string[]; // Adding as optional for UI purposes
    }

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
                    class="absolute top-0 bottom-0 left-1/2 z-1 hidden w-0.5 -translate-x-1/2 bg-linear-to-b from-transparent via-[#51A687]/50 via-10% to-transparent md:block">
                </div>

                <div class="relative space-y-8 md:space-y-0">
                    <article v-for="(event, index) in events" :key="event.id" class="relative">
                        <!-- Timeline Node -->
                        <div class="absolute top-34 left-1/2 z-10 hidden -translate-x-1/2 -translate-y-1/2 md:block">
                            <div
                                class="h-3 w-3 rotate-45 border-2 border-[#51A687] bg-black shadow-[0_0_20px_rgba(81,166,135,0.8)]">
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-1 items-start gap-12 rounded-[2.5rem] border border-white/10 bg-white/3 p-6 md:grid-cols-2 md:gap-24 md:border-0 md:bg-transparent md:p-0 md:py-24">
                            <!-- Poster Column -->
                            <div :class="index % 2 === 0 ? 'md:order-1' : 'md:order-2'" class="flex justify-center">
                                <div
                                    class="relative aspect-3/4 w-full overflow-hidden rounded-2xl border border-white/10 shadow-2xl md:max-w-md md:rounded-3xl">
                                    <img :src="event.poster" :alt="event.title" class="h-full w-full object-cover" />
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
                                            class="mb-2 font-chillax text-4xl leading-[1.1] text-white md:mb-6 md:text-6xl lg:text-7xl">
                                            {{ event.title }}
                                        </h2>

                                        <!-- Music Styles -->
                                        <div class="mb-4 flex flex-wrap gap-2 md:mb-8"
                                            :class="index % 2 !== 0 ? 'md:justify-end' : 'md:justify-start'">
                                            <span v-for="genre in event.genres || ['House', 'Techno']" :key="genre"
                                                class="text-[10px] font-bold tracking-[0.3em] text-[#51A687] uppercase md:text-xs">
                                                {{ genre }}
                                                <span
                                                    v-if="(event.genres || ['House', 'Techno']).indexOf(genre) < (event.genres || ['House', 'Techno']).length - 1"
                                                    class="ml-1 text-gray-700">/</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="mt-6 mb-8 flex items-center gap-3 text-gray-400 md:mt-0 md:mb-10" :class="index % 2 !== 0 ? 'md:justify-end' : ''
                                    ">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="2" stroke="currentColor" class="h-5 w-5 text-[#51A687]">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                    </svg>
                                    <span class="text-lg font-medium tracking-wide uppercase">
                                        {{ event.city }}, {{ event.country }}
                                    </span>
                                </div>

                                <!-- CTAs -->
                                <div class="flex flex-col gap-6 sm:flex-row" :class="index % 2 !== 0
                                    ? 'md:flex-row-reverse'
                                    : 'md:flex-row'
                                    ">
                                    <AppButton :href="`/events/${event.id}`" variant="outline" size="lg"
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
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                            stroke="currentColor" class="h-6 w-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
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
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="h-12 w-12">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
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
