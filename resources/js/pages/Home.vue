<script setup lang="ts">
    import { onMounted, ref } from 'vue';
    import MainLayout from '@/layouts/MainLayout.vue';
    import HeroHeader from '@/components/HeroHeader.vue';
    import UpcomingEvent from '@/components/UpcomingEvent.vue';
    import NewsSection from '@/components/NewsSection.vue';
    import MaximSection from '@/components/MaximSection.vue';
    import SpotifyPlaylist from '@/components/SpotifyPlaylist.vue';
    import BentoGallery from '@/components/BentoGallery.vue';
    import { Seo } from '@/types/seo';

    const props = defineProps<{
        posts: any[];
        upcomingEvent: Event | null;
        heroPreheading: string;
        heroTitle: string;
        heroSubheading: string;
        heroVideoUrl: string | null;
        heroPosterUrl: string | null;
        bentoGallery: any[];
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
    <MainLayout :seo="seo">
        <div class="relative h-screen w-full overflow-hidden">
            <!-- Background Video with subtle scale animation -->
            <div class="absolute inset-0 scale-105 animate-slow-zoom">
                <video ref="heroVideo" :data-src="props.heroVideoUrl || '/abstract.webm'"
                    :poster="props.heroPosterUrl || undefined" autoplay loop muted playsinline tabindex="-1"
                    aria-hidden="true" class="h-full w-full object-cover"></video>
            </div>

            <!-- Overlays -->
            <div class="absolute inset-0 z-0 bg-black/40"></div>
            <div class="absolute inset-0 z-0 bg-radial-vignette"></div>
            <div class="absolute inset-0 z-0 opacity-[0.03] pointer-events-none noise-overlay"></div>
            <div class="absolute inset-0 z-0 bg-linear-to-b from-black/20 via-transparent to-black"></div>

            <!-- Content -->
            <div class="relative z-10 flex h-full flex-col items-center justify-center">
                <HeroHeader size="xl">
                    <template #top>
                        <div class="overflow-hidden py-2">
                            <span
                                class="block font-synonym text-xs md:text-sm tracking-[0.5em] text-white/70 uppercase mb-4 animate-fade-in-up opacity-0">
                                {{ props.heroPreheading }}
                            </span>
                        </div>
                    </template>

                    <div class="overflow-hidden py-10 -my-10">
                        <span class="animate-reveal-title opacity-0 block">
                            {{ props.heroTitle }}
                        </span>
                    </div>

                    <template #bottom>
                        <div class="overflow-hidden py-2">
                            <span
                                class="block font-synonym text-xs md:text-sm tracking-[0.3em] text-white/50 uppercase mt-6 animate-fade-in-up [animation-delay:800ms] opacity-0">
                                {{ props.heroSubheading }}
                            </span>
                        </div>
                    </template>
                </HeroHeader>
            </div>

            <!-- Scroll Indicator -->
            <div
                class="absolute bottom-12 left-1/2 -translate-x-1/2 z-10 flex flex-col items-center gap-4 animate-fade-in [animation-delay:1500ms] opacity-0">
                <span class="font-synonym text-[10px] tracking-[0.4em] text-white/50 uppercase rotate-0">Scroll</span>
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

        <BentoGallery v-if="props.bentoGallery.length === 6" :images="props.bentoGallery" />
    </MainLayout>
</template>
