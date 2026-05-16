<script setup lang="ts">
    import Header from '@/components/Header.vue';
    import UpcomingEvent from '@/components/UpcomingEvent.vue';
    import NewsSection from '@/components/NewsSection.vue';
    import MaximSection from '@/components/MaximSection.vue';
    import SpotifyPlaylist from '@/components/SpotifyPlaylist.vue';
    import BentoGallery from '@/components/BentoGallery.vue';
    import Footer from '@/components/Footer.vue';
    import SeoMeta from '@/components/SeoMeta.vue';
    import { Seo } from '@/types/seo';

    const props = defineProps<{
        posts: any[];
        upcomingEvent: Event | null;
        spotifyPlaylistHeading: string | null;
        spotifyPlaylistId: string | null;
        showSpotifyPlaylist: boolean;
        seo: Seo;
    }>();
</script>

<template>
    <SeoMeta :seo="seo" />

    <Header />

    <div class="relative z-10 rounded-b-[6rem] overflow-hidden bg-black">
        <div class="relative h-screen w-full overflow-hidden">
            <video src="/abstract.mp4" autoplay loop muted playsinline
                class="absolute top-1/2 left-1/2 min-h-full min-w-full -translate-x-1/2 -translate-y-1/2 object-cover"></video>
            <div class="absolute inset-0 z-0 bg-black/60 backdrop-blur-sm"></div>
            <div class="absolute inset-0 z-0 bg-linear-to-b from-transparent via-transparent to-black"></div>

            <div class="relative z-10 flex h-full items-center justify-center">
                <h1 class="font-chillax text-[14rem] text-white">Symbiosa</h1>
            </div>
        </div>

        <UpcomingEvent v-if="props.upcomingEvent" :event="props.upcomingEvent" />

        <NewsSection v-if="props.posts.length > 0" :posts="props.posts" />

        <MaximSection />

        <SpotifyPlaylist v-if="props.showSpotifyPlaylist && props.spotifyPlaylistId"
            :playlistId="props.spotifyPlaylistId" :heading="props.spotifyPlaylistHeading ?? undefined" />

        <BentoGallery />
    </div>

    <Footer />
</template>
