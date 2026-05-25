<script setup lang="ts">
    import { Clock } from '@lucide/vue';
    import AppButton from '@/components/AppButton.vue';
    import { archives } from '@/routes';
</script>

<template>
    <section class="flex flex-col items-center justify-center py-16 text-center">
        <div class="mb-8 flex h-24 w-24 items-center justify-center rounded-full bg-white/5 text-gray-600">
            <Clock aria-hidden="true" class="h-10 w-10" />
        </div>
        <h2 class="font-chillax text-4xl text-white">
            Aucun événement programmé
        </h2>
        <p class="mt-4 max-w-sm text-gray-400">
            Nous préparons de nouvelles expériences. En attendant les prochaines dates, découvre nos anciens événements.
        </p>
        <AppButton :href="archives.url()" variant="outline" size="md" class="mt-10">
            Voir les archives
        </AppButton>
    </section>
</template>
