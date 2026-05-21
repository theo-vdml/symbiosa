<script setup lang="ts">
    import { computed, ref } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';
    import AppButton from '@/components/AppButton.vue';
    import SponsorMarquee from '@/components/SponsorMarquee.vue';
    import EventFaq from '@/components/EventFaq.vue';
    import { Calendar, MapPin, X, ChevronLeft, ChevronRight, Download } from '@lucide/vue';
    import events from '@/routes/events';
    import SeoMeta from '@/components/SeoMeta.vue';
    import { Seo } from '@/types/seo';
    import { useEventDates } from '@/composables/useEventDates';
    import { useGoogleMapsUrl } from '@/composables/useGoogleMapsUrl';

    const props = defineProps<{
        event: Event;
        seo: Seo;
    }>();

    const eventDates = useEventDates(() => props.event);
    const addressHref = useGoogleMapsUrl(() => props.event.address)


    const getPerformanceTime = (time: string) => {
        return time.substring(0, 5).replace(':', 'h');
    };

    // Lightbox State
    const selectedImageIndex = ref<number | null>(null);
    const isLightboxOpen = computed(() => selectedImageIndex.value !== null);
    const loadedImages = ref<Set<number>>(new Set());
    const isLightboxImageLoaded = ref(false);

    const handleImageLoad = (id: number) => {
        loadedImages.value.add(id);
    };

    const openLightbox = (index: number) => {
        isLightboxImageLoaded.value = false;
        selectedImageIndex.value = index;
        if (typeof document !== 'undefined') {
            document.body.style.overflow = 'hidden';
        }
    };

    const closeLightbox = () => {
        selectedImageIndex.value = null;
        if (typeof document !== 'undefined') {
            document.body.style.overflow = '';
        }
    };

    const nextImage = () => {
        if (selectedImageIndex.value === null || !props.event.gallery_urls) return;
        isLightboxImageLoaded.value = false;
        selectedImageIndex.value = (selectedImageIndex.value + 1) % props.event.gallery_urls.length;
    };

    const prevImage = () => {
        if (selectedImageIndex.value === null || !props.event.gallery_urls) return;
        isLightboxImageLoaded.value = false;
        selectedImageIndex.value = (selectedImageIndex.value - 1 + props.event.gallery_urls.length) % props.event.gallery_urls.length;
    };

    const downloadImage = async () => {
        if (selectedImageIndex.value === null || !props.event.gallery_urls) return;

        try {
            const imageUrl = props.event.gallery_urls[selectedImageIndex.value].url;
            const response = await fetch(imageUrl);
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);

            const link = document.createElement('a');
            link.href = url;
            link.download = `symbiosa-event-${props.event.slug}-${selectedImageIndex.value + 1}.jpg`;
            document.body.appendChild(link);
            link.click();

            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        } catch (error) {
            console.error('Download failed:', error);
            // Fallback to opening in new tab if fetch fails (e.g. CORS)
            const imageUrl = props.event.gallery_urls[selectedImageIndex.value].url;
            window.open(imageUrl, '_blank');
        }
    };

    // Keyboard navigation
    if (typeof window !== 'undefined') {
        window.addEventListener('keydown', (e) => {
            if (!isLightboxOpen.value) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowRight') nextImage();
            if (e.key === 'ArrowLeft') prevImage();
        });
    }

</script>

<template>
    <SeoMeta :seo="seo" />

    <Header />

    <div class="relative z-10 rounded-b-[3rem] lg:rounded-b-[6rem] bg-black min-h-screen">

        <!-- Hero Banner Section -->
        <section class="relative h-[85vh] w-full overflow-hidden">
            <template v-if="event.background_url">
                <img :src="event.background_url" :srcset="event.background_responsive?.srcset"
                    sizes="(max-width: 768px) 200vw, 100vw" class="absolute inset-0 h-full w-full object-cover"
                    alt="" />
                <div class="absolute inset-0 bg-linear-to-t from-black via-black/40 to-black/20"></div>
                <div class="absolute inset-0 bg-[url('/noise.png')] opacity-[0.05] mix-blend-soft-light"></div>
            </template>

            <template v-else>
                <div class="absolute inset-0 bg-linear-to-b from-[#51A687]/30 to-transparent"></div>
            </template>

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
                                <span class="text-xs font-medium tracking-[0.2em] text-white/50 uppercase">
                                    {{ eventDates.startWeekday }}
                                </span>
                                <span class="text-2xl font-chillax text-white uppercase">
                                    {{ eventDates.startLong }}
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
                                <span class="text-xs font-medium tracking-[0.2em] text-white/50 uppercase">
                                    {{ event.country }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <main class="relative z-10 mx-auto max-w-7xl px-6 pb-24 md:px-10 lg:px-14">
            <!-- Action Bar -->
            <div v-if="event.ticketing_status === 'open' && !event.is_visible_in_archives"
                class="relative -translate-y-1/2 z-20 flex justify-center px-4">
                <AppButton :href="events.ticketing(event.slug).url" variant="primary" size="lg"
                    class="w-full sm:w-auto border-[#51A687]/50 bg-[#51A687]/10 backdrop-blur-xl hover:bg-[#51A687]/20">
                    Réserver mes places
                </AppButton>
            </div>

            <section class="mt-12 grid grid-cols-1 lg:grid-cols-12 gap-16 lg:gap-24">
                <!-- Description & Lineup -->
                <div :class="[event.is_visible_in_archives ? 'lg:col-span-12' : 'lg:col-span-8', 'space-y-16']">
                    <div class="space-y-6">
                        <h2 class="font-chillax text-4xl text-white">À propos</h2>
                        <div class="prose prose-invert prose-lg max-w-none prose-headings:font-chillax prose-headings:font-normal prose-p:text-gray-400 prose-li:text-gray-400 prose-strong:text-white prose-em:text-gray-200"
                            v-html="event.body">
                        </div>
                    </div>

                    <div v-if="event.artists && event.artists.length" class="space-y-12">
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
                                <img :src="artist.portrait_url" :alt="artist.name"
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
                                <div v-if="artist.pivot.performance_time" class="absolute bottom-8 right-8 text-right">
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
                <div v-if="!event.is_visible_in_archives" class="lg:col-span-4 space-y-6 relative">
                    <!-- Practical Info Card -->
                    <div class="rounded-3xl border border-white/10 bg-white/5 p-8 space-y-8">
                        <h3 class="font-chillax text-2xl text-white uppercase tracking-wider">Infos Pratiques</h3>
                        <div class="space-y-6">
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Date</p>
                                <p class="text-white font-medium">
                                    {{ eventDates.startWeekday }} {{ eventDates.startLong }}
                                </p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Heures</p>
                                <p class="text-white font-medium">
                                    {{ eventDates.startTime }} - {{ eventDates.endTime }}
                                </p>
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
                            <div class="space-y-1" v-if="event.address && event.address !== ''">
                                <p class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Lieu</p>
                                <p class="text-white font-medium">{{ event.address }}</p>
                            </div>
                            <AppButton v-if="addressHref" :href="addressHref" variant="outline" size="md"
                                rel="noopener noreferrer" class="w-full" target="_blank" external>
                                Voir sur Maps
                            </AppButton>
                        </div>
                    </div>

                    <!-- Ticketing Card -->
                    <div v-if="event.ticketing_status !== 'none'"
                        class="sticky top-32 rounded-3xl border border-white/10 bg-white/5 p-8 space-y-8 shadow-2xl">
                        <div class="space-y-1">
                            <h3 class="font-chillax text-2xl text-white uppercase tracking-wider">Billetterie</h3>
                        </div>

                        <div v-if="event.ticketing_status === 'open'">
                            <AppButton :href="events.ticketing(event.slug).url" variant="outline" size="md"
                                class="w-full">
                                Acheter ma place
                            </AppButton>
                        </div>

                        <div v-else-if="event.ticketing_status === 'coming_soon'" class="text-center">
                            <p class="text-gray-400 text-sm">
                                La billetterie n'est pas encore ouverte.
                            </p>
                        </div>

                        <div v-else-if="event.ticketing_status === 'closed'" class="text-center">
                            <p class="text-gray-400 text-sm">
                                La billetterie est désormais fermée.
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Gallery Section -->
            <section v-if="event.is_visible_in_archives && event.gallery_urls?.length" class="mt-32 space-y-12">
                <div class="space-y-4">
                    <p class="text-xs font-bold tracking-[0.35em] text-[#51A687] uppercase">
                        Souvenirs
                    </p>
                    <h2 class="font-chillax text-4xl md:text-5xl text-white">
                        Galerie Photo
                    </h2>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <div v-for="(image, index) in event.gallery_urls" :key="image.id" @click="openLightbox(index)"
                        class="aspect-square overflow-hidden rounded-2xl bg-white/5 border border-white/10 group cursor-zoom-in relative">
                        <!-- Skeleton Placeholder -->
                        <div class="absolute inset-0 bg-[#51A687]/5 animate-pulse"
                            :class="{ 'opacity-0': loadedImages.has(image.id) }"></div>

                        <img :src="image.thumb" @load="handleImageLoad(image.id)"
                            class="h-full w-full object-cover transition-all duration-700 group-hover:scale-110 relative z-10"
                            loading="lazy" alt="Event gallery image" />
                    </div>
                </div>
            </section>

            <!-- Lightbox -->
            <Teleport to="body">
                <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0"
                    enter-to-class="opacity-100" leave-active-class="transition duration-200 ease-in"
                    leave-from-class="opacity-100" leave-to-class="opacity-0">
                    <div v-if="isLightboxOpen"
                        class="fixed inset-0 z-100 flex items-center justify-center bg-black/95 px-4">
                        <!-- Close button -->
                        <button @click="closeLightbox"
                            class="absolute top-6 right-6 z-110 rounded-full bg-white/10 p-3 text-white backdrop-blur-md transition-colors hover:bg-white/20">
                            <X class="h-6 w-6" />
                        </button>

                        <!-- Download button -->
                        <button @click="downloadImage"
                            class="absolute top-6 right-24 z-110 flex items-center gap-2 rounded-full bg-[#51A687] px-4 py-2.5 text-xs font-bold tracking-widest text-white uppercase transition-transform hover:scale-105 active:scale-95">
                            <Download class="h-4 w-4" />
                            <span>Télécharger</span>
                        </button>

                        <!-- Navigation -->
                        <button @click.stop="prevImage"
                            class="absolute left-6 z-110 rounded-full bg-white/5 p-4 text-white backdrop-blur-md transition-colors hover:bg-white/10 hidden md:block">
                            <ChevronLeft class="h-8 w-8" />
                        </button>

                        <button @click.stop="nextImage"
                            class="absolute right-6 z-110 rounded-full bg-white/5 p-4 text-white backdrop-blur-md transition-colors hover:bg-white/10 hidden md:block">
                            <ChevronRight class="h-8 w-8" />
                        </button>

                        <!-- Image Container -->
                        <div class="relative max-h-[85vh] max-w-5xl" @click.stop>
                            <!-- Lightbox Loading State -->
                            <div v-if="!isLightboxImageLoaded"
                                class="absolute inset-0 flex items-center justify-center">
                                <div
                                    class="h-12 w-12 animate-spin rounded-full border-4 border-[#51A687]/20 border-t-[#51A687]">
                                </div>
                            </div>

                            <img v-if="event.gallery_urls" :src="event.gallery_urls[selectedImageIndex!].url"
                                @load="isLightboxImageLoaded = true"
                                class="max-h-[85vh] w-full object-contain shadow-2xl transition-opacity duration-300"
                                :class="isLightboxImageLoaded ? 'opacity-100' : 'opacity-0'" alt="" />

                            <!-- Counter -->
                            <div
                                class="absolute -bottom-10 left-1/2 -translate-x-1/2 text-white/50 text-sm font-medium">
                                {{ selectedImageIndex! + 1 }} / {{ event.gallery_urls?.length }}
                            </div>
                        </div>

                        <!-- Mobile navigation overlay -->
                        <div class="absolute inset-y-0 left-0 w-1/4 md:hidden" @click="prevImage"></div>
                        <div class="absolute inset-y-0 right-0 w-1/4 md:hidden" @click="nextImage"></div>
                    </div>
                </Transition>
            </Teleport>

            <!-- Sponsors Section -->
            <section class="mt-32 space-y-10">
                <p class="text-center text-[10px] font-bold tracking-[0.3em] text-white/40 uppercase">
                    Cet événement ne serait pas possible sans nos sponsors
                </p>

                <SponsorMarquee v-if="event.sponsors?.length" :sponsors="event.sponsors" />
            </section>

            <!-- FAQ Section -->
            <EventFaq v-if="!event.is_visible_in_archives" :faq="event.faq" />
        </main>
    </div>

    <Footer />
</template>

<style scoped></style>
