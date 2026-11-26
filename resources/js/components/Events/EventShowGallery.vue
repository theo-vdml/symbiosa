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
    downloadImage,
} = useLightbox(() => props.images);

const onDownload = () => {
    downloadImage(`symbiosa-event-${props.eventSlug}`);
};
</script>

<template>
    <section v-if="images?.length" class="mt-32 space-y-12">
        <div class="space-y-4">
            <p
                class="text-xs font-bold tracking-[0.35em] text-[#51A687] uppercase"
            >
                Souvenirs
            </p>
            <h2 class="font-chillax text-4xl text-white md:text-5xl">
                Galerie Photo
            </h2>
        </div>

        <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-4">
            <div
                v-for="(image, index) in images"
                :key="image.id"
                @click="openLightbox(index)"
                class="group relative aspect-square cursor-zoom-in overflow-hidden rounded-2xl border border-white/10 bg-white/5"
            >
                <!-- Skeleton Placeholder -->
                <div
                    class="absolute inset-0 animate-pulse bg-[#51A687]/5"
                    :class="{ 'opacity-0': loadedImages.has(image.id) }"
                ></div>

                <img
                    :src="image.thumb"
                    @load="handleImageLoad(image.id)"
                    class="relative z-10 h-full w-full object-cover transition-all duration-700 group-hover:scale-110"
                    loading="lazy"
                    :alt="'Image de la galerie #' + (index + 1)"
                />
            </div>
        </div>

        <EventShowLightbox
            :is-open="isLightboxOpen"
            :images="images"
            :selected-index="selectedImageIndex"
            :is-image-loaded="isLightboxImageLoaded"
            @close="closeLightbox"
            @prev="prevImage"
            @next="nextImage"
            @download="onDownload"
            @load="isLightboxImageLoaded = true"
        />
    </section>
</template>
