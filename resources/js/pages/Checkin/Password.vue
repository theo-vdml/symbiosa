<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AppButton from '@/components/AppButton.vue';
import checkinRoute from '@/routes/checkin';

const props = defineProps<{
    checkinList: {
        id: number;
        name: string;
        token: string;
    }
}>();

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(checkinRoute.authenticate(props.checkinList.token).url);
};
</script>

<template>
    <div class="min-h-screen bg-black flex items-center justify-center p-6">
        <div class="w-full max-w-md bg-zinc-900 border border-zinc-800 rounded-2xl p-8">
            <h1 class="font-chillax text-3xl text-white mb-2">{{ props.checkinList.name }}</h1>
            <p class="text-zinc-400 mb-8">Cette liste est protégée par un mot de passe.</p>

            <form @submit.prevent="submit" class="space-y-6">
                <div>
                    <label for="password" class="block text-sm font-medium text-zinc-300 mb-2">Mot de passe</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        class="w-full bg-black border border-zinc-800 rounded-lg px-4 py-3 text-white focus:outline-hidden focus:ring-2 focus:ring-zinc-700"
                        placeholder="••••••••"
                    />
                    <div v-if="form.errors.password" class="mt-2 text-sm text-red-500">
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
