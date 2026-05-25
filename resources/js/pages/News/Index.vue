<script setup lang="ts">
    import { computed, ref } from 'vue';
    import MainLayout from '@/layouts/MainLayout.vue';
    import PageHeader from '@/components/PageHeader.vue';
    import { Link } from '@inertiajs/vue3';

    interface NewsProps {
        preheading: string
        heading: string
        description: string
        posts: {
            id: number
            title: string
            slug: string
            status: string
            content: string
            excerpt: string
            cover_url: string
            thumbnail_url: string
            created_at: string
            updated_at: string
            published_at: string
            category_id: number | null
        }[]
        categories: {
            id: number
            name: string
            slug: string
            created_at: string
            updated_at: string
        }[]
        seo: any
    }

    const props = defineProps<NewsProps>();

    const selectedCategories = ref<number[]>([]);

    const filteredNews = computed(() => {
        if (selectedCategories.value.length === 0) {
            return props.posts;
        }
        return props.posts.filter((item) =>
            item.category_id !== null && selectedCategories.value.includes(item.category_id),
        );
    });

    function toggleCategory(cat: number) {
        const idx = selectedCategories.value.indexOf(cat);
        if (idx === -1) {
            selectedCategories.value.push(cat);
        } else {
            selectedCategories.value.splice(idx, 1);
        }
        if (selectedCategories.value.length === props.categories.length) {
            selectedCategories.value = [];
        }
    }

    const formatDate = (dateString: string) => {
        const date = new Date(dateString);
        return new Intl.DateTimeFormat('fr-FR', {
            month: 'long',
            year: 'numeric',
        }).format(date);
    };
</script>

<template>
    <MainLayout :seo="seo" has-background>
        <div class="relative z-10 mx-auto max-w-6xl px-4 pt-34 pb-32 md:px-10">

            <PageHeader :preheading="preheading" :heading="heading" :description="description" />

            <!-- Category filters -->
            <div class="mb-10 flex flex-wrap items-center gap-1.5" v-if="props.categories.length > 1">
                <span class="mr-1 text-[10px] font-bold tracking-[0.2em] text-gray-500 uppercase">Catégorie</span>
                <button type="button" @click="selectedCategories = []" :class="[
                    'cursor-pointer rounded-full border px-2.5 py-1 text-[10px] font-bold tracking-[0.18em] uppercase transition-colors',
                    selectedCategories.length === 0
                        ? 'border-[#51A687] bg-[#51A687]/20 text-white'
                        : 'border-white/15 bg-white/5 text-gray-300 hover:border-white/30',
                ]">
                    All
                </button>
                <button v-for="cat in props.categories" :key="cat.id" type="button" @click="toggleCategory(cat.id)"
                    :class="[
                        'cursor-pointer rounded-full border px-2.5 py-1 text-[10px] font-bold tracking-[0.18em] uppercase transition-colors',
                        selectedCategories.includes(cat.id)
                            ? 'border-[#51A687] bg-[#51A687]/20 text-white'
                            : 'border-white/15 bg-white/5 text-gray-300 hover:border-white/30',
                    ]">
                    {{ cat.name }}
                </button>
            </div>

            <!-- News grid -->
            <section v-if="filteredNews.length" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <Link v-for="news in filteredNews" :key="news.id" :href="`/news/${news.slug}`"
                    class="group relative flex flex-col overflow-hidden rounded-2xl border border-white/8 bg-white/2 transition-all duration-300 hover:border-white/18 hover:bg-white/5 hover:shadow-[0_0_40px_rgba(200,10,69,0.1)]">
                    <!-- Image -->
                    <div class="relative aspect-16/10 overflow-hidden">
                        <img :src="news.thumbnail_url" :alt="news.title"
                            class="h-full w-full object-cover transition-all duration-700 group-hover:scale-105 group-hover:brightness-110" />
                        <div class="absolute inset-0 bg-linear-to-t from-black/60 to-transparent"></div>
                        <span v-if="news.category_id"
                            class="absolute left-4 top-4 rounded-full border border-[#51A687]/60 bg-[#51A687]/30 px-3 py-1 text-[10px] font-bold tracking-[0.18em] text-white uppercase backdrop-blur-sm">
                            {{props.categories.find((cat) => cat.id === news.category_id)?.name}}
                        </span>
                    </div>

                    <!-- Content -->
                    <div class="flex flex-1 flex-col gap-3 p-5">
                        <span class="text-[11px] font-medium text-gray-500 capitalize">{{ formatDate(news.published_at)
                            }}</span>
                        <h3
                            class="font-chillax text-lg leading-snug text-white transition-colors duration-300 group-hover:text-[#51A687] md:text-xl">
                            {{ news.title }}
                        </h3>
                        <p class="line-clamp-3 text-sm leading-relaxed text-gray-400">
                            {{ news.excerpt }}
                        </p>
                        <p
                            class="mt-auto pt-2 text-[10px] font-bold tracking-[0.14em] text-[#51A687] uppercase opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                            Lire la suite →
                        </p>
                    </div>
                </Link>
            </section>

            <!-- Empty state -->
            <section v-else class="flex flex-col items-center justify-center py-20 text-center">
                <h2 class="font-chillax text-3xl text-white md:text-4xl">
                    Aucun article trouvé
                </h2>
                <p class="mt-4 max-w-sm text-gray-400">
                    Essaie de désélectionner certains filtres.
                </p>
            </section>
        </div>
    </MainLayout>
</template>
