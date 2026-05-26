<script setup lang="ts">
    import { computed } from 'vue';
    import { Minus, Plus } from '@lucide/vue';

    const props = defineProps<{
        type: 'ticket' | 'addon';
        id: number;
        priceId?: number;
        title: string;
        description?: string;
        price: number;
        quantity: number;
        max_per_order?: number;
        available_stock?: number | null;
        disabled?: boolean;
        disabled_reason?: string;
    }>();

    const emit = defineEmits<{
        (e: 'update-quantity', details: { type: 'ticket' | 'addon', id: number, priceId?: number }, change: number, maxPerOrder?: number): void;
    }>();

    const maxAllowed = computed(() => {
        const maxPO = props.max_per_order ?? 10;
        if (props.available_stock === null || props.available_stock === undefined) return maxPO;
        return Math.min(props.available_stock, maxPO);
    });

    const addItem = () => {
        if (props.quantity < maxAllowed.value) {
            emit('update-quantity', { type: props.type, id: props.id, priceId: props.priceId }, 1, maxAllowed.value);
        }
    };

    const removeItem = () => {
        if (props.quantity > 0) {
            emit('update-quantity', { type: props.type, id: props.id, priceId: props.priceId }, -1, maxAllowed.value);
        }
    };

</script>
<template>
    <div class="group/price relative flex flex-col sm:flex-row sm:items-center justify-between p-8 gap-6 transition-all duration-300"
        :class="[
            disabled
                ? 'opacity-60 grayscale pointer-events-none'
                : 'bg-white/1 hover:bg-white/3'
        ]">

        <div class="flex-1 flex items-center">
            <div class="space-y-1">
                <h4 class="font-chillax text-lg text-white uppercase tracking-widest">
                    {{ title }}
                </h4>
                <p v-if="description" class="text-white/80 text-xs uppercase tracking-widest">
                    {{ description }}
                </p>
                <p v-if="available_stock !== null && available_stock !== undefined && available_stock < ((max_per_order ?? 10) * 2) && !disabled"
                    class="text-orange-400 text-[10px] font-bold uppercase tracking-[0.2em] pt-1">
                    Il ne reste plus que {{ available_stock }} places
                </p>
            </div>
        </div>

        <div class="flex items-center justify-between sm:justify-end gap-10">
            <div class="text-right">
                <p class="text-2xl font-chillax text-white tracking-tighter">
                    <span class="sr-only">Prix : </span>{{ price }}€
                </p>
            </div>

            <!-- Consistent size container for both selector and badges -->
            <div class="w-45 flex justify-end">
                <div v-if="!disabled"
                    class="flex items-center gap-6 bg-black/40 rounded-full p-1.5 border border-white/10 shadow-inner w-full justify-between"
                    role="group" :aria-label="`Quantité pour ${title}`">
                    <button @click="removeItem"
                        class="h-10 w-10 flex items-center justify-center rounded-full bg-white/5 text-white hover:bg-[#51A687] hover:text-black transition-all duration-300 disabled:opacity-10"
                        :disabled="quantity === 0"
                        :aria-label="`Retirer un ${title}`">
                        <Minus class="w-4 h-4" aria-hidden="true" />
                    </button>
                    <span class="w-6 text-center font-chillax text-2xl text-white" aria-live="polite">
                        <span class="sr-only">Quantité sélectionnée : </span>{{ quantity }}
                    </span>
                    <button @click="addItem"
                        class="h-10 w-10 flex items-center justify-center rounded-full bg-white/5 text-white hover:bg-[#51A687] hover:text-black transition-all duration-300 disabled:opacity-10"
                        :disabled="quantity === maxAllowed"
                        :aria-label="`Ajouter un ${title}`">
                        <Plus class="w-4 h-4" aria-hidden="true" />
                    </button>
                </div>
                <div v-else
                    class="flex items-center justify-center w-full h-13.5 rounded-full bg-white/10 border border-white/20 backdrop-blur-md">
                    <span class="text-[10px] font-bold tracking-[0.3em] uppercase text-white">
                        <span class="sr-only">Statut : </span>{{ disabled_reason }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
