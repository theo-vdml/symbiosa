<script setup lang="ts">
    import AppButton from '@/components/AppButton.vue';
    import { ArrowRight } from '@lucide/vue';

    interface Props {
        playlistId: string;
        heading?: string;
        forceDark?: boolean;
    }

    const props = withDefaults(defineProps<Props>(), {
        playlistId: '1t4HqapUmOPIamxdTYPBHC',
        heading: 'Sound of Symbiosa',
        forceDark: false,
    });
</script>

<template>
    <section class="relative overflow-hidden bg-black px-12 py-24 lg:px-32">
        <!-- Horizontal Glow Orb spanning across the section (Permanent Red Light) -->
        <div
            class="pointer-events-none absolute top-1/2 left-1/2 h-125 w-[150%] -translate-x-1/2 -translate-y-1/2 rounded-[100%] bg-[#06402B] opacity-25 blur-[160px]">
        </div>

        <div class="relative z-10 mx-auto max-w-7xl">
            <!-- Header -->
            <div class="mb-16 flex flex-col items-center text-center">
                <div class="mb-4 flex items-center gap-4">
                    <div class="h-px w-8 bg-[#51A687]"></div>
                    <span class="text-sm font-bold tracking-[0.3em] text-[#51A687] uppercase">
                        Playlist
                    </span>
                    <div class="h-px w-8 bg-[#51A687]"></div>
                </div>
                <h2 class="font-chillax text-5xl text-white uppercase italic">
                    {{ heading }}
                </h2>
            </div>

            <!-- Spotify Embed -->
            <div class="group relative">
                <div class="relative overflow-hidden rounded-4xl border border-white/10 bg-[#121212] shadow-2xl">
                    <iframe title="Spotify Playlist Iframe" style="border-radius: 12px;"
                        :src="`https://open.spotify.com/embed/playlist/${playlistId}${props.forceDark ? '?theme=0' : ''}`"
                        width="100%" height="500" allow="
                            autoplay;
                            clipboard-write;
                            encrypted-media;
                            fullscreen;
                            picture-in-picture;
                        " loading="lazy" class="w-full"></iframe>
                </div>
            </div>

            <!-- Call to action or footer text -->
            <div class="mt-12 text-center">
                <AppButton :href="`https://open.spotify.com/playlist/${playlistId}`" variant="primary" external>
                    <template #left-icon>
                        <img src="/spotify-icon.svg" alt="Spotify Icon" class="w-6 h-6 mr-2" />
                    </template>
                    Suivre sur Spotify
                </AppButton>
            </div>
        </div>
    </section>
</template>

<style scoped>
.font-chillax {
    font-family: 'Chillax', sans-serif;
}
</style>
