<script setup lang="ts">
// No extra logic needed for this Simple UI component
</script>

<template>
    <section
        class="relative flex w-full items-center justify-center overflow-hidden bg-black py-64 md:py-80"
    >
        <!-- Intense Distant Glows -->
        <div
            class="absolute top-1/2 left-1/2 h-240 w-240 -translate-x-1/2 -translate-y-1/2 rounded-full bg-white/3 blur-[180px]"
        ></div>

        <!-- Large Background Text with Masking/Subtraction effect look -->
        <div
            class="pointer-events-none absolute inset-0 flex items-center justify-center overflow-hidden select-none"
        >
            <span
                class="font-chillax leading-none font-black whitespace-nowrap text-transparent uppercase opacity-20 md:text-[55rem]"
                style="
                    -webkit-text-stroke: 1px rgba(255, 255, 255, 0.15);
                    filter: blur(1px);
                "
            >
                SYMBIOSA
            </span>
        </div>

        <div class="relative z-10 w-full max-w-full overflow-hidden px-4">
            <div class="flex flex-col items-center justify-center text-center">
                <!-- Massive Typography Adjusted with stricter width constraints -->
                <h2
                    class="bg-linear-to-b from-white via-white/70 to-white/10 bg-clip-text pt-12 pb-8 font-chillax text-[9vw] leading-[0.8] font-black tracking-tighter whitespace-nowrap text-transparent uppercase italic md:text-[min(10rem,13vw)] lg:text-[min(12rem,15vw)]"
                >
                    <span class="block translate-x-[1vw] opacity-95"
                        >Live music</span
                    >
                    <span class="block opacity-80">for</span>
                    <span class="block -translate-x-[1vw] opacity-70"
                        >living people.</span
                    >
                </h2>

                <!-- More brutalist/architectural UI elements -->
                <div class="mt-24 flex flex-col items-center gap-12">
                    <div
                        class="relative h-32 w-px bg-linear-to-b from-white/60 via-white/20 to-transparent"
                    >
                        <div
                            class="absolute top-0 left-1/2 h-2 w-2 -translate-x-1/2 rounded-full bg-white shadow-[0_0_15px_rgba(255,255,255,0.8)]"
                        ></div>
                    </div>

                    <div class="flex flex-col items-center gap-4">
                        <div
                            class="flex items-center gap-12 font-mono text-[12px] tracking-[0.6em] text-white/50 uppercase"
                        >
                            <span class="transition-colors hover:text-white"
                                >50,3341° N</span
                            >
                            <span class="transition-colors hover:text-white"
                                >4,4156° E
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Radical Screen Splits (Horizontal Lines) -->
        <div
            class="absolute top-0 left-0 h-px w-full bg-linear-to-r from-transparent via-white/20 to-transparent"
        ></div>
        <div
            class="absolute bottom-0 left-0 h-px w-full bg-linear-to-r from-transparent via-white/20 to-transparent"
        ></div>

        <!-- Deep Cinematic Fade Out -->
        <div
            class="absolute top-0 left-0 h-80 w-full bg-linear-to-b from-black via-black/95 to-transparent"
        ></div>
        <div
            class="absolute bottom-0 left-0 h-80 w-full bg-linear-to-t from-black via-black/95 to-transparent"
        ></div>
    </section>
</template>

