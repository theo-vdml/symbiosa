<script setup lang="ts">
import { useForm } from '@inertiajs/vue3';
import AppButton from '@/components/AppButton.vue';
import checkinRoute from '@/routes/checkin';

const props = defineProps<{
    checkinList: {
        id: number;
        name: string;
        token: string;
    };
}>();

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(checkinRoute.authenticate(props.checkinList.token).url);
};
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-black p-6">
        <div
            class="w-full max-w-md rounded-2xl border border-zinc-800 bg-zinc-900 p-8"
        >
            <h1 class="mb-2 font-chillax text-3xl text-white">
                {{ props.checkinList.name }}
            </h1>
            <p class="mb-8 text-zinc-400">
                Cette liste est protégée par un mot de passe.
            </p>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label
                        for="password"
                        class="mb-2 block text-sm font-medium text-zinc-300"
                        >Mot de passe</label
                    >
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        class="w-full rounded-lg border border-zinc-800 bg-black px-4 py-3 text-white focus:ring-2 focus:ring-zinc-700 focus:outline-hidden"
                        placeholder="••••••••"
                    />
                    <div
                        v-if="form.errors.password"
                        class="mt-2 text-sm text-red-500"
                    >
                        {{ form.errors.password }}
                    </div>
                </div>

                <AppButton
                    type="submit"
                    class="w-full"
                    :loading="form.processing"
                >
                    Accéder à la liste
                </AppButton>
            </form>
        </div>
    </div>
</template>
