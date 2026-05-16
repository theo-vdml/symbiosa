<script setup lang="ts">
    import { Head, Link } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';
    import AppButton from '@/components/AppButton.vue';
    import { CheckCircle, Calendar, MapPin, Ticket } from '@lucide/vue';
    import events from '@/routes/events';

    const props = defineProps<{
        checkout: any;
    }>();

    const formatEuro = (amount: number) => {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(amount);
    };

    const event = props.checkout.reservations[0]?.reservable?.event;

    const getDateFormatted = (dateStr: string) => {
        return new Date(dateStr).toLocaleDateString('fr-FR', {
            day: 'numeric', month: 'long', year: 'numeric'
        });
    };
</script>

<template>
    <Head title="Paiement Réussi" />
    <Header />

    <div class="relative z-10 bg-black min-h-screen pb-24 pt-32 flex items-center justify-center">
        <main class="mx-auto max-w-2xl px-6 text-center space-y-12">
            
            <div class="space-y-6">
                <div class="w-24 h-24 rounded-full bg-[#51A687]/10 border border-[#51A687]/20 flex items-center justify-center mx-auto">
                    <CheckCircle class="w-12 h-12 text-[#51A687]" />
                </div>
                <h1 class="font-chillax text-4xl md:text-6xl text-white uppercase tracking-tight leading-none">
                    Merci pour<br/>
                    <span class="text-[#51A687]">votre commande</span>
                </h1>
                <p class="text-white/40 uppercase tracking-[0.2em] text-xs">
                    Référence : {{ checkout.uuid.split('-')[0] }}
                </p>
            </div>

            <div v-if="event" class="p-8 rounded-[2.5rem] border border-white/10 bg-white/5 space-y-6 backdrop-blur-xl text-left">
                <div class="space-y-2">
                    <p class="text-[10px] font-bold tracking-[0.3em] text-[#51A687] uppercase">Votre Événement</p>
                    <h3 class="font-chillax text-2xl text-white uppercase tracking-wider">{{ event.title }}</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-white/5">
                    <div class="flex items-center gap-3 text-white/60">
                        <Calendar class="w-4 h-4 text-[#51A687]" />
                        <span class="font-chillax uppercase tracking-widest text-xs">{{ getDateFormatted(event.date) }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-white/60">
                        <MapPin class="w-4 h-4 text-[#51A687]" />
                        <span class="font-chillax uppercase tracking-widest text-xs">{{ event.city }}, {{ event.country }}</span>
                    </div>
                </div>

                <div class="pt-6 border-t border-white/5 space-y-4">
                    <div v-for="reservation in checkout.reservations" :key="reservation.id" class="flex justify-between items-center">
                        <div class="flex items-center gap-3">
                            <Ticket class="w-4 h-4 text-white/20" />
                            <p class="text-white text-xs uppercase tracking-widest">{{ reservation.quantity }}x {{ reservation.reservable.name }}</p>
                        </div>
                        <p class="text-white font-chillax text-sm">{{ formatEuro((reservation.unit_price * reservation.quantity) / 100) }}</p>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <p class="text-xs text-white/40 uppercase tracking-widest leading-relaxed">
                    Un email de confirmation contenant vos billets a été envoyé à <br/>
                    <span class="text-white">{{ checkout.customer_email }}</span>
                </p>
                
                <AppButton :href="events.index().url" variant="outline" size="lg" class="px-12 border-white/10 text-white/60 hover:text-white hover:border-white/40">
                    Retour à l'accueil
                </AppButton>
            </div>

        </main>
    </div>

    <Footer />
</template>
