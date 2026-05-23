<script setup lang="ts">
    interface Props {
        heading?: string;
        size?: 'lg' | 'xl';
    }

    withDefaults(defineProps<Props>(), {
        size: 'lg'
    });
</script>

<template>
    <div class="flex flex-col items-center text-center px-4">
        <!-- Top Slot (above heading) -->
        <div v-if="$slots.top" class="mb-8">
            <slot name="top" />
        </div>

        <!-- Main Heading -->
        <h1 :class="[
            'font-chillax text-white uppercase tracking-tight leading-[0.9]',
            size === 'xl' ? 'text-[clamp(4rem,18vw,14rem)] leading-[0.85]' : 'text-5xl md:text-7xl lg:text-8xl'
        ]">
            <slot>{{ heading }}</slot>
        </h1>

        <!-- Bottom Slot (below heading) -->
        <div v-if="$slots.bottom" class="mt-8">
            <slot name="bottom" />
        </div>
    </div>
</template>
