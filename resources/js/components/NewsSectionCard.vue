<script setup lang="ts">
    import { computed } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import newsRoute from '@/routes/news';
    import { ArrowRight } from '@lucide/vue';

    const props = defineProps<{
        post: any;
        layout: 'horizontal' | 'featured' | 'sidebar';
    }>();

    const formatDate = (dateString: string) => {
        return new Intl.DateTimeFormat('fr-FR', { month: 'long', year: 'numeric' }).format(new Date(dateString));
    };

    // Dynamic Class Mappings to maintain original styles
    const cardClasses = computed(() => ({
        'group relative flex overflow-hidden border border-white/8 bg-white/2 transition-all duration-300 hover:border-white/18 hover:bg-white/5': true,
        'flex-col md:flex-row rounded-3xl hover:shadow-[0_0_50px_rgba(200,10,69,0.15)] w-full': props.layout === 'horizontal',
        'flex-col rounded-3xl hover:shadow-[0_0_50px_rgba(200,10,69,0.15)] h-full': props.layout === 'featured',
        'flex-col md:flex-row xl:flex-col rounded-2xl hover:shadow-[0_0_40px_rgba(200,10,69,0.1)]': props.layout === 'sidebar',
    }));

    const imageContainerClasses = computed(() => ({
        'relative overflow-hidden shrink-0': true,
        'aspect-video md:aspect-auto md:w-1/3 border-b md:border-b-0 md:border-r border-white/8': props.layout === 'horizontal',
        'aspect-video': props.layout === 'featured',
        'aspect-video md:w-2/5 xl:w-full': props.layout === 'sidebar',
    }));

    const contentContainerClasses = computed(() => ({
        'flex flex-1 flex-col justify-center gap-4': true,
        'p-8 md:p-12 xl:p-16': props.layout === 'horizontal',
        'p-8': props.layout === 'featured',
        'p-5 md:p-8 xl:p-5 gap-3': props.layout === 'sidebar',
    }));

    const titleClasses = computed(() => ({
        'font-chillax leading-tight text-white transition-colors duration-300 group-hover:text-[#51A687] text-balance': true,
        'text-2xl md:text-3xl xl:text-4xl': props.layout === 'horizontal',
        'text-lg md:text-4xl': props.layout === 'featured',
        'text-lg md:text-xl xl:text-lg leading-snug': props.layout === 'sidebar',
    }));

    const excerptClasses = computed(() => ({
        'text-gray-400 leading-relaxed': true,
        'line-clamp-3 text-base xl:text-lg max-w-3xl': props.layout === 'horizontal',
        'line-clamp-3 md:line-clamp-6 text-sm md:text-base w-full': props.layout === 'featured',
        'line-clamp-2 text-sm': props.layout === 'sidebar',
    }));

    // Shared styles that don't change much
    const imageClasses = "h-full w-full object-cover transition-all duration-700 group-hover:scale-105 group-hover:brightness-110";
    const badgeClasses = "absolute left-6 top-6 rounded-full border border-[#51A687]/60 bg-[#51A687]/30 px-4 py-1.5 text-xs font-bold tracking-[0.18em] text-white uppercase backdrop-blur-sm";
    const dateClasses = "text-xs font-medium text-gray-500 capitalize";
    const footerLinkClasses = "mt-4 text-xs font-bold tracking-[0.14em] text-[#51A687] uppercase opacity-0 transition-opacity duration-200 group-hover:opacity-100 flex items-center";
</script>

<template>
    <Link :href="newsRoute.show.url(post.slug)" :class="cardClasses">
        <!-- Image Container -->
        <div :class="imageContainerClasses">
            <img :src="post.thumbnail_url" :alt="post.title" :class="imageClasses" />
            <div class="absolute inset-0 bg-linear-to-t from-black/60 to-transparent"></div>
            <span :class="badgeClasses">
                {{ post.category?.name || 'Sans catégorie' }}
            </span>
        </div>

        <!-- Content Container -->
        <div :class="contentContainerClasses">
            <span :class="dateClasses">{{ formatDate(post.published_at) }}</span>
            <h3 :class="titleClasses">{{ post.title }}</h3>
            <p :class="excerptClasses">{{ post.excerpt }}</p>
            <p :class="footerLinkClasses">
                <span>Lire la suite</span>
                <ArrowRight class="inline-block w-4 h-4 ml-1 -mt-0.5" />
            </p>
        </div>
    </Link>
</template>
