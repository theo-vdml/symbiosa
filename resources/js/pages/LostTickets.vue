<script setup lang="ts">
import { useForm, usePage } from '@inertiajs/vue3';
import { Mail, HelpCircle, CheckCircle2, ArrowRight } from '@lucide/vue';
import { computed } from 'vue';
import AppButton from '@/components/AppButton.vue';
import PageHeader from '@/components/PageHeader.vue';
import MainLayout from '@/layouts/MainLayout.vue';
import type { Seo } from '@/types/seo';

defineProps<{
    preheading: string;
    heading: string;
    description: string;
    helpItems: { title: string; content: string }[];
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
            <PageHeader
                :preheading="preheading"
                :heading="heading"
                :description="description"
            />

            <div class="grid grid-cols-1 gap-12 md:grid-cols-12">
                <!-- Form Section -->
                <div class="md:col-span-7">
                    <div
                        v-if="status"
                        class="space-y-6 rounded-2xl border border-[#51A687]/20 bg-[#51A687]/5 p-8"
                    >
                        <div class="flex items-center gap-4">
                            <CheckCircle2 class="h-6 w-6 text-[#51A687]" />
                            <h2
                                class="font-bold tracking-wider text-white uppercase"
                            >
                                Demande traitée
                            </h2>
                        </div>
                        <p class="leading-relaxed text-gray-300">
                            {{ status }}
                        </p>
                        <AppButton
                            variant="outline"
                            size="sm"
                            @click="status = null"
                        >
                            Faire une autre recherche
                        </AppButton>
                    </div>

                    <form v-else @submit.prevent="submit" class="space-y-8">
                        <div class="space-y-4">
                            <label
                                for="email"
                                class="block text-xs font-bold tracking-widest text-gray-500 uppercase"
                            >
                                Votre adresse email
                            </label>
                            <input
                                v-model="form.email"
                                type="email"
                                id="email"
                                required
                                placeholder="nom@exemple.com"
                                class="w-full rounded-xl border border-white/10 bg-white/5 px-6 py-4 text-white transition-all focus:border-[#51A687] focus:outline-none"
                            />
                            <p
                                v-if="form.errors.email"
                                class="text-sm text-red-500"
                            >
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <AppButton
                            type="submit"
                            :disabled="form.processing"
                            class="w-full md:w-auto"
                            :loading="form.processing"
                        >
                            Récupérer mes billets
                        </AppButton>
                    </form>
                </div>

                <!-- Info Section -->
                <div class="space-y-10 md:col-span-5">
                    <div
                        v-for="(item, index) in helpItems"
                        :key="index"
                        class="space-y-4"
                    >
                        <div class="flex items-center gap-3 text-[#51A687]">
                            <h3
                                class="text-sm font-bold tracking-wider uppercase"
                            >
                                {{ item.title }}
                            </h3>
                        </div>
                        <p
                            class="text-sm leading-relaxed whitespace-pre-line text-gray-400"
                        >
                            {{ item.content }}
                        </p>
                    </div>
                    <AppButton variant="outline" href="/contact">
                        Contactez-nous
                        <template #right-icon>
                            <ArrowRight class="h-4 w-4" />
                        </template>
                    </AppButton>
                </div>
            </div>
        </div>
    </MainLayout>
</template>
