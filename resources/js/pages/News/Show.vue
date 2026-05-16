<script setup lang="ts">
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';
    import SeoMeta from '@/components/SeoMeta.vue';
    import { Seo } from '@/types/seo';

    const props = defineProps<{
        post: {
            id: number
            title: string
            slug: string
            status: string
            content: string
            excerpt: string
            thumbnail: string
            created_at: string
            updated_at: string
            published_at: string
            category_id: number | null
        },
        seo: Seo;
    }>();

    const formatDate = (dateString: string) => {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('fr-FR', {
            day: 'numeric',
            month: 'long',
            year: 'numeric',
        }).format(date);
    }

</script>

<template>
    <SeoMeta :seo="seo" />

    <Header />

    <div class="relative z-10 min-h-[120vh] overflow-hidden rounded-b-[6rem] bg-black">
        <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-black via-black to-black"></div>
        <div
            class="pointer-events-none absolute -top-32 left-1/2 h-115 w-[130%] -translate-x-1/2 rounded-full bg-[#06402B]/18 blur-[150px]">
        </div>
        <div class="pointer-events-none absolute inset-0 bg-[url('/noise.png')] opacity-[0.04] mix-blend-soft-light">
        </div>

        <!-- Hero Section -->
        <section class="relative h-[45vh] w-full overflow-hidden md:h-[65vh]">
            <img :src="`/storage/${post.thumbnail}`" :alt="post.title"
                class="h-full w-full object-cover transition-transform duration-1000" />
            <div class="absolute inset-0 bg-linear-to-t from-black via-transparent to-black/20"></div>
        </section>

        <main class="relative z-10 mx-auto max-w-6xl px-6 pb-24 pt-16 md:px-10 lg:px-14">
            <section class="mx-auto mb-10 max-w-4xl space-y-5 text-center md:text-left">
                <p class="text-sm font-medium text-gray-400">
                    {{ formatDate(post.published_at) }}
                </p>
                <h1 class="font-chillax text-4xl leading-[0.95] text-white md:text-6xl lg:text-7xl">
                    {{ post.title }}
                </h1>
                <p class="font-synonym text-base leading-relaxed text-gray-300">
                    {{ post.excerpt }}
                </p>
            </section>

            <article
                class="mx-auto max-w-4xl space-y-10 font-synonym text-base leading-relaxed text-gray-300 prose prose-invert">
                <div v-html="post.content"></div>
            </article>
        </main>
    </div>

    <Footer />
</template>

<style scoped>
.prose {
    --tw-prose-body: #d1d5db;
    --tw-prose-headings: #ffffff;
}
</style>
