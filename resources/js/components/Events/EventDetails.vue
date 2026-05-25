<script setup lang="ts">
    import { MapPin } from '@lucide/vue';
    import AppButton from '@/components/AppButton.vue';
    import { useEventDates } from '@/composables/useEventDates';
    import events from '@/routes/events';

    interface Props {
        event: Event;
        isEven: boolean;
    }

    const props = defineProps<Props>();
    const { startDay, startMonth } = useEventDates(() => props.event);
</script>

<template>
    <div :class="[isEven ? 'md:pl-12' : 'md:pr-12 md:text-right']" class="flex flex-col pb-4 md:pb-0">
        <div class="flex flex-row items-start gap-4 md:flex-col md:gap-6"
            :class="!isEven ? 'md:items-end' : 'md:items-start'">

            <!-- Date Badge -->
            <div class="shrink-0">
                <div
                    class="flex h-16 w-16 md:h-20 md:w-20 flex-col items-center justify-center rounded-2xl border border-[#51A687]/30 bg-[#51A687]/10 text-center backdrop-blur-md">
                    <span class="font-chillax text-2xl md:text-3xl leading-none text-white">
                        {{ startDay }}
                    </span>
                    <span class="text-[9px] md:text-[10px] font-bold tracking-[0.25em] text-[#51A687] uppercase">
                        {{ startMonth }}
                    </span>
                </div>
            </div>

            <div class="flex flex-col" :class="!isEven ? 'md:items-end' : 'md:items-start'">

                <!-- Title -->
                <h2 class="mb-3 font-chillax text-2xl leading-[1.1] text-white md:mb-4 md:text-4xl lg:text-5xl">
                    {{ event.title }}
                </h2>

                <!-- Location -->
                <div class="mb-4 flex items-center gap-2 text-gray-400 md:mb-6"
                    :class="!isEven ? 'md:flex-row-reverse' : ''">
                    <MapPin class="h-3.5 w-3.5 md:h-5 md:w-5 text-[#51A687]/80" aria-hidden="true" />
                    <span
                        class="text-[11px] md:text-lg font-bold md:font-medium tracking-[0.2em] md:tracking-wide uppercase">
                        {{ event.city }}, {{ event.country }}
                    </span>
                </div>

                <!-- Music Styles -->
                <div v-if="event.genres && event.genres.length > 0" class="mb-6 flex flex-wrap gap-2 md:mb-8"
                    :class="!isEven ? 'md:justify-end' : 'md:justify-start'">
                    <span v-for="(genre, genreIndex) in event.genres" :key="genre.id"
                        class="text-[10px] font-bold tracking-[0.3em] text-[#51A687] uppercase md:text-xs">
                        {{ genre.name }}
                        <span v-if="genreIndex < event.genres.length - 1" class="ml-1 text-gray-700">/</span>
                    </span>
                </div>

                <!-- Description Preview -->
                <div v-if="event.description"
                    class="mb-8 line-clamp-4 text-sm leading-relaxed text-gray-400 md:text-base"
                    :class="!isEven ? 'md:text-right' : 'md:text-left'" v-html="event.description">
                </div>
            </div>
        </div>

        <!-- CTAs -->
        <div class="flex flex-col gap-6 sm:flex-row" :class="!isEven
            ? 'md:flex-row-reverse'
            : 'md:flex-row'
            ">
            <AppButton :href="events.show(event.slug).url" variant="outline" size="lg" class="w-full md:w-auto">
                Découvrir l'expérience
            </AppButton>
        </div>
    </div>
</template>
