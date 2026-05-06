<script setup lang="ts">
    import { ref, computed, onMounted, onUnmounted } from 'vue';
    import { Head, useForm } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';
    import AppButton from '@/components/AppButton.vue';
    import { CreditCard, Clock, Info, ShieldCheck, ChevronRight, User } from '@lucide/vue';

    const props = defineProps<{
        checkout: any;
    }>();

    const form = useForm({
        email: props.checkout.customer_email || '',
        name: props.checkout.customer_name || '',
        accept_cgv: false,
        accept_rgpd: false,
    });

    const timeLeft = ref('');
    let timer: any = null;

    const calculateTimeLeft = () => {
        const now = new Date().getTime();
        const expires = new Date(props.checkout.expires_at).getTime();
        const diff = expires - now;

        if (diff <= 0) {
            timeLeft.value = 'EXPIRÉ';
            if (timer) clearInterval(timer);
            return;
        }

        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        timeLeft.value = `${minutes}:${seconds.toString().padStart(2, '0')}`;
    };

    onMounted(() => {
        calculateTimeLeft();
        timer = setInterval(calculateTimeLeft, 1000);
    });

    onUnmounted(() => {
        if (timer) clearInterval(timer);
    });

    const totalAmount = computed(() => {
        return props.checkout.reservations.reduce((acc: number, res: any) => acc + (res.unit_price * res.quantity), 0) / 100;
    });

    const formatEuro = (amount: number) => {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(amount);
    };

    const event = computed(() => props.checkout.reservations[0]?.reservable?.event);

    const handleSubmit = () => {
        // Logique de paiement à venir
        console.log('Soumission du formulaire', form.data());
    };
</script>

<template>

    <Head title="Finaliser ma commande" />
    <Header />

    <div class="relative z-10 bg-black min-h-screen pb-24 pt-32">
        <main class="mx-auto max-w-3xl px-6">

            <!-- Header de la page -->
            <div class="text-center space-y-6 mb-16">
                <h1 class="font-chillax text-4xl md:text-6xl text-white uppercase tracking-tight leading-none">
                    Finaliser<br />
                    <span class="text-[#51A687]">ma commande</span>
                </h1>

                <!-- Timer Compact -->
                <div
                    class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-[#51A687]/10 border border-[#51A687]/20 text-[#51A687]">
                    <Clock class="w-4 h-4" />
                    <span class="text-xs font-bold tracking-[0.2em] uppercase">Temps restant : {{ timeLeft }}</span>
                </div>
            </div>

            <div class="space-y-12">

                <!-- Section 1 : Résumé de la commande -->
                <section class="space-y-6">
                    <div class="flex items-center gap-6 border-b border-white/10 pb-6">
                        <div
                            class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-[#51A687] text-[#51A687] font-chillax text-lg pt-0.5">
                            1</div>
                        <h2 class="font-chillax text-2xl text-white uppercase tracking-wider">Résumé de la commande</h2>
                    </div>

                    <div class="space-y-4">
                        <div v-for="reservation in checkout.reservations" :key="reservation.id"
                            class="flex justify-between items-center py-4 px-6 rounded-2xl bg-white/5 border border-white/5">
                            <div class="space-y-1">
                                <p class="text-white text-sm font-medium uppercase tracking-wide">{{
                                    reservation.reservable.name }}</p>
                                <p class="text-[10px] text-white/40 uppercase tracking-widest">
                                    {{ reservation.quantity }} x {{ formatEuro(reservation.unit_price / 100) }}
                                </p>
                            </div>
                            <p class="text-white font-chillax text-lg">{{ formatEuro((reservation.unit_price *
                                reservation.quantity) / 100) }}</p>
                        </div>
                    </div>
                </section>

                <!-- Section 2 : Vos Informations -->
                <section class="space-y-6">
                    <div class="flex items-center gap-6 border-b border-white/10 pb-6">
                        <div
                            class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-[#51A687] text-[#51A687] font-chillax text-lg pt-0.5">
                            2</div>
                        <h2 class="font-chillax text-2xl text-white uppercase tracking-wider">Vos Informations</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="relative group">
                            <User
                                class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-white/20 group-focus-within:text-[#51A687] transition-colors" />
                            <input v-model="form.name" type="text"
                                class="w-full bg-white/5 border border-white/10 rounded-2xl pl-14 pr-6 py-5 text-white placeholder:text-white/20 focus:border-[#51A687]/50 focus:ring-0 transition-all outline-none text-sm"
                                placeholder="Nom Complet" />
                        </div>
                        <div class="relative group">
                            <CreditCard
                                class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 text-white/20 group-focus-within:text-[#51A687] transition-colors" />
                            <input v-model="form.email" type="email"
                                class="w-full bg-white/5 border border-white/10 rounded-2xl pl-14 pr-6 py-5 text-white placeholder:text-white/20 focus:border-[#51A687]/50 focus:ring-0 transition-all outline-none text-sm"
                                placeholder="Adresse Email" />
                        </div>
                    </div>
                </section>

                <!-- Section 3 : Paiement & Validation -->
                <section class="space-y-6">
                    <div class="flex items-center gap-6 border-b border-white/10 pb-6">
                        <div
                            class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-[#51A687] text-[#51A687] font-chillax text-lg pt-0.5">
                            3</div>
                        <h2 class="font-chillax text-2xl text-white uppercase tracking-wider">Paiement</h2>
                    </div>

                    <div class="rounded-3xl bg-white/5 border border-white/10 overflow-hidden">
                        <!-- Total Bar -->
                        <div class="flex justify-between items-center p-8 bg-white/5">
                            <span class="text-[10px] font-bold tracking-[0.3em] text-white/40 uppercase">Total à
                                régler</span>
                            <span class="text-4xl font-chillax text-[#51A687] tracking-tighter">{{
                                formatEuro(totalAmount) }}</span>
                        </div>

                        <!-- Info Note -->
                        <div class="p-8 space-y-8">

                            <div class="space-y-4">
                                <!-- CGV Checkbox -->
                                <label class="flex items-start gap-4 cursor-pointer group/legal">
                                    <div class="relative flex items-center justify-center mt-0.5 shrink-0">
                                        <input v-model="form.accept_cgv" type="checkbox" class="peer sr-only" />
                                        <div
                                            class="w-5 h-5 rounded-md border-2 border-white/10 bg-white/5 transition-all duration-300 peer-checked:bg-[#51A687] peer-checked:border-[#51A687]">
                                        </div>
                                        <svg class="absolute w-3 h-3 text-black opacity-0 transition-opacity peer-checked:opacity-100"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                            <path d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="text-[10px] text-white/40 uppercase tracking-widest leading-relaxed">
                                        J'ai lu et j'accepte les <a href="#"
                                            class="text-white hover:text-[#51A687] underline underline-offset-4 transition-colors">conditions
                                            générales de vente</a>.
                                    </span>
                                </label>

                                <!-- RGPD Checkbox -->
                                <label class="flex items-start gap-4 cursor-pointer group/legal">
                                    <div class="relative flex items-center justify-center mt-0.5 shrink-0">
                                        <input v-model="form.accept_rgpd" type="checkbox" class="peer sr-only" />
                                        <div
                                            class="w-5 h-5 rounded-md border-2 border-white/10 bg-white/5 transition-all duration-300 peer-checked:bg-[#51A687] peer-checked:border-[#51A687]">
                                        </div>
                                        <svg class="absolute w-3 h-3 text-black opacity-0 transition-opacity peer-checked:opacity-100"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4">
                                            <path d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span class="text-[10px] text-white/40 uppercase tracking-widest leading-relaxed">
                                        J'accepte que mes données soient utilisées pour le traitement de ma commande
                                        conformément à la <a href="#"
                                            class="text-white hover:text-[#51A687] underline underline-offset-4 transition-colors">politique
                                            de confidentialité</a>.
                                    </span>
                                </label>
                            </div>

                            <div class="space-y-4">
                                <button @click="handleSubmit" :disabled="!form.accept_cgv || !form.accept_rgpd"
                                    class="relative w-full h-16 rounded-2xl bg-[#635BFF] hover:bg-[#7a73ff] disabled:bg-white/10 disabled:cursor-not-allowed transition-all duration-300 overflow-hidden shadow-[0_4px_12px_rgba(99,91,255,0.2)] hover:shadow-[0_4px_20px_rgba(99,91,255,0.4)] disabled:shadow-none">
                                    <div class="flex items-center justify-center h-full">
                                        <span
                                            class="text-sm font-bold uppercase tracking-widest text-white transition-opacity duration-300"
                                            :class="{ 'opacity-20': !form.accept_cgv || !form.accept_rgpd }">Payer
                                            avec</span>
                                        <img src="/stripe.svg"
                                            class="h-8 brightness-0 invert transition-all duration-300"
                                            :class="{ 'opacity-20': !form.accept_cgv || !form.accept_rgpd }"
                                            alt="Stripe" />
                                    </div>
                                </button>

                                <div
                                    class="flex items-center justify-center gap-2 text-[10px] text-white/30 uppercase tracking-[0.2em]">
                                    <ShieldCheck class="w-3.5 h-3.5" />
                                    Paiement 100% sécurisé via Stripe
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Footer de la page -->
                <div class="flex flex-col items-center gap-6 pt-12 border-t border-white/5">
                    <div class="flex items-center gap-4 opacity-30 grayscale">
                        <img src="/sponsors/logo_01.svg" class="h-6" alt="Visa" />
                        <img src="/sponsors/logo_02.svg" class="h-6" alt="Mastercard" />
                        <img src="/sponsors/logo_03.svg" class="h-6" alt="Stripe" />
                    </div>
                    <p class="text-[10px] text-white/50 uppercase tracking-[0.3em]">
                        Référence : {{ checkout.uuid }}
                    </p>
                </div>
            </div>

        </main>
    </div>

    <Footer />
</template>
