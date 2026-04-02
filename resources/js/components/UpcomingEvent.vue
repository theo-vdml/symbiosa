<script setup lang="ts">
import AppButton from '@/components/AppButton.vue';

interface Props {
  eventTitle: string;
  eventDate: string;
  eventLocation: string;
  eventImage: string;
  ticketLink: string;
  moreInfoLink: string;
}

defineProps<Props>();
</script>

<template>
  <section class="relative bg-black pb-24 pt-48 px-12 lg:px-32 overflow-hidden">
    <!-- Horizontal Glow Orb spanning across the section -->
    <div
        class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[150%] h-[500px] bg-[#c80a45] rounded-[100%] blur-[160px] opacity-25 pointer-events-none"
    ></div>

    <div class="relative z-10 max-w-7xl mx-auto flex flex-col md:flex-row items-center gap-16">
      <!-- Image / Poster Container -->
      <div class="relative w-full md:w-5/12 group">
        <div class="relative z-10 overflow-hidden rounded-2xl shadow-2xl border border-white/10">
          <img
            :src="eventImage"
            :alt="eventTitle"
            class="w-full h-auto object-cover"
          />
        </div>
      </div>

      <!-- Content -->
      <div class="w-full md:w-7/12 flex flex-col space-y-10">
        <div class="space-y-6 text-center md:text-left">
          <div class="flex items-center gap-4 justify-center md:justify-start">
            <div class="h-px w-8 bg-[#c80a45]"></div>
            <span class="text-[#c80a45] text-sm font-bold tracking-[0.3em] uppercase">
                Coming Soon
            </span>
          </div>
          <h2 class="text-6xl md:text-8xl font-chillax font-bold text-white leading-tight">
            {{ eventTitle }}
          </h2>
          <div class="flex flex-col space-y-4 text-xl text-gray-400 font-medium">
            <div class="flex items-center gap-4">
              <span class="w-8 h-8 flex items-center justify-center text-[#c80a45]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                </svg>
              </span>
              {{ eventDate }}
            </div>
            <div class="flex items-center gap-4">
              <span class="w-8 h-8 flex items-center justify-center text-[#c80a45]">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
                </svg>
              </span>
              {{ eventLocation }}
            </div>
          </div>
        </div>

        <!-- CTAs -->
        <div class="flex flex-col sm:flex-row gap-6 pt-4">
          <AppButton
            :href="ticketLink"
            variant="primary"
            size="lg"
          >
            <template #left-icon>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-12v.75m0 3v.75m0 3v.75m0 3V18M3.75 6.75h16.5M3.75 9.75h16.5M3.75 12.75h16.5M3.75 15.75h16.5M12 6.75v10.5m-3.75-10.5v10.5m7.5-10.5v10.5" />
              </svg>
            </template>
            Acheter un ticket
          </AppButton>
          <AppButton
            :href="moreInfoLink"
            variant="outline"
            size="lg"
          >
            <template #left-icon>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
              </svg>
            </template>
            Plus d'informations
          </AppButton>
        </div>

        <div class="md:block hidden">
          <AppButton
            href="/agenda"
            variant="ghost"
            className="justify-start px-0 hover:bg-transparent text-gray-400 hover:text-white"
          >
            Voir tout le calendrier
            <template #right-icon>
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 transition-transform group-hover:translate-x-1">
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
              </svg>
            </template>
          </AppButton>
        </div>
      </div>
    </div>

    <!-- Mobile CTA -->
    <div class="mt-12 flex justify-center md:hidden">
      <AppButton
        :href="calendarLink"
        variant="primary"
        size="md"
        className="w-full"
      >
        Voir tout le calendrier
        <template #right-icon>
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 transition-transform group-hover:translate-x-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
          </svg>
        </template>
      </AppButton>
    </div>
  </section>
</template>
