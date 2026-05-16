<script setup lang="ts">
    import NavItem from '@/components/NavItem.vue';
    import events from '@/routes/events';
    import { Link, usePage } from '@inertiajs/vue3';
    import { ref, watch } from 'vue';

    const isMenuOpen = ref(false);
    const page = usePage();

    // Fermer le menu lors d'un changement de route
    watch(() => page.url, () => {
        isMenuOpen.value = false;
    });

    const menuItems = [
        { to: events.index.url(), label: 'Calendrier' },
        { to: '/archives', label: 'Archives' },
        { to: '/news', label: 'Actualité' },
        { to: '/about', label: 'A Propos' },
        { to: '/contact', label: 'Contact' },
    ];
</script>

<template>
    <nav
        class="fixed top-0 left-0 z-50 flex w-full items-center justify-between p-8 md:p-12 mix-blend-difference pointer-events-none">

        <!-- Desktop Navigation Left -->
        <div class="hidden md:block pointer-events-auto w-full">
            <ul class="flex items-center gap-8 font-synonym text-sm font-medium tracking-widest text-white uppercase">
                <NavItem :to="events.index.url()" label="Calendrier" />
                <NavItem to="/archives" label="Archives" />
            </ul>
        </div>

        <!-- Logo -->
        <Link href="/" class="pointer-events-auto shrink-0">
            <span class="font-chillax text-2xl text-white">
                Symbiosa
            </span>
        </Link>

        <!-- Desktop Navigation Right -->
        <div class="hidden md:block pointer-events-auto w-full">
            <ul
                class="flex items-center justify-end gap-8 font-synonym text-sm font-medium tracking-widest text-white uppercase">
                <NavItem to="/news" label="Actualité" />
                <NavItem to="/about" label="A Propos" />
                <NavItem to="/contact" label="Contact" />
            </ul>
        </div>

        <!-- Burger Menu Button -->
        <button
            @click="isMenuOpen = !isMenuOpen"
            class="group pointer-events-auto flex flex-col gap-1.5 md:hidden focus:outline-none relative z-50"
            aria-label="Toggle menu"
        >
            <div
                class="h-px w-6 bg-white transition-all duration-300 ease-in-out origin-center"
                :class="{ 'rotate-45 translate-y-[3.5px]': isMenuOpen }"
            ></div>
            <div
                class="h-px w-6 bg-white transition-all duration-300 ease-in-out origin-center"
                :class="{ '-rotate-45 -translate-y-[3.5px]': isMenuOpen }"
            ></div>
        </button>

        <!-- Mobile Menu Overlay -->
        <Teleport to="body">
            <Transition
                enter-active-class="transition duration-700 ease-[0.76,0,0.24,1]"
                enter-from-class="-translate-y-full"
                enter-to-class="translate-y-0"
                leave-active-class="transition duration-500 ease-[0.76,0,0.24,1]"
                leave-from-class="translate-y-0"
                leave-to-class="-translate-y-full"
            >
                <div
                    v-if="isMenuOpen"
                    class="fixed inset-0 z-40 bg-black flex flex-col items-center justify-center p-8"
                >
                    <ul class="flex flex-col items-center gap-10 font-synonym text-2xl font-medium tracking-[0.2em] text-white uppercase">
                        <NavItem 
                            v-for="item in menuItems" 
                            :key="item.to"
                            :to="item.to" 
                            :label="item.label"
                        />
                    </ul>
                </div>
            </Transition>
        </Teleport>
    </nav>
</template>
