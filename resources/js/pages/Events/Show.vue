<script setup lang="ts">
    import { Head, Link } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';
    import AppButton from '@/components/AppButton.vue';

    interface LineupArtist {
        name: string;
        time: string;
        image: string;
        genres: string[];
        link?: string;
    }

    interface PracticalInfo {
        label: string;
        value: string;
    }

    interface EventDetails {
        title: string;
        type: string;
        genres: string[];
        date: string;
        isoDate: string;
        location: string;
        fullLocation: string;
        image: string;
        description: string;
        lineup: LineupArtist[];
        practicalInfo: PracticalInfo[];
        startingPrice: string;
    }

    const props = defineProps<{
        slug: string;
    }>();

    const sponsors = [
        { logo: '/sponsors/logo_dark_01.svg', link: '#' },
        { logo: '/sponsors/logo_dark_02.svg', link: '#' },
        { logo: '/sponsors/logo_dark_03.svg', link: '#' },
        { logo: '/sponsors/logo_dark_04.svg', link: '#' },
        { logo: '/sponsors/logo_dark_05.svg', link: '#' },
        { logo: '/sponsors/logo_dark_06.svg', link: '#' },
        { logo: '/sponsors/logo_dark_07.svg', link: '#' },
        { logo: '/sponsors/logo_dark_08.svg', link: '#' },
        { logo: '/sponsors/logo_dark_09.svg', link: '#' },
    ];

    const faqs = [
        {
            question: "Puis-je acheter mes tickets sur place ?",
            answer: "Oui, si l'évènement n'est pas complet. Mais les prix seront majorés, nous recommandons vivement les préventes qui vous offrent la sécurité."
        },
        {
            question: "Peut-on sortir et rentrer à nouveau ?",
            answer: "Non, la sortie est définitive. Si vous devez bénéficier d'une exception, pour une raison valable tel qu'un besoin médical, veuillez en informer la sécurité à votre arrivée."
        },
        {
            question: "Comment peut-on payer sur place ?",
            answer: "La vente de boissons se fait par le biais de tickets, ceux-ci peuvent être achetés par carte ou en liquide."
        },
        {
            question: "Est ce que les bouchons d'oreille sont disponibles ?",
            answer: "Bien que nous en distribuons gratuitement sur place, nous vous recommandons de vous munir de la paire qui vous conviens le mieux afin de profiter au maximum de l'expérience."
        },
        {
            question: "Puis-je revendre mon billet ?",
            answer: "Les billets sont nominatif, le seul moyen de le revendre est par le biais de notre plateforme. Connectez-vous au compte avec lequel vous avez effectuer l'achat afin de remettre vos place en vente."
        },
        {
            question: "Que se passe t'il si la soirée est annulée ?",
            answer: "Si la soirée est annulée par notre faute, nous mettrons tout en place pour vous indemniser dans les plus bref délais. Nous recommandons cependant de prendre l'assurance annulation sur vos billets dans le cas ou l'annulation ne dépend pas de nous (pandémie, artiste malade, ...)"
        },
        {
            question: "Puis-je amener mes propres boissons/nourriture ?",
            answer: "Non, aucune consomation exterieur ne sera accèptée, il vous sera demandé de les laisser a l'entrée."
        }
    ];

    // Mock data - in a real app, this would come from props or an API
    const event: EventDetails = {
        title: 'EDEN Opening',
        type: 'Festival',
        genres: ['House', 'Techno'],
        date: 'Samedi 28 Octobre 2026',
        isoDate: '2026-10-28T20:00:00+02:00',
        location: 'Gembloux, Belgique',
        fullLocation: 'Espace Magnum, Chaussée de Namur 123, 5030 Gembloux',
        image: '/eden_poster_light.png',
        description: `
<p>Plongez dans l'univers d'<strong>EDEN</strong>, une expérience immersive unique où la nature rencontre les rythmes électroniques. Pour ce premier événement de la saison, nous vous préparons une scénographie exceptionnelle et une programmation House & Techno de premier choix.</p>
<h3>Une Scénographie Inédite</h3>
<p>L'Espace Magnum sera transformé pour l'occasion en un <em>jardin futuriste</em>, mêlant végétation luxuriante et installations lumineuses de pointe. Venez vibrer au son des meilleurs artistes de la scène actuelle dans un cadre hors du commun.</p>
<ul>
    <li>Des espaces chill-out repensés pour la détente</li>
    <li>Un système son massif optimisé pour la techno</li>
    <li>Des shows visuels et lasers à couper le souffle</li>
</ul>
<h3>La Vision EDEN</h3>
<p>Notre but est de créer plus qu'une simple soirée : un véritable sanctuaire où la bienveillance, la musique et le respect de notre environnement cohabitent. Préparez-vous à une aventure sensorielle où chaque détail a été pensé pour vous déconnecter du quotidien.</p>
`,
        lineup: [
            { name: 'Koda b2b Olvr', time: '02:00', image: '/artists/koda_olvr.png', genres: ['Hard Techno'], link: 'https://www.instagram.com/kodaa_music/' },
            { name: 'NoID', time: '00:00', image: '/artists/artist_picture_02.png', genres: ['Industrial', 'Techno', 'Dark'] },
            { name: 'Biname', time: '22:00', image: '/artists/artist_picture_03.png', genres: ['Techno', 'Acid'] },
            { name: 'Omdat Het Kan & Average Rob', time: '20:00', image: '/artists/artist_picture_04.png', genres: ['House'] },
        ],
        practicalInfo: [
            { label: 'Date', value: '28 Octobre 2026' },
            { label: 'Heures', value: '20:00 — 04:00' },
            { label: 'Lieu', value: 'Espace Magnum, Gembloux' },
            { label: 'Âge minimum', value: '18+' },
            { label: 'Dress Code', value: 'Casual Chic' },
        ],
        startingPrice: '25€'
    };
</script>

<template>

    <Head :title="event.title" />

    <Header />

    <div class="relative z-10 rounded-b-[6rem] bg-black min-h-screen">
        <!-- Hero Banner Section -->
        <section class="relative h-[85vh] w-full overflow-hidden">
            <img src="/origins/poster_background_landscape.png" class="absolute inset-0 h-full w-full object-cover"
                alt="" />

            <!-- Overlays -->
            <div class="absolute inset-0 bg-linear-to-t from-black via-black/40 to-black/20"></div>
            <div class="absolute inset-0 bg-[url('/noise.png')] opacity-[0.05] mix-blend-soft-light"></div>

            <div class="relative z-10 flex h-full flex-col items-center justify-end pb-32 text-center px-6">
                <div class="space-y-12 max-w-4xl">
                    <div class="flex flex-wrap justify-center gap-3">
                        <span
                            class="rounded-full border border-[#51A687]/40 bg-[#51A687]/15 px-4 py-1.5 text-[10px] font-bold tracking-[0.25em] text-white uppercase backdrop-blur-md">
                            {{ event.type }}
                        </span>
                        <span v-for="genre in event.genres" :key="genre"
                            class="rounded-full border border-white/20 bg-white/5 px-4 py-1.5 text-[10px] font-bold tracking-[0.25em] text-gray-200 uppercase backdrop-blur-md">
                            {{ genre }}
                        </span>
                    </div>

                    <h1 class="font-chillax text-7xl md:text-9xl text-white leading-[0.85] tracking-tight uppercase">
                        {{ event.title }}
                    </h1>

                    <div class="grid grid-cols-2 gap-16 pt-16 max-w-2xl mx-auto w-full">
                        <!-- Date Column -->
                        <div class="flex flex-col items-center gap-4 text-center group">
                            <div
                                class="h-12 w-12 flex items-center justify-center rounded-full bg-white/5 border border-white/10 text-[#51A687]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                </svg>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-medium tracking-[0.2em] text-white/50 uppercase">Samedi</span>
                                <span class="text-2xl font-chillax text-white uppercase">
                                    28 Octobre<br />
                                    2026
                                </span>
                            </div>
                        </div>

                        <!-- Location Column -->
                        <div class="flex flex-col items-center gap-4 text-center group">
                            <div
                                class="h-12 w-12 flex items-center justify-center rounded-full bg-white/5 border border-white/10 text-[#51A687]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                                </svg>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-2xl font-chillax text-white uppercase">Gembloux</span>
                                <span
                                    class="text-xs font-medium tracking-[0.2em] text-white/50 uppercase">Belgique</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <main class="relative z-10 mx-auto max-w-7xl px-6 pb-24 md:px-10 lg:px-14">
            <!-- Action Bar -->
            <div class="relative -translate-y-1/2 z-20 flex justify-center px-4">
                <AppButton href="#" variant="primary" size="lg"
                    class="w-full sm:w-auto shadow-2xl shadow-[#51A687]/20 border-[#51A687]/50 bg-[#51A687]/10 backdrop-blur-xl hover:bg-[#51A687]/20">
                    Réserver mes places
                </AppButton>
            </div>

            <section class="mt-12 grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-24">
                <!-- Description & Lineup -->
                <div class="lg:col-span-8 space-y-16">
                    <div class="space-y-6">
                        <h2 class="font-chillax text-4xl text-white">À propos</h2>
                        <div class="prose prose-invert prose-lg max-w-none prose-headings:font-chillax prose-headings:font-normal prose-p:text-gray-400 prose-li:text-gray-400 prose-strong:text-white prose-em:text-gray-200"
                            v-html="event.description">
                        </div>
                    </div>

                    <div class="space-y-12">
                        <h2 class="font-chillax text-4xl text-white">Line-up</h2>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <component :is="artist.link ? 'a' : 'div'" v-for="(artist, index) in event.lineup"
                                :key="artist.name" :href="artist.link" :target="artist.link ? '_blank' : undefined"
                                :rel="artist.link ? 'noopener noreferrer' : undefined" :class="[
                                    'relative overflow-hidden group bg-[#052519] transition-all duration-500',
                                    'rounded-tl-[3rem] rounded-br-[3rem]',
                                    artist.link ? 'cursor-pointer' : 'cursor-default',
                                    (event.lineup.length % 2 !== 0 && index === 0) ||
                                        (event.lineup.length % 2 === 0 && (index === 0 || index === 1))
                                        ? 'md:col-span-2 h-80 md:h-96' : 'h-80'
                                ]">
                                <!-- Artist Image - Clean and visible -->
                                <img :src="artist.image" :alt="artist.name"
                                    class="absolute inset-0 h-full w-full object-cover transition-all duration-700 group-hover:scale-105" />

                                <!-- Luminous Overlays - More vibrant and light -->
                                <div class="absolute inset-0 bg-[#51A687]/15 mix-blend-overlay transition-opacity">
                                </div>
                                <div
                                    class="absolute inset-0 bg-linear-to-t from-[#51A687]/50 via-transparent to-transparent opacity-60 group-hover:opacity-30 transition-opacity">
                                </div>
                                <div class="absolute inset-0 bg-linear-to-t from-black/60  to-transparent">
                                </div>

                                <!-- Backlight Glow -->
                                <div
                                    class="absolute -bottom-12 -left-12 h-48 w-48 rounded-full bg-[#51A687]/30 blur-[60px] opacity-40 group-hover:opacity-100 transition-all duration-700">
                                </div>

                                <!-- Genre Badges (Strict Hero Style) -->
                                <div class="absolute top-6 right-8 flex flex-wrap gap-2 justify-end max-w-[70%]">
                                    <span v-for="genre in artist.genres" :key="genre"
                                        class="rounded-full border border-white/20 bg-white/5 px-4 py-1.5 text-[10px] font-bold tracking-[0.25em] text-gray-200 uppercase backdrop-blur-md">
                                        {{ genre }}
                                    </span>
                                </div>

                                <!-- Artist Info -->
                                <div class="absolute bottom-8 left-8 right-32 pointer-events-none">
                                    <h3 :class="[
                                        'font-chillax text-white tracking-tighter leading-tight font-normal drop-shadow-md',
                                        (event.lineup.length % 2 !== 0 && index === 0) ||
                                            (event.lineup.length % 2 === 0 && (index === 0 || index === 1))
                                            ? 'text-4xl md:text-6xl' : 'text-2xl md:text-4xl'
                                    ]">
                                        <template v-if="artist.name.includes(' b2b ')">
                                            <span>{{ artist.name.split(' b2b ')[0] }}</span>
                                            <span
                                                class="text-xl md:text-3xl lowercase  font-semibold mx-4 align-middle">b2b</span>
                                            <span>{{ artist.name.split(' b2b ')[1] }}</span>
                                        </template>
                                        <template v-else>
                                            {{ artist.name }}
                                        </template>
                                    </h3>
                                </div>

                                <!-- Time Label -->
                                <div class="absolute bottom-8 right-8 text-right">
                                    <p
                                        class="font-chillax text-xl md:text-2xl text-white/80 font-semibold tracking-widest group-hover:text-white transition-colors">
                                        {{ artist.time }}
                                    </p>
                                </div>
                            </component>
                        </div>
                    </div>
                </div>

                <!-- Sidebar / Practical Info -->
                <div class="lg:col-span-4 space-y-6 relative">
                    <!-- Practical Info Card -->
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-8 space-y-8">
                        <h3 class="font-chillax text-2xl text-white uppercase tracking-wider">Infos Pratiques</h3>
                        <div class="space-y-6">
                            <div v-for="info in event.practicalInfo" :key="info.label" class="space-y-1">
                                <p class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">{{ info.label
                                    }}</p>
                                <p class="text-white font-medium">{{ info.value }}</p>
                            </div>
                        </div>
                        <AppButton href="#" variant="outline" size="md" class="w-full">
                            Voir sur Google Maps
                        </AppButton>
                    </div>

                    <!-- Ticketing Card -->
                    <div class="sticky top-32 rounded-3xl border border-white/10 bg-white/5 p-8 space-y-8 shadow-2xl">
                        <div class="space-y-1">
                            <h3 class="font-chillax text-2xl text-white uppercase tracking-wider">Billetterie</h3>
                        </div>

                        <div class="space-y-2">
                            <p class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">À partir de</p>
                            <p class="text-3xl font-chillax text-white">{{ event.startingPrice }}</p>
                        </div>

                        <AppButton href="#" variant="outline" size="md" class="w-full">
                            Acheter ma place
                        </AppButton>
                    </div>
                </div>
            </section>

            <!-- Sponsors Section -->
            <section class="mt-32 space-y-10">
                <p class="text-center text-[10px] font-bold tracking-[0.3em] text-white/40 uppercase">
                    Cet événement ne serait pas possible sans nos sponsors
                </p>

                <div class="relative flex overflow-hidden marquee-container">
                    <div class="marquee-content flex items-center gap-20 py-4 pr-20 shrink-0">
                        <!-- First set of logos -->
                        <a v-for="(sponsor, index) in sponsors" :key="'s1-' + index" :href="sponsor.link"
                            target="_blank" class="shrink-0 transition-transform duration-300 hover:scale-110">
                            <img :src="sponsor.logo"
                                class="h-10 w-auto opacity-80 fill-white transition-all duration-300 hover:opacity-100"
                                alt="Sponsor Logo" />
                        </a>
                    </div>
                    <div class="marquee-content flex items-center gap-20 py-4 pr-20 shrink-0" aria-hidden="true">
                        <!-- Second set for seamless loop -->
                        <a v-for="(sponsor, index) in sponsors" :key="'s2-' + index" :href="sponsor.link"
                            target="_blank" class="shrink-0 transition-transform duration-300 hover:scale-110">
                            <img :src="sponsor.logo"
                                class="h-10 w-auto opacity-80 fill-white transition-all duration-300 hover:opacity-100"
                                alt="Sponsor Logo" />
                        </a>
                    </div>
                </div>
            </section>

            <!-- FAQ Section -->
            <section class="mt-40 mb-20">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-12 lg:gap-20">
                    <!-- Left Column -->
                    <div class="md:col-span-5 space-y-2">
                        <p
                            class="font-chillax text-2xl text-white uppercase tracking-wider text-center md:text-right leading-tight">
                            Une question ?</p>
                        <h2
                            class="font-chillax text-4xl md:text-5xl lg:text-7xl text-[#51A687] leading-[0.9] uppercase tracking-tighter text-center md:text-right">
                            ON PEUT VOUS AIDER
                        </h2>
                    </div>

                    <!-- Right Column -->
                    <div class="md:col-span-7 space-y-10 max-w-2xl">
                        <div v-for="(faq, index) in faqs" :key="index" class="space-y-3">
                            <h3 class="font-chillax text-lg text-white uppercase tracking-wide">
                                {{ faq.question }}
                            </h3>
                            <p class="text-gray-400 leading-relaxed">
                                {{ faq.answer }}
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <Footer />
</template>

<style scoped>
.marquee-content {
    animation: marquee 40s linear infinite;
}

.marquee-container:hover .marquee-content {
    animation-play-state: paused;
}

@keyframes marquee {
    0% {
        transform: translateX(0);
    }

    100% {
        transform: translateX(-100%);
    }
}
</style>
