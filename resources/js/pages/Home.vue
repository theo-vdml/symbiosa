<script setup lang="ts">
import { onMounted, ref } from 'vue';
import BentoGallery from '@/components/BentoGallery.vue';
import HeroHeader from '@/components/HeroHeader.vue';
import MaximSection from '@/components/MaximSection.vue';
import NewsSection from '@/components/NewsSection.vue';
import SpotifyPlaylist from '@/components/SpotifyPlaylist.vue';
import UpcomingEvent from '@/components/UpcomingEvent.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { Seo } from '@/types/seo';

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
                <video
                    ref="heroVideo"
                    :data-src="props.heroVideoUrl || '/abstract.webm'"
                    :poster="props.heroPosterUrl || undefined"
                    autoplay
                    loop
                    muted
                    playsinline
                    tabindex="-1"
                    aria-hidden="true"
                    class="h-full w-full object-cover"
                ></video>
            </div>

            <!-- Overlays -->
            <div class="absolute inset-0 z-0 bg-black/40"></div>
            <div class="bg-radial-vignette absolute inset-0 z-0"></div>
            <div
                class="noise-overlay pointer-events-none absolute inset-0 z-0 opacity-[0.03]"
            ></div>
            <div
                class="absolute inset-0 z-0 bg-linear-to-b from-black/20 via-transparent to-black"
            ></div>

            <!-- Content -->
            <div
                class="relative z-10 flex h-full flex-col items-center justify-center"
            >
                <HeroHeader size="xl">
                    <template #top>
                        <div class="overflow-hidden py-2">
                            <span
                                class="mb-4 block animate-fade-in-up font-synonym text-xs tracking-[0.5em] text-white/70 uppercase opacity-0 md:text-sm"
                            >
                                {{ props.heroPreheading }}
                            </span>
                        </div>
                    </template>

                    <div class="-my-10 overflow-hidden py-10">
                        <span class="block animate-reveal-title opacity-0">
                            {{ props.heroTitle }}
                        </span>
                    </div>

                    <template #bottom>
                        <div class="overflow-hidden py-2">
                            <span
                                class="mt-6 block animate-fade-in-up font-synonym text-xs tracking-[0.3em] text-white/50 uppercase opacity-0 [animation-delay:800ms] md:text-sm"
                            >
                                {{ props.heroSubheading }}
                            </span>
                        </div>
                    </template>
                </HeroHeader>
            </div>

            <!-- Scroll Indicator -->
            <div
                class="absolute bottom-12 left-1/2 z-10 flex -translate-x-1/2 animate-fade-in flex-col items-center gap-4 opacity-0 [animation-delay:1500ms]"
            >
                <span
                    class="rotate-0 font-synonym text-[10px] tracking-[0.4em] text-white/50 uppercase"
                    >Scroll</span
                >
                <div class="relative h-12 w-px overflow-hidden bg-white/20">
                    <div
                        class="absolute inset-0 animate-scroll-line bg-white/60"
                    ></div>
                </div>
            </div>
        </div>

        <UpcomingEvent
            v-if="props.upcomingEvent"
            :event="props.upcomingEvent"
        />

        <NewsSection v-if="props.posts.length > 0" :posts="props.posts" />

        <MaximSection />

        <SpotifyPlaylist
            v-if="props.showSpotifyPlaylist && props.spotifyPlaylistId"
            :playlistId="props.spotifyPlaylistId"
            :heading="props.spotifyPlaylistHeading ?? undefined"
            :forceDark="props.spotifyPlaylistForceDark"
        />

        <BentoGallery
            v-if="props.bentoGallery.length === 6"
            :images="props.bentoGallery"
        />
    </MainLayout>
</template>
