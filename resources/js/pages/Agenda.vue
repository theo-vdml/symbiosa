<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';

interface AgendaEvent {
    id: number;
    title: string;
    type: string;
    genres: string[];
    isoDate: string;
    location: string;
    image: string;
    infoLink: string;
}

const typeOptions = ['DJ Set', 'Festival', 'Open Air'];
const genreOptions = ['House', 'Techno', 'Hardstyle'];

const events: AgendaEvent[] = [
    {
        id: 1,
        title: 'EDEN Opening',
        type: 'Festival',
        genres: ['House'],
        isoDate: '2026-10-28T20:00:00+02:00',
        location: 'Gembloux, Belgique',
        image: '/eden_poster_light.png',
        infoLink: '/events/eden-opening',
    },
    {
        id: 2,
        title: 'EDEN Night 01',
        type: 'DJ Set',
        genres: ['House', 'Techno'],
        isoDate: '2026-11-18T20:00:00+01:00',
        location: 'Gembloux, Belgique',
        image: '/eden_poster_light.png',
        infoLink: '/events/eden-night-01',
    },
    {
        id: 3,
        title: 'EDEN Night 02',
        type: 'DJ Set',
        genres: ['Techno', 'Hardstyle'],
        isoDate: '2026-12-16T20:00:00+01:00',
        location: 'Gembloux, Belgique',
        image: '/eden_poster_light.png',
        infoLink: '/events/eden-night-02',
    },
    {
        id: 4,
        title: 'EDEN Winter Session',
        type: 'Festival',
        genres: ['Techno'],
        isoDate: '2027-01-20T20:00:00+01:00',
        location: 'Gembloux, Belgique',
        image: '/eden_poster_light.png',
        infoLink: '/events/eden-winter-session',
    },
    {
        id: 5,
        title: 'EDEN Spring Session',
        type: 'Open Air',
        genres: ['House'],
        isoDate: '2027-03-17T20:00:00+01:00',
        location: 'Gembloux, Belgique',
        image: '/eden_poster_light.png',
        infoLink: '/events/eden-spring-session',
    },
];

const futureEvents = computed(() => {
    const now = new Date();
    return [...events].filter((event) => new Date(event.isoDate) >= now);
});

const selectedTypes = ref<string[]>([]);
const selectedGenres = ref<string[]>([]);

const isGenreEnabled = computed(() => selectedTypes.value.length <= 1);

const filteredFutureEvents = computed(() => {
    return futureEvents.value.filter((event) => {
        // No type selected: genres drive the filter.
        if (selectedTypes.value.length === 0) {
            if (selectedGenres.value.length === 0) {
                return true;
            }
            return event.genres.some((genre) => selectedGenres.value.includes(genre));
        }

        // One type selected: enforce type + optional genre constraint.
        if (selectedTypes.value.length === 1) {
            const isTypeMatch = event.type === selectedTypes.value[0];
            if (!isTypeMatch) {
                return false;
            }

            if (selectedGenres.value.length === 0) {
                return true;
            }

            return event.genres.some((genre) => selectedGenres.value.includes(genre));
        }

        // Multiple types selected: show union of selected types (genres are ignored).
        return selectedTypes.value.includes(event.type);
    });
});

function toggleFilter(group: 'type' | 'genre', value: string) {
    if (group === 'genre' && !isGenreEnabled.value) {
        return;
    }

    const target = group === 'type' ? selectedTypes : selectedGenres;
    if (target.value.includes(value)) {
        target.value = target.value.filter((item) => item !== value);
    } else {
        target.value = [...target.value, value];
    }

    // If user selects all options of a group, it means "no filter" on this group.
    if (selectedTypes.value.length === typeOptions.length) {
        selectedTypes.value = [];
    }

    if (selectedGenres.value.length === genreOptions.length) {
        selectedGenres.value = [];
    }

    if (selectedTypes.value.length > 1 && selectedGenres.value.length > 0) {
        selectedGenres.value = [];
    }
}

function getDateParts(isoDate: string) {
    const date = new Date(isoDate);
    const day = new Intl.DateTimeFormat('fr-BE', { day: '2-digit' }).format(date);
    const month = new Intl.DateTimeFormat('fr-BE', { month: 'short' })
        .format(date)
        .replace('.', '')
        .toUpperCase();
    return { day, month };
}
</script>

<template>
    <Head title="Calendrier" />

    <Header />

    <div class="relative z-10 overflow-hidden rounded-b-[6rem] bg-black">
        <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-black via-black to-black"></div>
        <div class="pointer-events-none absolute -top-32 left-1/2 h-115 w-[130%] -translate-x-1/2 rounded-full bg-[#c80a45]/18 blur-[150px]"></div>
        <div class="pointer-events-none absolute inset-0 bg-[url('/noise.png')] opacity-[0.04] mix-blend-soft-light"></div>

        <main class="relative z-10 mx-auto max-w-7xl px-6 pb-24 pt-40 md:px-12 lg:px-24">
            <section class="mb-14 space-y-4 text-center md:text-left">
                <p class="text-xs font-bold tracking-[0.35em] text-[#c80a45] uppercase">Calendrier</p>
                <h1 class="font-chillax text-5xl leading-[0.92] text-white md:text-7xl lg:text-8xl">
                    Tous les événements à venir
                </h1>
                <p class="max-w-2xl text-sm text-gray-300 md:text-base">
                    Explore les prochaines dates Symbiosa et filtre rapidement selon l'ambiance musicale que tu recherches.
                </p>

                <div class="space-y-2.5 pt-1">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="mr-1 text-[10px] font-bold tracking-[0.2em] text-gray-500 uppercase">Type</span>
                        <button
                            v-for="type in typeOptions"
                            :key="type"
                            type="button"
                            @click="toggleFilter('type', type)"
                            :class="[
                                'rounded-full border px-2.5 py-1 text-[10px] font-bold tracking-[0.18em] uppercase transition-colors',
                                selectedTypes.includes(type)
                                    ? 'border-[#c80a45] bg-[#c80a45]/20 text-white'
                                    : 'border-white/15 bg-white/5 text-gray-300 hover:border-white/30',
                            ]"
                        >
                            {{ type }}
                        </button>
                    </div>

                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="mr-1 text-[10px] font-bold tracking-[0.2em] text-gray-500 uppercase">Genre</span>
                        <button
                            v-for="genre in genreOptions"
                            :key="genre"
                            type="button"
                            @click="toggleFilter('genre', genre)"
                            :disabled="!isGenreEnabled"
                            :class="[
                                'rounded-full border px-2.5 py-1 text-[10px] font-bold tracking-[0.18em] uppercase transition-colors',
                                selectedGenres.includes(genre)
                                    ? 'border-[#c80a45] bg-[#c80a45]/20 text-white'
                                    : 'border-white/15 bg-white/5 text-gray-300 hover:border-white/30',
                                !isGenreEnabled && 'cursor-not-allowed opacity-35 hover:border-white/15',
                            ]"
                        >
                            {{ genre }}
                        </button>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                <Link
                    v-for="event in filteredFutureEvents"
                    :key="event.id"
                    :href="event.infoLink"
                    class="group overflow-hidden rounded-2xl border border-white/10 bg-white/3 transition-all duration-300 hover:-translate-y-0.5 hover:border-white/20 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#c80a45]"
                >
                    <div class="relative aspect-3/4 overflow-hidden">
                        <img
                            :src="event.image"
                            :alt="event.title"
                            class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.03]"
                        />
                        <div class="pointer-events-none absolute inset-0 bg-linear-to-t from-black/70 via-black/10 to-transparent"></div>

                        <div class="absolute top-4 left-4 flex h-20 w-20 flex-col items-center justify-center rounded-md border border-white/25 bg-black/70 text-center backdrop-blur-sm">
                            <p class="font-chillax text-4xl leading-none text-white">{{ getDateParts(event.isoDate).day }}</p>
                            <p class="text-[10px] font-bold tracking-[0.2em] text-gray-200 uppercase">{{ getDateParts(event.isoDate).month }}</p>
                        </div>
                    </div>

                    <div class="space-y-3.5 p-5">
                        <div class="flex flex-wrap gap-2">
                            <span class="rounded-full border border-[#c80a45]/40 bg-[#c80a45]/15 px-3 py-1 text-[10px] font-bold tracking-[0.18em] text-white uppercase">
                                {{ event.type }}
                            </span>
                            <span
                                v-for="genre in event.genres"
                                :key="`${event.id}-${genre}`"
                                class="rounded-full border border-white/20 bg-white/5 px-3 py-1 text-[10px] font-bold tracking-[0.18em] text-gray-200 uppercase"
                            >
                                {{ genre }}
                            </span>
                        </div>

                        <h2 class="font-chillax text-[1.9rem] leading-none text-white md:text-[2.1rem]">{{ event.title }}</h2>

                        <div class="pt-2">
                            <p class="inline-flex max-w-full items-center gap-2 rounded-full border border-white/15 bg-white/5 px-3 py-1.5 text-xs font-medium tracking-wide text-gray-200">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="2"
                                    stroke="currentColor"
                                    class="h-3.5 w-3.5 text-[#c80a45]"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                                <span class="truncate">{{ event.location }}</span>
                            </p>
                        </div>
                    </div>
                </Link>
            </section>
        </main>
    </div>

    <Footer />

</template>

