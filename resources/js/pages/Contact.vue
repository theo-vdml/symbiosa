<script setup lang="ts">
    import { ref } from 'vue';
    import { Head } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';
    import SeoMeta from '@/components/SeoMeta.vue';
    import { Seo } from '@/types/seo';

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
    <SeoMeta :seo="seo" />
    <Header />

    <div class="relative z-10 min-h-screen overflow-hidden rounded-b-[6rem] bg-black">
        <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-black via-black to-black"></div>
        <div
            class="pointer-events-none absolute -top-32 left-1/2 h-115 w-[130%] -translate-x-1/2 rounded-full bg-[#06402B]/18 blur-[150px]">
        </div>
        <div class="pointer-events-none absolute inset-0 bg-[url('/noise.png')] opacity-[0.04] mix-blend-soft-light">
        </div>

        <main class="relative z-10 mx-auto max-w-6xl px-6 pt-34 pb-32 md:px-10 lg:px-14">

            <!-- Page header -->
            <section class="mb-20 space-y-3 text-center md:text-left">
                <p class="text-xs font-bold tracking-[0.35em] text-[#51A687] uppercase">
                    {{ settings.subheading }}
                </p>
                <h1 class="font-chillax text-5xl leading-[0.92] text-white md:text-7xl lg:text-8xl">
                    {{ settings.heading }}
                </h1>
                <p class="max-w-2xl text-sm text-gray-300 md:text-base">
                    {{ settings.description }}
                </p>
            </section>

            <!-- Layout: FAQ & Contact Emails Stacked -->
            <div class="w-full space-y-24">

                <!-- FAQ Section -->
                <div v-if="settings.faq_items && settings.faq_items.length > 0" class="space-y-10">
                    <div>
                        <h2 class="font-chillax text-4xl text-white uppercase tracking-wide">
                            {{ settings.faq_heading }}
                        </h2>
                        <p class="mt-2 text-sm text-gray-400">
                            {{ settings.faq_description }}
                        </p>
                    </div>

                    <div class="space-y-4 mt-12">
                        <div v-for="(item, index) in settings.faq_items" :key="index" class="border-b border-white/10">
                            <button type="button" @click="toggleFaq(index)"
                                class="w-full flex items-center justify-between py-6 text-left group cursor-pointer">
                                <h3
                                    class="font-chillax text-xl md:text-2xl text-white uppercase tracking-wide group-hover:text-[#51A687] transition-colors pr-8">
                                    {{ item.question }}
                                </h3>
                                <span
                                    class="text-[#51A687] font-mono text-3xl font-light leading-none transition-transform duration-500 origin-center"
                                    :class="{ 'rotate-45': openFaq === index }">
                                    +
                                </span>
                            </button>
                            <div class="grid transition-all duration-500 ease-in-out"
                                :class="openFaq === index ? 'grid-rows-[1fr] opacity-100 pb-6' : 'grid-rows-[0fr] opacity-0 pb-0'">
                                <div class="overflow-hidden">
                                    <p
                                        class="font-synonym text-gray-400 text-base md:text-lg leading-relaxed max-w-3xl whitespace-pre-line">
                                        {{ item.answer }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Section -->
                <div v-if="settings.email_options && settings.email_options.length > 0" class="pt-8">
                    <h2 class="mb-10 font-chillax text-4xl text-white uppercase tracking-wide">
                        {{ settings.email_heading }}
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        <div v-for="(option, index) in settings.email_options" :key="index"
                            class="rounded-2xl border border-white/10 bg-white/5 p-8 backdrop-blur-sm flex flex-col items-center text-center space-y-6 hover:border-[#51A687]/50 transition-colors">
                            <div>
                                <p class="mb-2 font-chillax text-white uppercase tracking-wider text-sm">{{ option.label
                                    }}</p>
                                <a :href="`mailto:${option.email}`"
                                    class="font-synonym text-xl text-[#51A687] hover:underline">{{ option.email }}</a>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <Footer />
</template>
