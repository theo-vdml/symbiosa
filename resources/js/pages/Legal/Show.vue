<script setup lang="ts">
    import MainLayout from '@/layouts/MainLayout.vue';

    const props = defineProps<{
        page: {
            id: number
            title: string
            slug: string
        },
        version: {
            content: string
            version_number: number
            updated_at: string
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
    <MainLayout :title="page.title" has-background>
        <div class="relative z-10 mx-auto max-w-4xl px-6 pb-24 pt-32 md:px-10">
            <header class="mb-12 space-y-4">
                <h1 class="font-chillax text-4xl leading-[0.95] text-white md:text-6xl">
                    {{ page.title }}
                </h1>
                <p class="text-sm font-medium text-gray-400">
                    Dernière mise à jour le {{ formatDate(version.updated_at) }}
                </p>
            </header>

            <article class="font-synonym text-base leading-relaxed text-gray-300 prose prose-invert max-w-none">
                <div v-html="version.content"></div>
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
