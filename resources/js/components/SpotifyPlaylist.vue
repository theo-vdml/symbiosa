<script setup lang="ts">
interface Props {
    playlistId?: string;
    title?: string;
    subtitle?: string;
}

withDefaults(defineProps<Props>(), {
    playlistId: '1t4HqapUmOPIamxdTYPBHC', // Default to a Techno/Electronic playlist if none provided
    title: 'Sound of Symbiosa',
    subtitle: 'Playlist',
});
</script>

<template>
    <section class="relative overflow-hidden bg-black px-12 py-24 lg:px-32">
        <!-- Horizontal Glow Orb spanning across the section (Permanent Red Light) -->
        <div
            class="pointer-events-none absolute top-1/2 left-1/2 h-[500px] w-[150%] -translate-x-1/2 -translate-y-1/2 rounded-[100%] bg-[#c80a45] opacity-25 blur-[160px]"
        ></div>

        <div class="relative z-10 mx-auto max-w-7xl">
            <!-- Header -->
            <div class="mb-16 flex flex-col items-center text-center">
                <div class="mb-4 flex items-center gap-4">
                    <div class="h-px w-8 bg-[#c80a45]"></div>
                    <span
                        class="text-sm font-bold tracking-[0.3em] text-[#c80a45] uppercase"
                    >
                        {{ subtitle }}
                    </span>
                    <div class="h-px w-8 bg-[#c80a45]"></div>
                </div>
                <h2
                    class="font-chillax text-5xl font-bold text-white uppercase italic"
                >
                    {{ title }}
                </h2>
            </div>

            <!-- Spotify Embed -->
            <div class="group relative">
                <div
                    class="relative overflow-hidden rounded-4xl border border-white/10 bg-[#121212] shadow-2xl"
                >
                    <iframe
                        style="border-radius: 12px"
                        :src="`https://open.spotify.com/embed/playlist/${playlistId}?utm_source=generator&theme=0`"
                        width="100%"
                        height="500"
                        allow="
                            autoplay;
                            clipboard-write;
                            encrypted-media;
                            fullscreen;
                            picture-in-picture;
                        "
                        loading="lazy"
                        class="w-full"
                    ></iframe>
                </div>
            </div>

            <!-- Call to action or footer text -->
            <div class="mt-12 text-center">
                <a
                    :href="`https://open.spotify.com/playlist/${playlistId}`"
                    target="_blank"
                    class="group inline-flex items-center gap-3 rounded-full border border-white/10 bg-white/5 px-8 py-4 text-white transition-all duration-300 hover:bg-white/10"
                >
                    <svg
                        class="h-6 w-6 text-[#1DB954]"
                        viewBox="0 0 24 24"
                        fill="currentColor"
                    >
                        <path
                            d="M12 0C5.373 0 0 5.373 0 12s5.373 12 12 12 12-5.373 12-12S18.627 0 12 0zm5.49 17.306c-.215.353-.674.464-1.026.249-2.858-1.746-6.456-2.14-10.693-1.17-.404.093-.81-.157-.903-.561-.093-.404.156-.81.561-.903 4.634-1.06 8.595-.61 11.808 1.352.352.215.463.674.249 1.026zm1.466-3.26c-.27.442-.846.582-1.288.312-3.272-2.01-8.258-2.592-12.126-1.417-.497.15-1.023-.13-1.173-.627-.152-.497.13-1.023.627-1.173 4.417-1.34 9.907-.695 13.65 1.603.442.27.583.846.312 1.288h-.002zm.127-3.398C15.197 8.245 8.79 8.032 5.06 9.165c-.628.19-1.293-.16-1.484-.789-.19-.628.16-1.293.789-1.484 4.286-1.3 11.36-1.056 16.038 1.72.565.335.753 1.067.42 1.632-.335.565-1.066.753-1.632.42z"
                        />
                    </svg>
                    <span class="text-sm font-bold tracking-wide uppercase"
                        >Suivre sur Spotify</span
                    >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="h-4 w-4 transition-transform group-hover:translate-x-1"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3"
                        />
                    </svg>
                </a>
            </div>
        </div>
    </section>
</template>

<style scoped>
.font-chillax {
    font-family: 'Chillax', sans-serif;
}
</style>

