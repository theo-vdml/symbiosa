<script setup lang="ts">
    import { computed, HTMLAttributes } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import { cn } from '@/lib/utils';
    import { Loader2 } from '@lucide/vue';

    defineOptions({
        inheritAttrs: false,
    });

    interface Props {
        href?: string;
        as?: 'a' | 'button' | typeof Link;
        variant?: 'primary' | 'outline' | 'glass' | 'ghost';
        size?: 'sm' | 'md' | 'lg';
        class?: HTMLAttributes['class'];
        external?: boolean;
        loading?: boolean;
        disabled?: boolean;
    }

    const props = withDefaults(defineProps<Props>(), {
        variant: 'primary',
        size: 'md',
        as: 'button',
        loading: false,
        disabled: false,
    });

    const componentType = computed(() => {
        if (props.href) {
            return props.external ? 'a' : Link;
        }
        return props.as;
    });

    const variantClasses = {
        primary: 'border border-white/10 bg-white/5 text-white hover:bg-white/10',
        outline: 'border border-white/10 bg-white/5 text-white hover:bg-white/10',
        glass: 'border border-white/10 bg-white/5 text-white hover:bg-white/10',
        ghost: 'text-gray-500 hover:text-white',
    };

    const sizeClasses = {
        sm: 'px-4 py-2 text-sm',
        md: 'px-8 py-4 text-sm tracking-wide uppercase',
        lg: 'px-8 py-4 text-base font-bold tracking-wide uppercase',
    };

    const baseClasses =
        'group inline-flex items-center justify-center gap-2 font-bold rounded-full transition-all duration-300 text-center disabled:opacity-80 disabled:cursor-not-allowed';

    const computedClasses = computed(() => {
        return cn(
            baseClasses,
            variantClasses[props.variant],
            sizeClasses[props.size],
            props.class
        );
    });

</script>

<template>
    <component :is="componentType" :href="href" :disabled="disabled || loading" :class="computedClasses"
        v-bind="$attrs">
        <Loader2 v-if="loading" class="w-4 h-4 animate-spin shrink-0" />
        <slot name="left-icon" />
        <slot />
        <slot name="right-icon" />
    </component>
</template>
