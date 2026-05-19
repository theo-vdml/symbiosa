<script setup lang="ts">
    import { Link } from '@inertiajs/vue3';
    import AppButton from './AppButton.vue';
</script>

<template>
    <section
        class="relative flex min-h-screen w-full items-center justify-center overflow-hidden bg-black py-48 md:py-80 lg:py-96">

        <!-- Full-screen Background Video with Rounded Mask -->
        <div class="absolute inset-0 z-0">
            <video src="/abstract.mp4" autoplay loop muted playsinline
                class="h-full w-full object-cover opacity-60 grayscale-[0.2]"></video>

            <!-- The "Melting Mask" Overlays -->
            <!-- 1. Central Portal Glow -->
            <div
                class="absolute inset-0 bg-[radial-gradient(circle_at_center,transparent_0%,black_75%)] md:bg-[radial-gradient(circle_at_center,transparent_0%,black_65%)]">
            </div>

            <!-- 2. Extra Layer of Blur around the edges to truly "melt" -->
            <div
                class="absolute inset-0 backdrop-blur-xs mask-[radial-gradient(circle_at_center,transparent_20%,black_80%)]">
            </div>

            <!-- 3. Overall Cinematic Polish -->
            <div class="absolute inset-0 bg-radial-vignette opacity-90"></div>
            <div class="absolute inset-0 opacity-[0.03] pointer-events-none noise-overlay"></div>
        </div>

        <!-- Content -->
        <div class="relative z-10 w-full max-w-5xl px-8">
            <div class="flex flex-col items-center text-center">
                <h2 class="font-chillax flex flex-col items-center gap-4 md:gap-8">
                    <span
                        class="block text-4xl md:text-7xl lg:text-9xl font-light text-white leading-none tracking-tight opacity-0 animate-fade-in-up [animation-delay:200ms]">
                        Live music
                    </span>

                    <span
                        class="block font-synonym text-2xl md:text-4xl lg:text-5xl italic text-white/40 opacity-0 animate-fade-in-up [animation-delay:400ms]">
                        for
                    </span>

                    <span
                        class="block text-4xl md:text-7xl lg:text-9xl font-black uppercase tracking-tighter text-white leading-none opacity-0 animate-fade-in-up [animation-delay:600ms]">
                        Living people
                    </span>
                </h2>

                <!-- CTA Button -->
                <div class="mt-20 md:mt-32 opacity-0 animate-fade-in [animation-delay:1.2s]">
                    <AppButton href="/about" variant="primary" size="lg">
                        Notre Univers
                    </AppButton>
                </div>
            </div>
        </div>

        <!-- Section Transitions -->
        <div class="absolute inset-x-0 top-0 h-64 bg-linear-to-b from-black to-transparent"></div>
        <div class="absolute inset-x-0 bottom-0 h-64 bg-linear-to-t from-black to-transparent"></div>
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
