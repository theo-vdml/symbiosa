<script setup lang="ts">
    import { Head } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';

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
            category_id: number
        }
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

    <Head title="Actualité - Dans les coulisses de EDEN 2026" />

    <Header />

    <div class="relative z-10 min-h-[120vh] overflow-hidden rounded-b-[6rem] bg-black">
        <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-black via-black to-black"></div>
        <div
            class="pointer-events-none absolute -top-32 left-1/2 h-115 w-[130%] -translate-x-1/2 rounded-full bg-[#06402B]/18 blur-[150px]">
        </div>
        <div class="pointer-events-none absolute inset-0 bg-[url('/noise.png')] opacity-[0.04] mix-blend-soft-light">
        </div>

        <main class="relative z-10 mx-auto max-w-6xl px-6 pb-24 pt-32 md:px-10 lg:px-14">
            <section class="mx-auto mb-16 max-w-4xl">
                <div
                    class="relative overflow-hidden rounded-xl bg-black/40 shadow-[0_30px_80px_rgba(0,0,0,0.45)] ring-1 ring-white/10">
                    <img :src="`/storage/${post.thumbnail}`" :alt="post.title" class="h-full w-full object-cover" />
                    <div class="pointer-events-none absolute inset-0 bg-black/18"></div>
                </div>
            </section>

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
