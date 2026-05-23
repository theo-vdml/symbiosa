<script setup lang="ts">
    import { computed, onMounted, onUnmounted } from 'vue';
    import { router } from '@inertiajs/vue3';
    import { useIntervalFn } from '@vueuse/core';
    import MainLayout from '@/layouts/MainLayout.vue';
    import HeroHeader from '@/components/HeroHeader.vue';
    import AppButton from '@/components/AppButton.vue';
    import { CheckCircle2, Calendar, MapPin, Loader2, Download, AlertCircle } from '@lucide/vue';
    import events from '@/routes/events';

    const props = defineProps<{
        checkout: any;
    }>();

    const formatEuro = (amount: number) => {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(amount);
    };

    const event = props.checkout.reservations[0]?.reservable?.event;

    const getDateFormatted = (dateStr: string) => {
        if (!dateStr) return '';
        return new Date(dateStr).toLocaleDateString('fr-FR', {
            day: 'numeric', month: 'long', year: 'numeric'
        });
    };

    const isCompleted = computed(() => props.checkout.status === 'completed');
    const isProcessing = computed(() => ['processing', 'pending'].includes(props.checkout.status));

    // Polling
    const { pause, resume } = useIntervalFn(() => {
        if (isProcessing.value) {
            router.reload({ only: ['checkout'] });
        } else {
            pause();
        }
    }, 3000, { immediate: false });

    onMounted(() => { if (isProcessing.value) resume(); });
    onUnmounted(() => pause());
</script>

<template>
    <MainLayout :title="isCompleted ? 'Paiement Réussi' : 'Vérification...'" has-background>

        <!-- Hero Section -->
        <section v-if="event" class="relative h-[50vh] w-full overflow-hidden">
            <img v-if="event.background_url" :src="event.background_url" :srcset="event.background_responsive?.srcset"
                sizes="(max-width: 768px) 200vw, 100vw"
                class="absolute inset-0 h-full w-full object-cover grayscale opacity-30" alt="" />
            <div class="absolute inset-0 bg-linear-to-t from-black via-black/40 to-black/20"></div>

            <div class="relative z-10 flex h-full flex-col items-center justify-end pb-20">
                <HeroHeader size="lg">
                    <template v-if="isCompleted">
                        Paiement <span class="text-[#51A687]">réussi</span>
                    </template>
                    <template v-else-if="isProcessing">
                        Confirmation en cours
                    </template>
                    <template v-else>
                        Erreur de paiement
                    </template>

                    <template #bottom>
                        <div class="space-y-4">
                            <h2 class="font-chillax text-2xl text-white uppercase">{{ event.title }}</h2>
                            <div class="flex flex-wrap items-center justify-center gap-6 text-gray-400">
                                <div class="flex items-center gap-2">
                                    <Calendar class="w-4 h-4 text-[#51A687]" />
                                    <span class="text-sm uppercase tracking-widest">{{ getDateFormatted(event.date)
                                        }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <MapPin class="w-4 h-4 text-[#51A687]" />
                                    <span class="text-sm uppercase tracking-widest">{{ event.city }}, {{ event.country
                                        }}</span>
                                </div>
                            </div>
                        </div>
                    </template>
                </HeroHeader>
            </div>
        </section>

        <!-- Content Area -->
        <div class="relative z-10 max-w-xl mx-auto px-6 py-20">

            <!-- PROCESSING -->
            <div v-if="isProcessing" class="text-center space-y-8 animate-fade-in">
                <Loader2 class="w-10 h-10 text-[#51A687] animate-spin mx-auto" />
                <p class="font-synonym text-gray-300 text-base uppercase tracking-widest leading-relaxed">
                    Nous attendons la validation de votre paiement par Stripe...
                </p>
            </div>

            <!-- SUCCESS -->
            <div v-else-if="isCompleted" class="space-y-12 animate-fade-in-up">
                <div class="space-y-4">
                    <p class="text-gray-300 text-center text-lg mb-8 leading-relaxed">
                        Votre commande est validée. Les billets ont été envoyés par mail à
                        <span class="text-white font-medium">{{ checkout.customer_email }}</span>
                    </p>

                    <!-- Items List (Same style as Checkout page) -->
                    <div class="space-y-3">
                        <div v-for="reservation in checkout.reservations" :key="reservation.id"
                            class="flex justify-between items-center py-5 px-8 rounded-2xl bg-white/5 border border-white/10">
                            <div class="space-y-1">
                                <p class="text-white text-sm font-bold uppercase">{{ reservation.reservable.name }}</p>
                                <p class="text-xs text-gray-400 uppercase tracking-widest">{{ reservation.quantity }}
                                    unité(s)
                                </p>
                            </div>
                            <p class="text-white font-chillax text-lg">
                                {{ formatEuro((reservation.unit_price * reservation.quantity) / 100) }}
                            </p>
                        </div>

                        <div class="flex justify-between items-end px-8 pt-8 border-t border-white/10 mt-6">
                            <p class="text-gray-400 text-xs uppercase tracking-[0.2em]">Total payé</p>
                            <p class="font-chillax text-4xl text-[#51A687] tracking-tighter">
                                {{formatEuro(checkout.reservations.reduce((acc: number, res: any) =>
                                    acc + (res.unit_price * res.quantity), 0) / 100)
                                }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4 pt-8">
                    <AppButton :href="`/checkout/${checkout.uuid}/download`" external variant="primary" size="lg"
                        class="w-full sm:w-auto border-[#51A687]/50 bg-[#51A687]/10 backdrop-blur-xl hover:bg-[#51A687]/20">
                        <template #left-icon>
                            <Download class="w-5 h-5" />
                        </template>
                        Télécharger les billets
                    </AppButton>
                    <AppButton :href="events.index().url" variant="outline" size="lg"
                        class="border-white/10 text-gray-400 hover:text-white">
                        Retour au calendrier
                    </AppButton>
                </div>

                <p class="text-center text-xs text-white/60 uppercase tracking-[0.2em]">
                    Référence : {{ checkout.uuid }}
                </p>
            </div>

            <!-- ERROR -->
            <div v-else class="text-center space-y-10 animate-fade-in">
                <AlertCircle class="w-12 h-12 text-red-500 mx-auto" />
                <p class="font-synonym text-gray-300 text-lg uppercase tracking-widest leading-relaxed">
                    La transaction a échoué ou la session a expiré.
                </p>
                <AppButton :href="events.index().url" variant="primary" size="lg">
                    Retour au calendrier
                </AppButton>
            </div>

        </div>
    </MainLayout>
</template>
