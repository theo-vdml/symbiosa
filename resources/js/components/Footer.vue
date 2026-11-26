<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const page = usePage();
const footerLegalPages = computed(
    () => page.props.footerLegalPages as Array<{ title: string; slug: string }>,
);
const contactEmails = computed(
    () => page.props.contactEmails as Array<{ label: string; email: string }>,
);
const asbl = computed(
    () =>
        page.props.asbl as {
            name: string;
            address: string;
            vat: string | null;
        },
);
</script>

<template>
    <footer
        class="relative z-0 h-fit w-full bg-white text-black focus-within:relative focus-within:z-20 md:sticky md:bottom-0"
    >
        <h2 class="sr-only">Informations de pied de page - Symbiosa</h2>
        <div
            class="flex h-full flex-col items-center justify-between px-12 pt-16 pb-4 md:pt-24"
        >
            <div
                class="grid w-full max-w-7xl grid-cols-1 gap-12 md:grid-cols-3"
            >
                <nav
                    aria-label="Navigation pied de page"
                    class="space-y-1 text-center md:text-left"
                >
                    <h3 class="sr-only">Liens utiles</h3>
                    <ul class="flex list-none flex-col">
                        <li>
                            <Link
                                href="/"
                                class="cursor-pointer text-base transition-colors hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-black"
                            >
                                Accueil
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/events"
                                class="cursor-pointer text-base transition-colors hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-black"
                            >
                                Calendrier
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/archives"
                                class="cursor-pointer text-base transition-colors hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-black"
                            >
                                Archives
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/about"
                                class="cursor-pointer text-base transition-colors hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-black"
                            >
                                À propos
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/contact"
                                class="cursor-pointer text-base transition-colors hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-black"
                            >
                                Contact
                            </Link>
                        </li>
                        <li>
                            <Link
                                href="/lost-tickets"
                                class="cursor-pointer text-base transition-colors hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-black"
                            >
                                J'ai perdu mes billets
                            </Link>
                        </li>

                        <li
                            v-for="legalPage in footerLegalPages"
                            :key="legalPage.slug"
                        >
                            <Link
                                :href="`/legal/${legalPage.slug}`"
                                class="cursor-pointer text-base transition-colors hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-black"
                            >
                                {{ legalPage.title }}
                            </Link>
                        </li>
                    </ul>
                </nav>

                <!-- Middle Column: Info -->
                <div
                    class="flex flex-col items-center justify-center space-y-4 border-y border-black/10 py-8 md:border-x md:border-y-0 md:border-black/20 md:py-0"
                >
                    <h3 class="sr-only">Coordonnées de l'association</h3>
                    <address class="space-y-1 text-center text-base not-italic">
                        <p class="font-bold tracking-widest uppercase">
                            {{ asbl.name }}
                        </p>
                        <p class="whitespace-pre-line">{{ asbl.address }}</p>
                    </address>
                    <div v-if="asbl.vat" class="h-px w-8 bg-black/20"></div>
                    <div v-if="asbl.vat" class="text-xs">
                        <p class="font-bold">
                            TVA: <span translate="no">{{ asbl.vat }}</span>
                        </p>
                    </div>
                </div>

                <!-- Right Column: Contact -->
                <div
                    class="flex flex-col space-y-6 text-center text-base md:text-right"
                >
                    <h3 class="sr-only">Contactez-nous</h3>
                    <div v-for="option in contactEmails" :key="option.email">
                        <p class="mb-1 font-bold">{{ option.label }}</p>
                        <a
                            :href="`mailto:${option.email}`"
                            class="hover:underline focus-visible:outline-2 focus-visible:outline-offset-4 focus-visible:outline-black"
                            >{{ option.email }}</a
                        >
                    </div>
                </div>
            </div>

            <div
                class="relative mt-16 flex w-full items-center justify-center md:mt-24"
            >
                <div
                    class="font-chillax text-[15vw] leading-[0.8] select-none md:text-[8vw] lg:text-[12rem] xl:text-[14rem]"
                    aria-hidden="true"
                >
                    Symbiosa
                </div>
            </div>
        </div>
    </footer>
</template>
