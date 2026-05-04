<script setup lang="ts">
    import { ref, computed, onMounted, onUnmounted } from 'vue';
    import { Head } from '@inertiajs/vue3';
    import Header from '@/components/Header.vue';
    import Footer from '@/components/Footer.vue';
    import AppButton from '@/components/AppButton.vue';

    const faqItems = [
        {
            question: "J'ai perdu mes billets",
            answer: "Retrouvez-les dans votre boîte mail (cherchez 'Symbiosa' ou 'Ticket'). Si rien n'apparaît, utilisez le formulaire."
        },
        {
            question: "Devenir Partenaire",
            answer: "Sélectionnez 'Sponsoring' et présentez votre vision. Nous cherchons des collaborations qui font sens."
        },
        {
            question: "Candidature Artiste",
            answer: "Envoyez vos sets. Nous cherchons des identités sonores tranchées et organiques."
        },
        {
            question: "Accès & PMR",
            answer: "La majorité de nos lieux sont adaptés. Contactez-nous pour préparer votre venue dans les meilleures conditions."
        }
    ];

    const form = ref({
        name: '',
        email: '',
        subject: '',
        message: '',
        consent: false
    });

    const subjects = [
        { value: 'helpdesk', label: 'Helpdesk / Billetterie' },
        { value: 'sponsoring', label: 'Sponsoring / Partenariat' },
        { value: 'artiste', label: 'Candidature Artiste' },
        { value: 'benevolat', label: 'Bénévolat' },
        { value: 'autre', label: 'Autre' }
    ];

    const messagePlaceholder = computed(() => {
        switch (form.value.subject) {
            case 'helpdesk': return "Précisez l'événement et l'adresse e-mail de la commande...";
            case 'sponsoring': return "Présentez-nous brièvement votre marque ou votre projet...";
            case 'artiste': return "Ajoutez un lien vers votre Soundcloud/portfolio et décrivez votre univers...";
            case 'benevolat': return "Dites-nous ce qui vous motive et vos éventuelles expériences...";
            case 'autre':
            default:
                return "Comment pouvons-nous vous aider ?";
        }
    });

    const isSubmitted = ref(false);

    const openFaq = ref<number | null>(null);
    const selectOpen = ref(false);
    const selectRef = ref<HTMLElement | null>(null);

    const toggleFaq = (index: number) => {
        openFaq.value = openFaq.value === index ? null : index;
    };

    const toggleSelect = () => {
        selectOpen.value = !selectOpen.value;
    };

    const selectSubject = (val: string) => {
        form.value.subject = val;
        selectOpen.value = false;
    };

    const closeSelect = (e: MouseEvent) => {
        if (selectRef.value && !selectRef.value.contains(e.target as Node)) {
            selectOpen.value = false;
        }
    };

    onMounted(() => {
        document.addEventListener('click', closeSelect);
    });

    onUnmounted(() => {
        document.removeEventListener('click', closeSelect);
    });

    const submitForm = () => {
        if (!form.value.consent || !form.value.subject) return;
        isSubmitted.value = true;
        setTimeout(() => {
            isSubmitted.value = false;
            form.value = { name: '', email: '', subject: '', message: '', consent: false };
        }, 5000);
    };
</script>
<template>

    <Head title="Contact" />

    <Header />

    <div class="relative z-10 min-h-[100vh] overflow-hidden rounded-b-[6rem] bg-black">
        <div class="pointer-events-none absolute inset-0 bg-linear-to-b from-black via-black to-black"></div>
        <div
            class="pointer-events-none absolute -top-32 left-1/2 h-115 w-[130%] -translate-x-1/2 rounded-full bg-[#06402B]/18 blur-[150px]">
        </div>
        <div class="pointer-events-none absolute inset-0 bg-[url('/noise.png')] opacity-[0.04] mix-blend-soft-light">
        </div>

        <main class="relative z-10 mx-auto max-w-6xl px-6 pt-34 pb-32 md:px-10 lg:px-14">

            <!-- Page header matching News/Events -->
            <section class="mb-20 space-y-3 text-center md:text-left">
                <p class="text-xs font-bold tracking-[0.35em] text-[#51A687] uppercase">
                    Nous contacter
                </p>
                <h1 class="font-chillax text-5xl leading-[0.92] text-white md:text-7xl lg:text-8xl">
                    Contact
                </h1>
                <p class="max-w-2xl text-sm text-gray-300 md:text-base">
                    Une question, une idée ou un projet ? Laissez-nous un message et notre équipe reviendra vers vous.
                </p>
            </section>

            <!-- Layout: FAQ & Form Stacked -->
            <div class="w-full space-y-24">

                <!-- FAQ Section -->
                <div class="space-y-10">
                    <div class="çpb-6">
                        <h2 class="font-chillax text-4xl text-white uppercase tracking-wide">
                            Questions Fréquentes
                        </h2>
                        <p class="mt-2 text-sm text-gray-400">
                            La réponse à votre question se trouve peut-être déjà ici.
                        </p>
                    </div>

                    <div class="space-y-4 mt-12">
                        <div v-for="(item, index) in faqItems" :key="index" class="border-b border-white/10">
                            <button type="button" @click="toggleFaq(index)"
                                class="w-full flex items-center justify-between py-6 text-left group cursor-pointer">
                                <h3
                                    class="font-chillax text-xl md:text-2xl text-white uppercase tracking-wide group-hover:text-[#51A687] transition-colors pr-8">
                                    {{ item.question }} ?
                                </h3>
                                <span
                                    class="text-[#51A687] font-mono text-3xl font-light leading-none transition-transform duration-500 origin-center"
                                    :class="{ 'rotate-45': openFaq === index }">
                                    +
                                </span>
                            </button>
                            <div class="grid transition-all duration-500 ease-in-out"
                                :class="openFaq === index ? 'grid-rows-[1fr] opacity-100 pb-6' : 'grid-rows-[0fr] opacity-0 pb-0'">
                                <div class="overflow-hidden">
                                    <p
                                        class="font-synonym text-gray-400 text-base md:text-lg leading-relaxed max-w-3xl">
                                        {{ item.answer }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <div class="pt-8">
                    <h2 class="mb-10 font-chillax text-4xl text-white uppercase tracking-wide">
                        Laissez-nous un message
                    </h2>
                    <div class="rounded-2xl border border-white/10 bg-white/5 p-6 md:p-10 backdrop-blur-sm">
                        <form v-if="!isSubmitted" @submit.prevent="submitForm" class="space-y-8">

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Nom
                                        complet</label>
                                    <input v-model="form.name" type="text" required
                                        class="w-full border-b border-white/20 bg-transparent py-3 font-synonym text-white focus:border-[#51A687] focus:outline-none focus:ring-0 transition-colors"
                                        placeholder="Entrez votre nom" />
                                </div>

                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">E-mail</label>
                                    <input v-model="form.email" type="email" required
                                        class="w-full border-b border-white/20 bg-transparent py-3 font-synonym text-white focus:border-[#51A687] focus:outline-none focus:ring-0 transition-colors"
                                        placeholder="Entrez votre e-mail" />
                                </div>
                            </div>

                            <div class="space-y-2" ref="selectRef">
                                <label class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Sujet de
                                    la demande</label>
                                <div class="relative">
                                    <!-- Custom Select Trigger -->
                                    <button type="button" @click.stop.prevent="toggleSelect"
                                        class="w-full flex items-center justify-between border-b border-white/20 bg-transparent py-3 font-synonym focus:outline-none transition-colors cursor-pointer"
                                        :class="[form.subject === '' ? 'text-white/40' : 'text-white', selectOpen ? 'border-[#51A687]' : '']">
                                        <span>
                                            {{form.subject ? subjects.find(s => s.value === form.subject)?.label :
                                            'Sélectionnez un sujet' }}
                                        </span>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                            stroke-width="2" stroke="currentColor"
                                            class="w-4 h-4 transition-transform duration-300"
                                            :class="selectOpen ? 'rotate-180 text-[#51A687]' : 'text-white/50'">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>

                                    <!-- Custom Select Dropdown Options -->
                                    <div v-show="selectOpen"
                                        class="absolute z-50 w-full mt-2 rounded-xl border border-white/10 bg-[#0a0a0a]/95 backdrop-blur-xl shadow-2xl overflow-hidden origin-top animate-in fade-in zoom-in-95 duration-200">
                                        <div class="py-2">
                                            <button v-for="sub in subjects" :key="sub.value" type="button"
                                                @click="selectSubject(sub.value)"
                                                class="w-full text-left px-5 py-3 font-synonym text-white/70 hover:text-white hover:bg-white/5 transition-colors flex items-center justify-between"
                                                :class="{ 'bg-white/5 text-white': form.subject === sub.value }">
                                                {{ sub.label }}
                                                <svg v-if="form.subject === sub.value"
                                                    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                                                    fill="currentColor" class="w-4 h-4 text-[#51A687]">
                                                    <path fill-rule="evenodd"
                                                        d="M19.916 4.626a.75.75 0 01.208 1.04l-9 13.5a.75.75 0 01-1.154.114l-6-6a.75.75 0 011.06-1.06l5.353 5.353 8.493-12.739a.75.75 0 011.04-.208z"
                                                        clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    <input type="hidden" v-model="form.subject" required>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Message</label>
                                <textarea v-model="form.message" rows="5" required
                                    class="w-full border-b border-white/20 bg-transparent py-3 font-synonym text-white focus:border-[#51A687] focus:outline-none focus:ring-0 transition-colors resize-none"
                                    :placeholder="messagePlaceholder"></textarea>
                            </div>

                            <!-- Consent Checkbox -->
                            <div class="pt-2 pb-4">
                                <label class="group flex items-start gap-3 cursor-pointer">
                                    <div class="relative flex items-center justify-center mt-0.5 shrink-0">
                                        <input type="checkbox" v-model="form.consent" required
                                            class="peer appearance-none w-5 h-5 border border-white/20 rounded bg-transparent checked:bg-[#51A687] checked:border-[#51A687] transition-colors focus:outline-none focus:ring-2 focus:ring-[#51A687]/30 cursor-pointer" />
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                            stroke="currentColor" stroke-width="3" stroke-linecap="round"
                                            stroke-linejoin="round"
                                            class="absolute w-3 h-3 text-black opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                    </div>
                                    <span
                                        class="font-synonym text-sm text-gray-400 group-hover:text-gray-300 transition-colors leading-relaxed select-none">
                                        J'accepte que les informations saisies soient exploitées dans le cadre de ma
                                        demande et de la relation qui peut en découler.
                                    </span>
                                </label>
                            </div>

                            <div class="pt-4 flex justify-end">
                                <AppButton as="button" type="submit" variant="primary" size="md"
                                    :disabled="!form.consent || !form.subject"
                                    :class="{ 'opacity-50 cursor-not-allowed': !form.consent || !form.subject }">
                                    Envoyer le message
                                </AppButton>
                            </div>
                        </form>

                        <div v-else
                            class="flex flex-col items-center justify-center py-20 text-center animate-in fade-in zoom-in duration-500">
                            <h3 class="font-chillax text-3xl text-[#51A687] uppercase">Message Envoyé</h3>
                            <p class="mt-4 max-w-sm font-synonym text-gray-400">
                                Nous avons bien reçu votre demande. Notre équipe reviendra vers vous très vite.
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <Footer />
</template>

<style scoped>
select {
    -ms-expand: none;
}
</style>
