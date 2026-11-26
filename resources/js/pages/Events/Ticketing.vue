<script setup lang="ts">
import { Link, useForm } from '@inertiajs/vue3';
import { Ticket, Calendar, MapPin, Info } from '@lucide/vue';
import { ref, computed } from 'vue';
import AppButton from '@/components/AppButton.vue';
import HeroHeader from '@/components/HeroHeader.vue';
import TicketingItem from '@/components/Ticketing/TicketingItem.vue';
import TicketingSection from '@/components/Ticketing/TicketingSection.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import events from '@/routes/events';

interface CartItem {
    type: 'ticket' | 'addon';
    id: number; // ID du TicketType ou de l'Addon
    priceId?: number; // ID du TicketPrice spécifique
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
    items: [] as CartItem[],
});

const formatEuro = (amount: number) => {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount);
};

const getDateFormatted = (dateStr: string) => {
    return new Date(dateStr).toLocaleDateString('fr-FR', {
        day: 'numeric',
        month: 'long',
        year: 'numeric',
    });
};

const getItemQuantity = (
    type: 'ticket' | 'addon',
    id: number,
    priceId?: number,
) => {
    return (
        form.items.find(
            (item) =>
                item.type === type &&
                item.id === id &&
                item.priceId === priceId,
        )?.qty || 0
    );
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
                price: price.price_in_euro,
            });
        });
    });

    props.event.addons?.forEach((addon: any) => {
        lookup.set(`addon_${addon.id}`, {
            type: 'addon',
            id: addon.id,
            name: addon.name,
            price: addon.price_in_euro,
        });
    });

    return lookup;
});

const cartDetails = computed(() => {
    const items = [];
    let total = 0;

    for (const item of form.items) {
        const key =
            item.type === 'ticket'
                ? `ticket_${item.id}_price_${item.priceId}`
                : `addon_${item.id}`;
        const product = productsLookup.value.get(key);

        if (product) {
            const subtotal = product.price * item.qty;
            total += subtotal;
            items.push({
                key,
                name: product.name,
                price: product.price,
                qty: item.qty,
                subtotal,
            });
        }
    }

    return { items, total };
});

const totalItems = computed(() => {
    return form.items.reduce((sum, item) => sum + item.qty, 0);
});

const hasProducts = computed(() => {
    return (
        props.event.ticket_types?.length > 0 || props.event.addons?.length > 0
    );
});

const updateQuantity = (
    details: { type: 'ticket' | 'addon'; id: number; priceId?: number },
    change: number,
    max: number = 99,
) => {
    const index = form.items.findIndex(
        (item) =>
            item.type === details.type &&
            item.id === details.id &&
            item.priceId === details.priceId,
    );

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

const cartStatus = computed(() => {
    if (totalItems.value === 0) {
        return 'Votre panier est vide.';
    }

    return `Panier mis à jour. ${totalItems.value} article${totalItems.value > 1 ? 's' : ''}, total : ${formatEuro(cartDetails.value.total)}.`;
});
</script>

<template>
    <MainLayout :title="`Billetterie - ${event.title}`">
        <!-- Hero Section -->
        <section
            v-if="event"
            class="relative flex min-h-[50vh] w-full items-center justify-center overflow-hidden py-24"
            aria-labelledby="hero-title"
        >
            <img
                v-if="event.background_url"
                :src="event.background_url"
                :srcset="event.background_responsive?.srcset"
                sizes="(max-width: 768px) 200vw, 100vw"
                class="absolute inset-0 h-full w-full object-cover opacity-30 grayscale"
                alt=""
                aria-hidden="true"
            />
            <div
                class="absolute inset-0 bg-linear-to-t from-black via-black/40 to-black/20"
                aria-hidden="true"
            ></div>

            <div
                class="relative z-10 flex w-full flex-col items-center justify-center"
            >
                <HeroHeader size="lg" id="hero-title">
                    Billetterie

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
                                    <Calendar
                                        class="h-4 w-4 text-[#51A687]"
                                        aria-hidden="true"
                                    />
                                    <span
                                        class="text-sm tracking-widest uppercase"
                                    >
                                        <span class="sr-only">Date : </span
                                        >{{ getDateFormatted(event.date) }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <MapPin
                                        class="h-4 w-4 text-[#51A687]"
                                        aria-hidden="true"
                                    />
                                    <span
                                        class="text-sm tracking-widest uppercase"
                                    >
                                        <span class="sr-only">Lieu : </span
                                        >{{ event.city }}, {{ event.country }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </HeroHeader>
            </div>
        </section>

        <div class="relative z-10 mx-auto max-w-7xl px-6 pt-12">
            <!-- Retour -->
            <Link
                :href="events.show(event.slug).url"
                aria-label="Retour à la page de l'événement"
                class="group mb-12 inline-flex items-center gap-3 text-[10px] font-bold tracking-[0.3em] text-white/40 uppercase transition-all duration-300 hover:text-[#51A687]"
            >
                <span
                    class="text-xl transition-transform group-hover:-translate-x-1"
                    aria-hidden="true"
                    >←</span
                >
                <span>Retour à l'événement</span>
            </Link>

            <div class="space-y-12 pb-24">
                <div
                    v-if="form.errors.items || $page.props.flash.error"
                    role="alert"
                    class="flex items-center gap-6 rounded-4xl border border-red-500/20 bg-red-500/5 p-6 backdrop-blur-sm"
                >
                    <div
                        class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-red-500/20 bg-red-500/10"
                    >
                        <Info class="h-5 w-5 text-red-500" aria-hidden="true" />
                    </div>
                    <div class="space-y-1">
                        <p
                            class="text-[10px] font-bold tracking-[0.2em] text-red-500 uppercase"
                        >
                            Attention
                        </p>
                        <p
                            class="text-xs leading-relaxed tracking-widest text-white/60 uppercase"
                        >
                            {{ form.errors.items || $page.props.flash.error }}
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-12 xl:grid-cols-12">
                    <!-- Sélection Billets -->
                    <main class="space-y-12 xl:col-span-8">
                        <h2
                            class="pt-8 font-chillax text-4xl leading-none tracking-widest text-white uppercase md:text-5xl"
                        >
                            Choisissez vos billets
                        </h2>

                        <template v-if="hasProducts">
                            <!-- Tickets -->
                            <TicketingSection
                                v-for="type in event.ticket_types"
                                :key="type.id"
                                :title="type.name"
                                :description="type.description"
                            >
                                <TicketingItem
                                    v-for="price in type.prices"
                                    :key="`ticket_${type.id}_price_${price.id}`"
                                    :type="'ticket'"
                                    :id="type.id"
                                    :priceId="price.id"
                                    :title="price.name"
                                    :price="price.price_in_euro"
                                    :disabled="price.status !== 'open'"
                                    :disabled_reason="
                                        price.status === 'upcoming'
                                            ? 'Bientôt'
                                            : 'Épuisé'
                                    "
                                    :quantity="
                                        getItemQuantity(
                                            'ticket',
                                            type.id,
                                            price.id,
                                        )
                                    "
                                    @update-quantity="updateQuantity"
                                    :max_per_order="type.max_per_order ?? 10"
                                    :available_stock="type.available_stock"
                                />
                            </TicketingSection>

                            <!-- Addons -->
                            <TicketingSection
                                v-if="event.addons?.length"
                                title="Extras"
                                description="Ajoutez des options supplémentaires"
                            >
                                <TicketingItem
                                    v-for="addon in event.addons"
                                    :key="`addon_${addon.id}`"
                                    :description="addon.description"
                                    :type="'addon'"
                                    :id="addon.id"
                                    :title="addon.name"
                                    :price="addon.price_in_euro"
                                    :disabled="addon.status !== 'open'"
                                    :disabled_reason="
                                        addon.status === 'upcoming'
                                            ? 'Bientôt'
                                            : 'Épuisé'
                                    "
                                    :quantity="
                                        getItemQuantity('addon', addon.id)
                                    "
                                    @update-quantity="updateQuantity"
                                    :max_per_order="addon.max_per_order ?? 10"
                                    :available_stock="addon.available_stock"
                                />
                            </TicketingSection>
                        </template>

                        <div
                            v-else
                            class="space-y-8 rounded-[3rem] border border-white/10 bg-white/5 px-12 py-24 text-center backdrop-blur-xl"
                        >
                            <div
                                class="mx-auto flex h-24 w-24 items-center justify-center rounded-full border border-white/10 bg-white/5"
                            >
                                <Ticket
                                    class="h-10 w-10 text-white/20"
                                    aria-hidden="true"
                                />
                            </div>
                            <div class="space-y-4">
                                <p
                                    class="text-[10px] font-bold tracking-[0.4em] text-[#51A687] uppercase"
                                >
                                    Indisponible
                                </p>
                                <h3
                                    class="font-chillax text-2xl tracking-widest text-white uppercase"
                                >
                                    Aucun billet en vente
                                </h3>
                                <p
                                    class="mx-auto max-w-md text-sm leading-relaxed tracking-widest text-white/40 uppercase"
                                >
                                    Il n'y a actuellement aucun billet ou option
                                    disponible pour cet événement. Revenez plus
                                    tard !
                                </p>
                            </div>
                            <Link
                                :href="events.show(event.slug).url"
                                class="inline-flex h-12 items-center rounded-full border border-white/10 px-8 text-[10px] font-bold tracking-[0.2em] text-white uppercase transition-colors hover:bg-white/10"
                            >
                                Retour à l'événement
                            </Link>
                        </div>
                    </main>

                    <!-- Panier Sidebar -->
                    <aside class="xl:col-span-4" aria-labelledby="cart-title">
                        <div class="sticky top-32 mt-32 space-y-6">
                            <div
                                class="space-y-8 rounded-[2.5rem] border border-white/10 bg-white/5 p-8 backdrop-blur-xl"
                            >
                                <h3
                                    id="cart-title"
                                    class="font-chillax text-2xl tracking-wider text-white uppercase"
                                >
                                    Votre Commande
                                </h3>

                                <div class="sr-only" aria-live="polite">
                                    {{ cartStatus }}
                                </div>

                                <div class="space-y-4">
                                    <div
                                        v-if="totalItems === 0"
                                        class="space-y-4 py-12 text-center"
                                    >
                                        <div
                                            class="mx-auto flex h-16 w-16 items-center justify-center rounded-full border border-white/10 bg-white/5 opacity-50"
                                        >
                                            <Ticket
                                                class="h-6 w-6 text-white"
                                                aria-hidden="true"
                                            />
                                        </div>
                                        <p
                                            class="text-sm tracking-widest text-white/40 uppercase"
                                        >
                                            Panier vide
                                        </p>
                                    </div>

                                    <div
                                        v-for="item in cartDetails.items"
                                        :key="item.key"
                                        class="flex items-center justify-between border-b border-white/5 py-3"
                                    >
                                        <div class="space-y-0.5">
                                            <p
                                                class="text-xs font-medium tracking-wide text-white uppercase"
                                            >
                                                {{ item.name }}
                                            </p>
                                            <p
                                                class="text-[10px] text-white/50 uppercase"
                                            >
                                                <span class="sr-only"
                                                    >Quantité : </span
                                                >{{ item.qty }} x
                                                {{ formatEuro(item.price) }}
                                            </p>
                                        </div>
                                        <p class="font-chillax text-white">
                                            <span class="sr-only"
                                                >Sous-total :
                                            </span>
                                            {{ formatEuro(item.subtotal) }}
                                        </p>
                                    </div>
                                </div>

                                <div class="space-y-6 pt-6">
                                    <div class="flex items-end justify-between">
                                        <p
                                            class="text-[10px] font-bold tracking-[0.3em] text-white/50 uppercase"
                                        >
                                            Total
                                        </p>
                                        <p
                                            class="font-chillax text-5xl tracking-tighter text-[#51A687]"
                                        >
                                            <span class="sr-only"
                                                >Total à régler :
                                            </span>
                                            {{ formatEuro(cartDetails.total) }}
                                        </p>
                                    </div>

                                    <AppButton
                                        @click="handleCheckout"
                                        variant="primary"
                                        size="lg"
                                        :disabled="cartDetails.total === 0"
                                        :loading="form.processing"
                                        class="w-full border-[#51A687]/50 bg-[#51A687]/10 text-[#51A687] backdrop-blur-xl hover:bg-[#51A687]/20 disabled:opacity-20"
                                        aria-label="Procéder au paiement"
                                    >
                                        Commander
                                    </AppButton>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
