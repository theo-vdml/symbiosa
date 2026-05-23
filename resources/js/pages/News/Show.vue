<script setup lang="ts">
    import MainLayout from '@/layouts/MainLayout.vue';
    import { Seo } from '@/types/seo';

    const props = defineProps<{
        post: {
            id: number
            title: string
            slug: string
            status: string
            content: string
            excerpt: string
            cover_url: string
            cover_responsive: {
                srcset: string
            } | null
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
    <MainLayout :seo="seo" has-background>
        <!-- Hero Section -->
        <section class="relative h-[45vh] w-full overflow-hidden md:h-[65vh]">
            <img :src="post.cover_url" :srcset="post.cover_responsive?.srcset" sizes="(max-width: 768px) 200vw, 100vw"
                :alt="post.title" class="h-full w-full object-cover transition-transform duration-1000" />
            <div class="absolute inset-0 bg-linear-to-t from-black via-transparent to-black/20"></div>
        </section>

        <div class="relative z-10 mx-auto max-w-6xl px-6 pb-24 pt-16 md:px-10 lg:px-14">
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
        </div>
    </MainLayout>
</template>

<style scoped>
.prose {
    --tw-prose-body: #d1d5db;
    --tw-prose-headings: #ffffff;
}
</style>
