<script setup lang="ts">
    import { Head } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import UpcomingEvent from '@/components/UpcomingEvent.vue';
    import NewsSection from '@/components/NewsSection.vue';
    import MaximSection from '@/components/MaximSection.vue';
    import SpotifyPlaylist from '@/components/SpotifyPlaylist.vue';
    import BentoGallery from '@/components/BentoGallery.vue';
    import Footer from '@/components/Footer.vue';

    const props = defineProps<{
        posts: any[];
        upcomingEvent: Event | null;
        spotifyPlaylistHeading: string | null;
        spotifyPlaylistId: string | null;
        showSpotifyPlaylist: boolean;
    }>();
</script>

<template>

    <Head>
        <title>{{ seo?.title ?? 'Home' }}</title>
        <meta v-if="seo?.description" name="description" :content="seo.description" />
        <meta v-if="seo?.keywords" name="keywords" :content="seo.keywords" />
        <meta v-if="seo?.robots" name="robots" :content="seo.robots" />
        <link v-if="seo?.canonical_url" rel="canonical" :href="seo.canonical_url" />

        <!-- Open Graph -->
        <meta property="og:title" :content="seo?.og_title ?? seo?.title ?? 'Home'" />
        <meta v-if="seo?.og_description ?? seo?.description" property="og:description" :content="seo?.og_description ?? seo?.description" />
        <meta v-if="seo?.og_image" property="og:image" :content="`/storage/${seo.og_image}`" />
        <meta property="og:type" :content="seo?.og_type ?? 'website'" />

        <!-- Twitter -->
        <meta name="twitter:card" :content="seo?.twitter_card ?? 'summary_large_image'" />
        <meta name="twitter:title" :content="seo?.twitter_title ?? seo?.title ?? 'Home'" />
        <meta v-if="seo?.twitter_description ?? seo?.description" name="twitter:description" :content="seo?.twitter_description ?? seo?.description" />
        <meta v-if="seo?.twitter_image" name="twitter:image" :content="`/storage/${seo.twitter_image}`" />
        <component :is="'script'" v-if="seo?.json_ld" type="application/ld+json" v-html="seo.json_ld" />
    </Head>

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
