<script setup lang="ts">
import { Minus, Plus } from '@lucide/vue';
import { computed } from 'vue';

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
    (
        e: 'update-quantity',
        details: { type: 'ticket' | 'addon'; id: number; priceId?: number },
        change: number,
        maxPerOrder?: number,
    ): void;
}>();

const maxAllowed = computed(() => {
    const maxPO = props.max_per_order ?? 10;

    if (props.available_stock === null || props.available_stock === undefined) {
        return maxPO;
    }

    return Math.min(props.available_stock, maxPO);
});

const addItem = () => {
    if (props.quantity < maxAllowed.value) {
        emit(
            'update-quantity',
            { type: props.type, id: props.id, priceId: props.priceId },
            1,
            maxAllowed.value,
        );
    }
};

const removeItem = () => {
    if (props.quantity > 0) {
        emit(
            'update-quantity',
            { type: props.type, id: props.id, priceId: props.priceId },
            -1,
            maxAllowed.value,
        );
    }
};
</script>
<template>
    <div
        class="group/price relative flex flex-col justify-between gap-6 p-8 transition-all duration-300 sm:flex-row sm:items-center"
        :class="[
            disabled
                ? 'pointer-events-none opacity-60 grayscale'
                : 'bg-white/1 hover:bg-white/3',
        ]"
    >
        <div class="flex flex-1 items-center">
            <div class="space-y-1">
                <h4
                    class="font-chillax text-lg tracking-widest text-white uppercase"
                >
                    {{ title }}
                </h4>
                <p
                    v-if="description"
                    class="text-xs tracking-widest text-white/80 uppercase"
                >
                    {{ description }}
                </p>
                <p
                    v-if="
                        available_stock !== null &&
                        available_stock !== undefined &&
                        available_stock < (max_per_order ?? 10) * 2 &&
                        !disabled
                    "
                    class="pt-1 text-[10px] font-bold tracking-[0.2em] text-orange-400 uppercase"
                >
                    Il ne reste plus que {{ available_stock }} places
                </p>
            </div>
        </div>

        <div class="flex items-center justify-between gap-10 sm:justify-end">
            <div class="text-right">
                <p class="font-chillax text-2xl tracking-tighter text-white">
                    <span class="sr-only">Prix : </span>{{ price }}€
                </p>
            </div>

            <!-- Consistent size container for both selector and badges -->
            <div class="flex w-45 justify-end">
                <div
                    v-if="!disabled"
                    class="flex w-full items-center justify-between gap-6 rounded-full border border-white/10 bg-black/40 p-1.5 shadow-inner"
                    role="group"
                    :aria-label="`Quantité pour ${title}`"
                >
                    <button
                        @click="removeItem"
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-white/5 text-white transition-all duration-300 hover:bg-[#51A687] hover:text-black disabled:opacity-10"
                        :disabled="quantity === 0"
                        :aria-label="`Retirer un ${title}`"
                    >
                        <Minus class="h-4 w-4" aria-hidden="true" />
                    </button>
                    <span
                        class="w-6 text-center font-chillax text-2xl text-white"
                        aria-live="polite"
                    >
                        <span class="sr-only">Quantité sélectionnée : </span
                        >{{ quantity }}
                    </span>
                    <button
                        @click="addItem"
                        class="flex h-10 w-10 items-center justify-center rounded-full bg-white/5 text-white transition-all duration-300 hover:bg-[#51A687] hover:text-black disabled:opacity-10"
                        :disabled="quantity === maxAllowed"
                        :aria-label="`Ajouter un ${title}`"
                    >
                        <Plus class="h-4 w-4" aria-hidden="true" />
                    </button>
                </div>
                <div
                    v-else
                    class="flex h-13.5 w-full items-center justify-center rounded-full border border-white/20 bg-white/10 backdrop-blur-md"
                >
                    <span
                        class="text-[10px] font-bold tracking-[0.3em] text-white uppercase"
                    >
                        <span class="sr-only">Statut : </span
                        >{{ disabled_reason }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
