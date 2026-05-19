<script setup lang="ts">
    import { Link, usePage } from '@inertiajs/vue3';
    import { computed } from 'vue';

    const page = usePage();
    const footerLegalPages = computed(() => page.props.footerLegalPages as Array<{ title: string, slug: string }>);
    const contactEmails = computed(() => page.props.contactEmails as Array<{ label: string, email: string }>);
</script>

<template>
    <footer class="relative z-0 h-fit w-full bg-white text-black md:sticky md:bottom-0">
        <div class="flex h-full flex-col items-center justify-between px-12 pt-16 pb-4 md:pt-24">
            <div class="grid w-full max-w-7xl grid-cols-1 gap-12 md:grid-cols-3">
                <!-- Left Column: Navigation -->
                <div class="flex flex-col space-y-1 text-center md:text-left">
                    <Link href="/" class="text-base cursor-pointer transition-colors hover:underline">Home</Link>
                    <Link href="/agenda" class="text-base cursor-pointer transition-colors hover:underline">Calendrier
                    </Link>
                    <Link href="/archives" class="text-base cursor-pointer transition-colors hover:underline">Archives
                    </Link>
                    <Link href="/about" class="text-base cursor-pointer transition-colors hover:underline">A propos
                    </Link>
                    <Link href="/contact" class="text-base cursor-pointer transition-colors hover:underline">Contact
                    </Link>

                    <Link v-for="legalPage in footerLegalPages" :key="legalPage.slug" :href="`/legal/${legalPage.slug}`"
                        class="text-base cursor-pointer transition-colors hover:underline">
                    {{ legalPage.title }}
                    </Link>
                </div>

                <!-- Middle Column: Info -->
                <div class="flex flex-col items-center justify-center space-y-4 py-8 border-y border-black/10 md:py-0 md:border-y-0 md:border-x md:border-black/20">
                    <div class="text-center text-base space-y-1">
                        <p class="font-bold uppercase tracking-widest">Symbiosa ASBL</p>
                        <p>Rue de la rue n°12</p>
                        <p>5030 Gembloux,</p>
                        <p>Belgique</p>
                    </div>
                    <div class="h-px w-8 bg-black/20"></div>
                    <div class="text-xs">
                        <p class="font-bold">TVA: 0120.9303.29029</p>
                    </div>
                </div>

                <!-- Right Column: Contact -->
                <div class="flex flex-col space-y-6 text-center text-base md:text-right">
                    <div v-for="option in contactEmails" :key="option.email">
                        <p class="mb-1 font-bold">{{ option.label }}</p>
                        <a :href="`mailto:${option.email}`" class="hover:underline">{{ option.email }}</a>
                    </div>
                </div>
            </div>

            <div class="relative mt-16 flex w-full items-center justify-center md:mt-24">
                <h2 class="font-chillax text-[15vw] leading-[0.8] select-none md:text-[8vw] lg:text-[12rem] xl:text-[14rem]">
                    Symbiosa
                </h2>
            </div>
        </div>
    </footer>
</template>
