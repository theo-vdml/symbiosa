<script setup lang="ts">
    import { Head, Link } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';
    import AppButton from '@/components/AppButton.vue';
    import { Calendar, MapPin } from '@lucide/vue';

    interface Genre {
        id: number;
        name: string;
        slug: string;
    }

    interface Event {
        id: number;
        title: string;
        slug: string;
        date: string;
        start_time: string;
        end_time: string;
        city: string;
        country: string;
        address: string;
        dress_code: string;
        minimum_age: number;
        description: string;
        background: string;
        poster: string;
        created_at: string;
        updated_at: string;
        faq: {
            question: string;
            answer: string;
        }[];
        genres?: Genre[];
        sponsors?: {
            name: string;
            logo: string;
            website: string;
        }[];
        artists: {
            name: string;
            thumbnail: string;
            website: string;
            genres?: Genre[];
            biography: string;
            pivot: {
                performance_time: string;
                sort_order: number;
            }
        }[]
    }

    const props = defineProps<{
        event: Event;
    }>();

    interface LineupArtist {
        name: string;
        time: string;
        image: string;
        genres: string[];
        link?: string;
    }

    const lineup: LineupArtist[] = [
        { name: 'Koda b2b Olvr', time: '02:00', image: '/artists/koda_olvr.png', genres: ['Hard Techno'], link: 'https://www.instagram.com/kodaa_music/' },
        { name: 'NoID', time: '00:00', image: '/artists/artist_picture_02.png', genres: ['Industrial', 'Techno', 'Dark'] },
        { name: 'Biname', time: '22:00', image: '/artists/artist_picture_03.png', genres: ['Techno', 'Acid'] },
        { name: 'Omdat Het Kan & Average Rob', time: '20:00', image: '/artists/artist_picture_04.png', genres: ['House'] },
    ]

    const getWeekday = (dateStr: string) => {
        const date = new Date(dateStr);
        return date.toLocaleDateString('fr-FR', { weekday: 'long' });
    };

    const getDateFormatted = (dateStr: string) => {
        const date = new Date(dateStr);
        return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
    };

    const getOpeningHours = (startTime: string, endTime: string) => {
        const start = new Date(`1970-01-01T${startTime}Z`);
        const end = new Date(`1970-01-01T${endTime}Z`);

        const options: Intl.DateTimeFormatOptions = {
            hour: '2-digit',
            minute: '2-digit',
        };

        return `${start.toLocaleTimeString('fr-FR', options)} - ${end.toLocaleTimeString('fr-FR', options)}`;
    };

    const getPerformanceTime = (time: string) => {
        return time.substring(0, 5).replace(':', 'h');
    };

</script>

<template>

    <Head :title="event.title" />

    <Header />

    <div class="relative z-10 rounded-b-[6rem] bg-black min-h-screen">
        <!-- Hero Banner Section -->
        <section class="relative h-[85vh] w-full overflow-hidden">
            <img :src="'/' + event.background" class="absolute inset-0 h-full w-full object-cover" alt="" />

            <!-- Overlays -->
            <div class="absolute inset-0 bg-linear-to-t from-black via-black/40 to-black/20"></div>
            <div class="absolute inset-0 bg-[url('/noise.png')] opacity-[0.05] mix-blend-soft-light"></div>

            <div class="relative z-10 flex h-full flex-col items-center justify-end pb-32 text-center px-6">
                <div class="space-y-12 max-w-4xl">
                    <div class="flex flex-wrap justify-center gap-3">
                        <span v-for="genre in event.genres" :key="genre.id"
                            class="rounded-full border border-white/20 bg-white/5 px-4 py-1.5 text-[10px] font-bold tracking-[0.25em] text-gray-200 uppercase backdrop-blur-md">
                            {{ genre.name }}
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
                                <Calendar class="w-6 h-6" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-medium tracking-[0.2em] text-white/50 uppercase">{{
                                    getWeekday(event.date) }}</span>
                                <span class="text-2xl font-chillax text-white uppercase">
                                    {{ getDateFormatted(event.date) }}
                                </span>
                            </div>
                        </div>

                        <!-- Location Column -->
                        <div class="flex flex-col items-center gap-4 text-center group">
                            <div
                                class="h-12 w-12 flex items-center justify-center rounded-full bg-white/5 border border-white/10 text-[#51A687]">
                                <MapPin class="w-6 h-6" />
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-2xl font-chillax text-white uppercase">{{ event.city }}</span>
                                <span class="text-xs font-medium tracking-[0.2em] text-white/50 uppercase">{{
                                    event.country }}</span>
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
                            <component :is="artist.website ? 'a' : 'div'" v-for="(artist, index) in (event.artists)"
                                :key="artist.name" :href="artist.website"
                                :target="artist.website ? '_blank' : undefined"
                                :rel="artist.website ? 'noopener noreferrer' : undefined" :class="[
                                    'relative overflow-hidden group bg-[#052519] transition-all duration-500',
                                    'rounded-tl-[3rem] rounded-br-[3rem]',
                                    artist.website ? 'cursor-pointer' : 'cursor-default',
                                    (event.artists.length % 2 !== 0 && index === 0) ||
                                        (event.artists.length % 2 === 0 && (index === 0 || index === 1))
                                        ? 'md:col-span-2 h-80 md:h-96' : 'h-80'
                                ]">
                                <!-- Artist Image - Clean and visible -->
                                <img :src="'/' + artist.thumbnail" :alt="artist.name"
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
                                    <span v-for="genre in artist.genres" :key="genre.name"
                                        class="rounded-full border border-white/20 bg-white/5 px-4 py-1.5 text-[10px] font-bold tracking-[0.25em] text-gray-200 uppercase backdrop-blur-md">
                                        {{ genre.name }}
                                    </span>
                                </div>

                                <!-- Artist Info -->
                                <div class="absolute bottom-8 left-8 right-32 pointer-events-none">
                                    <h3 :class="[
                                        'font-chillax text-white tracking-tighter leading-tight font-normal drop-shadow-md',
                                        (event.artists.length % 2 !== 0 && index === 0) ||
                                            (event.artists.length % 2 === 0 && (index === 0 || index === 1))
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
                                        {{ getPerformanceTime(artist.pivot.performance_time) }}
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
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Date</p>
                                <p class="text-white font-medium">{{ getDateFormatted(event.date) }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Heures</p>
                                <p class="text-white font-medium">{{ getOpeningHours(event.start_time,
                                    event.end_time) }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Lieu</p>
                                <p class="text-white font-medium">{{ event.address }}</p>
                            </div>
                            <div class="space-y-1" v-if="event.minimum_age && event.minimum_age > 0">
                                <p class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Age Minimum
                                </p>
                                <p class="text-white font-medium">{{ event.minimum_age }}</p>
                            </div>
                            <div class="space-y-1" v-if="event.dress_code && event.dress_code !== ''">
                                <p class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Dress Code
                                </p>
                                <p class="text-white font-medium">{{ event.dress_code }}</p>
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
                            <p class="text-3xl font-chillax text-white">25€</p>
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
                        <a v-for="(sponsor, index) in event.sponsors" :key="'s1-' + index" :href="sponsor.website"
                            target="_blank" class="shrink-0 transition-transform duration-300 hover:scale-110">
                            <img :src="'/' + sponsor.logo"
                                class="h-10 w-auto opacity-80 fill-white transition-all duration-300 hover:opacity-100"
                                alt="Sponsor Logo" />
                        </a>
                    </div>
                    <div class="marquee-content flex items-center gap-20 py-4 pr-20 shrink-0" aria-hidden="true">
                        <!-- Second set for seamless loop -->
                        <a v-for="(sponsor, index) in event.sponsors" :key="'s2-' + index" :href="sponsor.website"
                            target="_blank" class="shrink-0 transition-transform duration-300 hover:scale-110">
                            <img :src="'/' + sponsor.logo"
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
                        <div v-for="(faq, index) in event.faq" :key="index" class="space-y-3">
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
