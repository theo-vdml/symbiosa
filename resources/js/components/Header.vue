<script setup lang="ts">
import NavItem from '@/components/NavItem.vue';
import { Link } from '@inertiajs/vue3';
</script>
<template>
    <nav
        class="fixed top-0 left-0 z-50 flex w-full items-center justify-between p-12 mix-blend-difference pointer-events-none"
    >
        <Link href="/">
            <span class="cursor-pointer font-chillax text-2xl text-white pointer-events-auto">
                Symbiosa
            </span>
        </Link>

        <div class="hidden md:block pointer-events-auto">
            <ul
                class="flex items-center gap-8 font-synonym text-sm font-medium tracking-widest text-white uppercase"
            >
                <NavItem to="/agenda" label="Calendrier" />
                <NavItem to="/archives" label="Archives" />
                <NavItem to="/news" label="Actualité" />
                <NavItem to="#" label="A Propos" />
                <NavItem to="#" label="Contact" />
            </ul>
        </div>

        <div class="flex flex-col gap-1.5 md:hidden">
            <div class="h-[1px] w-6 bg-white"></div>
            <div class="h-[1px] w-6 bg-white"></div>
        </div>
    </nav>
</template>
