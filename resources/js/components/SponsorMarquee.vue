<script setup lang="ts">
    import { ref, computed } from 'vue';
    import { useElementSize } from '@vueuse/core';

    const props = defineProps<{
        sponsors: Sponsor[];
    }>();

    const container = ref<HTMLElement | null>(null);
    const content = ref<HTMLElement | null>(null);

    const { width: containerWidth } = useElementSize(container);
    const { width: contentWidth } = useElementSize(content);

    const shouldScroll = computed(() => {
        if (!containerWidth.value || !contentWidth.value) return false;
        // We only scroll if the content (single set) is wider than the container
        return contentWidth.value > containerWidth.value;
    });
</script>

<template>
    <div ref="container" class="relative flex overflow-hidden group w-full py-4">
        <!-- Gradient Overlays - Fade in/out on the sides to avoid sharp cuts -->
        <div v-if="shouldScroll"
            class="pointer-events-none absolute inset-y-0 left-0 z-10 w-32 bg-linear-to-r from-black via-black/50 to-transparent transition-opacity duration-500">
        </div>
        <div v-if="shouldScroll"
            class="pointer-events-none absolute inset-y-0 right-0 z-10 w-32 bg-linear-to-l from-black via-black/50 to-transparent transition-opacity duration-500">
        </div>

        <div class="flex items-center flex-nowrap shrink-0" :class="{
            'animate-marquee group-hover:[animation-play-state:paused]': shouldScroll,
            'justify-center w-full': !shouldScroll
        }" :style="shouldScroll ? { willChange: 'transform' } : {}">
            <!-- First set of logos -->
            <div ref="content" class="flex items-center shrink-0 gap-20 flex-nowrap" :class="{ 'pr-20': shouldScroll }">
                <a v-for="(sponsor, index) in sponsors" :key="'s1-' + index" :href="sponsor.website" target="_blank"
                    class="shrink-0 transition-transform duration-300 hover:scale-110 p-2">
                    <img :src="sponsor.logo_url"
                        class="h-12 w-auto opacity-80 fill-white transition-all duration-300 hover:opacity-100"
                        alt="Sponsor Logo" />
                </a>
            </div>

            <!-- Second set for seamless loop, only if scrolling -->
            <div v-if="shouldScroll" class="flex items-center shrink-0 gap-20 flex-nowrap pr-20" aria-hidden="true">
                <a v-for="(sponsor, index) in sponsors" :key="'s2-' + index" :href="sponsor.website" target="_blank"
                    class="shrink-0 transition-transform duration-300 hover:scale-110 p-2">
                    <img :src="sponsor.logo_url"
                        class="h-12 w-auto opacity-80 fill-white transition-all duration-300 hover:opacity-100"
                        alt="Sponsor Logo" />
                </a>
            </div>
        </div>
    </div>
</template>

<style scoped>
.animate-marquee {
    animation: marquee 40s linear infinite;
}

@keyframes marquee {
    0% {
        transform: translateX(0);
    }

    100% {
        transform: translateX(-50%);
    }
}
</style>
