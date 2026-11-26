<script setup lang="ts">
import { useId } from 'vue';
import type { Component } from 'vue';

const props = defineProps<{
    modelValue: string;
    type?: string;
    placeholder?: string;
    icon?: Component;
    error?: string;
    maxlength?: number;
    disabled?: boolean;
    inputClass?: string;
}>();

defineEmits(['update:modelValue']);

const id = useId();
</script>

<template>
    <div class="space-y-2">
        <div class="group relative">
            <label :for="id" class="sr-only">{{ placeholder }}</label>
            <component
                v-if="icon"
                :is="icon"
                class="absolute top-1/2 left-5 h-4 w-4 -translate-y-1/2 transition-colors"
                :class="
                    disabled
                        ? 'text-white/50'
                        : 'text-white/80 group-focus-within:text-[#51A687]'
                "
            />
            <input
                :id="id"
                :value="modelValue"
                @input="
                    $emit(
                        'update:modelValue',
                        ($event.target as HTMLInputElement).value,
                    )
                "
                :type="type || 'text'"
                :placeholder="placeholder"
                :maxlength="maxlength"
                :disabled="disabled"
                :aria-invalid="!!error"
                :aria-describedby="error ? `${id}-error` : undefined"
                class="w-full rounded-2xl border border-white/20 bg-white/10 py-5 pr-6 text-sm text-white transition-all outline-none placeholder:text-white/40 focus:border-[#51A687]/50 focus:ring-0 disabled:cursor-not-allowed disabled:border-white/15 disabled:bg-white/5 disabled:text-white/60 disabled:placeholder:text-white/30"
                :class="[
                    icon ? 'pl-14' : 'pl-6',
                    error ? 'border-red-500/50' : '',
                    inputClass,
                ]"
            />
        </div>
        <p
            v-if="error"
            :id="`${id}-error`"
            class="ml-5 text-[10px] font-bold tracking-widest text-red-400 uppercase"
            role="alert"
        >
            {{ error }}
        </p>
    </div>
</template>
