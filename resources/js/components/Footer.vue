<script setup lang="ts">
    import { Link, usePage } from '@inertiajs/vue3';
    import { computed, ref } from 'vue';

    const page = usePage();
    const footerLegalPages = computed(() => page.props.footerLegalPages as Array<{ title: string, slug: string }>);
    const contactEmails = computed(() => page.props.contactEmails as Array<{ label: string, email: string }>);
</script>

<template>
    <footer
        class="relative z-0 h-fit w-full bg-white text-black md:sticky md:bottom-0 focus-within:relative focus-within:z-20">
        <h2 class="sr-only">Informations de pied de page - Symbiosa</h2>
        <div class="flex h-full flex-col items-center justify-between px-12 pt-16 pb-4 md:pt-24">
            <div class="grid w-full max-w-7xl grid-cols-1 gap-12 md:grid-cols-3">
                <nav aria-label="Navigation pied de page" class="space-y-1 text-center md:text-left">
                    <h3 class="sr-only">Liens utiles</h3>
                    <ul class="list-none flex flex-col">
                        <li>
                            <Link href="/"
                                class="text-base cursor-pointer transition-colors hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-black">
                                Accueil
                            </Link>
                        </li>
                        <li>
                            <Link href="/agenda"
                                class="text-base cursor-pointer transition-colors hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-black">
                                Calendrier
                            </Link>
                        </li>
                        <li>
                            <Link href="/archives"
                                class="text-base cursor-pointer transition-colors hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-black">
                                Archives
                            </Link>
                        </li>
                        <li>
                            <Link href="/about"
                                class="text-base cursor-pointer transition-colors hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-black">
                                À propos
                            </Link>
                        </li>
                        <li>
                            <Link href="/contact"
                                class="text-base cursor-pointer transition-colors hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-black">
                                Contact
                            </Link>
                        </li>
                        <li>
                            <Link href="/lost-tickets"
                                class="text-base cursor-pointer transition-colors hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-black">
                                J'ai perdu mes billets
                            </Link>
                        </li>

                        <li v-for="legalPage in footerLegalPages" :key="legalPage.slug">
                            <Link :href="`/legal/${legalPage.slug}`"
                                class="text-base cursor-pointer transition-colors hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-black">
                                {{ legalPage.title }}
                            </Link>
                        </li>
                    </ul>
                </nav>

                <!-- Middle Column: Info -->
                <div
                    class="flex flex-col items-center justify-center space-y-4 py-8 border-y border-black/10 md:py-0 md:border-y-0 md:border-x md:border-black/20">
                    <h3 class="sr-only">Coordonnées de l'association</h3>
                    <address class="text-center text-base space-y-1 not-italic">
                        <p class="font-bold uppercase tracking-widest">Symbiosa ASBL</p>
                        <p>Rue de la rue n°12</p>
                        <p>5030 Gembloux,</p>
                        <p>Belgique</p>
                    </address>
                    <div class="h-px w-8 bg-black/20"></div>
                    <div class="text-xs">
                        <p class="font-bold">TVA: <span translate="no">0120.9303.29029</span></p>
                    </div>
                </div>

                <!-- Right Column: Contact -->
                <div class="flex flex-col space-y-6 text-center text-base md:text-right">
                    <h3 class="sr-only">Contactez-nous</h3>
                    <div v-for="option in contactEmails" :key="option.email">
                        <p class="mb-1 font-bold">{{ option.label }}</p>
                        <a :href="`mailto:${option.email}`"
                            class="hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-black">{{
                                option.email }}</a>
                    </div>
                </div>
            </div>

            <div class="relative mt-16 flex w-full items-center justify-center md:mt-24">
                <div class="font-chillax text-[15vw] leading-[0.8] select-none md:text-[8vw] lg:text-[12rem] xl:text-[14rem]"
                    aria-hidden="true">
                    Symbiosa
                </div>
            </div>
        </div>
    </footer>
</template>
