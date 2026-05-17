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

    const props = defineProps<{
        events: ArchiveEvent[];
    }>();

    const search = ref('');
    const pageSize = 12;
    const visibleCount = ref(pageSize);

    const filteredPastEvents = computed(() => {
        const term = search.value.trim().toLowerCase();
        if (!term) {
            return props.events;
        }

        return props.events.filter((event) => {
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
            const date = new Date(event.isoDate);
            if (isNaN(date.getTime())) return;

            const year = new Intl.DateTimeFormat('fr-BE', {
                year: 'numeric',
            }).format(date);
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
        const date = new Date(isoDate);
        if (isNaN(date.getTime())) return 'DATE INCONNUE';

        return new Intl.DateTimeFormat('fr-BE', {
            day: '2-digit',
            month: 'short',
            year: 'numeric',
        })
            .format(date)
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
