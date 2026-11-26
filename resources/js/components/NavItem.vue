<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

defineProps<{
    to: string;
    label: string;
}>();
</script>

<template>
    <li class="group relative py-1">
        <Link
            :href="to"
            class="rounded-sm px-1 transition-colors hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white/50"
            >{{ label }}</Link
        >
        <span
            class="absolute bottom-0 left-0 h-px w-full origin-left scale-x-0 bg-white transition-transform duration-300 group-focus-within:scale-x-100 group-hover:scale-x-100"
        ></span>
    </li>
</template>
