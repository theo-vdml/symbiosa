<script setup lang="ts">
    interface Props {
        title: string;
        posterUrl?: string | null;
        backgroundUrl?: string | null;
        backgroundResponsive?: { srcset: string } | null;
    }

    defineProps<Props>();
</script>

<template>
    <div
        class="relative aspect-3/4 w-full overflow-hidden rounded-2xl border border-white/10 shadow-2xl md:max-w-md md:rounded-3xl">
        <img v-if="posterUrl" :src="posterUrl" :alt="title" class="absolute inset-0 h-full w-full object-cover" />

        <!-- Fallback 1: Background Image (Atmospheric but clear) -->
        <template v-else-if="backgroundUrl">
            <img :src="backgroundUrl" :srcset="backgroundResponsive?.srcset" sizes="(max-width: 768px) 150vw, 1080px"
                :alt="title"
                class="absolute inset-0 h-full w-full object-cover transition-transform duration-700 hover:scale-105" />
            <div class="absolute inset-0 bg-linear-to-t from-black/60 via-black/10 to-transparent">
            </div>
        </template>

        <!-- Fallback 2: Abstract Branded Glow (Final fallback) -->
        <div v-else
            class="absolute inset-0 flex flex-col items-center justify-center bg-[#010806] text-center overflow-hidden">

            <!-- Animated Corner-to-Corner Light Sources -->
            <div class="absolute inset-0">
                <!-- Light Source 1 -->
                <div class="absolute h-0 w-0 animate-corner-path-1">
                    <div class="absolute -translate-x-1/2 -translate-y-1/2">
                        <div class="h-96 w-96 rounded-full bg-[#51A687]/40 blur-[100px]">
                        </div>
                        <div
                            class="absolute top-1/2 left-1/2 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#51A687]/60 blur-[60px]">
                        </div>
                        <div
                            class="absolute top-1/2 left-1/2 h-32 w-32 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/30 blur-2xl">
                        </div>
                    </div>
                </div>

                <!-- Light Source 2 -->
                <div class="absolute h-0 w-0 animate-corner-path-2">
                    <div class="absolute -translate-x-1/2 -translate-y-1/2">
                        <div class="h-96 w-96 rounded-full bg-[#51A687]/40 blur-[100px]">
                        </div>
                        <div
                            class="absolute top-1/2 left-1/2 h-64 w-64 -translate-x-1/2 -translate-y-1/2 rounded-full bg-[#51A687]/60 blur-[60px]">
                        </div>
                        <div
                            class="absolute top-1/2 left-1/2 h-32 w-32 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/30 blur-2xl">
                        </div>
                    </div>
                </div>
            </div>

            <div class="absolute inset-0 bg-[url('/noise.png')] opacity-40 mix-blend-soft-light">
            </div>

            <!-- Centered Title Overlay -->
            <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-8">
                <span class="font-chillax text-3xl md:text-5xl text-white uppercase tracking-tighter leading-[0.9]">
                    {{ title }}
                </span>
            </div>
        </div>
    </div>
</template>

<style scoped>
@keyframes corner-path {

    0%,
    100% {
        top: 0%;
        left: 100%;
    }

    25% {
        top: 100%;
        left: 100%;
    }

    50% {
        top: 100%;
        left: 0%;
    }

    75% {
        top: 0%;
        left: 0%;
    }
}

.animate-corner-path-1 {
    animation: corner-path 30s linear infinite;
}

.animate-corner-path-2 {
    animation: corner-path 30s linear infinite;
    animation-delay: -15s;
}
</style>
