<script setup lang="ts">
interface Props {
    preheading?: string;
    heading: string;
    description?: string;
}

defineProps<Props>();
</script>

<template>
    <header class="mb-12 space-y-4 text-center md:mb-20 md:text-left">
        <p
            v-if="preheading"
            class="text-xs font-bold tracking-[0.35em] text-[#51A687] uppercase"
        >
            {{ preheading }}
        </p>
        <h1
            class="font-chillax text-5xl leading-[0.92] text-balance text-white md:text-7xl lg:text-8xl"
        >
            <span v-html="heading"></span>
        </h1>
        <div
            v-if="description || $slots.default"
            class="max-w-2xl font-synonym text-sm leading-relaxed text-balance text-gray-300 md:text-base"
        >
            <slot>
                {{ description }}
            </slot>
        </div>
    </header>
</template>
