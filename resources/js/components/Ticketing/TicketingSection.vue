<script setup lang="ts">
import { useId } from 'vue';

defineProps<{
    title: string;
    description: string;
}>();

const titleId = useId();
</script>

<template>
    <section
        :aria-labelledby="titleId"
        class="group relative overflow-hidden rounded-[3rem] border border-white/10 bg-white/3 backdrop-blur-sm transition-all duration-500"
    >
        <!-- Ticket Type Header -->
        <div
            class="border-b border-white/5 bg-linear-to-br from-white/2 to-transparent p-8 sm:p-10"
        >
            <div class="space-y-4 text-center sm:text-left">
                <h3
                    :id="titleId"
                    class="font-chillax text-2xl leading-none tracking-[0.2em] text-white uppercase md:text-3xl"
                >
                    {{ title }}
                </h3>
                <p
                    class="max-w-2xl text-[10px] leading-relaxed tracking-[0.2em] text-white/40 uppercase md:text-xs"
                >
                    {{ description }}
                </p>
            </div>
        </div>

        <div class="divide-y divide-white/5">
            <slot />
        </div>
    </section>
</template>
