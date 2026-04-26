<script setup lang="ts">
    import { Head } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';
</script>

<template>

    <Head title="Actualité - Dans les coulisses de EDEN 2026" />

    <Header />

    <div class="relative z-10 min-h-[120vh] overflow-hidden rounded-b-[6rem] bg-black">
        <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-black via-black to-black"></div>
        <div
            class="pointer-events-none absolute -top-32 left-1/2 h-115 w-[130%] -translate-x-1/2 rounded-full bg-[#06402B]/18 blur-[150px]">
        </div>
        <div class="pointer-events-none absolute inset-0 bg-[url('/noise.png')] opacity-[0.04] mix-blend-soft-light">
        </div>

        <main class="relative z-10 mx-auto max-w-6xl px-6 pb-24 pt-32 md:px-10 lg:px-14">
            <section class="mx-auto mb-16 max-w-4xl">
                <div
                    class="relative overflow-hidden rounded-xl bg-black/40 shadow-[0_30px_80px_rgba(0,0,0,0.45)] ring-1 ring-white/10">
                    <img src="/photo_07.jpg" alt="EDEN 2026 coulisses" class="h-full w-full object-cover" />
                    <div class="pointer-events-none absolute inset-0 bg-black/18"></div>
                </div>
            </section>

            <section class="mx-auto mb-10 max-w-4xl space-y-5 text-center md:text-left">
                <p class="text-sm font-medium text-gray-400">
                    Jeudi 3 avril 2026
                </p>
                <h1 class="font-chillax text-4xl leading-[0.95] text-white md:text-6xl lg:text-7xl">
                    Dans les coulisses de EDEN 2026, une edition pensee comme un vrai recit de nuit
                </h1>
                <p class="font-synonym text-base leading-relaxed text-gray-300">
                    Avant meme l'ouverture des portes, EDEN 2026 se construit comme un article de fond a vivre sur place
                    : une nuit imaginee
                    avec plus de precision, plus de respiration et une attention particuliere accordee a ce que le
                    public ressent entre deux
                    moments forts.
                </p>
            </section>

            <article class="mx-auto max-w-4xl space-y-10 font-synonym text-base leading-relaxed text-gray-300">
                <p>
                    Il y a quelques mois encore, EDEN 2026 n'etait qu'une intuition partagee entre plusieurs membres du
                    collectif : celle de
                    faire moins dans la surenchere visible, et davantage dans la coherence globale. L'objectif n'est
                    plus seulement d'enchainer
                    les temps forts, mais de construire une progression sensible, presque narrative, ou chaque detail
                    soutient le suivant.
                </p>

                <section class="space-y-5">
                    <h2 class="font-chillax text-3xl leading-none text-white md:text-4xl">Une ecriture plus lente, plus
                        precise</h2>
                    <p>
                        Le travail commence bien avant la programmation. Cette annee, l'equipe a d'abord defini
                        l'intention generale de la nuit :
                        un depart presque suspendu, une montee progressive de la tension, puis une derniere partie plus
                        dense, plus physique, sans
                        jamais perdre la sensation d'espace. Cette approche change la maniere de penser l'ensemble : la
                        lumiere, le rythme des
                        transitions, la lecture du lieu, jusqu'a la facon dont le public se deplace dans l'evenement.
                    </p>
                    <p>
                        La direction artistique ne cherche donc pas a remplir chaque seconde. Au contraire, elle assume
                        des respirations, des zones
                        plus sombres, des instants ou l'on laisse simplement le son et la matiere visuelle prendre leur
                        place. C'est ce type de
                        nuance qui donne aujourd'hui a Symbiosa une identite plus mature, moins demonstrative, mais
                        paradoxalement plus marquante.
                    </p>
                </section>

                <blockquote
                    class="border-l-2 border-[#06402B]/70 pl-5 font-chillax text-2xl leading-tight text-white md:text-3xl">
                    "On veut que l'experience laisse une impression durable, pas seulement une accumulation de moments
                    spectaculaires."
                </blockquote>

                <section class="space-y-5">
                    <h2 class="font-chillax text-3xl leading-none text-white md:text-4xl">Le son, la matiere, la
                        circulation</h2>
                    <p>
                        Dans les coulisses, une grande partie du travail se joue sur des choses presque invisibles.
                        Comment faire respirer une
                        scenographie sans l'ecraser sous la technique ? Comment garder un son ample, physique, mais
                        lisible ? Comment produire une
                        ambiance dense sans transformer le lieu en decor rigide ? Ces questions occupent une place
                        centrale dans la preparation.
                    </p>
                    <p>
                        Le son a ete repense pour gagner en profondeur plutot qu'en brutalite. La lumiere, elle, suit
                        une logique plus organique,
                        avec des variations qui accompagnent les etats de la nuit au lieu de les surligner. Meme la
                        circulation du public est
                        consideree comme une partie du recit : ce qu'on decouvre en arrivant, ce qu'on apercoit en se
                        retournant, ce qu'on percoit
                        a travers le mouvement des autres.
                    </p>
                </section>

                <figure class="grid gap-5 sm:grid-cols-2">
                    <img src="/photo_08.jpg" alt="Setup lumiere"
                        class="h-full w-full rounded-xl object-cover shadow-[0_20px_50px_rgba(0,0,0,0.25)]" />
                    <img src="/photo_09.jpg" alt="Ambiance public"
                        class="h-full w-full rounded-xl object-cover shadow-[0_20px_50px_rgba(0,0,0,0.25)]" />
                </figure>

                <section class="space-y-5">
                    <h2 class="font-chillax text-3xl leading-none text-white md:text-4xl">Une edition qui assume plus de
                        fond</h2>
                    <p>
                        Ce qui se dessine avec EDEN 2026, c'est peut-etre moins un changement de format qu'un changement
                        de posture. Le collectif ne
                        cherche plus uniquement a impressionner ; il cherche a installer une signature. Une maniere de
                        recevoir, de raconter, de
                        mettre en scene la nuit qui semble moins liee a l'effet immediat et davantage a la memoire que
                        l'evenement laisse ensuite.
                    </p>
                    <p>
                        Pour un public, cette difference est subtile sur le papier. Mais sur place, elle change tout. On
                        ne traverse plus seulement
                        une programmation ; on entre dans une atmosphere qui a ete pensee dans sa continuité. Et c'est
                        probablement la que cette
                        edition trouvera sa vraie force.
                    </p>
                </section>
            </article>
        </main>
    </div>

    <Footer />
</template>
