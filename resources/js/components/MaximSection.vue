<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import AppButton from './AppButton.vue';

const videoElement = ref<HTMLVideoElement | null>(null);

onMounted(() => {
    if (videoElement.value && videoElement.value.dataset.src) {
        videoElement.value.src = videoElement.value.dataset.src;
        videoElement.value.load();
    }
});
</script>

<template>
    <section
        class="relative flex min-h-screen w-full items-center justify-center overflow-hidden bg-black py-48 md:py-80 lg:py-96"
    >
        <!-- Full-screen Background Video with Rounded Mask -->
        <div class="absolute inset-0 z-0">
            <video
                ref="videoElement"
                data-src="/abstract.webm"
                autoplay
                loop
                muted
                playsinline
                class="h-full w-full object-cover opacity-60 grayscale-[0.2]"
                tabindex="-1"
                aria-hidden="true"
            ></video>

            <!-- The "Melting Mask" Overlays -->
            <!-- 1. Central Portal Glow -->
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_0%,black_75%)] md:bg-[radial-gradient(circle_at_center,transparent_0%,black_65%)]"
            ></div>

            <!-- 2. Extra Layer of Blur around the edges to truly "melt" -->
            <div
                class="absolute inset-0 mask-[radial-gradient(circle_at_center,transparent_20%,black_80%)] backdrop-blur-xs"
            ></div>

            <!-- 3. Overall Cinematic Polish -->
            <div class="bg-radial-vignette absolute inset-0 opacity-90"></div>
            <div
                class="noise-overlay pointer-events-none absolute inset-0 opacity-[0.03]"
            ></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 w-full max-w-5xl px-8">
            <div class="flex flex-col items-center text-center">
                <h2
                    class="flex flex-col items-center gap-4 font-chillax md:gap-8"
                >
                    <span
                        class="block animate-fade-in-up text-4xl leading-none font-light tracking-tight text-white opacity-0 [animation-delay:200ms] md:text-7xl lg:text-9xl"
                    >
                        Live music
                    </span>

                    <span
                        class="block animate-fade-in-up font-synonym text-2xl text-white/60 italic opacity-0 [animation-delay:400ms] md:text-4xl lg:text-5xl"
                    >
                        for
                    </span>

                    <span
                        class="block animate-fade-in-up text-4xl leading-none font-black tracking-tighter text-white uppercase opacity-0 [animation-delay:600ms] md:text-7xl lg:text-9xl"
                    >
                        Living people
                    </span>
                </h2>

                <!-- CTA Button -->
                <div
                    class="mt-20 animate-fade-in opacity-0 [animation-delay:1.2s] md:mt-32"
                >
                    <AppButton href="/about" variant="primary" size="lg">
                        Notre Univers
                    </AppButton>
                </div>
            </div>
        </div>

        <!-- Section Transitions -->
        <div
            class="absolute inset-x-0 top-0 h-64 bg-linear-to-b from-black to-transparent"
        ></div>
        <div
            class="absolute inset-x-0 bottom-0 h-64 bg-linear-to-t from-black to-transparent"
        ></div>
    </section>
</template>

<style scoped>
.animate-fade-in-up {
    animation: fadeInUp 1.2s cubic-bezier(0.215, 0.61, 0.355, 1) forwards;
}

.animate-fade-in {
    animation: fadeIn 1.5s ease-out forwards;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }

    to {
        opacity: 1;
    }
}

.noise-overlay {
    background-image: url('/noise.png');
}
</style>
