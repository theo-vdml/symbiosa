<script setup lang="ts">
    import { CheckCircle } from '@lucide/vue';
    import AppButton from '@/components/AppButton.vue';
    import { archives } from '@/routes';
</script>

<template>
    <div class="mt-32 flex flex-col items-center text-center bg-black z-2 relative">
        <div class="mb-6 flex h-12 w-12 items-center justify-center rounded-full bg-[#51A687]/10 text-[#51A687]">
            <CheckCircle aria-hidden="true" class="h-6 w-6" />
        </div>
        <h3 class="font-chillax text-2xl text-white">
            C'est tout pour le moment !
        </h3>
        <p class="mt-3 max-w-sm text-sm text-gray-400">
            Tu as vu tous nos événements à venir. En attendant les prochaines annonces, replonge dans nos souvenirs.
        </p>
        <AppButton :href="archives.url()" variant="outline" size="md" class="mt-10">
            Consulter les archives
        </AppButton>
    </div>
</template>
