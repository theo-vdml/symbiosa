<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import Header from '@/components/Header.vue';
import UpcomingEvent from '@/components/UpcomingEvent.vue';
import NewsSection from '@/components/NewsSection.vue';
import SpotifyPlaylist from '@/components/SpotifyPlaylist.vue';
import BentoGallery from '@/components/BentoGallery.vue';
import Footer from '@/components/Footer.vue';
</script>

<template>
    <Head title="Home" />

    <Header />

    <div class="relative z-10 rounded-b-[6rem] overflow-hidden bg-black">
        <div class="relative h-screen w-full overflow-hidden">
            <video
                src="/aftermovie.mp4"
                autoplay
                loop
                muted
                playsinline
                class="absolute top-1/2 left-1/2 min-h-full min-w-full -translate-x-1/2 -translate-y-1/2 object-cover"
            ></video>

            <div
                class="absolute inset-0 z-0 bg-black/40 backdrop-blur-xs"
            ></div>

            <div class="relative z-10 flex h-full items-center justify-center">
                <h1 class="font-chillax text-[14rem] text-white">Symbiosa</h1>
            </div>
        </div>

        <UpcomingEvent
            eventTitle="EDEN"
            eventDate="Samedi 28 Octobre 2026"
            eventLocation="Gembloux, Belgique"
            eventImage="/eden_poster.png"
            ticketLink="/tickets/eden"
            moreInfoLink="/events/eden"
            calendarLink="/events"
        />

        <NewsSection />

        <SpotifyPlaylist />

        <BentoGallery />
    </div>

    <Footer />
</template>
