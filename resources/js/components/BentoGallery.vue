<template>
    <div class="grid grid-cols-2 lg:grid-cols-4 lg:aspect-4/3 gap-2 bg-black w-full mx-auto pt-24">

        <!-- photo_01: Big featured image -->
        <div class="col-span-2 lg:row-span-2 lg:col-start-2 lg:row-start-1 overflow-hidden aspect-video lg:aspect-auto">
            <img src="/photo_01.jpg" class="w-full h-full object-cover">
        </div>

        <!-- photo_00: Tall image -->
        <div
            class="col-span-1 lg:row-span-2 lg:col-start-1 lg:row-start-1 overflow-hidden aspect-square lg:aspect-auto">
            <img src="/photo_00.jpg" class="w-full h-full object-cover">
        </div>

        <!-- photo_02: Small square -->
        <div class="col-span-1 lg:col-start-4 lg:row-start-1 overflow-hidden aspect-square lg:aspect-auto">
            <img src="/photo_02.jpg" class="w-full h-full object-cover">
        </div>

        <!-- photo_04: Wide image -->
        <div class="col-span-2 lg:col-span-2 lg:row-start-3 lg:col-start-1 overflow-hidden aspect-video lg:aspect-auto">
            <img src="/photo_04.jpg" class="w-full h-full object-cover">
        </div>

        <!-- photo_03: Tall image -->
        <div
            class="col-span-1 lg:row-span-2 lg:col-start-4 lg:row-start-2 overflow-hidden aspect-square lg:aspect-auto">
            <img src="/photo_03.jpg" class="w-full h-full object-cover">
        </div>

        <!-- photo_05: Small square -->
        <div class="col-span-1 lg:col-start-3 lg:row-start-3 overflow-hidden aspect-square lg:aspect-auto">
            <img src="/photo_05.jpg" class="w-full h-full object-cover">
        </div>

    </div>
</template>
