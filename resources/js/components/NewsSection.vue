<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

interface NewsItem {
    id: number;
    title: string;
    description: string;
    image: string;
    date: string;
    category: string;
}

const newsList: NewsItem[] = [
    {
        id: 1,
        title: "Le festival EDEN 2026 : Ce qu'il faut savoir sur cette édition exceptionnelle",
        description: "Plongez au cœur de l'expérience Symbiosa pour cette édition exceptionnelle à Gembloux. Découvrez la programmation complète et les nouveautés de cette année qui s'annonce mémorable pour tous les passionnés de musique électronique.",
        image: "https://picsum.photos/800/600?random=1",
        date: "Mars 2026",
        category: "Annonce"
    },
    {
        id: 2,
        title: "Nouveau Soundsystem Révolutionnaire",
        description: "Une immersion sonore inédite grâce à notre nouveau partenariat technique avec les plus grands ingénieurs du son actuels pour une clarté absolue.",
        image: "https://picsum.photos/400/300?random=2",
        date: "Février 2026",
        category: "Technique"
    },
    {
        id: 3,
        title: "Aftermovie 2025 disponible maintenant",
        description: "Revivez les meilleurs moments de l'édition précédente en vidéo haute définition avec des interviews exclusives des artistes.",
        image: "https://picsum.photos/400/300?random=3",
        date: "Janvier 2026",
        category: "Média"
    }
];

const mainNews = computed(() => newsList[0]);
const secondaryNews = computed(() => newsList.slice(1));
</script>

<template>
    <section class="relative bg-black py-24 px-12 lg:px-32">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="flex items-end justify-between mb-16">
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        <div class="h-px w-8 bg-[#c80a45]"></div>
                        <span class="text-[#c80a45] text-sm font-bold tracking-[0.3em] uppercase">
                            Actualités
                        </span>
                    </div>
                    <h2 class="text-5xl font-chillax font-bold text-white uppercase italic">
                        News & Stories
                    </h2>
                </div>

                <Link
                    href="/news"
                    class="hidden md:flex items-center gap-2 text-gray-400 hover:text-white transition-colors duration-300 group font-medium"
                >
                    Toutes les actualités
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 transition-transform group-hover:translate-x-1">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </Link>
            </div>

            <!-- News Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                <!-- Main News -->
                <div v-if="mainNews" class="lg:col-span-8 group cursor-pointer relative p-6 -m-6 rounded-[2rem] transition-all duration-500 border border-transparent hover:border-white/20 hover:bg-white/[0.05] hover:shadow-[0_0_50px_rgba(200,10,69,0.15)]">
                    <div class="relative overflow-hidden rounded-2xl aspect-[16/9] mb-8 border border-white/5 shadow-2xl">
                        <img
                            :src="mainNews.image"
                            :alt="mainNews.title"
                            class="w-full h-full object-cover transition-all duration-700 group-hover:scale-110 group-hover:brightness-110"
                        />
                        <div class="absolute top-6 left-6">
                            <span class="px-4 py-1.5 bg-[#c80a45] text-white text-xs font-bold uppercase tracking-wider rounded-full shadow-lg">
                                {{ mainNews.category }}
                            </span>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <span class="text-gray-500 text-sm font-medium">{{ mainNews.date }}</span>
                        <h3 class="text-3xl md:text-4xl font-chillax font-bold text-white group-hover:text-[#c80a45] transition-colors duration-300 leading-tight text-balance">
                            {{ mainNews.title }}
                        </h3>
                        <p class="text-gray-400 leading-relaxed max-w-2xl line-clamp-3">
                            {{ mainNews.description }}
                        </p>
                    </div>
                </div>

                <!-- Secondary News -->
                <div class="lg:col-span-4 flex flex-col gap-10">
                    <div v-for="news in secondaryNews" :key="news.id" class="group cursor-pointer flex flex-col sm:flex-row lg:flex-col gap-6 relative p-6 -m-6 rounded-[2rem] transition-all duration-500 border border-transparent hover:border-white/20 hover:bg-white/[0.05] hover:shadow-[0_0_30px_rgba(200,10,69,0.1)]">
                        <div class="relative overflow-hidden rounded-xl aspect-video sm:w-48 lg:w-full border border-white/5 flex-shrink-0 shadow-xl">
                            <img
                                :src="news.image"
                                :alt="news.title"
                                class="w-full h-auto object-cover transition-all duration-700 group-hover:scale-110 group-hover:brightness-110"
                            />
                            <div class="absolute top-3 left-3">
                                <span class="px-3 py-1 bg-[#c80a45] text-white text-[10px] font-bold uppercase tracking-wider rounded-full shadow-lg">
                                    {{ news.category }}
                                </span>
                            </div>
                        </div>
                        <div class="space-y-3">
                            <span class="text-gray-500 text-xs font-medium">{{ news.date }}</span>
                            <h4 class="text-xl font-chillax font-bold text-white group-hover:text-[#c80a45] transition-colors duration-300 leading-snug text-balance">
                                {{ news.title }}
                            </h4>
                            <p class="text-gray-400 text-sm line-clamp-2 leading-relaxed">
                                {{ news.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile CTA -->
            <div class="mt-12 md:hidden">
                <Link
                    href="/news"
                    class="flex items-center justify-center gap-2 py-4 border border-white/10 rounded-full text-white font-bold hover:bg-white hover:text-black transition-all duration-300"
                >
                    Toutes les actualités
                </Link>
            </div>
        </div>
    </section>
</template>

