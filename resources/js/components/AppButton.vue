<script setup lang="ts">
    import { computed } from 'vue';
    import { Link } from '@inertiajs/vue3';
    import { cn } from '@/lib/utils';

    import { Loader2 } from '@lucide/vue';

    interface Props {
        href?: string;
        as?: 'a' | 'button' | typeof Link;
        variant?: 'primary' | 'outline' | 'glass' | 'ghost';
        size?: 'sm' | 'md' | 'lg';
        className?: string;
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
        'group inline-flex items-center justify-center gap-2 font-bold rounded-full transition-all duration-300 text-center disabled:opacity-50 disabled:cursor-not-allowed';
</script>

<template>
    <component :is="componentType" :href="href" :disabled="disabled || loading" :class="cn(
        baseClasses,
        variantClasses[variant],
        sizeClasses[size],
        className,
    )
        ">
        <Loader2 v-if="loading" class="w-4 h-4 animate-spin shrink-0" />
        <slot name="left-icon" />
        <slot />
        <slot name="right-icon" />
    </component>
</template>
