<script setup lang="ts">
    import { ref, computed } from 'vue';
    import { Head, Link, useForm } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';
    import AppButton from '@/components/AppButton.vue';
    import { Ticket, Calendar, MapPin, Info } from '@lucide/vue';
    import events from '@/routes/events';
    import TicketingSection from '@/components/Ticketing/TicketingSection.vue';
    import TicketingItem from '@/components/Ticketing/TicketingItem.vue';

    interface CartItem {
        type: 'ticket' | 'addon';
        id: number;        // ID du TicketType ou de l'Addon
        priceId?: number;  // ID du TicketPrice spécifique
        qty: number;
    }

    interface CartItemDetails {
        name: string;
        price: number;
        type: 'ticket' | 'addon';
        id: number;
        priceId?: number;
    }

    const props = defineProps<{
        event: any; // Using any for now as Event type isn't fully defined here
    }>();

    const form = useForm({
        items: [] as CartItem[]
    });

    const formatEuro = (amount: number) => {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(amount);
    };

    const getDateFormatted = (dateStr: string) => {
        return new Date(dateStr).toLocaleDateString('fr-FR', {
            day: 'numeric', month: 'long', year: 'numeric'
        });
    };

    const getItemQuantity = (type: 'ticket' | 'addon', id: number, priceId?: number) => {
        return form.items.find(item => item.type === type && item.id === id && item.priceId === priceId)?.qty || 0;
    };

    const productsLookup = computed(() => {
        const lookup = new Map<string, CartItemDetails>();

        props.event.ticket_types?.forEach((type: any) => {
            type.prices.forEach((price: any) => {
                lookup.set(`ticket_${type.id}_price_${price.id}`, {
                    type: 'ticket',
                    id: type.id,
                    priceId: price.id,
                    name: `${type.name} - ${price.name}`,
                    price: price.price_in_euro
                });
            });
        });

        props.event.addons?.forEach((addon: any) => {
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

        for (const item of form.items) {
            const key = item.type === 'ticket' ? `ticket_${item.id}_price_${item.priceId}` : `addon_${item.id}`;
            const product = productsLookup.value.get(key);
            if (product) {
                const subtotal = product.price * item.qty;
                total += subtotal;
                items.push({ key, name: product.name, price: product.price, qty: item.qty, subtotal });
            }
        }
        return { items, total };
    });

    const totalItems = computed(() => {
        return form.items.reduce((sum, item) => sum + item.qty, 0);
    });

    const hasProducts = computed(() => {
        return (props.event.ticket_types?.length > 0) || (props.event.addons?.length > 0);
    });

    const updateQuantity = (details: { type: 'ticket' | 'addon', id: number, priceId?: number }, change: number, max: number = 99) => {
        const index = form.items.findIndex(item => item.type === details.type && item.id === details.id && item.priceId === details.priceId);

        if (index === -1) {
            if (change > 0) {
                form.items.push({ ...details, qty: change });
            }
        } else {
            const next = form.items[index].qty + change;
            if (next <= 0) {
                form.items.splice(index, 1);
            } else if (next <= max) {
                form.items[index].qty = next;
            }
        }
    };

    const handleCheckout = () => {
        if (cartDetails.value.total > 0) {
            form.post(events.checkout.store(props.event.slug).url);
        }
    };
</script>

<template>

    <Head :title="`Billetterie - ${event.title}`" />
    <Header />

    <div class="relative z-10 rounded-b-[3rem] lg:rounded-b-[6rem] bg-black min-h-screen pb-24">
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

            <div class="space-y-12">
                <div v-if="form.errors.items || $page.props.flash.error"
                    class="p-6 rounded-4xl border border-red-500/20 bg-red-500/5 backdrop-blur-sm flex gap-6 items-center">
                    <div
                        class="shrink-0 w-10 h-10 rounded-full bg-red-500/10 flex items-center justify-center border border-red-500/20">
                        <Info class="w-5 h-5 text-red-500" />
                    </div>
                    <div class="space-y-1">
                        <p class="text-[10px] font-bold tracking-[0.2em] text-red-500 uppercase">Attention</p>
                        <p class="text-xs text-white/60 leading-relaxed uppercase tracking-widest">
                            {{ form.errors.items || $page.props.flash.error }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-12 gap-12">
                    <!-- Sélection Billets -->
                    <div class="xl:col-span-8 space-y-12">
                        <h2
                            class="font-chillax text-4xl md:text-5xl text-white uppercase tracking-widest leading-none pt-8">
                            Billetterie</h2>

                        <template v-if="hasProducts">
                            <!-- Tickets -->
                            <TicketingSection v-for="type in event.ticket_types" :key="type.id" :title="type.name"
                                :description="type.description">
                                <TicketingItem v-for="price in type.prices" :key="`ticket_${type.id}_price_${price.id}`"
                                    :type="'ticket'" :id="type.id" :priceId="price.id" :title="price.name"
                                    :price="price.price_in_euro" :disabled="price.status !== 'open'"
                                    :disabled_reason="price.status === 'upcoming' ? 'Bientôt' : 'Épuisé'"
                                    :quantity="getItemQuantity('ticket', type.id, price.id)"
                                    @update-quantity="updateQuantity" :max_per_order="type.max_per_order ?? 10"
                                    :available_stock="type.available_stock" />
                            </TicketingSection>

                            <!-- Addons -->
                            <TicketingSection v-if="event.addons?.length" title="Extras"
                                description="Ajoutez des options supplémentaires">
                                <TicketingItem v-for="addon in event.addons" :key="`addon_${addon.id}`"
                                    :description="addon.description" :type="'addon'" :id="addon.id" :title="addon.name"
                                    :price="addon.price_in_euro" :disabled="addon.status !== 'open'"
                                    :disabled_reason="addon.status === 'upcoming' ? 'Bientôt' : 'Épuisé'"
                                    :quantity="getItemQuantity('addon', addon.id)" @update-quantity="updateQuantity"
                                    :max_per_order="addon.max_per_order ?? 10"
                                    :available_stock="addon.available_stock" />
                            </TicketingSection>
                        </template>

                        <div v-else
                            class="py-24 px-12 text-center space-y-8 rounded-[3rem] border border-white/10 bg-white/5 backdrop-blur-xl">
                            <div
                                class="w-24 h-24 rounded-full bg-white/5 border border-white/10 flex items-center justify-center mx-auto">
                                <Ticket class="w-10 h-10 text-white/20" />
                            </div>
                            <div class="space-y-4">
                                <p class="text-[10px] font-bold tracking-[0.4em] text-[#51A687] uppercase">Indisponible
                                </p>
                                <h3 class="font-chillax text-2xl text-white uppercase tracking-widest">Aucun billet en
                                    vente</h3>
                                <p
                                    class="max-w-md mx-auto text-sm text-white/40 leading-relaxed uppercase tracking-widest">
                                    Il n'y a actuellement aucun billet ou option disponible pour cet événement. Revenez
                                    plus tard !
                                </p>
                            </div>
                            <Link :href="events.show(event.slug).url"
                                class="inline-flex h-12 items-center px-8 rounded-full border border-white/10 text-[10px] font-bold tracking-[0.2em] text-white uppercase hover:bg-white/10 transition-colors">
                                Retour à l'événement
                            </Link>
                        </div>
                    </div>

                    <!-- Panier Sidebar -->
                    <div class="xl:col-span-4">
                        <div class="sticky top-32 space-y-6 mt-32">
                            <div
                                class="rounded-[2.5rem] border border-white/10 bg-white/5 p-8 space-y-8 backdrop-blur-xl">
                                <h3 class="font-chillax text-2xl text-white uppercase tracking-wider">Votre Commande
                                </h3>

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
                                            <p class="text-white text-xs font-medium uppercase tracking-wide">{{
                                                item.name
                                            }}</p>
                                            <p class="text-[10px] text-white/50 uppercase">{{ item.qty }} x {{
                                                formatEuro(item.price) }}</p>
                                        </div>
                                        <p class="text-white font-chillax">{{ formatEuro(item.subtotal) }}</p>
                                    </div>
                                </div>

                                <div class="pt-6 space-y-6">
                                    <div class="flex justify-between items-end">
                                        <p class="text-[10px] font-bold tracking-[0.3em] text-white/50 uppercase">Total
                                        </p>
                                        <p class="text-5xl font-chillax text-[#51A687] tracking-tighter">{{
                                            formatEuro(cartDetails.total) }}</p>
                                    </div>

                                    <AppButton @click="handleCheckout" variant="primary" size="lg"
                                        :disabled="cartDetails.total === 0" :loading="form.processing"
                                        class="w-full border-[#51A687]/50 bg-[#51A687]/10 backdrop-blur-xl hover:bg-[#51A687]/20 text-[#51A687] disabled:opacity-20">
                                        Commander
                                    </AppButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <Footer />
</template>
