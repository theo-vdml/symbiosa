<script setup lang="ts">
    interface Props {
        preheading?: string;
        heading: string;
        description?: string;
    }

    defineProps<Props>();
</script>

<template>
    <header class="mb-12 md:mb-20 space-y-4 text-center md:text-left">
        <p v-if="preheading" class="text-xs font-bold tracking-[0.35em] text-[#51A687] uppercase">
            {{ preheading }}
        </p>
        <h1 class="font-chillax text-5xl leading-[0.92] text-white md:text-7xl lg:text-8xl text-balance">
            <span v-html="heading"></span>
        </h1>
        <div v-if="description || $slots.default"
            class="max-w-2xl font-synonym text-sm text-gray-300 md:text-base leading-relaxed text-balance">
            <slot>
                {{ description }}
            </slot>
        </div>
    </header>
</template>
