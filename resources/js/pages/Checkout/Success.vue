<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import {
    CheckCircle2,
    Calendar,
    MapPin,
    Loader2,
    Download,
    AlertCircle,
} from '@lucide/vue';
import { useIntervalFn } from '@vueuse/core';
import { computed, onMounted, onUnmounted } from 'vue';
import AppButton from '@/components/AppButton.vue';
import HeroHeader from '@/components/HeroHeader.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import events from '@/routes/events';

const props = defineProps<{
    checkout: any;
}>();

const formatEuro = (amount: number) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount);
};

const event = props.checkout.reservations[0]?.reservable?.event;

const getDateFormatted = (dateStr: string) => {
    if (!dateStr) {
        return '';
    }

    return new Date(dateStr).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const isCompleted = computed(() => props.checkout.status === 'completed');
const isProcessing = computed(() =>
    ['processing', 'pending'].includes(props.checkout.status),
);

// Polling
const { pause, resume } = useIntervalFn(
    () => {
        if (isProcessing.value) {
            router.reload({ only: ['checkout'] });
        } else {
            pause();
        }
    },
    3000,
    { immediate: false },
);

onMounted(() => {
    if (isProcessing.value) {
        resume();
    }
});
onUnmounted(() => pause());
</script>

<template>
    <MainLayout
        :title="isCompleted ? 'Paiement Réussi' : 'Vérification...'"
        has-background
    >
        <!-- Hero Section -->
        <section v-if="event" class="relative h-[50vh] w-full overflow-hidden">
            <img
                v-if="event.background_url"
                :src="event.background_url"
                :srcset="event.background_responsive?.srcset"
                sizes="(max-width: 768px) 200vw, 100vw"
                class="absolute inset-0 h-full w-full object-cover opacity-30 grayscale"
                alt=""
            />
            <div
                class="absolute inset-0 bg-linear-to-t from-black via-black/40 to-black/20"
            ></div>

            <div
                class="relative z-10 flex h-full flex-col items-center justify-end pb-20"
            >
                <HeroHeader size="lg">
                    <template v-if="isCompleted">
                        Paiement <span class="text-[#51A687]">réussi</span>
                    </template>
                    <template v-else-if="isProcessing">
                        Confirmation en cours
                    </template>
                    <template v-else> Erreur de paiement </template>

                    <template #bottom>
                        <div class="space-y-4">
                            <h2
                                class="font-chillax text-2xl text-white uppercase"
                            >
                                {{ event.title }}
                            </h2>
                            <div
                                class="flex flex-wrap items-center justify-center gap-6 text-gray-400"
                            >
                                <div class="flex items-center gap-2">
                                    <Calendar class="h-4 w-4 text-[#51A687]" />
                                    <span
                                        class="text-sm tracking-widest uppercase"
                                        >{{
                                            getDateFormatted(event.date)
                                        }}</span
                                    >
                                </div>
                                <div class="flex items-center gap-2">
                                    <MapPin class="h-4 w-4 text-[#51A687]" />
                                    <span
                                        class="text-sm tracking-widest uppercase"
                                        >{{ event.city }},
                                        {{ event.country }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </template>
                </HeroHeader>
            </div>
        </section>

        <!-- Content Area -->
        <main class="relative z-10 mx-auto max-w-xl px-6 py-20">
            <!-- PROCESSING -->
            <div
                v-if="isProcessing"
                class="animate-fade-in space-y-8 text-center"
                role="status"
                aria-live="polite"
            >
                <Loader2
                    class="mx-auto h-10 w-10 animate-spin text-[#51A687]"
                    aria-hidden="true"
                />
                <p
                    class="font-synonym text-base leading-relaxed tracking-widest text-gray-300 uppercase"
                >
                    Nous attendons la validation de votre paiement par Stripe...
                </p>
            </div>

            <!-- SUCCESS -->
            <div
                v-else-if="isCompleted"
                class="animate-fade-in-up space-y-12"
                role="status"
                aria-live="polite"
            >
                <div class="space-y-4">
                    <p
                        class="mb-8 text-center text-lg leading-relaxed text-gray-300"
                    >
                        Votre commande est validée. Les billets ont été envoyés
                        par mail à
                        <span class="font-medium text-white">{{
                            checkout.customer_email
                        }}</span>
                    </p>

                    <!-- Items List (Same style as Checkout page) -->
                    <section
                        aria-label="Détails de la commande"
                        class="space-y-3"
                    >
                        <div
                            v-for="reservation in checkout.reservations"
                            :key="reservation.id"
                            class="flex items-center justify-between rounded-2xl border border-white/10 bg-white/5 px-8 py-5"
                        >
                            <div class="space-y-1">
                                <h3
                                    class="text-sm font-bold text-white uppercase"
                                >
                                    {{ reservation.reservable.name }}
                                </h3>
                                <p
                                    class="text-xs tracking-widest text-gray-400 uppercase"
                                >
                                    {{ reservation.quantity }}
                                    unité(s)
                                </p>
                            </div>
                            <p class="font-chillax text-lg text-white">
                                <span class="sr-only">Prix :</span>
                                {{
                                    formatEuro(
                                        (reservation.unit_price *
                                            reservation.quantity) /
                                            100,
                                    )
                                }}
                            </p>
                        </div>

                        <div
                            class="mt-6 flex items-end justify-between border-t border-white/10 px-8 pt-8"
                        >
                            <p
                                class="text-xs tracking-[0.2em] text-gray-400 uppercase"
                            >
                                Total payé
                            </p>
                            <p
                                class="font-chillax text-4xl tracking-tighter text-[#51A687]"
                            >
                                {{
                                    formatEuro(
                                        checkout.reservations.reduce(
                                            (acc: number, res: any) =>
                                                acc +
                                                res.unit_price * res.quantity,
                                            0,
                                        ) / 100,
                                    )
                                }}
                            </p>
                        </div>
                    </section>
                </div>

                <div class="flex flex-col gap-4 pt-8">
                    <AppButton
                        :href="`/checkout/${checkout.uuid}/download`"
                        external
                        variant="primary"
                        size="lg"
                        class="w-full border-[#51A687]/50 bg-[#51A687]/10 backdrop-blur-xl hover:bg-[#51A687]/20 sm:w-auto"
                    >
                        <template #left-icon>
                            <Download class="h-5 w-5" />
                        </template>
                        Télécharger les billets
                    </AppButton>
                    <AppButton
                        :href="events.index().url"
                        variant="outline"
                        size="lg"
                        class="border-white/10 text-gray-400 hover:text-white"
                    >
                        Retour au calendrier
                    </AppButton>
                </div>

                <p
                    class="text-center text-xs tracking-[0.2em] text-white/60 uppercase"
                >
                    Référence : {{ checkout.uuid }}
                </p>
            </div>

            <!-- ERROR -->
            <div
                v-else
                class="animate-fade-in space-y-10 text-center"
                role="alert"
            >
                <AlertCircle
                    class="mx-auto h-12 w-12 text-red-500"
                    aria-hidden="true"
                />
                <p
                    class="font-synonym text-lg leading-relaxed tracking-widest text-gray-300 uppercase"
                >
                    La transaction a échoué ou la session a expiré.
                </p>
                <AppButton
                    :href="events.index().url"
                    variant="primary"
                    size="lg"
                >
                    Retour au calendrier
                </AppButton>
            </div>
        </main>
    </MainLayout>
</template>
