<script setup lang="ts">
    import { Head, Link } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';
    import AppButton from '@/components/AppButton.vue';
    import { computed } from 'vue';

    const props = defineProps<{
        status: number;
    }>();

    const title = computed(() => {
        return {
            503: 'Service Indisponible',
            500: 'Erreur Serveur',
            404: 'Page Non Trouvée',
            403: 'Accès Interdit',
        }[props.status] || 'Erreur';
    });

    const description = computed(() => {
        return {
            503: 'Le service est temporairement indisponible pour cause de maintenance.',
            500: 'Une erreur interne est survenue sur nos serveurs.',
            404: 'La page que vous recherchez n\'existe pas ou a été déplacée.',
            403: 'Vous n\'avez pas l\'autorisation d\'accéder à cette ressource.',
        }[props.status] || 'Une erreur inattendue est survenue.';
    });
</script>

<template>
    <Head :title="title" />

    <Header />

    <div class="relative z-10 overflow-hidden bg-black">
        <div class="relative flex min-h-screen w-full flex-col items-center justify-center text-center">
            <!-- --- LAYER 0: CINEMATIC BACKGROUND --- -->
            <div class="absolute inset-0 z-0">
                <!-- Background Video -->
                <div class="absolute inset-0 scale-105 animate-slow-zoom opacity-40">
                    <video src="/abstract.mp4" autoplay loop muted playsinline class="h-full w-full object-cover"></video>
                </div>

                <!-- Overlays for Readability -->
                <div class="absolute inset-0 bg-black/40"></div>
                <div class="absolute inset-0 bg-radial-gradient from-transparent via-black/40 to-black/90"></div>
                <div class="absolute inset-0 bg-linear-to-b from-black via-transparent to-black"></div>
                <div class="absolute inset-0 bg-[url('/noise.png')] opacity-[0.05] mix-blend-soft-light"></div>
                
                <!-- Branded Glow -->
                <div class="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 w-full max-w-4xl aspect-square bg-[#51A687]/10 blur-[120px] rounded-full"></div>
            </div>

            <!-- --- LAYER 1: CONTENT --- -->
            <div class="relative z-10 flex flex-col items-center px-6">
                <!-- Main Title -->
                <h1 class="font-chillax text-5xl md:text-7xl lg:text-8xl text-white uppercase italic tracking-tighter leading-none mb-6 animate-fade-in-up">
                    {{ title }}
                </h1>

                <!-- Description -->
                <p class="max-w-md font-synonym text-gray-400 text-base md:text-lg leading-relaxed mb-10 animate-fade-in-up [animation-delay:200ms]">
                    {{ description }}
                </p>

                <!-- CTA -->
                <div class="animate-fade-in-up [animation-delay:400ms]">
                    <AppButton href="/" variant="primary" size="lg" class="border-[#51A687]/50 bg-[#51A687]/10 backdrop-blur-xl hover:bg-[#51A687]/20">
                        Retour à l'accueil
                    </AppButton>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Redundant animations removed, using global ones from app.css */
</style>
