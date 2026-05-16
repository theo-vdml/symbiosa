<script setup lang="ts">
interface FaqItem {
    question: string;
    answer: string;
}

const props = defineProps<{
    faq: FaqItem[];
}>();
</script>

<template>
    <section v-if="faq && faq.length > 0" class="mt-40 mb-20">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 lg:gap-20">
            <!-- Left Column -->
            <div class="md:col-span-5 space-y-2">
                <p
                    class="font-chillax text-2xl text-white uppercase tracking-wider text-center md:text-right leading-tight">
                    Une question ?</p>
                <h2
                    class="font-chillax text-4xl md:text-5xl lg:text-7xl text-[#51A687] leading-[0.9] uppercase tracking-tighter text-center md:text-right">
                    ON PEUT VOUS AIDER
                </h2>
            </div>

            <!-- Right Column -->
            <div class="md:col-span-7 space-y-10 max-w-2xl">
                <div v-for="(item, index) in faq" :key="index" class="space-y-3">
                    <h3 class="font-chillax text-lg text-white uppercase tracking-wide">
                        {{ item.question }}
                    </h3>
                    <p class="text-gray-400 leading-relaxed">
                        {{ item.answer }}
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
