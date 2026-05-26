<script setup lang="ts">
    import { ref, computed, onMounted, onUnmounted } from 'vue';
    import { useForm, router } from '@inertiajs/vue3';
    import MainLayout from '@/layouts/MainLayout.vue';
    import HeroHeader from '@/components/HeroHeader.vue';
    import AppButton from '@/components/AppButton.vue';
    import CheckoutInput from '@/components/CheckoutInput.vue';
    import { Clock, Info, ShieldCheck, ChevronRight, User, Mail, CheckCircle2, Lock, Calendar, MapPin } from '@lucide/vue';
    import checkoutRoute from '@/routes/checkout';
    import EventController from '@/actions/App/Http/Controllers/EventController';

    const props = defineProps<{
        checkout: any;
        legalPages: any[];
    }>();

    const getFormFields = () => {
        const fields: any = {
            email: props.checkout.customer_email || '',
            name: props.checkout.customer_name || '',
        };

        props.legalPages.forEach(page => {
            fields['accept_' + page.slug.replace(/-/g, '_')] = false;
        });

        return fields;
    }

    const form = useForm(getFormFields());
    const verificationForm = useForm({
        code: '',
    });

    const currentStep = computed(() => {
        if (props.checkout.email_verified_at) return 4;
        if (props.checkout.email_verification_code) return 3;
        return 2;
    });

    const isSendingVerification = ref(false);

    const allLegalAccepted = computed(() => {
        return props.legalPages.every(page => (form as any)['accept_' + page.slug.replace(/-/g, '_')]);
    });

    const timeLeft = ref('');
    const timeLeftA11y = ref('');
    let timer: any = null;

    const calculateTimeLeft = () => {
        const now = new Date().getTime();
        const expires = new Date(props.checkout.expires_at).getTime();
        const diff = expires - now;

        if (diff <= 0) {
            timeLeft.value = 'EXPIRÉ';
            timeLeftA11y.value = 'Votre session a expiré.';
            if (timer) clearInterval(timer);
            return;
        }

        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        timeLeft.value = `${minutes}:${seconds.toString().padStart(2, '0')}`;

        // Update a11y string every minute or on initial load
        if (seconds === 0 || timeLeftA11y.value === '') {
            timeLeftA11y.value = `Temps restant pour finaliser votre commande : ${minutes} minute${minutes > 1 ? 's' : ''}.`;
        }
    };

    const event = props.checkout.reservations[0]?.reservable?.event;

    const getDateFormatted = (dateStr: string) => {
        if (!dateStr) return '';
        return new Date(dateStr).toLocaleDateString('fr-FR', {
            day: 'numeric', month: 'long', year: 'numeric'
        });
    };

    onMounted(() => {
        calculateTimeLeft();
        timer = setInterval(calculateTimeLeft, 1000);
    });

    onUnmounted(() => {
        if (timer) clearInterval(timer);
    });

    const totalAmount = computed(() => {
        return props.checkout.reservations.reduce((acc: number, res: any) => acc + (res.unit_price * res.quantity), 0) / 100;
    });

    const formatEuro = (amount: number) => {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(amount);
    };

    const handleSendVerification = () => {
        isSendingVerification.value = true;

        router.post(checkoutRoute.sendVerification(props.checkout.uuid).url, {
            email: form.email,
            name: form.name
        }, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                isSendingVerification.value = false;
            },
            onError: () => {
                isSendingVerification.value = false;
            }
        });
    };

    const handleVerifyCode = () => {
        verificationForm.post(checkoutRoute.verify(props.checkout.uuid).url, {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                verificationForm.code = '';
            }
        });
    };

    const isResetting = ref(false);
    const handleResetVerification = () => {
        isResetting.value = true;
        router.get(checkoutRoute.show(props.checkout.uuid).url, { reset_verification: 1 }, {
            onFinish: () => isResetting.value = false
        });
    };

    const handleSubmit = () => {
        form.post(checkoutRoute.start(props.checkout.uuid).url);
    };
</script>

<template>
    <MainLayout title="Finaliser ma commande">

        <section v-if="EventController"
            class="relative min-h-[50vh] w-full overflow-hidden flex items-center justify-center py-24">
            <img v-if="event.background_url" :src="event.background_url" :srcset="event.background_responsive?.srcset"
                sizes="(max-width: 768px) 200vw, 100vw"
                class="absolute inset-0 h-full w-full object-cover grayscale opacity-30" alt="" />
            <div class="absolute inset-0 bg-linear-to-t from-black via-black/40 to-black/20"></div>

            <div class="relative z-10 flex w-full flex-col items-center justify-center">
                <HeroHeader size="lg">
                    <template #top>
                        <section aria-label="Temps restant pour finaliser la commande" class="flex items-center gap-6">
                            <!-- Timer Compact -->
                            <div class="inline-flex items-center gap-3 px-4 py-2 rounded-full bg-[#51A687]/15 border border-[#51A687]/30 text-[#51A687]"
                                aria-hidden="true">
                                <Clock class="w-4 h-4" />
                                <span class="text-xs font-bold tracking-[0.2em] uppercase">Temps restant : {{ timeLeft
                                }}</span>
                            </div>
                            <!-- A11y Timer -->
                            <div class="sr-only" aria-live="polite">
                                {{ timeLeftA11y }}
                            </div>
                        </section>
                    </template>

                    Finaliser ma <span class="text-[#51A687]">commande</span>

                    <template #bottom>
                        <div class="space-y-4">
                            <h2 class="font-chillax text-2xl text-white uppercase">{{ event.title }}</h2>
                            <div class="flex flex-wrap items-center justify-center gap-6 text-gray-400">
                                <div class="flex items-center gap-2">
                                    <Calendar class="w-4 h-4 text-[#51A687]" />
                                    <span class="text-sm uppercase tracking-widest">
                                        {{ getDateFormatted(event.date) }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <MapPin class="w-4 h-4 text-[#51A687]" />
                                    <span class="text-sm uppercase tracking-widest">
                                        {{ event.city }}, {{ event.country }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>
                </HeroHeader>
            </div>
        </section>

        <div class="mx-auto max-w-3xl px-6 pb-24 pt-8 space-y-12">
            <!-- Flash Message -->
            <div v-if="$page.props.flash.error || $page.props.flash.message" role="alert"
                class="p-6 rounded-4xl border flex gap-6 items-center"
                :class="$page.props.flash.error ? 'border-red-500/40 bg-red-500/10' : 'border-[#51A687]/40 bg-[#51A687]/10'">
                <div class="shrink-0 w-10 h-10 rounded-full flex items-center justify-center border"
                    :class="$page.props.flash.error ? 'bg-red-500/20 border-red-500/40' : 'bg-[#51A687]/20 border-[#51A687]/40'">
                    <Info v-if="$page.props.flash.error" class="w-5 h-5 text-red-500" />
                    <CheckCircle2 v-else class="w-5 h-5 text-[#51A687]" />
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold tracking-[0.2em] uppercase"
                        :class="$page.props.flash.error ? 'text-red-500' : 'text-[#51A687]'">
                        {{ $page.props.flash.error ? 'Erreur' : 'Succès' }}
                    </p>
                    <p class="text-xs text-white/90 leading-relaxed uppercase tracking-widest">
                        {{ $page.props.flash.error || $page.props.flash.message }}
                    </p>
                </div>
            </div>

            <!-- Section 1 : Résumé de la commande -->
            <section class="space-y-6" aria-labelledby="step-1-title">
                <div class="flex items-center gap-6 border-b border-white/20 pb-6">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-[#51A687] text-[#51A687] font-chillax text-lg pt-0.5"
                        aria-hidden="true">
                        1</div>
                    <h2 id="step-1-title" class="font-chillax text-2xl text-white uppercase tracking-wider">
                        <span class="sr-only">Étape 1 :</span> Résumé de la commande
                    </h2>
                </div>

                <div class="space-y-4">
                    <div v-for="reservation in checkout.reservations" :key="reservation.id"
                        class="flex justify-between items-center py-4 px-6 rounded-2xl bg-white/10 border border-white/10">
                        <div class="space-y-1">
                            <p class="text-white text-sm font-semibold uppercase tracking-wide">{{
                                reservation.reservable.name }}</p>
                            <p class="text-[10px] text-white/60 uppercase tracking-widest">
                                {{ reservation.quantity }} x {{ formatEuro(reservation.unit_price / 100) }}
                            </p>
                        </div>
                        <p class="text-white font-chillax text-lg">{{ formatEuro((reservation.unit_price *
                            reservation.quantity) / 100) }}</p>
                    </div>
                </div>
            </section>

            <!-- Section 2 : Vos Informations -->
            <section class="space-y-6" aria-labelledby="step-2-title">
                <div class="flex items-center justify-between border-b border-white/20 pb-6">
                    <div class="flex items-center gap-6">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 text-[#51A687] font-chillax text-lg pt-0.5"
                            :class="currentStep >= 2 ? 'border-[#51A687]' : 'border-white/20 text-white/20'"
                            aria-hidden="true">
                            2</div>
                        <h2 id="step-2-title" class="font-chillax text-2xl text-white uppercase tracking-wider"
                            :aria-current="currentStep === 2 ? 'step' : undefined">
                            <span class="sr-only">Étape 2 :</span> Vos Informations
                        </h2>
                    </div>

                    <AppButton v-if="currentStep > 2" variant="outline" size="sm" @click="handleResetVerification"
                        :loading="isResetting" class="text-[10px]" aria-label="Modifier mes informations">
                        Modifier mes infos
                    </AppButton>

                </div>

                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <CheckoutInput v-model="form.name" placeholder="Nom Complet" :icon="User"
                            :error="form.errors.name" :disabled="currentStep > 2" autocomplete="name" />
                        <CheckoutInput v-model="form.email" type="email" placeholder="Adresse Email" :icon="Mail"
                            :error="form.errors.email" :disabled="currentStep > 2" autocomplete="email" />
                    </div>

                    <div v-if="currentStep === 2" class="flex justify-end">
                        <AppButton @click="handleSendVerification" :loading="isSendingVerification"
                            :disabled="!form.email || !form.name">
                            Vérifier mon email
                            <template #right-icon>
                                <ChevronRight class="w-4 h-4" />
                            </template>
                        </AppButton>
                    </div>
                </div>

                <!-- Verified Badge -->
                <div v-if="currentStep === 4"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-[#51A687]/15 border border-[#51A687]/30 text-[#51A687]"
                    role="status">
                    <CheckCircle2 class="w-3.5 h-3.5" />
                    <span class="text-[10px] font-bold uppercase tracking-widest">Email vérifié : <span
                            class="text-white">{{
                                form.email
                            }}</span></span>
                </div>
            </section>

            <!-- Section 3 : Vérification Email -->
            <section v-if="currentStep === 3" class="space-y-6 animate-in fade-in duration-300"
                aria-labelledby="step-3-title">
                <div class="flex items-center gap-6 border-b border-white/20 pb-6">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 border-[#51A687] text-[#51A687] font-chillax text-lg pt-0.5"
                        aria-hidden="true">
                        3</div>
                    <h2 id="step-3-title" class="font-chillax text-2xl text-white uppercase tracking-wider"
                        aria-current="step">
                        <span class="sr-only">Étape 3 :</span> Vérification
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                    <div class="space-y-2">
                        <CheckoutInput v-model="verificationForm.code" placeholder="Code à 6 chiffres"
                            :icon="ShieldCheck" :error="verificationForm.errors.code" :maxlength="6"
                            autocomplete="one-time-code" inputmode="numeric" />
                        <p class="text-[10px] text-white/40 uppercase tracking-widest ml-5">
                            Envoyé à <span class="text-white">{{ form.email }}</span>
                        </p>
                    </div>

                    <AppButton @click="handleVerifyCode" :loading="verificationForm.processing"
                        :disabled="verificationForm.code.length < 6" class="h-15.5 w-full">
                        Valider l'email
                    </AppButton>
                </div>
            </section>

            <!-- Section 4 : Paiement & Validation -->
            <section class="space-y-6" aria-labelledby="step-4-title">
                <div class="flex items-center gap-6 border-b border-white/20 pb-6">
                    <div class="flex items-center justify-center w-10 h-10 rounded-full border-2 font-chillax text-lg pt-0.5"
                        :class="currentStep >= 4 ? 'border-[#51A687] text-[#51A687]' : 'border-white/20 text-white/30'"
                        aria-hidden="true">
                        {{ currentStep === 3 ? '4' : '3' }}
                    </div>
                    <h2 id="step-4-title"
                        class="font-chillax text-2xl text-white uppercase tracking-wider flex items-center gap-3"
                        :aria-current="currentStep === 4 ? 'step' : undefined">
                        <span class="sr-only">Étape {{ currentStep === 3 ? '4' : '3' }} :</span>
                        Paiement
                        <Lock v-if="currentStep < 4" class="w-4 h-4 text-white/20" aria-hidden="true" />
                    </h2>
                </div>

                <div class="rounded-3xl bg-white/5 border border-white/20 overflow-hidden transition-all duration-500"
                    :class="{ 'opacity-70 pointer-events-none grayscale': currentStep < 4 }">

                    <!-- Total Bar -->
                    <div class="flex justify-between items-center p-8 bg-white/5">
                        <span class="text-[10px] font-bold tracking-[0.3em] text-white/50 uppercase">Total à
                            régler</span>
                        <span class="text-4xl font-chillax text-[#51A687] tracking-tighter">{{
                            formatEuro(totalAmount) }}</span>
                    </div>

                    <!-- Info Note -->
                    <div class="p-8 space-y-8">

                        <div class="space-y-6">
                            <!-- Dynamic Legal Checkboxes -->
                            <div v-for="page in legalPages" :key="page.id" class="space-y-2">
                                <label class="flex items-start gap-4 cursor-pointer group/legal">
                                    <div class="relative flex items-center justify-center mt-0.5 shrink-0">
                                        <input v-model="form['accept_' + page.slug.replace(/-/g, '_')]" type="checkbox"
                                            class="peer sr-only"
                                            :aria-invalid="!!form.errors['accept_' + page.slug.replace(/-/g, '_')]"
                                            :aria-describedby="form.errors['accept_' + page.slug.replace(/-/g, '_')] ? `error-${page.slug}` : undefined" />
                                        <div class="w-5 h-5 rounded-md border-2 border-white/20 bg-white/5 transition-all duration-300 peer-checked:bg-[#51A687] peer-checked:border-[#51A687]"
                                            aria-hidden="true"
                                            :class="{ 'border-red-500/50': form.errors['accept_' + page.slug.replace(/-/g, '_')] }">
                                        </div>
                                        <svg class="absolute w-3 h-3 text-black opacity-0 transition-opacity peer-checked:opacity-100"
                                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="4"
                                            aria-hidden="true">
                                            <path d="M5 13l4 4L19 7" />
                                        </svg>
                                    </div>
                                    <span
                                        class="text-[10px] text-white/60 uppercase tracking-widest leading-relaxed font-medium">
                                        J'ai lu et j'accepte <a :href="`/legal/${page.slug}`" target="_blank"
                                            class="text-white hover:text-[#51A687] underline underline-offset-4 transition-colors font-bold"
                                            :aria-label="`Lire les ${page.title} (s'ouvre dans un nouvel onglet)`">{{
                                                page.title }}</a>.
                                    </span>
                                </label>
                                <p v-if="form.errors['accept_' + page.slug.replace(/-/g, '_')]"
                                    :id="`error-${page.slug}`"
                                    class="text-[10px] text-red-400 font-bold uppercase tracking-widest ml-9"
                                    role="alert">
                                    {{ form.errors['accept_' + page.slug.replace(/-/g, '_')] }}
                                </p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <button @click="handleSubmit" :disabled="!allLegalAccepted || currentStep < 4"
                                class="relative w-full h-16 rounded-2xl bg-[#635BFF] hover:bg-[#7a73ff] disabled:bg-white/10 disabled:cursor-not-allowed transition-all duration-300 overflow-hidden shadow-[0_4px_12px_rgba(99,91,255,0.2)] hover:shadow-[0_4px_20px_rgba(99,91,255,0.4)] disabled:shadow-none">
                                <div class="flex items-center justify-center h-full">
                                    <span
                                        class="text-sm font-bold uppercase tracking-widest text-white transition-opacity duration-300"
                                        :class="{ 'opacity-30': !allLegalAccepted || currentStep < 4 }">Payer
                                        avec</span>
                                    <img src="/stripe.svg" class="h-8 brightness-0 invert transition-all duration-300"
                                        :class="{ 'opacity-30': !allLegalAccepted || currentStep < 4 }" alt="Stripe" />
                                </div>
                            </button>

                            <div
                                class="flex items-center justify-center gap-2 text-[10px] text-white/40 uppercase tracking-[0.2em] font-medium">
                                <ShieldCheck class="w-3.5 h-3.5" />
                                Paiement 100% sécurisé via Stripe
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer de la page -->
            <div class="flex flex-col items-center gap-6 pt-12">
                <p class="text-[10px] text-white/50 uppercase tracking-[0.3em] font-medium">
                    Référence : {{ checkout.uuid }}
                </p>
            </div>
        </div>
    </MainLayout>
</template>
