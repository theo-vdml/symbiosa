<script setup lang="ts">
    import { Head } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';
    import SeoMeta from '@/components/SeoMeta.vue';
    import { Seo } from '@/types/seo';

    interface Props {
        seo?: Seo;
        title?: string;
        hasBackground?: boolean;
    }

    defineProps<Props>();
</script>

<template>
    <!-- SEO & Head -->
    <SeoMeta v-if="seo" :seo="seo" />

    <Head v-else-if="title" :title="title" />

    <Header />

    <main class="relative z-10 overflow-hidden rounded-b-[3rem] lg:rounded-b-[6rem] bg-black min-h-screen">

        <template v-if="hasBackground">
            <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-black via-black to-black"></div>
            <div
                class="pointer-events-none absolute -top-32 left-1/2 h-115 w-[130%] -translate-x-1/2 rounded-full bg-[#06402B]/18 blur-[150px]">
            </div>
            <div
                class="pointer-events-none absolute inset-0 bg-[url('/noise.png')] opacity-[0.04] mix-blend-soft-light">
            </div>
        </template>

        <slot />
    </main>

    <Footer />
</template>
