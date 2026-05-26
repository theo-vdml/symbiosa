<script setup lang="ts">
    import { useId } from 'vue';

    const props = defineProps<{
        title: string;
        description: string;
    }>();

    const titleId = useId();
</script>

<template>
    <section :aria-labelledby="titleId"
        class="group relative overflow-hidden rounded-[3rem] border border-white/10 bg-white/3 backdrop-blur-sm transition-all duration-500">

        <!-- Ticket Type Header -->
        <div class="p-8 sm:p-10 border-b border-white/5 bg-linear-to-br from-white/2 to-transparent">
            <div class="space-y-4 text-center sm:text-left">
                <h3 :id="titleId" class="font-chillax text-2xl md:text-3xl text-white uppercase tracking-[0.2em] leading-none">
                    {{ title }}
                </h3>
                <p class="text-white/40 text-[10px] md:text-xs uppercase tracking-[0.2em] leading-relaxed max-w-2xl">
                    {{ description }}
                </p>
            </div>
        </div>

        <div class="divide-y divide-white/5">
            <slot />
        </div>
    </section>
</template>
