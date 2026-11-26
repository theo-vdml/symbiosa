<script setup lang="ts">
import { ref } from 'vue';
import PageHeader from '@/components/PageHeader.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { Seo } from '@/types/seo';

interface FaqItem {
    question: string;
    answer: string;
}

interface EmailOption {
    label: string;
    email: string;
}

interface ContactSettings {
    heading: string;
    subheading: string;
    description: string;
    faq_heading: string;
    faq_description: string;
    faq_items: FaqItem[];
    email_heading: string;
    email_options: EmailOption[];
}

defineProps<{
    settings: ContactSettings;
    seo: Seo;
}>();

const openFaq = ref<number | null>(null);

const toggleFaq = (index: number) => {
    openFaq.value = openFaq.value === index ? null : index;
};
</script>

<template>
    <MainLayout :seo="seo" has-background>
        <div
            class="relative z-10 mx-auto max-w-6xl px-6 pt-34 pb-32 md:px-10 lg:px-14"
        >
            <PageHeader
                :preheading="settings.subheading"
                :heading="settings.heading"
                :description="settings.description"
            />

            <!-- Layout: FAQ & Contact Emails Stacked -->
            <div class="w-full space-y-24">
                <!-- FAQ Section -->
                <div
                    v-if="settings.faq_items && settings.faq_items.length > 0"
                    class="space-y-10"
                >
                    <div>
                        <h2
                            class="font-chillax text-4xl tracking-wide text-white uppercase"
                        >
                            {{ settings.faq_heading }}
                        </h2>
                        <p class="mt-2 text-sm text-gray-400">
                            {{ settings.faq_description }}
                        </p>
                    </div>

                    <div class="mt-12 space-y-4">
                        <div
                            v-for="(item, index) in settings.faq_items"
                            :key="index"
                            class="border-b border-white/10"
                        >
                            <button
                                type="button"
                                @click="toggleFaq(index)"
                                class="group flex w-full cursor-pointer items-center justify-between py-6 text-left"
                            >
                                <h3
                                    class="pr-8 font-chillax text-xl tracking-wide text-white uppercase transition-colors group-hover:text-[#51A687] md:text-2xl"
                                >
                                    {{ item.question }}
                                </h3>
                                <span
                                    class="origin-center font-mono text-3xl leading-none font-light text-[#51A687] transition-transform duration-500"
                                    :class="{ 'rotate-45': openFaq === index }"
                                >
                                    +
                                </span>
                            </button>
                            <div
                                class="grid transition-all duration-500 ease-in-out"
                                :class="
                                    openFaq === index
                                        ? 'grid-rows-[1fr] pb-6 opacity-100'
                                        : 'grid-rows-[0fr] pb-0 opacity-0'
                                "
                            >
                                <div class="overflow-hidden">
                                    <p
                                        class="max-w-3xl font-synonym text-base leading-relaxed whitespace-pre-line text-gray-400 md:text-lg"
                                    >
                                        {{ item.answer }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Section -->
                <div
                    v-if="
                        settings.email_options &&
                        settings.email_options.length > 0
                    "
                    class="pt-8"
                >
                    <h2
                        class="mb-10 font-chillax text-4xl tracking-wide text-white uppercase"
                    >
                        {{ settings.email_heading }}
                    </h2>

                    <div class="grid grid-cols-1 gap-8 md:grid-cols-3">
                        <div
                            v-for="(option, index) in settings.email_options"
                            :key="index"
                            class="flex flex-col items-center space-y-6 rounded-2xl border border-white/10 bg-white/5 p-8 text-center backdrop-blur-sm transition-colors hover:border-[#51A687]/50"
                        >
                            <div>
                                <p
                                    class="mb-2 font-chillax text-sm tracking-wider text-white uppercase"
                                >
                                    {{ option.label }}
                                </p>
                                <a
                                    :href="`mailto:${option.email}`"
                                    class="font-synonym text-xl text-[#51A687] hover:underline"
                                    >{{ option.email }}</a
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
