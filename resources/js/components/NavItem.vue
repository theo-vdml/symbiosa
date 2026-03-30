<script setup lang="ts">

    defineProps<{
        to: string
        label: string
    }>()

</script>

<template>
    <li class="group relative overflow-hidden py-1">
        <a
            :href="to"
            class="transition-colors hover:text-white"
            >{{ label }}</a
        >
        <span
            class="absolute bottom-0 left-0 h-px w-full translate-x-[-101%] bg-white transition-transform duration-300 group-hover:translate-x-0"
        ></span>
    </li>
</template>
