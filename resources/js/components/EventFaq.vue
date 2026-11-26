<script setup lang="ts">
interface FaqItem {
    question: string;
    answer: string;
}

defineProps<{
    faq: FaqItem[];
}>();
</script>

<template>
    <section v-if="faq && faq.length > 0" class="mt-40 mb-20">
        <div class="grid grid-cols-1 gap-12 md:grid-cols-12 lg:gap-20">
            <!-- Left Column -->
            <div class="space-y-2 md:col-span-5">
                <p
                    class="text-center font-chillax text-2xl leading-tight tracking-wider text-white uppercase md:text-right"
                >
                    Une question ?
                </p>
                <h2
                    class="text-center font-chillax text-4xl leading-[0.9] tracking-tighter text-[#51A687] uppercase md:text-right md:text-5xl lg:text-7xl"
                >
                    ON PEUT VOUS AIDER
                </h2>
            </div>

            <!-- Right Column -->
            <div class="max-w-2xl space-y-10 md:col-span-7">
                <div
                    v-for="(item, index) in faq"
                    :key="index"
                    class="space-y-3"
                >
                    <h3
                        class="font-chillax text-lg tracking-wide text-white uppercase"
                    >
                        {{ item.question }}
                    </h3>
                    <p class="leading-relaxed text-gray-400">
                        {{ item.answer }}
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
