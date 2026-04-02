<script setup lang="ts">
import { computed, ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import Header from '@/components/Header.vue';
import Footer from '@/components/Footer.vue';

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
        image: '/photo_07.jpg',
        date: 'Mars 2026',
        category: 'Annonce',
    },
    {
        id: 2,
        title: 'Nouveau Soundsystem Révolutionnaire',
        description: "Une immersion sonore inédite grâce à notre nouveau partenariat technique avec les plus grands ingénieurs du son actuels pour une clarté absolue.",
        image: '/photo_08.jpg',
        date: 'Février 2026',
        category: 'Technique',
    },
    {
        id: 3,
        title: 'Aftermovie 2025 disponible maintenant',
        description: "Revivez les meilleurs moments de l'édition précédente en vidéo haute définition avec des interviews exclusives des artistes.",
        image: '/photo_09.jpg',
        date: 'Janvier 2026',
        category: 'Média',
    },
    {
        id: 4,
        title: 'EDEN 2025 : Retour sur une nuit inoubliable',
        description: "Retour en images sur l'édition 2025 qui a réuni plus de 2000 personnes à Gembloux. Une nuit de son, de lumière et d'émotion collective.",
        image: '/photo_00.jpg',
        date: 'Décembre 2025',
        category: 'Recap',
    },
    {
        id: 5,
        title: 'Lineup EDEN 2026 — Les premiers noms dévoilés',
        description: "Découvrez les premiers artistes confirmés pour l'édition 2026 de EDEN. Une sélection pointue entre techno sombre et house atmosphérique.",
        image: '/photo_01.jpg',
        date: 'Novembre 2025',
        category: 'Annonce',
    },
    {
        id: 6,
        title: 'Interview : dans la tête de nos sound designers',
        description: "Rencontre avec l'équipe technique derrière le son de Symbiosa. Ils nous expliquent comment ils sculptent chaque fréquence pour une expérience totale.",
        image: '/photo_02.jpg',
        date: 'Octobre 2025',
        category: 'Technique',
    },
    {
        id: 7,
        title: 'EDEN Closing Ritual — Le recap en photos',
        description: "118 photos, une nuit de clôture de saison exceptionnelle. Galerie complète de la soirée EDEN Closing Ritual à Gembloux.",
        image: '/photo_03.jpg',
        date: 'Octobre 2025',
        category: 'Média',
    },
    {
        id: 8,
        title: "Symbiosa : les coulisses d'un collectif en pleine expansion",
        description: "Comment Symbiosa est passé d'un collectif de quelques passionnés à l'un des organisateurs les plus suivis de Belgique en moins de trois ans.",
        image: '/photo_04.jpg',
        date: 'Septembre 2025',
        category: 'Communauté',
    },
    {
        id: 9,
        title: 'EDEN Sunset Garden — Aftermovie disponible',
        description: "L'aftermovie de notre soirée Open Air à Wavre est en ligne. Retrouvez l'ambiance unique de cette édition ensoleillée dans toute sa splendeur.",
        image: '/photo_05.jpg',
        date: 'Juillet 2025',
        category: 'Média',
    },
    {
        id: 10,
        title: "Rencontre avec Mina Lune — l'étoile montante de la scène techno belge",
        description: "Portrait exclusif de Mina Lune, headliner du Closing Ritual 2025. Elle nous parle de ses influences, de sa vision de la nuit et de ses projets à venir.",
        image: '/photo_06.jpg',
        date: 'Juin 2025',
        category: 'Communauté',
    },
    {
        id: 11,
        title: 'Partenariat Symbiosa × Studio Nebula',
        description: "Symbiosa s'associe à Studio Nebula pour la direction artistique de toutes les prochaines éditions. Une collaboration visuelle qui redéfinit l'identité du collectif.",
        image: '/photo_07.jpg',
        date: 'Avril 2025',
        category: 'Annonce',
    },
    {
        id: 12,
        title: 'Guide : comment préparer votre nuit EDEN',
        description: "Tout ce qu'il faut savoir avant de venir : transport, vestiaire, accès, lineup, conseils pratiques. Votre soirée parfaite commence ici.",
        image: '/photo_08.jpg',
        date: 'Mars 2025',
        category: 'Guide',
    },
];

const categories = ['Annonce', 'Technique', 'Média', 'Recap', 'Communauté', 'Guide'];
const selectedCategories = ref<string[]>([]);

const filteredNews = computed(() => {
    if (selectedCategories.value.length === 0) {
        return newsList;
    }
    return newsList.filter((item) =>
        selectedCategories.value.includes(item.category),
    );
});

function toggleCategory(cat: string) {
    const idx = selectedCategories.value.indexOf(cat);
    if (idx === -1) {
        selectedCategories.value.push(cat);
    } else {
        selectedCategories.value.splice(idx, 1);
    }
    // Si toutes les catégories sont sélectionnées = aucun filtre
    if (selectedCategories.value.length === categories.length) {
        selectedCategories.value = [];
    }
}
</script>

<template>
    <Head title="Actualités" />

    <Header />

    <div class="relative z-10 min-h-[120vh] overflow-hidden rounded-b-[6rem] bg-black">
        <div
            class="pointer-events-none absolute inset-0 bg-linear-to-b from-black via-black to-black"
        ></div>
        <div
            class="pointer-events-none absolute -top-32 left-1/2 h-115 w-[130%] -translate-x-1/2 rounded-full bg-[#c80a45]/18 blur-[150px]"
        ></div>
        <div
            class="pointer-events-none absolute inset-0 bg-[url('/noise.png')] opacity-[0.04] mix-blend-soft-light"
        ></div>

        <main class="relative z-10 mx-auto max-w-6xl px-6 pt-34 pb-24 md:px-10 lg:px-14">

            <!-- Page header -->
            <section class="mb-10 space-y-3 text-center md:text-left">
                <p class="text-xs font-bold tracking-[0.35em] text-[#c80a45] uppercase">
                    Actualités
                </p>
                <h1 class="font-chillax text-5xl leading-[0.92] text-white md:text-7xl lg:text-8xl">
                    News & Stories
                </h1>
                <p class="max-w-2xl text-sm text-gray-300 md:text-base">
                    Annonces, coulisses, aftermovies et portraits — tout ce qui fait vivre Symbiosa en dehors des nuits.
                </p>
            </section>

            <!-- Category filters -->
            <div class="mb-10 flex flex-wrap items-center gap-1.5">
                <span class="mr-1 text-[10px] font-bold tracking-[0.2em] text-gray-500 uppercase">Catégorie</span>
                <button
                    type="button"
                    @click="selectedCategories = []"
                    :class="[
                        'cursor-pointer rounded-full border px-2.5 py-1 text-[10px] font-bold tracking-[0.18em] uppercase transition-colors',
                        selectedCategories.length === 0
                            ? 'border-[#c80a45] bg-[#c80a45]/20 text-white'
                            : 'border-white/15 bg-white/5 text-gray-300 hover:border-white/30',
                    ]"
                >
                    All
                </button>
                <button
                    v-for="cat in categories"
                    :key="cat"
                    type="button"
                    @click="toggleCategory(cat)"
                    :class="[
                        'cursor-pointer rounded-full border px-2.5 py-1 text-[10px] font-bold tracking-[0.18em] uppercase transition-colors',
                        selectedCategories.includes(cat)
                            ? 'border-[#c80a45] bg-[#c80a45]/20 text-white'
                            : 'border-white/15 bg-white/5 text-gray-300 hover:border-white/30',
                    ]"
                >
                    {{ cat }}
                </button>
            </div>

            <!-- News grid -->
            <section
                v-if="filteredNews.length"
                class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3"
            >
                <Link
                    v-for="news in filteredNews"
                    :key="news.id"
                    href="#"
                    class="group relative flex flex-col overflow-hidden rounded-2xl border border-white/8 bg-white/2 transition-all duration-300 hover:border-white/18 hover:bg-white/5 hover:shadow-[0_0_40px_rgba(200,10,69,0.1)]"
                >
                    <!-- Image -->
                    <div class="relative aspect-16/10 overflow-hidden">
                        <img
                            :src="news.image"
                            :alt="news.title"
                            class="h-full w-full object-cover transition-all duration-700 group-hover:scale-105 group-hover:brightness-110"
                        />
                        <div class="absolute inset-0 bg-linear-to-t from-black/60 to-transparent"></div>
                        <span
                            class="absolute left-4 top-4 rounded-full border border-[#c80a45]/60 bg-[#c80a45]/30 px-3 py-1 text-[10px] font-bold tracking-[0.18em] text-white uppercase backdrop-blur-sm"
                        >
                            {{ news.category }}
                        </span>
                    </div>

                    <!-- Content -->
                    <div class="flex flex-1 flex-col gap-3 p-5">
                        <span class="text-[11px] font-medium text-gray-500">{{ news.date }}</span>
                        <h3
                            class="font-chillax text-lg leading-snug text-white transition-colors duration-300 group-hover:text-[#c80a45] md:text-xl"
                        >
                            {{ news.title }}
                        </h3>
                        <p class="line-clamp-3 text-sm leading-relaxed text-gray-400">
                            {{ news.description }}
                        </p>
                        <p class="mt-auto pt-2 text-[10px] font-bold tracking-[0.14em] text-[#c80a45] uppercase opacity-0 transition-opacity duration-200 group-hover:opacity-100">
                            Lire la suite →
                        </p>
                    </div>
                </Link>
            </section>

            <!-- Empty state -->
            <section
                v-else
                class="flex flex-col items-center justify-center py-20 text-center"
            >
                <h2 class="font-chillax text-3xl text-white md:text-4xl">
                    Aucun article trouvé
                </h2>
                <p class="mt-4 max-w-sm text-gray-400">
                    Essaie de désélectionner certains filtres.
                </p>
            </section>

        </main>
    </div>

    <Footer />
</template>













