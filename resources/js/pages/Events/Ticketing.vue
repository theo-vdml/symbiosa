<script setup lang="ts">
    import { ref, computed } from 'vue';
    import { Head, Link } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';
    import AppButton from '@/components/AppButton.vue';
    import { Ticket, Calendar, MapPin, Info } from '@lucide/vue';
    import events from '@/routes/events';
    import TicketingSection from '@/components/Ticketing/TicketingSection.vue';
    import TicketingItem from '@/components/Ticketing/TicketingItem.vue';

    type ItemKey = `ticket_${number}_price_${number}` | `addon_${number}`;

    interface CartItemDetails {
        name: string;
        price: number;
        type: 'ticket' | 'addon';
        id: number;
        priceId?: number;
    }

    const props = defineProps<{
        event: Event;
    }>();

    const cart = ref<Map<ItemKey, number>>(new Map());

    const formatEuro = (amount: number) => {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(amount);
    };

    const getDateFormatted = (dateStr: string) => {
        return new Date(dateStr).toLocaleDateString('fr-FR', {
            day: 'numeric', month: 'long', year: 'numeric'
        });
    };

    const productsLookup = computed(() => {
        const lookup = new Map<ItemKey, CartItemDetails>();

        props.event.ticket_types?.forEach(type => {
            type.prices.forEach(price => {
                lookup.set(`ticket_${type.id}_price_${price.id}`, {
                    type: 'ticket',
                    id: type.id,
                    priceId: price.id,
                    name: `${type.name} - ${price.name}`,
                    price: price.price_in_euro
                });
            });
        });

        props.event.addons?.forEach(addon => {
            lookup.set(`addon_${addon.id}`, {
                type: 'addon',
                id: addon.id,
                name: addon.name,
                price: addon.price_in_euro
            });
        });

        return lookup;
    });

    const cartDetails = computed(() => {
        const items = [];
        let total = 0;

        for (const [key, qty] of cart.value.entries()) {
            const product = productsLookup.value.get(key);
            if (product && qty > 0) {
                const subtotal = product.price * qty;
                total += subtotal;
                items.push({ key, name: product.name, price: product.price, qty, subtotal });
            }
        }
        return { items, total };
    });

    const totalItems = computed(() => {
        return Array.from(cart.value.values()).reduce((sum, qty) => sum + qty, 0);
    });

    const updateQuantity = (key: ItemKey, change: number, max: number = 99) => {
        const current = cart.value.get(key) || 0;
        const next = current + change;

        if (next <= 0) {
            cart.value.delete(key);
        } else if (next <= max) {
            cart.value.set(key, next);
        }
    };

    const handleCheckout = () => {
        if (cartDetails.value.total > 0) {
            const payload = Array.from(cart.value.entries()).map(([key, qty]) => {
                const product = productsLookup.value.get(key);
                return { qty, productId: product?.id, priceId: product?.priceId, type: product?.type };
            });
            console.log("Validation commande :", payload);
        }
    };
</script>

<template>

    <Head :title="`Billetterie - ${event.title}`" />
    <Header />

    <div class="relative z-10 rounded-b-[6rem] bg-black min-h-screen pb-24">
        <!-- Hero Section -->
        <section class="relative h-[45vh] w-full overflow-hidden">
            <img v-if="event.background" :src="'/' + event.background"
                class="absolute inset-0 h-full w-full object-cover" alt="" />
            <div class="absolute inset-0 bg-linear-to-t from-black via-black/40 to-black/20"></div>

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
                                event.country }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <main class="relative z-10 mx-auto max-w-7xl px-6 pt-12">
            <!-- Retour -->
            <Link :href="events.show(event.slug).url"
                class="group inline-flex items-center gap-3 text-[10px] font-bold tracking-[0.3em] text-white/40 hover:text-[#51A687] uppercase transition-all duration-300 mb-12">
                <span class="text-xl transition-transform group-hover:-translate-x-1">←</span>
                <span>Retour à l'événement</span>
            </Link>

            <div class="grid grid-cols-1 xl:grid-cols-12 gap-12">
                <!-- Sélection Billets -->
                <div class="xl:col-span-8 space-y-12">
                    <h2
                        class="font-chillax text-4xl md:text-5xl text-white uppercase tracking-widest leading-none pt-8">
                        Billetterie</h2>

                    <!-- Info Note -->
                    <div class="flex gap-6 p-6 rounded-4xl border border-[#51A687]/20 bg-[#51A687]/5 backdrop-blur-sm">
                        <div
                            class="shrink-0 w-10 h-10 rounded-full bg-[#51A687]/10 flex items-center justify-center border border-[#51A687]/20">
                            <Info class="w-5 h-5 text-[#51A687]" />
                        </div>
                        <div class="space-y-1">
                            <p class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Note importante
                            </p>
                            <p class="text-xs text-white/60 leading-relaxed uppercase tracking-widest">
                                Les places sont garanties uniquement après validation du paiement.
                            </p>
                        </div>
                    </div>

                    <!-- Tickets -->
                    <TicketingSection v-for="type in event.ticket_types" :key="type.id" :title="type.name"
                        :description="type.description">
                        <TicketingItem v-for="price in type.prices" :key="`ticket_${type.id}_price_${price.id}`"
                            :itemKey="`ticket_${type.id}_price_${price.id}`" :title="price.name"
                            :price="price.price_in_euro" :disabled="price.status !== 'open'"
                            :disabled_reason="price.status === 'upcoming' ? 'Bientôt' : 'Épuisé'"
                            :quantity="cart.get(`ticket_${type.id}_price_${price.id}`) || 0"
                            @update-quantity="updateQuantity" :max_per_order="type.max_per_order || 99" />
                    </TicketingSection>

                    <!-- Addons -->
                    <TicketingSection v-if="event.addons?.length" title="Extras"
                        description="Ajoutez des options supplémentaires">
                        <TicketingItem v-for="addon in event.addons" :key="`addon_${addon.id}`"
                            :description="addon.description" :itemKey="`addon_${addon.id}`" :title="addon.name"
                            :price="addon.price_in_euro" :disabled="addon.status !== 'open'"
                            :disabled_reason="addon.status === 'upcoming' ? 'Bientôt' : 'Épuisé'"
                            :quantity="cart.get(`addon_${addon.id}`) || 0" @update-quantity="updateQuantity"
                            :max_per_order="addon.max_per_order" />
                    </TicketingSection>
                </div>

                <!-- Panier Sidebar -->
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

                                <div v-for="item in cartDetails.items" :key="item.key"
                                    class="flex justify-between items-center py-3 border-b border-white/5">
                                    <div class="space-y-0.5">
                                        <p class="text-white text-xs font-medium uppercase tracking-wide">{{ item.name
                                        }}</p>
                                        <p class="text-[10px] text-white/50 uppercase">{{ item.qty }} x {{
                                            formatEuro(item.price) }}</p>
                                    </div>
                                    <p class="text-white font-chillax">{{ formatEuro(item.subtotal) }}</p>
                                </div>
                            </div>

                            <div class="pt-6 space-y-6">
                                <div class="flex justify-between items-end">
                                    <p class="text-[10px] font-bold tracking-[0.3em] text-white/50 uppercase">Total</p>
                                    <p class="text-5xl font-chillax text-[#51A687] tracking-tighter">{{
                                        formatEuro(cartDetails.total) }}</p>
                                </div>

                                <AppButton @click="handleCheckout" variant="primary" size="lg"
                                    :disabled="cartDetails.total === 0"
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
