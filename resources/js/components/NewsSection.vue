<script setup lang="ts">
    import { computed } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import { ArrowRight } from '@lucide/vue';
    import AppButton from '@/components/AppButton.vue';
    import newsRoute from '@/routes/news';
    import NewsSectionCard from './NewsSectionCard.vue';

    interface Post {
        id: number;
        title: string;
        slug: string;
        excerpt: string;
        cover_url: string;
        cover_responsive?: { src: string, srcset: string };
        thumbnail_url: string;
        published_at: string;
        category?: { id: number; name: string };
    }

    const props = defineProps<{ posts: Post[] }>();

    const isHorizontal = computed(() => props.posts.length <= 2);

    const formatDate = (dateString: string) => {
        return new Intl.DateTimeFormat('fr-FR', {
            month: 'long',
            year: 'numeric',
        }).format(new Date(dateString));
    };
</script>

<template>
    <section class="relative bg-black py-24 px-12 xl:px-32">
        <div class="max-w-7xl mx-auto">

            <!-- Header Section -->
            <div class="flex items-end justify-between mb-16">
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="h-px w-8 bg-[#51A687]"></div>
                        <span class="text-[#51A687] text-sm font-bold tracking-[0.3em] uppercase">Actualités</span>
                    </div>
                    <h2 class="text-5xl font-chillax  text-white uppercase italic">News & Stories</h2>
                </div>
                <div class="hidden md:block">
                    <AppButton :href="newsRoute.index.url()" variant="ghost" size="md"
                        className="text-gray-400 hover:text-white">
                        Toutes les actualités
                        <template #right-icon>
                            <ArrowRight class="w-5 h-5 transition-transform group-hover:translate-x-1" />
                        </template>
                    </AppButton>
                </div>
            </div>

            <!-- Content Grid -->
            <div :class="isHorizontal ? 'flex flex-col gap-8' : 'grid grid-cols-1 xl:grid-cols-12 gap-8'">

                <!-- Layout Logic -->
                <template v-if="isHorizontal">
                    <NewsSectionCard v-for="post in posts" :key="post.id" :post="post" layout="horizontal" />
                </template>

                <template v-else>
                    <!-- Featured Post (Left / Large) -->
                    <div class="xl:col-span-8">
                        <NewsSectionCard :post="posts[0]" layout="featured" />
                    </div>
                    <!-- Sidebar Posts (Right / Small) -->
                    <div class="xl:col-span-4 flex flex-col gap-8">
                        <NewsSectionCard v-for="post in posts.slice(1)" :key="post.id" :post="post" layout="sidebar" />
                    </div>
                </template>
            </div>

            <!-- Mobile CTA -->
            <div class="mt-12 flex justify-center md:hidden">
                <AppButton :href="newsRoute.index.url()" variant="primary" size="md" className="w-full">
                    Toutes les actualités
                    <template #right-icon>
                        <ArrowRight class="w-5 h-5 transition-transform group-hover:translate-x-1" />
                    </template>
                </AppButton>
            </div>
        </div>
    </section>
</template>
