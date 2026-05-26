<script setup lang="ts">
    import NavItem from '@/components/NavItem.vue';
    import events from '@/routes/events';
    import { Link, usePage } from '@inertiajs/vue3';
    import { onKeyStroke, useScrollLock } from '@vueuse/core';
    import { ref, watch, nextTick } from 'vue';

    const isMenuOpen = ref(false);
    const page = usePage();
    const menuRef = ref<HTMLElement | null>(null);
    const burgerRef = ref<HTMLElement | null>(null);

    const isLocked = useScrollLock(typeof window !== 'undefined' ? document.body : null);

    // Focus trap logic
    const getFocusableElements = () => {
        const elements: HTMLElement[] = [];
        if (burgerRef.value) elements.push(burgerRef.value);
        if (menuRef.value) {
            const focusableInMenu = Array.from(
                menuRef.value.querySelectorAll('a, button, input, textarea, select, [tabindex]:not([tabindex="-1"])')
            ) as HTMLElement[];
            elements.push(...focusableInMenu);
        }
        return elements;
    };

    const handleTab = (e: KeyboardEvent) => {
        if (!isMenuOpen.value) return;

        const elements = getFocusableElements();
        if (elements.length <= 1) return;

        const first = elements[0]; // Burger
        const last = elements[elements.length - 1]; // Last menu item
        const firstMenuItem = elements[1]; // First menu item

        if (e.shiftKey) {
            if (document.activeElement === first) {
                last.focus();
                e.preventDefault();
            } else if (document.activeElement === firstMenuItem) {
                first.focus();
                e.preventDefault();
            }
        } else {
            if (document.activeElement === first) {
                firstMenuItem.focus();
                e.preventDefault();
            } else if (document.activeElement === last) {
                first.focus();
                e.preventDefault();
            }
        }
    };

    onKeyStroke('Tab', (e) => {
        handleTab(e);
    });

    onKeyStroke('Escape', () => {
        isMenuOpen.value = false;
    });

    // Fermer le menu lors d'un changement de route
    watch(() => page.url, () => {
        isMenuOpen.value = false;
    });

    watch(isMenuOpen, (value) => {
        isLocked.value = value;
        if (value) {
            nextTick(() => {
                const elements = getFocusableElements();
                if (elements.length > 1) {
                    elements[1].focus();
                }
            });
        } else {
            nextTick(() => {
                burgerRef.value?.focus();
            });
        }
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
    <nav aria-label="Main navigation"
        class="fixed top-0 left-0 z-50 flex w-full items-center justify-between p-8 lg:p-12 mix-blend-difference pointer-events-none">

        <!-- Desktop Navigation Left -->
        <div class="hidden lg:block pointer-events-auto w-full">
            <ul class="flex items-center gap-8 font-synonym text-sm font-medium tracking-widest text-white uppercase">
                <NavItem :to="events.index.url()" label="Calendrier" />
                <NavItem to="/archives" label="Archives" />
            </ul>
        </div>

        <!-- Logo -->
        <Link href="/"
            class="pointer-events-auto shrink-0 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/50 rounded-sm px-2"
            aria-label="Symbiosa home">
            <span class="font-chillax text-2xl text-white">
                Symbiosa
            </span>
        </Link>

        <!-- Desktop Navigation Right -->
        <div class="hidden lg:block pointer-events-auto w-full">
            <ul
                class="flex items-center justify-end gap-8 font-synonym text-sm font-medium tracking-widest text-white uppercase">
                <NavItem to="/news" label="Actualité" />
                <NavItem to="/about" label="A Propos" />
                <NavItem to="/contact" label="Contact" />
            </ul>
        </div>

        <!-- Burger Menu Button -->
        <button ref="burgerRef" @click="isMenuOpen = !isMenuOpen"
            class="group pointer-events-auto flex flex-col gap-1.5 lg:hidden focus:outline-none focus-visible:ring-2 focus-visible:ring-white/50 rounded-sm p-2 relative z-50"
            :aria-expanded="isMenuOpen" aria-controls="mobile-menu"
            :aria-label="isMenuOpen ? 'Close menu' : 'Open menu'">
            <div class="h-px w-6 bg-white transition-all duration-300 ease-in-out origin-center"
                :class="{ 'rotate-45 translate-y-[3.5px]': isMenuOpen }"></div>
            <div class="h-px w-6 bg-white transition-all duration-300 ease-in-out origin-center"
                :class="{ '-rotate-45 translate-y-[-3.5px]': isMenuOpen }"></div>
        </button>

        <!-- Mobile Menu Overlay -->
        <Teleport to="body">
            <Transition enter-active-class="transition duration-700 ease-[0.76,0,0.24,1]"
                enter-from-class="-translate-y-full" enter-to-class="translate-y-0"
                leave-active-class="transition duration-500 ease-[0.76,0,0.24,1]" leave-from-class="translate-y-0"
                leave-to-class="-translate-y-full">
                <div v-if="isMenuOpen" id="mobile-menu" ref="menuRef"
                    class="fixed inset-0 z-40 bg-black flex flex-col items-center justify-center p-8" role="dialog"
                    aria-modal="true" aria-label="Mobile navigation">
                    <ul
                        class="flex flex-col items-center gap-10 font-synonym text-2xl font-medium tracking-[0.2em] text-white uppercase">
                        <NavItem v-for="item in menuItems" :key="item.to" :to="item.to" :label="item.label" />
                    </ul>
                </div>
            </Transition>
        </Teleport>
    </nav>
</template>
