<script setup lang="ts">
    import { computed, ref, watch } from 'vue';
    import MainLayout from '@/layouts/MainLayout.vue';
    import routes from '@/routes/events';
    import { Link } from '@inertiajs/vue3';

    const props = defineProps<{
        events: Event[];
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
    <MainLayout title="Archives" has-background>
        <div class="relative z-10 mx-auto max-w-4xl px-6 pt-34 pb-24 md:px-10">
            <section class="mb-12 space-y-3 text-center md:text-left">
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

            <section v-if="visiblePastEvents.length" class="space-y-5">
                <Link v-for="event in visiblePastEvents" :key="event.id" :href="routes.show(event.slug).url"
                    class="group relative flex items-center gap-6 overflow-hidden rounded-2xl border border-white/8 bg-white/3 p-2.5 pr-8 transition-all duration-300 hover:border-white/15 hover:bg-white/6">
                    <div class="relative aspect-video w-36 shrink-0 overflow-hidden rounded-xl bg-white/5 md:w-64">
                        <img v-if="event.background_url || event.poster_url"
                            :src="event.background_url || event.poster_url" :alt="event.title"
                            class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-105" />
                        <div class="absolute inset-0 bg-black/10 transition-opacity duration-300 group-hover:opacity-0">
                        </div>
                    </div>

                    <div class="min-w-0 flex-1 py-2">
                        <div class="flex items-center gap-2">
                            <span
                                class="text-[10px] font-bold tracking-[0.15em] text-[#51A687] uppercase opacity-80 group-hover:opacity-100 md:text-xs">
                                {{ formatDate(event.start_at) }}
                            </span>
                        </div>

                        <h2 class="truncate font-chillax text-2xl leading-tight text-white md:text-4xl">
                            {{ event.title }}
                        </h2>

                        <div class="flex items-center gap-x-3 text-[11px] text-gray-400 md:text-sm">
                            <p class="font-medium">{{ event.city }}, {{ event.country }}</p>
                            <span class="h-1 w-1 rounded-full bg-white/20"></span>
                            <p class="font-medium">{{ event.photo_count }} photos</p>
                        </div>
                    </div>

                    <div class="shrink-0">
                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-full border border-white/10 bg-white/5 transition-all duration-300 group-hover:border-[#51A687]/40 group-hover:bg-[#51A687]/10">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round"
                                class="text-white/30 transition-all duration-300 group-hover:translate-x-0.5 group-hover:text-white">
                                <path d="M5 12h14m-7-7 7 7-7 7" />
                            </svg>
                        </div>
                    </div>
                </Link>
            </section>

            <div v-if="hasMore" class="mt-12 flex justify-center">
                <button type="button" @click="loadMore"
                    class="cursor-pointer rounded-full border border-white/20 bg-white/5 px-6 py-2.5 text-xs font-bold tracking-[0.18em] text-white uppercase transition-colors hover:border-white/35">
                    Charger plus
                </button>
            </div>

            <section v-if="!visiblePastEvents.length"
                class="flex flex-col items-center justify-center py-20 text-center">
                <h2 class="font-chillax text-3xl text-white md:text-4xl">
                    Aucun résultat
                </h2>
                <p class="mt-4 max-w-sm text-gray-400">
                    Essaie avec un autre mot-cle pour retrouver une edition
                    passee.
                </p>
            </section>
        </div>
    </MainLayout>
</template>
