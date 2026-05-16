<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Seo } from '@/types/seo';

const props = defineProps<{
    seo: Seo;
}>();
</script>

<template>
    <Head>
        <title>{{ seo.title }}</title>
        <meta v-if="seo.description" name="description" :content="seo.description" />
        <meta v-if="seo.keywords" name="keywords" :content="seo.keywords" />
        <meta v-if="seo.robots" name="robots" :content="seo.robots" />
        <link v-if="seo.canonical_url" rel="canonical" :href="seo.canonical_url" />

        <!-- Open Graph -->
        <meta property="og:title" :content="seo.og_title || seo.title" />
        <meta v-if="seo.og_description || seo.description" property="og:description" :content="seo.og_description || seo.description" />
        <meta v-if="seo.og_image" property="og:image" :content="`/storage/${seo.og_image}`" />
        <meta property="og:type" :content="seo.og_type || 'website'" />

        <!-- Twitter -->
        <meta name="twitter:card" :content="seo.twitter_card || 'summary_large_image'" />
        <meta name="twitter:title" :content="seo.twitter_title || seo.title" />
        <meta v-if="seo.twitter_description || seo.description" name="twitter:description" :content="seo.twitter_description || seo.description" />
        <meta v-if="seo.twitter_image || seo.og_image" name="twitter:image" :content="`/storage/${seo.twitter_image || seo.og_image}`" />
        
        <component :is="'script'" v-if="seo.json_ld" type="application/ld+json" v-html="seo.json_ld" />
    </Head>
</template>
