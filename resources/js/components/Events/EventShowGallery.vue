<script setup lang="ts">
    import { useLightbox } from '@/composables/useLightbox';
    import EventShowLightbox from './EventShowLightbox.vue';

    interface Props {
        images: any[];
        eventSlug: string;
    }

    const props = defineProps<Props>();

    const {
        selectedImageIndex,
        isLightboxOpen,
        loadedImages,
        isLightboxImageLoaded,
        handleImageLoad,
        openLightbox,
        closeLightbox,
        nextImage,
        prevImage,
        downloadImage
    } = useLightbox(() => props.images);

    const onDownload = () => {
        downloadImage(`symbiosa-event-${props.eventSlug}`);
    };
</script>

<template>
    <section v-if="images?.length" class="mt-32 space-y-12">
        <div class="space-y-4">
            <p class="text-xs font-bold tracking-[0.35em] text-[#51A687] uppercase">
                Souvenirs
            </p>
            <h2 class="font-chillax text-4xl md:text-5xl text-white">
                Galerie Photo
            </h2>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div v-for="(image, index) in images" :key="image.id" @click="openLightbox(index)"
                class="aspect-square overflow-hidden rounded-2xl bg-white/5 border border-white/10 group cursor-zoom-in relative">
                <!-- Skeleton Placeholder -->
                <div class="absolute inset-0 bg-[#51A687]/5 animate-pulse"
                    :class="{ 'opacity-0': loadedImages.has(image.id) }"></div>

                <img :src="image.thumb" @load="handleImageLoad(image.id)"
                    class="h-full w-full object-cover transition-all duration-700 group-hover:scale-110 relative z-10"
                    loading="lazy" alt="Event gallery image" />
            </div>
        </div>

        <EventShowLightbox :is-open="isLightboxOpen" :images="images" :selected-index="selectedImageIndex"
            :is-image-loaded="isLightboxImageLoaded" @close="closeLightbox" @prev="prevImage" @next="nextImage"
            @download="onDownload" @load="isLightboxImageLoaded = true" />
    </section>
</template>
