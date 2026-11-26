<script setup lang="ts">
import AppButton from '@/components/AppButton.vue';
import EventFaq from '@/components/EventFaq.vue';
import EventShowGallery from '@/components/Events/EventShowGallery.vue';
import EventShowHero from '@/components/Events/EventShowHero.vue';
import EventShowLineup from '@/components/Events/EventShowLineup.vue';
import EventShowSidebar from '@/components/Events/EventShowSidebar.vue';
import SponsorMarquee from '@/components/SponsorMarquee.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { Seo } from '@/types/seo';
import events from '@/routes/events';

const props = defineProps<{
    event: Event;
    seo: Seo;
}>();
</script>

<template>
    <MainLayout :seo="seo">
        <!-- Hero Banner Section -->
        <EventShowHero :event="event" />

        <!-- Main Content -->
        <div
            class="relative z-10 mx-auto max-w-7xl px-6 pb-24 md:px-10 lg:px-14"
        >
            <!-- Action Bar -->
            <div
                v-if="event.ticketing_status === 'open' && !event.is_archived"
                class="relative z-20 flex -translate-y-1/2 justify-center px-4"
            >
                <AppButton
                    :href="events.ticketing(event.slug).url"
                    variant="primary"
                    size="lg"
                    class="w-full border-[#51A687]/50 bg-[#51A687]/10 backdrop-blur-xl hover:bg-[#51A687]/20 sm:w-auto"
                >
                    Réserver mes places
                </AppButton>
            </div>

            <div
                v-else-if="event.is_archived"
                class="relative z-20 flex -translate-y-1/2 justify-center px-4"
            >
                <div
                    class="flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-8 py-3 backdrop-blur-xl"
                >
                    <p
                        class="text-xs font-bold tracking-[0.2em] text-white/60 uppercase"
                    >
                        Événement passé
                    </p>
                </div>
            </div>

            <section
                class="mt-12 grid grid-cols-1 gap-16 lg:grid-cols-12 lg:gap-24"
            >
                <!-- Description & Lineup -->
                <div
                    :class="[
                        event.is_archived ? 'lg:col-span-12' : 'lg:col-span-8',
                        'space-y-16',
                    ]"
                >
                    <div v-if="event.body" class="space-y-6">
                        <h2 class="font-chillax text-4xl text-white">
                            À propos
                        </h2>
                        <div
                            class="prose prose-lg max-w-none prose-invert prose-headings:font-chillax prose-headings:font-normal prose-p:text-gray-400 prose-strong:text-white prose-em:text-gray-200 prose-li:text-gray-400"
                            v-html="event.body"
                        ></div>
                    </div>

                    <EventShowLineup
                        v-if="event.artists"
                        :artists="event.artists"
                    />
                </div>

                <!-- Sidebar / Practical Info -->
                <EventShowSidebar v-if="!event.is_archived" :event="event" />
            </section>

            <!-- Gallery Section -->
            <EventShowGallery
                v-if="event.is_archived && event.gallery_urls?.length"
                :images="event.gallery_urls"
                :event-slug="event.slug"
            />

            <!-- Sponsors Section -->
            <section v-if="event.sponsors?.length" class="mt-32 space-y-10">
                <p
                    class="center text-[10px] font-bold tracking-[0.3em] text-white/40 uppercase"
                >
                    Cet événement ne serait pas possible sans nos sponsors
                </p>

                <SponsorMarquee :sponsors="event.sponsors" />
            </section>

            <!-- FAQ Section -->
            <EventFaq
                v-if="!event.is_archived && event.faq?.length"
                :faq="event.faq"
            />
        </div>
    </MainLayout>
</template>

<style scoped></style>
