<script setup lang="ts">
    import { ref, computed } from 'vue';
    import { Head, Link } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';
    import AppButton from '@/components/AppButton.vue';
    import { Minus, Plus, Ticket, Calendar, MapPin, Info } from '@lucide/vue';
    import events from '@/routes/events';

    interface Price {
        id: number;
        name: string;
        price: number;
        active: boolean;
    }

    interface TicketType {
        id: number;
        name: string;
        description: string;
        prices: Price[];
    }

    interface Event {
        id: number;
        title: string;
        date: string;
        city: string;
        country: string;
        background: string;
        slug: string;
    }

    const props = defineProps<{
        event: Event;
        ticketTypes: TicketType[];
    }>();

    const extraOptions = [
        {
            id: 999,
            name: 'Options',
            description: 'Services supplémentaires pour améliorer votre expérience sur place.',
            prices: [
                { id: 1001, name: 'Lockers', price: 5, active: true },
                { id: 1002, name: 'Parking', price: 10, active: true },
                { id: 1003, name: 'Vestiaire', price: 2, active: true },
            ]
        }
    ];

    const allCategories = computed(() => [...props.ticketTypes, ...extraOptions]);

    // Initialize quantities for all items
    const quantities = ref<Record<number, number>>(
        allCategories.value.reduce((acc, type) => {
            type.prices.forEach(price => {
                acc[price.id] = 0;
            });
            return acc;
        }, {} as Record<number, number>)
    );

    const updateQuantity = (priceId: number, delta: number) => {
        const newQty = (quantities.value[priceId] || 0) + delta;
        if (newQty >= 0 && newQty <= 10) {
            quantities.value[priceId] = newQty;
        }
    };

    const getActivePrice = (ticketType: TicketType) => {
        return ticketType.prices.find(p => p.active);
    };

    const getPriceStatus = (type: TicketType, price: Price) => {
        if (price.active) return 'active';

        const activeIndex = type.prices.findIndex(p => p.active);
        const priceIndex = type.prices.findIndex(p => p.id === price.id);

        if (activeIndex === -1) {
            return 'sold_out';
        }

        return priceIndex < activeIndex ? 'sold_out' : 'soon';
    };

    const totalPrice = computed(() => {
        let total = 0;
        allCategories.value.forEach(type => {
            type.prices.forEach(price => {
                const qty = quantities.value[price.id] || 0;
                total += price.price * qty;
            });
        });
        return total;
    });

    const totalItems = computed(() => {
        return Object.values(quantities.value).reduce((sum, qty) => sum + qty, 0);
    });

    const handleCheckout = () => {
        if (totalPrice.value > 0) {
            alert('Redirection vers le paiement... (Total: ' + totalPrice.value + '€)');
        }
    };

    const getDateFormatted = (dateStr: string) => {
        const date = new Date(dateStr);
        return date.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' });
    };
</script>

<template>

    <Head :title="`Billetterie - ${event.title}`" />

    <Header />

    <div class="relative z-10 rounded-b-[6rem] bg-black min-h-screen pb-24">
        <!-- Hero Section -->
        <section class="relative h-[45vh] w-full overflow-hidden">
            <template v-if="event.background">
                <img :src="'/' + event.background" class="absolute inset-0 h-full w-full object-cover" alt="" />
                <div class="absolute inset-0 bg-linear-to-t from-black via-black/40 to-black/20"></div>
                <div class="absolute inset-0 bg-[url('/noise.png')] opacity-[0.05] mix-blend-soft-light"></div>
            </template>
            <template v-else>
                <div class="absolute inset-0 bg-linear-to-b from-[#51A687]/30 to-transparent"></div>
            </template>

            <div class="relative z-10 flex h-full flex-col items-center justify-end pb-16 text-center px-6">
                <div class="space-y-4 max-w-4xl">
                    <h1 class="font-chillax text-4xl md:text-7xl text-white leading-none tracking-tight uppercase">
                        {{ event.title }}
                    </h1>

                    <div class="flex flex-col md:flex-row items-center justify-center gap-4 md:gap-12 text-white/60">
                        <div class="flex items-center gap-2">
                            <Calendar class="w-4 h-4 text-[#51A687]" />
                            <span class="font-chillax uppercase tracking-widest text-sm">{{ getDateFormatted(event.date)
                                }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <MapPin class="w-4 h-4 text-[#51A687]" />
                            <span class="font-chillax uppercase tracking-widest text-sm">{{ event.city }}, {{
                                event.country
                                }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <main class="relative z-10 mx-auto max-w-7xl px-6 pt-12">
            <!-- Back to event -->
            <div class="mb-12">
                <Link :href="events.show(event.slug).url"
                    class="group inline-flex items-center gap-3 text-[10px] font-bold tracking-[0.3em] text-white/40 hover:text-[#51A687] uppercase transition-all duration-300">
                    <span class="text-xl transition-transform group-hover:-translate-x-1">←</span>
                    <span>Retour à l'événement</span>
                </Link>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-12">
                <!-- Ticket Selection -->
                <div class="xl:col-span-8 space-y-12">
                    <!-- Section Header -->
                    <div class="relative pt-8 pb-4">
                        <h2 class="font-chillax text-4xl md:text-5xl text-white uppercase tracking-widest leading-none">
                            Billetterie
                        </h2>
                    </div>

                    <!-- Reservation Disclaimer -->
                    <div class="flex gap-6 p-6 rounded-4xl border border-[#51A687]/20 bg-[#51A687]/5 backdrop-blur-sm">
                        <div
                            class="shrink-0 w-10 h-10 rounded-full bg-[#51A687]/10 flex items-center justify-center border border-[#51A687]/20">
                            <Info class="w-5 h-5 text-[#51A687]" />
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Note importante
                            </p>
                            <p class="text-xs text-white/60 leading-relaxed uppercase tracking-widest">
                                L'ajout de billets au panier ne constitue pas une réservation. Les places sont garanties
                                uniquement après la validation du paiement.
                            </p>
                        </div>
                    </div>

                    <div v-for="type in allCategories" :key="type.id"
                        class="group relative overflow-hidden rounded-[3rem] border border-white/10 bg-white/3 backdrop-blur-sm transition-all duration-500">

                        <!-- Ticket Type Header -->
                        <div class="p-8 sm:p-10 border-b border-white/5 bg-linear-to-br from-white/2 to-transparent">
                            <div class="space-y-4 text-center sm:text-left">
                                <h3
                                    class="font-chillax text-2xl md:text-3xl text-white uppercase tracking-[0.2em] leading-none">
                                    {{ type.name }}
                                </h3>
                                <p
                                    class="text-white/40 text-[10px] md:text-xs uppercase tracking-[0.2em] leading-relaxed max-w-2xl">
                                    {{ type.description }}
                                </p>
                            </div>
                        </div>

                        <!-- Prices List -->
                        <div class="divide-y divide-white/5">
                            <div v-for="price in type.prices" :key="price.id"
                                class="group/price relative flex flex-col sm:flex-row sm:items-center justify-between p-8 gap-6 transition-all duration-300"
                                :class="[
                                    price.active
                                        ? 'bg-white/1 hover:bg-white/3'
                                        : 'opacity-60 grayscale pointer-events-none'
                                ]">

                                <div class="flex-1 flex items-center">
                                    <div class="space-y-1">
                                        <h4 class="font-chillax text-lg text-white uppercase tracking-widest">
                                            {{ price.name }}
                                        </h4>
                                    </div>
                                </div>

                                <div class="flex items-center justify-between sm:justify-end gap-10">
                                    <div class="text-right">
                                        <p class="text-2xl font-chillax text-white tracking-tighter">{{ price.price }}€
                                        </p>
                                    </div>

                                    <!-- Consistent size container for both selector and badges -->
                                    <div class="w-45 flex justify-end">
                                        <div v-if="price.active"
                                            class="flex items-center gap-6 bg-black/40 rounded-full p-1.5 border border-white/10 shadow-inner w-full justify-between">
                                            <button @click="updateQuantity(price.id, -1)"
                                                class="h-10 w-10 flex items-center justify-center rounded-full bg-white/5 text-white hover:bg-[#51A687] hover:text-black transition-all duration-300 disabled:opacity-10"
                                                :disabled="quantities[price.id] === 0">
                                                <Minus class="w-4 h-4" />
                                            </button>
                                            <span class="w-6 text-center font-chillax text-2xl text-white">{{
                                                quantities[price.id]
                                            }}</span>
                                            <button @click="updateQuantity(price.id, 1)"
                                                class="h-10 w-10 flex items-center justify-center rounded-full bg-white/5 text-white hover:bg-[#51A687] hover:text-black transition-all duration-300 disabled:opacity-10"
                                                :disabled="quantities[price.id] === 10">
                                                <Plus class="w-4 h-4" />
                                            </button>
                                        </div>
                                        <div v-else
                                            class="flex items-center justify-center w-full h-13.5 rounded-full bg-white/10 border border-white/20 backdrop-blur-md">
                                            <span class="text-[10px] font-bold tracking-[0.3em] uppercase text-white">
                                                {{ getPriceStatus(type, price) === 'soon' ? 'Bientôt' : 'Épuisé' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Checkout Sidebar -->
                <div class="xl:col-span-4">
                    <div class="sticky top-32 space-y-6 mt-32">
                        <div class="rounded-[2.5rem] border border-white/10 bg-white/5 p-8 space-y-8 backdrop-blur-xl">
                            <h3 class="font-chillax text-2xl text-white uppercase tracking-wider">Votre Commande</h3>

                            <div class="space-y-4">
                                <div v-if="totalItems === 0" class="py-12 text-center space-y-4">
                                    <div
                                        class="w-16 h-16 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mx-auto opacity-50">
                                        <Ticket class="w-6 h-6 text-white" />
                                    </div>
                                    <p class="text-sm text-white/40 uppercase tracking-widest">Panier vide</p>
                                </div>

                                <template v-else>
                                    <template v-for="type in allCategories" :key="'summary-type-' + type.id">
                                        <div v-for="price in type.prices" :key="'summary-price-' + price.id">
                                            <div v-if="quantities[price.id] > 0"
                                                class="flex justify-between items-center py-3 border-b border-white/5">
                                                <div class="space-y-0.5">
                                                    <p class="text-white text-xs font-medium uppercase tracking-wide">
                                                        {{ type.name }}
                                                        <span v-if="type.prices.length > 1" class="text-white/40"> - {{
                                                            price.name }}</span>
                                                    </p>
                                                    <p class="text-[10px] text-white/50 uppercase">{{
                                                        quantities[price.id] }}
                                                        x {{ price.price }}€</p>
                                                </div>
                                                <p class="text-white font-chillax">{{ (quantities[price.id] || 0) *
                                                    price.price }}€</p>
                                            </div>
                                        </div>
                                    </template>
                                </template>
                            </div>

                            <div class="pt-6 space-y-6">
                                <div class="flex justify-between items-end">
                                    <p class="text-[10px] font-bold tracking-[0.3em] text-white/50 uppercase">Total</p>
                                    <p class="text-5xl font-chillax text-[#51A687] tracking-tighter">{{ totalPrice }}€
                                    </p>
                                </div>

                                <AppButton @click="handleCheckout" variant="primary" size="lg"
                                    :disabled="totalPrice === 0"
                                    class="w-full border-[#51A687]/50 bg-[#51A687]/10 backdrop-blur-xl hover:bg-[#51A687]/20 text-[#51A687] disabled:opacity-20">
                                    Commander
                                </AppButton>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <Footer />
</template>

<style scoped>
.font-chillax {
    font-family: 'Chillax', sans-serif;
}
</style>
