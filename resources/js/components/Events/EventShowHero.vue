<script setup lang="ts">
import { Calendar, MapPin } from '@lucide/vue';
import HeroHeader from '@/components/HeroHeader.vue';
import { useEventDates } from '@/composables/useEventDates';

interface Props {
    event: Event;
}

const props = defineProps<Props>();
const eventDates = useEventDates(() => props.event);
</script>

<template>
    <section class="relative h-[85vh] w-full overflow-hidden">
        <template v-if="event.background_url">
            <img
                :src="event.background_url"
                :srcset="event.background_responsive?.srcset"
                sizes="(max-width: 768px) 200vw, 100vw"
                class="absolute inset-0 h-full w-full object-cover"
                :alt="'Image de couverture de l\'événement ' + event.title"
            />
            <div
                class="absolute inset-0 bg-linear-to-t from-black via-black/40 to-black/20"
            ></div>
            <div
                class="absolute inset-0 bg-[url('/noise.png')] opacity-[0.05] mix-blend-soft-light"
            ></div>
        </template>

        <template v-else>
            <div
                class="absolute inset-0 bg-linear-to-b from-[#51A687]/30 to-transparent"
            ></div>
        </template>

        <div
            class="relative z-10 flex h-full flex-col items-center justify-end pb-32"
        >
            <HeroHeader :heading="event.title">
                <template #top>
                    <div class="flex flex-wrap justify-center gap-3">
                        <span
                            v-for="genre in event.genres"
                            :key="genre.id"
                            class="rounded-full border border-white/20 bg-white/5 px-4 py-1.5 text-[10px] font-bold tracking-[0.25em] text-gray-200 uppercase backdrop-blur-md"
                        >
                            {{ genre.name }}
                        </span>
                    </div>
                </template>

                <template #bottom>
                    <div
                        class="mx-auto grid w-full max-w-2xl grid-cols-2 gap-16 pt-16"
                    >
                        <!-- Date Column -->
                        <div
                            class="group flex flex-col items-center gap-4 text-center"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full border border-white/10 bg-white/5 text-[#51A687]"
                            >
                                <Calendar class="h-6 w-6" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <span
                                    class="text-xs font-medium tracking-[0.2em] text-white/50 uppercase"
                                >
                                    {{ eventDates.startWeekday }}
                                </span>
                                <span
                                    class="font-chillax text-2xl text-white uppercase"
                                >
                                    {{ eventDates.startLong }}
                                </span>
                            </div>
                        </div>

                        <!-- Location Column -->
                        <div
                            class="group flex flex-col items-center gap-4 text-center"
                        >
                            <div
                                class="flex h-12 w-12 items-center justify-center rounded-full border border-white/10 bg-white/5 text-[#51A687]"
                            >
                                <MapPin class="h-6 w-6" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <span
                                    class="font-chillax text-2xl text-white uppercase"
                                    >{{ event.city }}</span
                                >
                                <span
                                    class="text-xs font-medium tracking-[0.2em] text-white/50 uppercase"
                                >
                                    {{ event.country }}
                                </span>
                            </div>
                        </div>
                    </div>
                </template>
            </HeroHeader>
        </div>
    </section>
</template>
