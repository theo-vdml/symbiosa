<script setup lang="ts">
interface Props {
    heading?: string;
    size?: 'lg' | 'xl';
}

withDefaults(defineProps<Props>(), {
    size: 'lg',
});
</script>

<template>
    <div class="flex flex-col items-center px-4 text-center">
        <!-- Top Slot (above heading) -->
        <div v-if="$slots.top" class="mb-8">
            <slot name="top" />
        </div>

        <!-- Main Heading -->
        <h1
            :class="[
                'font-chillax leading-[0.9] tracking-tight text-white uppercase',
                size === 'xl'
                    ? 'text-[clamp(4rem,18vw,14rem)] leading-[0.85]'
                    : 'text-5xl md:text-7xl lg:text-8xl',
            ]"
        >
            <slot>{{ heading }}</slot>
        </h1>

        <!-- Bottom Slot (below heading) -->
        <div v-if="$slots.bottom" class="mt-8">
            <slot name="bottom" />
        </div>
    </div>
</template>
