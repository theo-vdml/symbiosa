<script setup lang="ts">
interface GalleryImage {
    url: string;
    srcset: string;
}

interface Props {
    images: GalleryImage[];
}

defineProps<Props>();
</script>

<template>
    <div
        class="mx-auto grid w-full grid-cols-2 gap-2 bg-black pt-24 lg:aspect-4/3 lg:grid-cols-4"
    >
        <div
            class="col-span-2 aspect-video overflow-hidden lg:col-start-2 lg:row-span-2 lg:row-start-1 lg:aspect-auto"
        >
            <img
                :src="images[1].url"
                :srcset="images[1].srcset"
                class="h-full w-full object-cover"
                alt="Image d'illustration"
                aria-hidden="true"
                role="presentation"
            />
        </div>

        <div
            class="col-span-1 aspect-square overflow-hidden lg:col-start-1 lg:row-span-2 lg:row-start-1 lg:aspect-auto"
        >
            <img
                :src="images[0].url"
                :srcset="images[0].srcset"
                class="h-full w-full object-cover"
                alt="Image d'illustration"
                aria-hidden="true"
                role="presentation"
            />
        </div>

        <div
            class="col-span-1 aspect-square overflow-hidden lg:col-start-4 lg:row-start-1 lg:aspect-auto"
        >
            <img
                :src="images[2].url"
                :srcset="images[2].srcset"
                class="h-full w-full object-cover"
                alt="Image d'illustration"
                aria-hidden="true"
                role="presentation"
            />
        </div>

        <div
            class="col-span-2 aspect-video overflow-hidden lg:col-span-2 lg:col-start-1 lg:row-start-3 lg:aspect-auto"
        >
            <img
                :src="images[3].url"
                :srcset="images[3].srcset"
                class="h-full w-full object-cover"
                alt="Image d'illustration"
                aria-hidden="true"
                role="presentation"
            />
        </div>

        <div
            class="col-span-1 aspect-square overflow-hidden lg:col-start-4 lg:row-span-2 lg:row-start-2 lg:aspect-auto"
        >
            <img
                :src="images[5].url"
                :srcset="images[5].srcset"
                class="h-full w-full object-cover"
                alt="Image d'illustration"
                aria-hidden="true"
                role="presentation"
            />
        </div>

        <div
            class="col-span-1 aspect-square overflow-hidden lg:col-start-3 lg:row-start-3 lg:aspect-auto"
        >
            <img
                :src="images[4].url"
                :srcset="images[4].srcset"
                class="h-full w-full object-cover"
                alt="Image d'illustration"
                aria-hidden="true"
                role="presentation"
            />
        </div>
    </div>
</template>
