<script setup lang="ts">
    import { computed, ref, watch } from 'vue';
    import { Head, Link } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';

    interface ArchiveEvent {
        id: number;
        title: string;
        type: string;
        genres: string[];
        isoDate: string;
        location: string;
        image: string;
        lineup: string[];
        photoCount: number;
        recapLink: string;
    }

    const archivedEvents: ArchiveEvent[] = [
        {
            id: 1,
            title: 'EDEN Closing Ritual',
            type: 'Festival',
            genres: ['House', 'Techno'],
            isoDate: '2025-10-18T22:00:00+02:00',
            location: 'Gembloux, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Mina Lune', 'Krys A', 'Noah V', 'Mira K'],
            photoCount: 118,
            recapLink: '/events/eden-closing-ritual',
        },
        {
            id: 2,
            title: 'EDEN Warehouse Night',
            type: 'DJ Set',
            genres: ['Techno'],
            isoDate: '2025-08-09T23:00:00+02:00',
            location: 'Namur, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Aster', 'Velin', 'Darya'],
            photoCount: 86,
            recapLink: '/events/eden-warehouse-night',
        },
        {
            id: 3,
            title: 'EDEN Sunset Garden',
            type: 'Open Air',
            genres: ['House'],
            isoDate: '2025-06-14T18:00:00+02:00',
            location: 'Wavre, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Rami', 'Aya Sol', 'Nox'],
            photoCount: 73,
            recapLink: '/events/eden-sunset-garden',
        },
        {
            id: 4,
            title: 'EDEN Midnight Motion',
            type: 'DJ Set',
            genres: ['Hardstyle', 'Techno'],
            isoDate: '2025-03-01T23:30:00+01:00',
            location: 'Liege, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Kael', 'Tyno', 'Selva'],
            photoCount: 95,
            recapLink: '/events/eden-midnight-motion',
        },
        {
            id: 5,
            title: 'EDEN Winter Archive',
            type: 'Festival',
            genres: ['House', 'Hardstyle'],
            isoDate: '2024-12-20T21:00:00+01:00',
            location: 'Bruxelles, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Kove', 'Nia', 'Renz', 'Silo'],
            photoCount: 132,
            recapLink: '/events/eden-winter-archive',
        },
        {
            id: 6,
            title: 'EDEN First Signal',
            type: 'Open Air',
            genres: ['House', 'Techno'],
            isoDate: '2024-09-07T17:00:00+02:00',
            location: 'Gembloux, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Lio', 'Mira K', 'Aster'],
            photoCount: 64,
            recapLink: '/events/eden-first-signal',
        },
        {
            id: 7,
            title: 'EDEN Neon Pulse',
            type: 'DJ Set',
            genres: ['Techno', 'House'],
            isoDate: '2024-07-22T22:30:00+02:00',
            location: 'Charleroi, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Syn', 'Vox', 'Keev'],
            photoCount: 56,
            recapLink: '/events/eden-neon-pulse',
        },
        {
            id: 8,
            title: 'EDEN Abyss Deep',
            type: 'Festival',
            genres: ['Hardstyle'],
            isoDate: '2024-05-31T21:00:00+02:00',
            location: 'Mons, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Raw Force', 'Sonic Boom', 'Titan'],
            photoCount: 154,
            recapLink: '/events/eden-abyss-deep',
        },
        {
            id: 9,
            title: 'EDEN Ethereal Waves',
            type: 'Open Air',
            genres: ['House', 'Techno'],
            isoDate: '2024-04-13T18:30:00+02:00',
            location: 'Liege, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Luna', 'Echo', 'Vera'],
            photoCount: 82,
            recapLink: '/events/eden-ethereal-waves',
        },
        {
            id: 10,
            title: 'EDEN Crystal Nights',
            type: 'DJ Set',
            genres: ['House'],
            isoDate: '2024-03-09T23:00:00+01:00',
            location: 'Tournai, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Crystal', 'Sol', 'Tara'],
            photoCount: 71,
            recapLink: '/events/eden-crystal-nights',
        },
        {
            id: 11,
            title: 'EDEN Inferno Fest',
            type: 'Festival',
            genres: ['Hardstyle', 'Techno'],
            isoDate: '2024-02-17T20:00:00+01:00',
            location: 'Antwerp, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Inferno', 'Blaze', 'Surge', 'Volt'],
            photoCount: 189,
            recapLink: '/events/eden-inferno-fest',
        },
        {
            id: 12,
            title: 'EDEN Silent Echo',
            type: 'DJ Set',
            genres: ['Techno'],
            isoDate: '2023-12-02T22:00:00+01:00',
            location: 'Brussels, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Echo', 'Silence', 'Lux'],
            photoCount: 45,
            recapLink: '/events/eden-silent-echo',
        },
        {
            id: 13,
            title: 'EDEN Aurora Lights',
            type: 'Open Air',
            genres: ['House'],
            isoDate: '2023-10-21T19:00:00+02:00',
            location: 'Waterloo, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Aurora', 'Zephyr', 'Stella'],
            photoCount: 93,
            recapLink: '/events/eden-aurora-lights',
        },
        {
            id: 14,
            title: 'EDEN Shadow Dance',
            type: 'Festival',
            genres: ['House', 'Techno'],
            isoDate: '2023-09-09T21:30:00+02:00',
            location: 'Gent, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Shadow', 'Dancer', 'Noir', 'Lume'],
            photoCount: 156,
            recapLink: '/events/eden-shadow-dance',
        },
        {
            id: 15,
            title: 'EDEN Rhythm Storm',
            type: 'DJ Set',
            genres: ['Hardstyle', 'Techno'],
            isoDate: '2023-08-15T23:30:00+02:00',
            location: 'Bruges, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Storm', 'Rhythm', 'Pulse'],
            photoCount: 67,
            recapLink: '/events/eden-rhythm-storm',
        },
        {
            id: 16,
            title: 'EDEN Velvet Dreams',
            type: 'Open Air',
            genres: ['House'],
            isoDate: '2023-07-08T18:00:00+02:00',
            location: 'Mechelen, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Velvet', 'Dreamz', 'Silk'],
            photoCount: 78,
            recapLink: '/events/eden-velvet-dreams',
        },
        {
            id: 17,
            title: 'EDEN Golden Hour',
            type: 'Festival',
            genres: ['House', 'Techno'],
            isoDate: '2023-06-03T20:00:00+02:00',
            location: 'Louvain, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Golden', 'Hour', 'Sunny', 'Ray'],
            photoCount: 142,
            recapLink: '/events/eden-golden-hour',
        },
        {
            id: 18,
            title: 'EDEN Void Echoes',
            type: 'DJ Set',
            genres: ['Techno'],
            isoDate: '2023-05-12T22:00:00+02:00',
            location: 'Namur, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Void', 'Echo', 'Abyss'],
            photoCount: 54,
            recapLink: '/events/eden-void-echoes',
        },
        {
            id: 19,
            title: 'EDEN Phoenix Rising',
            type: 'Festival',
            genres: ['Hardstyle', 'House'],
            isoDate: '2023-04-29T19:30:00+02:00',
            location: 'Gembloux, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Phoenix', 'Rise', 'Fire', 'Wing'],
            photoCount: 167,
            recapLink: '/events/eden-phoenix-rising',
        },
        {
            id: 20,
            title: 'EDEN Midnight Eclipse',
            type: 'Open Air',
            genres: ['Techno', 'House'],
            isoDate: '2023-03-18T21:00:00+01:00',
            location: 'Liege, Belgique',
            image: '/eden_poster_light.png',
            lineup: ['Eclipse', 'Night', 'Luna', 'Star'],
            photoCount: 98,
            recapLink: '/events/eden-midnight-eclipse',
        },
    ];

    const search = ref('');
    const pageSize = 12;
    const visibleCount = ref(pageSize);

    const pastEvents = computed(() => {
        const now = new Date();
        return [...archivedEvents]
            .filter((event) => new Date(event.isoDate) < now)
            .sort(
                (a, b) =>
                    new Date(b.isoDate).getTime() - new Date(a.isoDate).getTime(),
            );
    });

    const filteredPastEvents = computed(() => {
        const term = search.value.trim().toLowerCase();
        if (!term) {
            return pastEvents.value;
        }

        return pastEvents.value.filter((event) => {
            return event.title.toLowerCase().includes(term);
        });
    });

    const visiblePastEvents = computed(() => {
        return filteredPastEvents.value.slice(0, visibleCount.value);
    });

    const hasMore = computed(() => {
        return visibleCount.value < filteredPastEvents.value.length;
    });

    watch(search, () => {
        visibleCount.value = pageSize;
    });

    const eventsByYear = computed(() => {
        const groups = new Map<string, ArchiveEvent[]>();

        visiblePastEvents.value.forEach((event) => {
            const year = new Intl.DateTimeFormat('fr-BE', {
                year: 'numeric',
            }).format(new Date(event.isoDate));
            const current = groups.get(year) ?? [];
            current.push(event);
            groups.set(year, current);
        });

        return Array.from(groups.entries()).map(([year, events]) => ({
            year,
            events,
        }));
    });

    function formatDate(isoDate: string) {
        return new Intl.DateTimeFormat('fr-BE', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        })
            .format(new Date(isoDate))
            .replace('.', '')
            .toUpperCase();
    }

    function loadMore() {
        visibleCount.value += pageSize;
    }
</script>

<template>

    <Head title="Archives" />

    <Header />

    <div class="relative z-10 min-h-[120vh] overflow-hidden rounded-b-[6rem] bg-black">
        <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-black via-black to-black"></div>
        <div
            class="pointer-events-none absolute -top-32 left-1/2 h-115 w-[130%] -translate-x-1/2 rounded-full bg-[#06402B]/18 blur-[150px]">
        </div>
        <div class="pointer-events-none absolute inset-0 bg-[url('/noise.png')] opacity-[0.04] mix-blend-soft-light">
        </div>

        <main class="relative z-10 mx-auto max-w-6xl px-6 pt-34 pb-24 md:px-10 lg:px-14">
            <section class="mb-8 space-y-3 text-center md:text-left">
                <p class="text-xs font-bold tracking-[0.35em] text-[#51A687] uppercase">
                    Archives
                </p>
                <h1 class="font-chillax text-5xl leading-[0.92] text-white md:text-7xl lg:text-8xl">
                    Les souvenirs de Symbiosa
                </h1>
                <p class="max-w-2xl font-synonym text-sm text-gray-300 md:text-base">
                    Retrouvez tout nos évènements passés.
                </p>

                <div class="max-w-xl pt-2">
                    <input v-model="search" type="text" placeholder="Rechercher un évènement."
                        class="w-full rounded-full border border-white/15 bg-white/5 px-5 py-3 text-sm text-white placeholder:text-gray-500 focus:border-[#06402B]/70 focus:ring-2 focus:ring-[#06402B]/30 focus:outline-none" />
                </div>
            </section>

            <section v-if="eventsByYear.length" class="space-y-7">
                <article v-for="group in eventsByYear" :key="group.year"
                    class="grid gap-3 md:grid-cols-[72px,1fr] md:gap-5">
                    <div class="md:pt-1.5">
                        <p class="font-chillax text-3xl leading-none text-white/90 md:text-4xl">
                            {{ group.year }}
                        </p>
                    </div>

                    <div class="space-y-3.5">
                        <Link v-for="event in group.events" :key="event.id" :href="event.recapLink"
                            class="group relative block rounded-lg border border-white/8 bg-white/2 px-3 py-4 transition-all duration-200 hover:border-white/18 hover:bg-white/5">
                            <div class="min-w-0 space-y-2.5">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    <span
                                        class="rounded-md border border-white/20 bg-black/40 px-1.5 py-0.5 text-[9px] font-bold tracking-widest text-white uppercase">
                                        {{ formatDate(event.isoDate) }}
                                    </span>
                                    <span
                                        class="rounded-full border border-[#51A687]/40 bg-[#51A687]/15 px-2 py-0.5 text-[9px] font-bold tracking-[0.12em] text-white uppercase">
                                        {{ event.type }}
                                    </span>
                                </div>

                                <h2 class="font-chillax text-2xl leading-none text-white md:text-[1.85rem]">
                                    {{ event.title }}
                                </h2>

                                <div
                                    class="flex flex-wrap items-center gap-x-2 gap-y-0.5 text-[11px] text-gray-300 md:text-xs">
                                    <p class="font-medium">
                                        {{ event.location }}
                                    </p>
                                    <span class="text-gray-500">-</span>
                                    <p class="font-medium">
                                        {{ event.photoCount }} photos
                                    </p>
                                    <p class="ml-auto text-[10px] font-bold tracking-[0.14em] text-[#51A687] uppercase">
                                        Voir recap
                                    </p>
                                </div>
                            </div>
                        </Link>
                    </div>
                </article>
            </section>

            <div v-if="hasMore" class="mt-10 flex justify-center">
                <button type="button" @click="loadMore"
                    class="cursor-pointer rounded-full border border-white/20 bg-white/5 px-5 py-2 text-xs font-bold tracking-[0.18em] text-white uppercase transition-colors hover:border-white/35">
                    Charger plus
                </button>
            </div>

            <section v-if="!eventsByYear.length" class="flex flex-col items-center justify-center py-20 text-center">
                <h2 class="font-chillax text-3xl text-white md:text-4xl">
                    {{ }}
                </h2>
                <p class="mt-4 max-w-sm text-gray-400">
                    Essaie avec un autre mot-cle pour retrouver une edition
                    passee.
                </p>
            </section>
        </main>
    </div>

    <Footer />
</template>
