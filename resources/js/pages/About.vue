<script setup lang="ts">
import HeroHeader from '@/components/HeroHeader.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { Seo } from '@/types/seo';

interface Section {
    title: string;
    content: string;
    image: string;
}

defineProps<{
    sections: Section[];
    seo: Seo;
}>();
</script>

<template>
    <MainLayout :seo="seo" has-background>
        <div
            class="relative z-10 mx-auto max-w-6xl px-6 pt-48 pb-32 md:px-10 lg:pt-56"
        >
            <HeroHeader size="xl" class="mb-32 md:mb-48">
                <template #top>
                    <p
                        class="font-mono text-xs tracking-[0.5em] text-[#51A687] uppercase md:text-sm"
                    >
                        À propos de nous
                    </p>
                </template>

                Symbiosa
            </HeroHeader>

            <div class="relative mt-20 px-4 md:px-0">
                <!-- Timeline Line -->
                <div
                    class="absolute top-0 bottom-0 left-1/2 z-1 hidden w-px -translate-x-1/2 bg-[#51A687]/40 md:block"
                    style="
                        -webkit-mask-image: linear-gradient(
                            to bottom,
                            transparent,
                            black 5%,
                            black 95%,
                            transparent
                        );
                        mask-image: linear-gradient(
                            to bottom,
                            transparent,
                            black 5%,
                            black 95%,
                            transparent
                        );
                    "
                ></div>

                <div class="relative space-y-32 md:space-y-0">
                    <article
                        v-for="(node, index) in sections"
                        :key="index"
                        class="relative"
                    >
                        <!-- Timeline Node Anchor -->
                        <div
                            class="absolute top-1/2 left-1/2 z-10 hidden -translate-x-1/2 -translate-y-1/2 md:block"
                        >
                            <div
                                class="flex flex-col items-center justify-center bg-black py-4"
                            >
                                <span class="font-mono text-xs text-[#51A687]">
                                    {{
                                        (index + 1).toString().padStart(2, '0')
                                    }}
                                </span>
                            </div>
                        </div>

                        <div
                            class="grid grid-cols-1 items-center gap-12 md:grid-cols-2 md:gap-24 md:py-24"
                        >
                            <!-- Visual Column -->
                            <div
                                :class="
                                    index % 2 === 0
                                        ? 'md:order-1 md:pr-12'
                                        : 'md:order-2 md:pl-12'
                                "
                                class="flex justify-center"
                            >
                                <div
                                    class="relative flex aspect-4/5 w-full items-center justify-center"
                                >
                                    <!-- Stain Mask using external PNG -->
                                    <div
                                        class="absolute inset-0"
                                        :style="{
                                            '-webkit-mask-image':
                                                'url(/mask.png)',
                                            'mask-image': 'url(/mask.png)',
                                            '-webkit-mask-size': '100% 100%',
                                            'mask-size': '100% 100%',
                                            '-webkit-mask-repeat': 'no-repeat',
                                            'mask-repeat': 'no-repeat',
                                            '-webkit-mask-position': 'center',
                                            'mask-position': 'center',
                                            transform: `rotate(${index % 2 === 0 ? 0 : 180}deg) scaleX(${index === 2 ? -1 : 1})`,
                                        }"
                                    >
                                        <img
                                            :src="node.image"
                                            alt=""
                                            class="h-full w-full object-cover opacity-80 grayscale"
                                            :style="{
                                                transform: `scaleX(${index === 2 ? -1.1 : 1.1}) scaleY(1.1) rotate(${index % 2 === 0 ? 0 : -180}deg)`,
                                            }"
                                        />
                                        <div
                                            class="pointer-events-none absolute inset-0 bg-[#51A687] opacity-40 mix-blend-color"
                                        ></div>
                                    </div>
                                </div>
                            </div>

                            <!-- Text Column -->
                            <div
                                :class="[
                                    index % 2 === 0
                                        ? 'md:order-2 md:pl-12'
                                        : 'md:order-1 md:pr-12 md:text-right',
                                ]"
                                class="flex flex-col px-2 text-left md:px-0"
                            >
                                <h2
                                    class="mb-8 font-chillax text-4xl leading-[1.1] tracking-tight text-white md:text-5xl lg:text-6xl"
                                    :class="
                                        index % 2 !== 0 ? 'md:text-right' : ''
                                    "
                                >
                                    {{ node.title }}
                                </h2>

                                <div
                                    class="prose max-w-none font-synonym whitespace-pre-line prose-invert prose-p:text-lg prose-p:leading-relaxed prose-p:font-light prose-p:tracking-wide prose-p:text-gray-400 prose-strong:font-normal prose-strong:text-white"
                                    :class="
                                        index % 2 !== 0
                                            ? 'text-left md:text-right'
                                            : 'text-left'
                                    "
                                    v-html="node.content"
                                ></div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>

            <div
                class="mt-32 mb-32 flex flex-col items-center justify-center text-center"
            >
                <p
                    class="mb-2 font-mono text-xs tracking-[0.3em] text-gray-500 uppercase"
                >
                    À ceux qui partagent notre vision :
                </p>
                <h2
                    class="font-chillax text-[15vw] leading-none tracking-tighter text-white italic md:text-[10rem] lg:text-[12rem]"
                >
                    merci.
                </h2>
            </div>
        </div>
    </MainLayout>
</template>

<style scoped>
.prose {
    --tw-prose-body: #9ca3af;
    --tw-prose-headings: #ffffff;
}

/* Specific alignment handling for prose p tags when in right-aligned column */
@media (min-width: 768px) {
    .md\:text-right p {
        text-align: right;
    }
}
</style>
