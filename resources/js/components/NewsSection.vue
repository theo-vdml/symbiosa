<script setup lang="ts">
import { ArrowRight } from '@lucide/vue';
import { computed } from 'vue';
import AppButton from '@/components/AppButton.vue';
import NewsSectionCard from './NewsSectionCard.vue';
import newsRoute from '@/routes/news';

interface Post {
    id: number;
    title: string;
    slug: string;
    excerpt: string;
    cover_url: string;
    cover_responsive?: { src: string; srcset: string };
    thumbnail_url: string;
    published_at: string;
    category?: { id: number; name: string };
}

const props = defineProps<{ posts: Post[] }>();

const isHorizontal = computed(() => props.posts.length <= 2);
</script>

<template>
    <section class="relative bg-black px-6 py-24 md:px-12 xl:px-32">
        <div class="mx-auto max-w-7xl">
            <!-- Header Section -->
            <div class="mb-12 flex items-end justify-between md:mb-16">
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="h-px w-8 bg-[#51A687]"></div>
                        <span
                            class="text-sm font-bold tracking-[0.3em] text-[#51A687] uppercase"
                            >Actualités</span
                        >
                    </div>
                    <h2
                        class="font-chillax text-4xl text-white uppercase italic md:text-5xl"
                    >
                        News & Stories
                    </h2>
                </div>
                <div class="hidden md:block">
                    <AppButton
                        :href="newsRoute.index.url()"
                        variant="ghost"
                        size="md"
                        class="text-gray-400 hover:text-white"
                    >
                        Toutes les actualités
                        <template #right-icon>
                            <ArrowRight
                                class="h-5 w-5 transition-transform group-hover:translate-x-1"
                            />
                        </template>
                    </AppButton>
                </div>
            </div>

            <!-- Content Grid -->
            <div
                :class="
                    isHorizontal
                        ? 'flex flex-col gap-8'
                        : 'grid grid-cols-1 gap-8 xl:grid-cols-12'
                "
            >
                <!-- Layout Logic -->
                <template v-if="isHorizontal">
                    <NewsSectionCard
                        v-for="post in posts"
                        :key="post.id"
                        :post="post"
                        layout="horizontal"
                    />
                </template>

                <template v-else>
                    <!-- Featured Post (Left / Large) -->
                    <div class="xl:col-span-8">
                        <NewsSectionCard :post="posts[0]" layout="featured" />
                    </div>
                    <!-- Sidebar Posts (Right / Small) -->
                    <div class="flex flex-col gap-8 xl:col-span-4">
                        <NewsSectionCard
                            v-for="post in posts.slice(1)"
                            :key="post.id"
                            :post="post"
                            layout="sidebar"
                        />
                    </div>
                </template>
            </div>

            <!-- Mobile CTA -->
            <div class="mt-12 flex justify-center md:hidden">
                <AppButton
                    :href="newsRoute.index.url()"
                    variant="primary"
                    size="md"
                    class="w-full"
                >
                    Toutes les actualités
                    <template #right-icon>
                        <ArrowRight
                            class="h-5 w-5 transition-transform group-hover:translate-x-1"
                        />
                    </template>
                </AppButton>
            </div>
        </div>
    </section>
</template>
