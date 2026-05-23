<script setup lang="ts">
    import { useForm, usePage } from '@inertiajs/vue3';
    import { computed } from 'vue';
    import MainLayout from '@/layouts/MainLayout.vue';
    import PageHeader from '@/components/PageHeader.vue';
    import { Seo } from '@/types/seo';
    import AppButton from '@/components/AppButton.vue';
    import { Mail, HelpCircle, CheckCircle2, ArrowRight } from '@lucide/vue';

    defineProps<{
        seo: Seo;
    }>();

    const page = usePage();
    const status = computed(() => page.props.flash?.status ?? null);

    const form = useForm({
        email: '',
    });

    const submit = () => {
        form.post('/lost-tickets', {
            preserveScroll: true,
            onSuccess: () => form.reset(),
        });
    };
</script>

<template>
    <MainLayout :seo="seo" has-background>
        <div class="relative z-10 mx-auto max-w-6xl px-4 pt-34 pb-32 md:px-10">

            <PageHeader heading="Retrouvez vos <span class='text-[#51A687]'>billets</span>"
                description="Si vous avez perdu l'accès à vos billets, entrez l'adresse email utilisée lors de votre commande ci-dessous. Nous vous renverrons vos accès pour tous les événements à venir." />

            <div class="grid grid-cols-1 md:grid-cols-12 gap-12">
                <!-- Form Section -->
                <div class="md:col-span-7">
                    <div v-if="status" class="bg-[#51A687]/5 border border-[#51A687]/20 rounded-2xl p-8 space-y-6">
                        <div class="flex items-center gap-4">
                            <CheckCircle2 class="w-6 h-6 text-[#51A687]" />
                            <h2 class="text-white font-bold uppercase tracking-wider">Demande traitée</h2>
                        </div>
                        <p class="text-gray-300 leading-relaxed">
                            {{ status }}
                        </p>
                        <AppButton variant="outline" size="sm" @click="status = null">
                            Faire une autre recherche
                        </AppButton>
                    </div>

                    <form v-else @submit.prevent="submit" class="space-y-8">
                        <div class="space-y-4">
                            <label for="email" class="block text-xs font-bold text-gray-500 uppercase tracking-widest">
                                Votre adresse email
                            </label>
                            <input v-model="form.email" type="email" id="email" required placeholder="nom@exemple.com"
                                class="w-full px-6 py-4 bg-white/5 border border-white/10 rounded-xl text-white focus:outline-none focus:border-[#51A687] transition-all" />
                            <p v-if="form.errors.email" class="text-sm text-red-500">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <AppButton type="submit" :disabled="form.processing" class="w-full md:w-auto"
                            :loading="form.processing">
                            Récupérer mes billets
                        </AppButton>
                    </form>
                </div>

                <!-- Info Section -->
                <div class="md:col-span-5 space-y-10">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 text-[#51A687]">
                            <Mail class="w-5 h-5" />
                            <h3 class="font-bold text-sm uppercase tracking-wider">Délai d'envoi</h3>
                        </div>
                        <p class="text-gray-400 text-sm leading-relaxed">
                            Les emails sont généralement expédiés dans les 5 minutes. N'oubliez pas de vérifier votre
                            dossier "Indésirables" ou "Spam".
                        </p>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center gap-3 text-[#51A687]">
                            <HelpCircle class="w-5 h-5" />
                            <h3 class="font-bold text-sm uppercase tracking-wider">Besoin d'aide ?</h3>
                        </div>
                        <p class="text-gray-400 text-sm leading-relaxed mb-4">
                            Si vous ne recevez rien ou si vous avez changé d'adresse email, notre équipe support peut
                            intervenir manuellement.
                        </p>
                        <AppButton variant="ghost" href="/contact"
                            class="px-0 h-auto group text-[#51A687] hover:text-white">
                            Contactez-nous
                            <template #right-icon>
                                <ArrowRight class="w-4 h-4 transition-transform group-hover:translate-x-1" />
                            </template>
                        </AppButton>
                    </div>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
