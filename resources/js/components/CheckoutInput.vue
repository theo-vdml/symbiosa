<script setup lang="ts">
    import type { Component } from 'vue';

    defineProps<{
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
</script>

<template>
    <div class="space-y-2">
        <div class="relative group">
            <component v-if="icon" :is="icon" class="absolute left-5 top-1/2 -translate-y-1/2 w-4 h-4 transition-colors"
                :class="disabled ? 'text-white/50' : 'text-white/80 group-focus-within:text-[#51A687]'" />
            <input :value="modelValue" @input="$emit('update:modelValue', ($event.target as HTMLInputElement).value)"
                :type="type || 'text'" :placeholder="placeholder" :maxlength="maxlength" :disabled="disabled"
                class="w-full bg-white/10 border border-white/20 rounded-2xl pr-6 py-5 text-white placeholder:text-white/40 focus:border-[#51A687]/50 focus:ring-0 transition-all outline-none text-sm disabled:bg-white/5 disabled:border-white/15 disabled:text-white/60 disabled:placeholder:text-white/30 disabled:cursor-not-allowed"
                :class="[
                    icon ? 'pl-14' : 'pl-6',
                    error ? 'border-red-500/50' : '',
                    inputClass
                ]" />
        </div>
        <p v-if="error" class="text-[10px] text-red-400 font-bold uppercase tracking-widest ml-5">
            {{ error }}
        </p>
    </div>
</template>
