import { ref, computed } from 'vue';

export function useLightbox(images: () => any[] | undefined) {
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
        const imageList = images();
        if (selectedImageIndex.value === null || !imageList) return;
        isLightboxImageLoaded.value = false;
        selectedImageIndex.value = (selectedImageIndex.value + 1) % imageList.length;
    };

    const prevImage = () => {
        const imageList = images();
        if (selectedImageIndex.value === null || !imageList) return;
        isLightboxImageLoaded.value = false;
        selectedImageIndex.value = (selectedImageIndex.value - 1 + imageList.length) % imageList.length;
    };

    const downloadImage = async (filenamePrefix = 'symbiosa-image') => {
        const imageList = images();
        if (selectedImageIndex.value === null || !imageList) return;

        try {
            const currentImage = imageList[selectedImageIndex.value];
            const imageUrl = currentImage.url;
            const response = await fetch(imageUrl);
            const blob = await response.blob();
            const url = window.URL.createObjectURL(blob);

            const link = document.createElement('a');
            link.href = url;
            link.download = currentImage.file_name || `${filenamePrefix}-${selectedImageIndex.value + 1}.jpg`;
            document.body.appendChild(link);
            link.click();

            document.body.removeChild(link);
            window.URL.revokeObjectURL(url);
        } catch (error) {
            console.error('Download failed:', error);
            const imageUrl = imageList[selectedImageIndex.value].url;
            window.open(imageUrl, '_blank');
        }
    };

    // Keyboard navigation
    if (typeof window !== 'undefined') {
        const handleKeydown = (e: KeyboardEvent) => {
            if (!isLightboxOpen.value) return;
            if (e.key === 'Escape') closeLightbox();
            if (e.key === 'ArrowRight') nextImage();
            if (e.key === 'ArrowLeft') prevImage();
        };
        
        window.addEventListener('keydown', handleKeydown);
    }

    return {
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
    };
}
