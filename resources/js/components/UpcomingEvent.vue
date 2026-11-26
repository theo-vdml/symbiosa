<script setup lang="ts">
import AppButton from '@/components/AppButton.vue';
import { useEventDates } from '@/composables/useEventDates';
import events from '@/routes/events';

interface Props {
    event: Event;
}

const props = defineProps<Props>();

const eventDates = useEventDates(() => props.event);
</script>

<template>
    <section
        class="relative flex min-h-screen w-full items-center overflow-hidden bg-black py-24 lg:py-32"
    >
        <!-- --- LAYER 0: SMART BACKGROUND --- -->
        <div class="absolute inset-0 z-0">
            <!-- Priority 1: Event Background -->
            <img
                v-if="event.background_url"
                :src="event.background_url"
                :srcset="event.background_responsive?.srcset"
                sizes="(max-width: 768px) 200vw, 100vw"
                class="h-full w-full object-cover"
                :alt="`Visuel de l'événement ${event.title}`"
            />

            <!-- Priority 2: Branded Deep Gradient -->
            <template v-else>
                <div
                    class="h-full w-full bg-linear-to-b from-transparent via-[#51A687]/70 to-transparent"
                ></div>
                <div
                    class="absolute top-1/2 left-1/2 aspect-square w-1/2 -translate-1/2 bg-[#51A687]/70 blur-3xl"
                ></div>
            </template>

            <!-- Overlays for Readability -->
            <div class="absolute inset-0 bg-black/60"></div>
            <div
                class="absolute inset-0 bg-linear-to-b from-black via-transparent to-black"
            ></div>
            <div
                class="bg-radial-gradient absolute inset-0 from-transparent via-black/40 to-black/80"
            ></div>
            <div
                class="absolute inset-0 bg-[url('/noise.png')] opacity-[0.05] mix-blend-soft-light"
            ></div>
        </div>

        <div class="relative z-10 mx-auto w-full max-w-7xl px-6 lg:px-12">
            <div
                :class="[
                    'flex flex-col items-center gap-10 text-center md:gap-16 lg:gap-32',
                    event.poster_url ? 'lg:flex-row lg:text-left' : '',
                ]"
            >
                <!-- Left: The Poster (Optional) -->
                <div
                    v-if="event.poster_url"
                    class="hidden w-full shrink-0 justify-center md:flex md:w-8/12 lg:w-4/12 xl:w-5/12"
                >
                    <div
                        class="group relative w-full max-w-70 md:max-w-sm lg:max-w-none"
                    >
                        <div
                            class="relative z-10 aspect-3/4 w-full overflow-hidden rounded-2xl border border-white/10 shadow-2xl"
                        >
                            <img
                                :src="event.poster_url"
                                :alt="`Affiche de l'événement ${event.title}`"
                                class="h-full w-full object-cover"
                            />
                        </div>
                    </div>
                </div>

                <!-- Right: The Content -->
                <div
                    :class="[
                        'flex w-full flex-col items-center space-y-8 md:space-y-10',
                        event.poster_url
                            ? 'lg:w-7/12 lg:items-start'
                            : 'mx-auto max-w-4xl',
                    ]"
                >
                    <div
                        :class="[
                            'flex w-full flex-col items-center space-y-4 md:space-y-6',
                            event.poster_url ? 'lg:items-start' : '',
                        ]"
                    >
                        <!-- Header: City & Date -->
                        <div
                            :class="[
                                'flex flex-col items-center gap-2',
                                event.poster_url ? 'lg:items-start' : '',
                            ]"
                        >
                            <span
                                class="text-xs font-bold tracking-[0.3em] text-white uppercase md:text-sm md:tracking-[0.4em]"
                            >
                                {{ event.city }}
                            </span>
                            <span
                                class="text-xs font-bold tracking-[0.3em] text-[#51A687] uppercase md:text-sm md:tracking-[0.4em]"
                            >
                                {{ eventDates.startWeekday }}
                                {{ eventDates.startLong }}
                            </span>
                        </div>

                        <h2
                            :class="[
                                'font-chillax leading-[0.9] tracking-tight text-white uppercase md:leading-[0.85]',
                                event.poster_url
                                    ? 'text-4xl md:text-7xl lg:text-8xl'
                                    : 'text-5xl md:text-8xl lg:text-9xl',
                            ]"
                        >
                            {{ event.title }}
                        </h2>

                        <!-- Event Description -->
                        <div
                            v-if="event.description"
                            :class="[
                                'max-w-2xl text-center text-base leading-relaxed font-light text-white md:text-xl',
                                event.poster_url ? 'lg:text-left' : '',
                            ]"
                            v-html="event.description"
                        ></div>
                    </div>

                    <!-- CTAs -->
                    <div
                        :class="[
                            'flex w-full flex-col justify-center gap-4 pt-4 sm:flex-row md:gap-6',
                            event.poster_url ? 'lg:justify-start' : '',
                        ]"
                    >
                        <AppButton
                            v-if="event.ticketing_status === 'open'"
                            :href="events.ticketing(event.slug).url"
                            variant="primary"
                            size="lg"
                            class="w-full border-[#51A687]/50 bg-[#51A687]/10 backdrop-blur-xl hover:bg-[#51A687]/20 sm:w-auto"
                        >
                            Réserver mes places
                        </AppButton>
                        <AppButton
                            :href="events.show(event.slug).url"
                            variant="primary"
                            size="lg"
                            class="w-full border-[#51A687]/50 bg-[#51A687]/10 backdrop-blur-xl hover:bg-[#51A687]/20 sm:w-auto"
                        >
                            Découvrir l'expérience
                        </AppButton>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
