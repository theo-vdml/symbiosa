<script setup lang="ts">

    import { Link } from '@inertiajs/vue3';

    defineProps<{
        to: string
        label: string
    }>()

</script>

<template>
    <li class="group relative py-1">
        <Link
            :href="to"
            class="transition-colors hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-white/50 rounded-sm px-1"
            >{{ label }}</Link
        >
        <span
            class="absolute bottom-0 left-0 h-px w-full scale-x-0 bg-white transition-transform duration-300 origin-left group-hover:scale-x-100 group-focus-within:scale-x-100"
        ></span>
    </li>
</template>
