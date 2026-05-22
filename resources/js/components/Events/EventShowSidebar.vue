<script setup lang="ts">
    import AppButton from '@/components/AppButton.vue';
    import { useEventDates } from '@/composables/useEventDates';
    import { useGoogleMapsUrl } from '@/composables/useGoogleMapsUrl';
    import events from '@/routes/events';

    interface Props {
        event: Event;
    }

    const props = defineProps<Props>();
    const eventDates = useEventDates(() => props.event);
    const addressHref = useGoogleMapsUrl(() => props.event.address);
</script>

<template>
    <div class="lg:col-span-4 space-y-6 relative">
        <!-- Practical Info Card -->
        <div class="rounded-3xl border border-white/10 bg-white/5 p-8 space-y-8">
            <h3 class="font-chillax text-2xl text-white uppercase tracking-wider">Infos Pratiques</h3>
            <div class="space-y-6">
                <div class="space-y-1">
                    <p class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Date</p>
                    <p class="text-white font-medium">
                        {{ eventDates.startWeekday }} {{ eventDates.startLong }}
                    </p>
                </div>
                <div class="space-y-1">
                    <p class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Heures</p>
                    <p class="text-white font-medium">
                        {{ eventDates.startTime }} - {{ eventDates.endTime }}
                    </p>
                </div>
                <div class="space-y-1" v-if="event.minimum_age && event.minimum_age > 0">
                    <p class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Age Minimum
                    </p>
                    <p class="text-white font-medium">{{ event.minimum_age }}</p>
                </div>
                <div class="space-y-1" v-if="event.dress_code && event.dress_code !== ''">
                    <p class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Dress Code
                    </p>
                    <p class="text-white font-medium">{{ event.dress_code }}</p>
                </div>
                <div class="space-y-1" v-if="event.address && event.address !== ''">
                    <p class="text-[10px] font-bold tracking-[0.2em] text-[#51A687] uppercase">Lieu</p>
                    <p class="text-white font-medium">{{ event.address }}</p>
                </div>
                <AppButton v-if="addressHref" :href="addressHref" variant="outline" size="md" rel="noopener noreferrer"
                    class="w-full" target="_blank" external>
                    Voir sur Maps
                </AppButton>
            </div>
        </div>

        <!-- Ticketing Card -->
        <div v-if="event.ticketing_status !== 'none'"
            class="sticky top-32 rounded-3xl border border-white/10 bg-white/5 p-8 space-y-8 shadow-2xl">
            <div class="space-y-1">
                <h3 class="font-chillax text-2xl text-white uppercase tracking-wider">Billetterie</h3>
            </div>

            <div v-if="event.ticketing_status === 'open'">
                <AppButton :href="events.ticketing(event.slug).url" variant="outline" size="md" class="w-full">
                    Acheter ma place
                </AppButton>
            </div>

            <div v-else-if="event.ticketing_status === 'coming_soon'" class="text-center">
                <p class="text-gray-400 text-sm">
                    La billetterie n'est pas encore ouverte.
                </p>
            </div>

            <div v-else-if="event.ticketing_status === 'closed'" class="text-center">
                <p class="text-gray-400 text-sm">
                    La billetterie est désormais fermée.
                </p>
            </div>
        </div>
    </div>
</template>
