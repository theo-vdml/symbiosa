<script setup lang="ts">
    import { X, ChevronLeft, ChevronRight, Download } from '@lucide/vue';

    interface Props {
        isOpen: boolean;
        images: any[];
        selectedIndex: number | null;
        isImageLoaded: boolean;
    }

    defineProps<Props>();

    const emit = defineEmits(['close', 'prev', 'next', 'download', 'load']);
</script>

<template>
    <Teleport to="body">
        <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0"
            enter-to-class="opacity-100" leave-active-class="transition duration-200 ease-in"
            leave-from-class="opacity-100" leave-to-class="opacity-0">
            <div v-if="isOpen && selectedIndex !== null"
                class="fixed inset-0 z-100 flex items-center justify-center bg-black/95 px-4">
                
                <!-- Controls -->
                <div class="absolute top-6 right-6 z-110 flex items-center gap-4">
                    <!-- Download button -->
                    <button @click="emit('download')"
                        class="flex h-12 items-center gap-2 rounded-full bg-[#51A687] px-6 text-xs font-bold tracking-widest text-white uppercase transition-transform hover:scale-105 active:scale-95 shadow-lg">
                        <Download class="h-4.5 w-4.5" />
                        <span class="hidden sm:inline">Télécharger</span>
                    </button>

                    <!-- Close button -->
                    <button @click="emit('close')"
                        class="flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-white backdrop-blur-md transition-colors hover:bg-white/20">
                        <X class="h-6 w-6" />
                    </button>
                </div>

                <!-- Navigation -->
                <button @click.stop="emit('prev')"
                    class="absolute left-6 z-110 rounded-full bg-white/5 p-4 text-white backdrop-blur-md transition-colors hover:bg-white/10 hidden md:block">
                    <ChevronLeft class="h-8 w-8" />
                </button>

                <button @click.stop="emit('next')"
                    class="absolute right-6 z-110 rounded-full bg-white/5 p-4 text-white backdrop-blur-md transition-colors hover:bg-white/10 hidden md:block">
                    <ChevronRight class="h-8 w-8" />
                </button>

                <!-- Image Container -->
                <div class="relative max-h-[85vh] max-w-5xl" @click.stop>
                    <!-- Lightbox Loading State -->
                    <div v-if="!isImageLoaded" class="absolute inset-0 flex items-center justify-center">
                        <div
                            class="h-12 w-12 animate-spin rounded-full border-4 border-[#51A687]/20 border-t-[#51A687]">
                        </div>
                    </div>

                    <img :src="images[selectedIndex].url" @load="emit('load')"
                        class="max-h-[85vh] w-full object-contain shadow-2xl transition-opacity duration-300"
                        :class="isImageLoaded ? 'opacity-100' : 'opacity-0'" alt="" />

                    <!-- Counter -->
                    <div class="absolute -bottom-10 left-1/2 -translate-x-1/2 text-white/50 text-sm font-medium">
                        {{ selectedIndex + 1 }} / {{ images.length }}
                    </div>
                </div>

                <!-- Mobile navigation overlay -->
                <div class="absolute inset-y-0 left-0 w-1/4 md:hidden" @click="emit('prev')"></div>
                <div class="absolute inset-y-0 right-0 w-1/4 md:hidden" @click="emit('next')"></div>
            </div>
        </Transition>
    </Teleport>
</template>
