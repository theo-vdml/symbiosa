<script setup lang="ts">
    import { onMounted, ref } from 'vue';
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
        heroPreheading: string;
        heroTitle: string;
        heroSubheading: string;
        heroVideoUrl: string | null;
        heroPosterUrl: string | null;
        spotifyPlaylistHeading: string | null;
        spotifyPlaylistId: string | null;
        showSpotifyPlaylist: boolean;
        spotifyPlaylistForceDark: boolean;
        seo: Seo;
    }>();

    const heroVideo = ref<HTMLVideoElement | null>(null);

    onMounted(() => {
        if (heroVideo.value && heroVideo.value.dataset.src) {
            heroVideo.value.src = heroVideo.value.dataset.src;
            heroVideo.value.load();
        }
    });
</script>

<template>
    <SeoMeta :seo="seo" />

    <Header />

    <div class="relative z-10 rounded-b-[3rem] lg:rounded-b-[6rem] overflow-hidden bg-black">
        <div class="relative h-screen w-full overflow-hidden">
            <!-- Background Video with subtle scale animation -->
            <div class="absolute inset-0 scale-105 animate-slow-zoom">
                <video ref="heroVideo" :data-src="props.heroVideoUrl || '/abstract.webm'" :poster="props.heroPosterUrl || undefined"
                    autoplay loop muted playsinline class="h-full w-full object-cover"></video>
            </div>

            <!-- Overlays -->
            <div class="absolute inset-0 z-0 bg-black/40"></div>
            <div class="absolute inset-0 z-0 bg-radial-vignette"></div>
            <div class="absolute inset-0 z-0 opacity-[0.03] pointer-events-none noise-overlay"></div>
            <div class="absolute inset-0 z-0 bg-linear-to-b from-black/20 via-transparent to-black"></div>

            <!-- Content -->
            <div class="relative z-10 flex h-full flex-col items-center justify-center px-4 text-center">
                <div class="overflow-hidden py-2">
                    <span
                        class="block font-synonym text-xs md:text-sm tracking-[0.5em] text-white/70 uppercase mb-4 animate-fade-in-up opacity-0">
                        {{ props.heroPreheading }}
                    </span>
                </div>

                <div class="overflow-hidden py-10 -my-10">
                    <h1
                        class="font-chillax text-[clamp(4rem,18vw,14rem)] leading-[0.85] text-white tracking-tighter animate-reveal-title opacity-0">
                        {{ props.heroTitle }}
                    </h1>
                </div>

                <div class="overflow-hidden py-2">
                    <span
                        class="block font-synonym text-xs md:text-sm tracking-[0.3em] text-white/50 uppercase mt-6 animate-fade-in-up [animation-delay:800ms] opacity-0">
                        {{ props.heroSubheading }}
                    </span>
                </div>
            </div>

            <!-- Scroll Indicator -->
            <div
                class="absolute bottom-12 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-4 animate-fade-in [animation-delay:1500ms] opacity-0">
                <span class="font-synonym text-[10px] tracking-[0.4em] text-white/30 uppercase rotate-0">Scroll</span>
                <div class="h-12 w-px bg-white/20 relative overflow-hidden">
                    <div class="absolute inset-0 bg-white/60 animate-scroll-line"></div>
                </div>
            </div>
        </div>

        <UpcomingEvent v-if="props.upcomingEvent" :event="props.upcomingEvent" />

        <NewsSection v-if="props.posts.length > 0" :posts="props.posts" />

        <MaximSection />

        <SpotifyPlaylist v-if="props.showSpotifyPlaylist && props.spotifyPlaylistId"
            :playlistId="props.spotifyPlaylistId" :heading="props.spotifyPlaylistHeading ?? undefined"
            :forceDark="props.spotifyPlaylistForceDark" />

        <BentoGallery />
    </div>

    <Footer />
</template>
