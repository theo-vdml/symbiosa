<script setup lang="ts">
    import { Minus, Plus } from '@lucide/vue';

    type ItemKey = `ticket_${number}_price_${number}` | `addon_${number}`;

    const props = defineProps<{
        itemKey: ItemKey;
        title: string;
        description?: string;
        price: number;
        quantity: number;
        max_per_order?: number;
        disabled?: boolean;
        disabled_reason?: string;
    }>();

    const emit = defineEmits<{
        (e: 'update-quantity', itemKey: ItemKey, change: number, maxPerOrder?: number): void;
    }>();

    const addItem = () => {
        if (props.quantity < (props.max_per_order || 99)) {
            emit('update-quantity', props.itemKey, 1, props.max_per_order);
        }
    };

    const removeItem = () => {
        if (props.quantity > 0) {
            emit('update-quantity', props.itemKey, -1, props.max_per_order);
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
            </div>
        </div>

        <div class="flex items-center justify-between sm:justify-end gap-10">
            <div class="text-right">
                <p class="text-2xl font-chillax text-white tracking-tighter">{{
                    price }}€
                </p>
            </div>

            <!-- Consistent size container for both selector and badges -->
            <div class="w-45 flex justify-end">
                <div v-if="!disabled"
                    class="flex items-center gap-6 bg-black/40 rounded-full p-1.5 border border-white/10 shadow-inner w-full justify-between">
                    <button @click="removeItem"
                        class="h-10 w-10 flex items-center justify-center rounded-full bg-white/5 text-white hover:bg-[#51A687] hover:text-black transition-all duration-300 disabled:opacity-10"
                        :disabled="quantity === 0">
                        <Minus class="w-4 h-4" />
                    </button>
                    <span class="w-6 text-center font-chillax text-2xl text-white">{{
                        quantity
                    }}</span>
                    <button @click="addItem"
                        class="h-10 w-10 flex items-center justify-center rounded-full bg-white/5 text-white hover:bg-[#51A687] hover:text-black transition-all duration-300 disabled:opacity-10"
                        :disabled="quantity === (max_per_order || 99)">
                        <Plus class="w-4 h-4" />
                    </button>
                </div>
                <div v-else
                    class="flex items-center justify-center w-full h-13.5 rounded-full bg-white/10 border border-white/20 backdrop-blur-md">
                    <span class="text-[10px] font-bold tracking-[0.3em] uppercase text-white">
                        {{ disabled_reason }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</template>
