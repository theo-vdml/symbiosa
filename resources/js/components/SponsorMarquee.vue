<script setup lang="ts">
import { useElementSize } from '@vueuse/core';
import { ref, computed } from 'vue';

const props = defineProps<{
    sponsors: Sponsor[];
}>();

const container = ref<HTMLElement | null>(null);
const content = ref<HTMLElement | null>(null);

const { width: containerWidth } = useElementSize(container);
const { width: contentWidth } = useElementSize(content);

const shouldScroll = computed(() => {
    if (!containerWidth.value || !contentWidth.value) {
        return false;
    }

    // We only scroll if the content (single set) is wider than the container
    return contentWidth.value > containerWidth.value;
});
</script>

<template>
    <div
        ref="container"
        class="group relative flex w-full overflow-hidden py-4"
    >
        <!-- Gradient Overlays - Fade in/out on the sides to avoid sharp cuts -->
        <div
            v-if="shouldScroll"
            class="pointer-events-none absolute inset-y-0 left-0 z-10 w-32 bg-linear-to-r from-black via-black/50 to-transparent transition-opacity duration-500"
        ></div>
        <div
            v-if="shouldScroll"
            class="pointer-events-none absolute inset-y-0 right-0 z-10 w-32 bg-linear-to-l from-black via-black/50 to-transparent transition-opacity duration-500"
        ></div>

        <div
            class="flex shrink-0 flex-nowrap items-center"
            :class="{
                'animate-marquee group-hover:[animation-play-state:paused]':
                    shouldScroll,
                'w-full justify-center': !shouldScroll,
            }"
            :style="shouldScroll ? { willChange: 'transform' } : {}"
        >
            <!-- First set of logos -->
            <div
                ref="content"
                class="flex shrink-0 flex-nowrap items-center gap-20"
                :class="{ 'pr-20': shouldScroll }"
            >
                <a
                    v-for="(sponsor, index) in sponsors"
                    :key="'s1-' + index"
                    :href="sponsor.website"
                    target="_blank"
                    class="shrink-0 p-2 transition-transform duration-300 hover:scale-110"
                >
                    <img
                        :src="sponsor.logo_url"
                        class="h-12 w-auto fill-white opacity-80 transition-all duration-300 hover:opacity-100"
                        :alt="'Logo du sponsor ' + sponsor.name"
                    />
                </a>
            </div>

            <!-- Second set for seamless loop, only if scrolling -->
            <div
                v-if="shouldScroll"
                class="flex shrink-0 flex-nowrap items-center gap-20 pr-20"
                aria-hidden="true"
            >
                <a
                    v-for="(sponsor, index) in sponsors"
                    :key="'s2-' + index"
                    :href="sponsor.website"
                    target="_blank"
                    class="shrink-0 p-2 transition-transform duration-300 hover:scale-110"
                >
                    <img
                        :src="sponsor.logo_url"
                        class="h-12 w-auto fill-white opacity-80 transition-all duration-300 hover:opacity-100"
                        :alt="'Logo du sponsor ' + sponsor.name"
                    />
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
