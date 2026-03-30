<script setup lang="ts">
import { Link } from '@inertiajs/vue3';

interface Props {
  eventTitle: string;
  eventDate: string;
  eventLocation: string;
  eventImage: string;
  ticketLink: string;
  moreInfoLink: string;
  calendarLink: string;
}

defineProps<Props>();
</script>

<template>
  <section class="relative bg-black py-24 px-12 lg:px-32 overflow-hidden">
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
                Prochain Événement
            </span>
          </div>
          <h2 class="text-6xl md:text-8xl font-chillax font-bold text-white leading-tight">
            {{ eventTitle }}
          </h2>
          <div class="flex flex-col space-y-4 text-2xl text-gray-400 font-medium">
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
          <Link
            :href="ticketLink"
            class="px-10 py-5 bg-[#c80a45] text-white font-bold rounded-full text-center hover:bg-[#a6083a] transition-colors duration-300 shadow-lg hover:shadow-[0_0_20px_rgba(200,10,69,0.4)]"
          >
            Acheter un ticket
          </Link>
          <Link
            :href="moreInfoLink"
            class="px-10 py-5 border-2 border-white/20 text-white font-bold rounded-full text-center hover:bg-white hover:text-black transition-all duration-300"
          >
            Plus d'informations
          </Link>
        </div>

        <Link
          :href="calendarLink"
          class="inline-flex items-center gap-2 text-gray-500 hover:text-white font-medium transition-colors duration-300 group"
        >
          Voir tout le calendrier
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-4 h-4 transition-transform group-hover:translate-x-1">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
          </svg>
        </Link>
      </div>
    </div>
  </section>
</template>
