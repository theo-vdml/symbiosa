<script setup lang="ts">
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';

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
    <Header />

    <div class="relative z-10 min-h-screen overflow-hidden rounded-b-[3rem] lg:rounded-b-[6rem] bg-black">
        <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-black via-black to-black"></div>
        <div
            class="pointer-events-none absolute -top-32 left-1/2 h-115 w-[130%] -translate-x-1/2 rounded-full bg-[#06402B]/18 blur-[150px]">
        </div>
        <div class="pointer-events-none absolute inset-0 bg-[url('/noise.png')] opacity-[0.04] mix-blend-soft-light">
        </div>

        <main class="relative z-10 mx-auto max-w-4xl px-6 pb-24 pt-32 md:px-10">
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
