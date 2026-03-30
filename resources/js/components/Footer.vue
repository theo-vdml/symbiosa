<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <footer class="sticky bottom-0 h-fit w-full bg-white text-black">
        <div class="flex h-full flex-col items-center justify-between px-12 pt-24 pb-4">
            <div class="grid w-full max-w-7xl grid-cols-1 gap-12 md:grid-cols-3">
                <!-- Left Column: Navigation -->
                <div class="flex flex-col space-y-1 text-center md:text-left">
                    <Link href="/" class="text-base cursor-pointer transition-colors hover:underline">Home</Link>
                    <Link href="/calendar" class="text-base cursor-pointer transition-colors hover:underline">Calendrier</Link>
                    <Link href="/archives" class="text-base cursor-pointer transition-colors hover:underline">Archives</Link>
                    <Link href="/about" class="text-base cursor-pointer transition-colors hover:underline">A propos</Link>
                    <Link href="/contact" class="text-base cursor-pointer transition-colors hover:underline">Contact</Link>
                    <Link href="/status" class="text-base cursor-pointer transition-colors hover:underline">Status ASBL</Link>
                    <Link href="/legal" class="text-base cursor-pointer transition-colors hover:underline">Mentions Légales</Link>
                    <Link href="/roi" class="text-base cursor-pointer transition-colors hover:underline">R.O.I</Link>
                </div>

                <!-- Middle Column: Info -->
                <div class="flex flex-col items-center justify-center space-y-4 border-black/20 md:border-x">
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
                    <div>
                        <p class="mb-1 font-bold">Envie de nous poser une question ?</p>
                        <a href="mailto:hi@symbiosa.be" class="hover:underline">hi@symbiosa.be</a>
                    </div>
                    <div>
                        <p class="mb-1 font-bold">Envie de devenir un sponsors ?</p>
                        <a href="mailto:sponsors@symbiosa.be" class="hover:underline">sponsors@symbiosa.be</a>
                    </div>
                    <div>
                        <p class="mb-1 font-bold">Envie de devenir bénévole ?</p>
                        <a href="mailto:team@symbiosa.be" class="hover:underline">team@symbiosa.be</a>
                    </div>
                </div>
            </div>

            <div class="relative mt-24 flex w-full items-center justify-center overflow-visible">
                <h2 class="font-chillax text-[8vw] select-none md:text-[14rem]">
                    Symbiosa
                </h2>
            </div>
        </div>
    </footer>
</template>
