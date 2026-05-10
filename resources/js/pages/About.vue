<script setup lang="ts">
    import { Head } from '@inertiajs/vue3';
    import { Seo } from '@/types/seo';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';

    interface Section {
        title: string;
        content: string;
        image: string;
    }

    defineProps<{
        sections: Section[];
        seo?: Seo;
    }>();
</script>

<template>

    <Head>
        <title>{{ seo?.title ?? 'À Propos' }}</title>
        <meta v-if="seo?.description" name="description" :content="seo.description" />
        <meta v-if="seo?.keywords" name="keywords" :content="seo.keywords" />
        <meta v-if="seo?.robots" name="robots" :content="seo.robots" />
        <link v-if="seo?.canonical_url" rel="canonical" :href="seo.canonical_url" />

        <!-- Open Graph -->
        <meta property="og:title" :content="seo?.og_title ?? seo?.title ?? 'À Propos'" />
        <meta v-if="seo?.og_description ?? seo?.description" property="og:description" :content="seo?.og_description ?? seo?.description" />
        <meta v-if="seo?.og_image" property="og:image" :content="`/storage/${seo.og_image}`" />
        <meta property="og:type" :content="seo?.og_type ?? 'website'" />

        <!-- Twitter -->
        <meta name="twitter:card" :content="seo?.twitter_card ?? 'summary_large_image'" />
        <meta name="twitter:title" :content="seo?.twitter_title ?? seo?.title ?? 'À Propos'" />
        <meta v-if="seo?.twitter_description ?? seo?.description" name="twitter:description" :content="seo?.twitter_description ?? seo?.description" />
        <meta v-if="seo?.twitter_image" name="twitter:image" :content="`/storage/${seo.twitter_image}`" />
        <component :is="'script'" v-if="seo?.json_ld" type="application/ld+json" v-html="seo.json_ld" />
    </Head>

    <Header />

    <div class="relative z-10 overflow-hidden rounded-b-[6rem] bg-black min-h-screen">
        <!-- Background Effects -->
        <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-black via-black to-black"></div>
        <div
            class="pointer-events-none absolute -top-32 left-1/2 h-115 w-[130%] -translate-x-1/2 rounded-full bg-[#06402B]/18 blur-[150px]">
        </div>
        <div class="pointer-events-none absolute inset-0 bg-[url('/noise.png')] opacity-[0.04] mix-blend-soft-light">
        </div>

        <main class="relative z-10 mx-auto max-w-6xl px-6 pt-48 pb-32 md:px-10 lg:pt-56">
            <!-- Header -->
            <header class="mb-32 md:mb-48 space-y-8 text-center flex flex-col items-center justify-center">
                <p class="font-mono text-xs md:text-sm tracking-[0.5em] text-[#51A687] uppercase">À propos de nous</p>
                <h1 class="font-chillax text-[15vw] md:text-[10rem] leading-[0.75] text-white tracking-tighter">
                    Symbiosa
                </h1>
            </header>

            <div class="relative mt-20 px-4 md:px-0">
                <!-- Timeline Line -->
                <div class="absolute top-0 bottom-0 left-1/2 z-1 hidden w-px -translate-x-1/2 bg-[#51A687]/40 md:block"
                    style="-webkit-mask-image: linear-gradient(to bottom, transparent, black 5%, black 95%, transparent); mask-image: linear-gradient(to bottom, transparent, black 5%, black 95%, transparent);">
                </div>

                <div class="relative space-y-32 md:space-y-0">
                    <article v-for="(node, index) in sections" :key="index" class="relative">

                        <!-- Timeline Node Anchor -->
                        <div class="absolute top-1/2 left-1/2 z-10 hidden -translate-x-1/2 -translate-y-1/2 md:block">
                            <div class="flex flex-col items-center justify-center bg-black py-4">
                                <span class="font-mono text-xs text-[#51A687]">
                                    {{ (index + 1).toString().padStart(2, '0') }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 items-center gap-12 md:grid-cols-2 md:gap-24 md:py-24">

                            <!-- Visual Column -->
                            <div :class="index % 2 === 0 ? 'md:order-1 md:pr-12' : 'md:order-2 md:pl-12'"
                                class="flex justify-center">
                                <div class="relative w-full aspect-4/5 flex items-center justify-center">
                                    <!-- Stain Mask using external PNG -->
                                    <div class="absolute inset-0" :style="{
                                        '-webkit-mask-image': 'url(/mask.png)',
                                        'mask-image': 'url(/mask.png)',
                                        '-webkit-mask-size': '100% 100%',
                                        'mask-size': '100% 100%',
                                        '-webkit-mask-repeat': 'no-repeat',
                                        'mask-repeat': 'no-repeat',
                                        '-webkit-mask-position': 'center',
                                        'mask-position': 'center',
                                        'transform': `rotate(${index % 2 === 0 ? 0 : 180}deg) scaleX(${index === 2 ? -1 : 1})`
                                    }">
                                        <img :src="`/storage/${node.image}`" alt=""
                                            class="h-full w-full object-cover grayscale opacity-80"
                                            :style="{ transform: `scaleX(${index === 2 ? -1.1 : 1.1}) scaleY(1.1) rotate(${index % 2 === 0 ? 0 : -180}deg)` }" />
                                        <div
                                            class="pointer-events-none absolute inset-0 bg-[#51A687] mix-blend-color opacity-40">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Text Column -->
                            <div :class="[index % 2 === 0 ? 'md:order-2 md:pl-12' : 'md:order-1 md:pr-12 md:text-right']"
                                class="flex flex-col px-2 md:px-0 text-left">

                                <h2 class="mb-8 font-chillax text-4xl leading-[1.1] text-white md:text-5xl lg:text-6xl tracking-tight"
                                    :class="index % 2 !== 0 ? 'md:text-right' : ''">
                                    {{ node.title }}
                                </h2>

                                <div class="prose prose-invert font-synonym max-w-none whitespace-pre-line
                                    prose-p:text-gray-400 prose-p:text-lg prose-p:leading-relaxed prose-p:font-light prose-p:tracking-wide
                                    prose-strong:text-white prose-strong:font-normal"
                                    :class="index % 2 !== 0 ? 'md:text-right text-left' : 'text-left'"
                                    v-html="node.content">
                                </div>

                            </div>
                        </div>
                    </article>
                </div>
            </div>

            <div class="mt-32 mb-32 flex flex-col items-center justify-center text-center">
                <p class="mb-2 font-mono text-xs tracking-[0.3em] text-gray-500 uppercase">
                    À ceux qui partagent notre vision :
                </p>
                <h2
                    class="font-chillax text-[15vw] md:text-[10rem] lg:text-[12rem] leading-none text-white tracking-tighter italic">
                    merci.
                </h2>
            </div>

        </main>
    </div>

    <Footer />
</template>

<style scoped>
.prose {
    --tw-prose-body: #9ca3af;
    --tw-prose-headings: #ffffff;
}

/* Specific alignment handling for prose p tags when in right-aligned column */
@media (min-width: 768px) {
    .md\:text-right p {
        text-align: right;
    }
}
</style>
