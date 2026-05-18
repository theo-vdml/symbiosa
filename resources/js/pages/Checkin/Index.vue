<script setup lang="ts">
    import { ref, computed, onUnmounted, nextTick, onMounted, watch } from 'vue';
    import { router } from '@inertiajs/vue3';
    import {
        Search, X, Check, ChevronRight, Loader2, History, User, AlertCircle,
        CheckCircle2, XCircle, Scan, List, Clock, UserCheck, Ticket,
        Info,
        TicketCheck,
        PackageCheck,
        Package
    } from '@lucide/vue';
    import { Html5Qrcode } from "html5-qrcode";

    const props = defineProps<{
        checkinList: {
            id: number;
            name: string;
            event_title: string;
            public_url_token?: string;
        };
        tickets: Array<{
            id: number;
            public_id: string;
            buyer_name: string;
            buyer_email: string;
            reservable_name: string;
            reservable_id: number;
            reservable_type: string;
            price_name: string | null;
            checked_in_at: string | null;
        }>;
        stats: {
            total: number;
            scanned: number;
        };
        isPublic: boolean;
        scan_result?: {
            status: 'success' | 'already_scanned' | 'invalid' | 'unauthorized' | 'error';
            message: string;
            ticket?: any;
        };
        flash?: {
            scan_result?: any;
        };
    }>();

    const activeTab = ref<'scan' | 'search' | 'history'>('scan');
    const searchQuery = ref('');
    const localStats = ref({ ...props.stats });
    const html5QrCode = ref<Html5Qrcode | null>(null);
    const isScanning = ref(false);
    const lastScanResult = ref<{
        status: 'success' | 'already_scanned' | 'invalid' | 'unauthorized' | 'error';
        message: string;
        ticket?: any;
        timestamp: number;
    } | null>(null);

    const isLoading = ref(false);
    const selectedTicket = ref<any>(null);
    const sessionHistory = ref<Array<any>>([]);

    // Sync local stats when props update
    watch(() => props.stats, (newStats) => {
        localStats.value = { ...newStats };
    }, { deep: true });

    // React to scan results
    watch([() => props.scan_result, () => props.flash?.scan_result], ([newResult, flashResult]) => {
        const result = newResult || flashResult;
        if (result) {
            lastScanResult.value = {
                ...result,
                timestamp: Date.now()
            };
            if (result.ticket) {
                const index = sessionHistory.value.findIndex(h => h.id === result.ticket.id);
                if (index !== -1) sessionHistory.value.splice(index, 1);
                sessionHistory.value.unshift({
                    ...result.ticket,
                    scan_status: result.status,
                    scan_timestamp: Date.now()
                });
            }
        }
    }, { immediate: true });

    const startScanner = async () => {
        if (isScanning.value) return;
        await nextTick();
        html5QrCode.value = new Html5Qrcode("reader");
        isScanning.value = true;
        try {
            await html5QrCode.value.start(
                { facingMode: "environment" },
                {
                    fps: 25,
                    qrbox: (viewfinderWidth, viewfinderHeight) => {
                        const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
                        return { width: minEdge * 0.9, height: minEdge * 0.9 };
                    }
                },
                (decodedText) => handleScan(decodedText),
                () => { }
            );
        } catch (err) {
            console.error("Scanner error", err);
            isScanning.value = false;
        }
    };

    const stopScanner = async () => {
        if (html5QrCode.value && isScanning.value) {
            try { await html5QrCode.value.stop(); } catch (e) { }
            html5QrCode.value = null;
            isScanning.value = false;
        }
    };

    const handleScan = (publicId: string) => {
        if (isLoading.value) return;
        if (lastScanResult.value && lastScanResult.value.ticket?.public_id === publicId && Date.now() - lastScanResult.value.timestamp < 2000) return;

        isLoading.value = true;
        const scanUrl = props.isPublic ? `/checkin/${props.checkinList.public_url_token}/scan` : `/checkin/list/${props.checkinList.id}/scan`;
        router.post(scanUrl, { public_id: publicId }, { preserveState: true, preserveScroll: true, onFinish: () => { isLoading.value = false; } });
    };

    const toggleTicketStatus = (ticket: any) => {
        isLoading.value = true;
        router.post(`/checkin/tickets/${ticket.id}/toggle`, {}, { preserveState: true, preserveScroll: true, onFinish: () => { isLoading.value = false; } });
    };

    const openTicketDetails = (ticket: any) => { selectedTicket.value = ticket; };

    watch(activeTab, (newTab) => {
        if (newTab === 'scan') startScanner();
        else stopScanner();
    });

    onMounted(() => { if (activeTab.value === 'scan') startScanner(); });
    onUnmounted(() => stopScanner());

    const getStatusColor = (status: string) => {
        switch (status) {
            case 'success': return 'bg-green-500';
            case 'already_scanned': return 'bg-amber-500';
            case 'invalid': return 'bg-red-500';
            default: return 'bg-neutral-500';
        }
    };

    const getStatusIcon = (status: string) => {
        switch (status) {
            case 'success': return CheckCircle2;
            case 'already_scanned': return AlertCircle;
            case 'invalid': return XCircle;
            default: return Info;
        }
    };

    const filteredTickets = computed(() => {
        const q = searchQuery.value.toLowerCase();
        if (!q) return [];
        return props.tickets.filter(t =>
            (t.buyer_name?.toLowerCase() || '').includes(q) ||
            (t.buyer_email?.toLowerCase() || '').includes(q) ||
            t.public_id.toLowerCase().includes(q)
        ).slice(0, 50);
    });

    const scanPercentage = computed(() => {
        if (localStats.value.total === 0) return 0;
        return Math.round((localStats.value.scanned / localStats.value.total) * 100);
    });
</script>

<template>
    <div class="fixed inset-0 bg-white text-neutral-900 font-sans antialiased overflow-hidden flex flex-col">
        <!-- HEADER : Minimalist Dashboard with Circular Progress -->
        <header class="safe-top bg-white border-b border-neutral-100 z-50 shadow-2xl">
            <div class="px-6 py-6 flex items-center justify-between gap-6">
                <div class="min-w-0 flex-1">
                    <p class="text-lg font-black uppercase tracking-widest text-neutral-500">
                        {{ props.checkinList.event_title }}
                    </p>
                    <h1 class="font-chillax text-2xl font-medium text-neutral-900 leading-tight truncate">
                        {{ props.checkinList.name }}
                    </h1>
                </div>

                <!-- Circular Progress Indicator -->
                <div class="relative w-20 h-20 shrink-0">
                    <svg class="w-full h-full -rotate-90 transform" viewBox="0 0 36 36">
                        <!-- Background Circle -->
                        <circle cx="18" cy="18" r="16" fill="none" class="stroke-neutral-50" stroke-width="3.5">
                        </circle>
                        <!-- Progress Circle -->
                        <circle cx="18" cy="18" r="16" fill="none"
                            class="stroke-neutral-900 transition-all duration-1000 ease-out" stroke-width="3.5"
                            stroke-dasharray="101" :stroke-dashoffset="101 - (scanPercentage || 0)"
                            stroke-linecap="round"></circle>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-xl font-black text-neutral-900 leading-none">{{ scanPercentage }}%</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 relative overflow-hidden bg-black">
            <!-- Scanner Tab -->
            <div v-show="activeTab === 'scan'" class="absolute inset-0 flex flex-col bg-black">
                <div id="reader" class="flex-1"></div>
            </div>

            <!-- History Tab -->
            <div v-show="activeTab === 'history'"
                class="absolute inset-0 flex flex-col p-6 overflow-y-auto bg-zinc-950">
                <!-- Header Section -->
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-baseline gap-3">
                        <h2 class="font-chillax text-3xl font-medium text-white">Historique</h2>
                        <span v-if="sessionHistory.length > 0"
                            class="text-xs font-black px-2 py-0.5 rounded-md bg-zinc-800 text-zinc-400">
                            {{ sessionHistory.length }}
                        </span>
                    </div>
                    <button v-if="sessionHistory.length > 0" @click="sessionHistory = []"
                        class="text-xs font-bold uppercase tracking-widest text-zinc-400 hover:text-amber-500 bg-zinc-900 hover:bg-zinc-900/50 px-4 py-2 rounded-xl border border-zinc-800/80 transition-all active:scale-95">
                        Effacer
                    </button>
                </div>

                <!-- History List -->
                <div class="space-y-3">
                    <div v-for="item in sessionHistory" :key="item.id + item.scan_timestamp"
                        @click="openTicketDetails(item)"
                        class="bg-zinc-900/40 hover:bg-zinc-900 border border-zinc-800/60 rounded-2xl p-4 flex items-center gap-4 active:scale-[0.99] transition-all shadow-lg backdrop-blur-sm group cursor-pointer">

                        <!-- Status Icon Container -->
                        <div
                            :class="[getStatusColor(item.scan_status), 'shrink-0 w-12 h-12 rounded-xl text-white flex items-center justify-center shadow-lg transition-transform group-hover:scale-105']">
                            <component :is="getStatusIcon(item.scan_status)" :size="22" stroke-width="2.5" />
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <h4 class="font-black text-white text-base truncate leading-snug">
                                    {{ item.buyer_name }}
                                </h4>
                                <span class="shrink-0 font-mono text-xs font-bold text-zinc-500">
                                    {{ new Date(item.scan_timestamp).toLocaleTimeString([], {
                                        hour: '2-digit', minute:
                                            '2-digit'
                                    }) }}
                                </span>
                            </div>
                            <p class="text-xs text-zinc-400 font-medium truncate flex items-center gap-1.5">
                                <span class="inline-block w-1.5 h-1.5 rounded-full bg-zinc-700"></span>
                                {{ item.reservable_name }}
                            </p>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="sessionHistory.length === 0"
                        class="py-24 text-center flex flex-col items-center justify-center">
                        <div
                            class="w-16 h-16 rounded-2xl bg-zinc-900 border border-zinc-800 flex items-center justify-center mb-4 shadow-inner">
                            <Clock class="text-zinc-600 opacity-60" :size="28" />
                        </div>
                        <p class="font-black uppercase text-xs tracking-widest text-zinc-500 mb-1">Aucun scan récent</p>
                        <p class="text-sm text-zinc-600 max-w-[200px] mx-auto">Les billets scannés durant cette session
                            apparaîtront ici.</p>
                    </div>
                </div>
            </div>

            <!-- Search Tab : Redesigned List with Floating Bottom Input -->
            <div v-show="activeTab === 'search'" class="absolute inset-0 flex flex-col overflow-hidden">
                <div class="flex-1 overflow-y-auto p-0 pb-64 custom-scrollbar bg-black">
                    <div v-for="ticket in filteredTickets" :key="ticket.id" @click="openTicketDetails(ticket)"
                        class="bg-white border-b-2 border-black p-8 flex flex-col gap-4 active:scale-[0.98] transition-all shadow-xl odd:bg-neutral-300">

                        <!-- Main Info -->
                        <div class="flex items-center gap-6">
                            <div
                                :class="[ticket.checked_in_at ? 'bg-green-500 text-white shadow-lg shadow-green-500/20' : 'bg-neutral-100 text-neutral-300', 'shrink-0 w-20 h-20 rounded-3xl flex items-center justify-center transition-all']">
                                <template v-if="ticket.reservable_type === 'TicketType'">
                                    <TicketCheck v-if="ticket.checked_in_at" :size="40" stroke-width="2.5" />
                                    <Ticket v-else :size="40" />
                                </template>
                                <template v-else>
                                    <PackageCheck v-if="ticket.checked_in_at" :size="40" stroke-width="2.5" />
                                    <Package v-else :size="40" />
                                </template>
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-black text-3xl leading-none truncate text-neutral-900 mb-2">
                                    {{ ticket.buyer_name }}
                                </h4>
                                <p class="text-neutral-500 font-bold text-lg truncate">{{ ticket.buyer_email }}</p>
                            </div>
                        </div>

                        <div
                            class="bg-neutral-50/70 rounded-2xl p-4 flex items-center justify-between gap-4 border border-neutral-100">
                            <div class="flex flex-col">
                                <span class="text-lg font-black text-neutral-900">{{ ticket.reservable_name }}</span>
                            </div>
                            <div v-if="ticket.price_name" class="flex flex-col items-end">
                                <span
                                    class="text-sm font-black uppercase tracking-widest text-neutral-700 bg-white px-2.5 py-1 rounded-lg border border-neutral-200 shadow-sm">
                                    {{ ticket.price_name }}
                                </span>
                            </div>
                        </div>

                        <div class="w-full py-4 bg-neutral-800 rounded-2xl text-center">
                            <span class="font-mono text-base font-black text-white tracking-widest">
                                {{ ticket.public_id }}
                            </span>
                        </div>

                        <!-- Timestamp -->
                        <div v-if="ticket.checked_in_at"
                            class="flex items-center justify-center gap-3 py-4 bg-green-500 rounded-2xl border border-green-100 text-white">
                            <Clock :size="20" stroke-width="3" />
                            <span class="text-sm font-black uppercase tracking-widest">Validé à {{ new
                                Date(ticket.checked_in_at).toLocaleTimeString([], {
                                    hour: '2-digit', minute: '2-digit'
                                })
                            }}</span>
                        </div>
                    </div>

                    <div v-if="searchQuery && filteredTickets.length === 0"
                        class="py-20 text-center text-white flex flex-col items-center">
                        <TicketSlash class="mb-4" :size="64" />
                        <p class="font-black uppercase text-lg tracking-widest">Aucun résultat trouvé</p>
                    </div>
                    <div v-if="!searchQuery" class="py-20 text-center text-white">
                        <Search class="mx-auto mb-4 " :size="64" />
                        <p class="font-black uppercase text-lg tracking-widest">Commencez à chercher un nom</p>
                    </div>
                </div>

                <!-- Floating Bottom Input -->
                <div
                    class="absolute bottom-0 left-0 right-0 z-20 bg-linear-to-b from-transparent via-black/60 to-black p-6 py-12 rounded-t-lg">
                    <div class="relative group">
                        <Search class="absolute left-6 top-1/2 -translate-y-1/2 text-neutral-900 transition-colors"
                            :size="24" />
                        <input v-model="searchQuery" type="text" placeholder="Rechercher id, nom, email, ..."
                            class="w-full bg-white border-4 border-black rounded-[2.5rem] pl-16 pr-6 py-6 text-neutral-900 placeholder:text-neutral-500 focus:outline-none text-xl font-bold" />
                        <button v-if="searchQuery" @click="searchQuery = ''"
                            class="absolute right-6 top-1/2 -translate-y-1/2 text-neutral-500 hover:text-neutral-900">
                            <X :size="24" />
                        </button>
                    </div>
                </div>
            </div>
        </main>

        <!-- Bottom Navigation -->
        <nav class="bg-white border-t border-neutral-100 px-8 pt-3 pb-safe z-50">
            <div class="flex items-center justify-between max-w-lg mx-auto">
                <button @click="activeTab = 'search'"
                    :class="[activeTab === 'search' ? 'text-black' : 'text-neutral-500']"
                    class="flex flex-col items-center gap-1.5 py-2 flex-1 transition-all">
                    <List :size="24" :stroke-width="activeTab === 'search' ? 3 : 2" />
                    <span class="text-[9px] font-black uppercase tracking-widest">Liste</span>
                </button>

                <button @click="activeTab = 'scan'"
                    class="mx-8 relative -top-6 bg-neutral-900 text-white p-6 rounded-[2.5rem] shadow-2xl shadow-neutral-900/40 active:scale-90 transition-all border-8 border-white">
                    <Scan :size="32" stroke-width="3" />
                </button>

                <button @click="activeTab = 'history'"
                    :class="[activeTab === 'history' ? 'text-black' : 'text-neutral-500']"
                    class="flex flex-col items-center gap-1.5 py-2 flex-1 transition-all">
                    <Clock :size="24" :stroke-width="activeTab === 'history' ? 3 : 2" />
                    <span class="text-[9px] font-black uppercase tracking-widest">Historique</span>
                </button>
            </div>
        </nav>

        <!-- FULLSCREEN SCAN RESULT OVERLAY -->
        <Transition enter-active-class="transition duration-400 ease-out" enter-from-class="scale-125 opacity-0"
            enter-to-class="scale-100 opacity-100" leave-active-class="transition duration-200 ease-in"
            leave-from-class="scale-100 opacity-100" leave-to-class="scale-90 opacity-0">
            <div v-if="lastScanResult" class="fixed inset-0 z-100 flex flex-col p-8 overflow-hidden">
                <div :class="[getStatusColor(lastScanResult.status), 'absolute inset-0']"></div>
                <div class="relative flex-1 flex flex-col items-center justify-center text-white text-center">
                    <div class="mb-10 bg-white/20 p-10 rounded-[4rem] backdrop-blur-md">
                        <component :is="getStatusIcon(lastScanResult.status)" :size="96" stroke-width="2.5" />
                    </div>
                    <h2 class="text-4xl font-black uppercase tracking-tight mb-6 leading-tight">{{
                        lastScanResult.message }}</h2>
                    <template v-if="lastScanResult.ticket">
                        <p class="text-6xl font-black mb-4 truncate w-full leading-none">{{
                            lastScanResult.ticket.buyer_name }}</p>
                        <p class="text-xl font-bold opacity-80 uppercase tracking-widest mb-10">{{
                            lastScanResult.ticket.reservable_name }}</p>
                        <div
                            class="bg-black/10 backdrop-blur-sm px-8 py-4 rounded-3xl border border-white/20 flex items-center gap-4">
                            <Ticket :size="20" class="opacity-50" />
                            <span class="font-mono text-2xl font-bold tracking-widest">{{
                                lastScanResult.ticket.public_id }}</span>
                        </div>
                    </template>
                </div>
                <div class="relative mt-auto space-y-4">
                    <button @click="lastScanResult = null"
                        class="w-full py-7 bg-white text-neutral-900 rounded-[3rem] text-2xl font-black uppercase tracking-widest shadow-2xl active:scale-95 transition-all">OK,
                        suivant</button>
                    <button v-if="lastScanResult.status === 'already_scanned' && lastScanResult.ticket"
                        @click="toggleTicketStatus(lastScanResult.ticket); lastScanResult = null"
                        class="w-full py-5 bg-black/10 text-white border border-white/10 rounded-3xl text-[10px] font-black uppercase tracking-widest">Annuler
                        la validation</button>
                </div>
            </div>
        </Transition>

        <!-- TICKET DETAIL MODAL -->
        <div v-if="selectedTicket" class="fixed inset-0 z-100 flex flex-col justify-end">
            <!-- Backdrop: Fades in/out independently -->
            <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="opacity-0"
                enter-to-class="opacity-100" leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100" leave-to-class="opacity-0" appear>
                <div class="absolute inset-0 bg-neutral-900/80 backdrop-blur-md" @click="selectedTicket = null"></div>
            </Transition>

            <!-- Content: Slides up/down -->
            <Transition enter-active-class="transition duration-400 cubic-bezier(0.16, 1, 0.3, 1)"
                enter-from-class="translate-y-full" enter-to-class="translate-y-0"
                leave-active-class="transition duration-300 ease-in" leave-from-class="translate-y-0"
                leave-to-class="translate-y-full" appear>
                <div
                    class="relative bg-white rounded-t-[3.5rem] p-8 pb-12 shadow-2xl flex flex-col max-h-[92vh] border-t border-neutral-200">
                    <!-- Close Header -->
                    <div class="flex items-center justify-between mb-8">
                        <button @click="selectedTicket = null"
                            class="w-12 h-12 rounded-2xl bg-neutral-100 flex items-center justify-center text-neutral-900 active:scale-90 transition-all">
                            <X :size="24" stroke-width="3" />
                        </button>
                        <div class="flex-1 text-center">
                            <div class="w-12 h-1.5 bg-neutral-200 rounded-full mx-auto"></div>
                        </div>
                        <div class="w-12"></div>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar pr-1">
                        <div class="flex items-start justify-between mb-10 mt-6">
                            <div class="min-w-0 pr-6">
                                <h2 class="text-4xl font-black text-neutral-900 leading-[0.9] mb-3 wrap-break-word">{{
                                    selectedTicket.buyer_name }}</h2>
                                <p class="text-neutral-500 font-bold text-lg break-all">{{ selectedTicket.buyer_email }}
                                </p>
                            </div>
                            <div
                                :class="[selectedTicket.checked_in_at ? 'bg-green-500 text-white' : 'bg-neutral-900 text-white', 'shrink-0 p-6 rounded-[2.5rem] transition-all scale-110']">
                                <template v-if="selectedTicket.reservable_type === 'TicketType'">
                                    <TicketCheck v-if="selectedTicket.checked_in_at" :size="40" stroke-width="2.5" />
                                    <Ticket v-else :size="40" />
                                </template>
                                <template v-else>
                                    <PackageCheck v-if="selectedTicket.checked_in_at" :size="40" stroke-width="2.5" />
                                    <Package v-else :size="40" />
                                </template>
                            </div>
                        </div>

                        <div class="grid gap-4 mb-10">
                            <div
                                class="bg-neutral-50 rounded-[2.5rem] p-8 border-2 border-neutral-100 flex flex-col gap-4">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p
                                            class="text-sm font-black uppercase tracking-[0.2em] text-neutral-400 mb-0.5">
                                            {{ selectedTicket.reservable_type === 'TicketType' ? 'Billet' : 'Extra' }}
                                        </p>
                                        <p class="text-2xl font-black text-neutral-900 leading-tight">{{
                                            selectedTicket.reservable_name }}</p>
                                    </div>
                                    <Ticket class="text-neutral-900" :size="32" stroke-width="2.5" />
                                </div>

                                <div v-if="selectedTicket.price_name"
                                    class="pt-4 border-t border-neutral-200/50 flex items-center justify-between">
                                    <div>
                                        <p
                                            class="text-sm font-black uppercase tracking-[0.2em] text-neutral-400 mb-0.5">
                                            Tarif
                                        </p>
                                        <p class="text-lg font-bold text-neutral-700">{{ selectedTicket.price_name }}
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-neutral-900 rounded-[2.5rem] p-8 flex flex-col gap-1">
                                <p class="text-[10px] font-black uppercase tracking-[0.3em] text-neutral-100">
                                    Identifiant Public
                                </p>
                                <p class="font-mono text-2xl font-black text-white tracking-[0.2em] mt-1">
                                    {{ selectedTicket.public_id }}
                                </p>
                            </div>

                            <div v-if="selectedTicket.checked_in_at"
                                class="bg-green-50 rounded-[2.5rem] p-8 flex items-center gap-6 border-2 border-green-200">
                                <div class="bg-green-500 text-white p-4 rounded-3xl shadow-lg shadow-green-500/20">
                                    <Check :size="28" stroke-width="4" />
                                </div>
                                <div class="min-w-0">
                                    <p class="font-black text-green-900 uppercase text-sm tracking-tight">Ticket Validé
                                    </p>
                                    <p class="text-green-700 font-bold text-base mt-0.5">
                                        {{
                                            new Date(selectedTicket.checked_in_at).toLocaleDateString('fr-FR', {
                                                day:
                                                    'numeric',
                                                month: 'long'
                                            })
                                        }} à {{
                                            new Date(selectedTicket.checked_in_at).toLocaleTimeString('fr-FR', {
                                                hour:
                                                    '2-digit',
                                                minute: '2-digit'
                                            })
                                        }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-neutral-200">
                        <button @click="toggleTicketStatus(selectedTicket); selectedTicket = null" :class="[
                            'w-full py-7 rounded-[2.5rem] text-xl font-black uppercase tracking-widest transition-all active:scale-[0.98] flex items-center justify-center gap-3',
                            selectedTicket.checked_in_at
                                ? 'bg-red-500 text-white hover:bg-red-600'
                                : 'bg-emerald-600 text-white hover:bg-emerald-700 '
                        ]">
                            <UserCheck v-if="!selectedTicket.checked_in_at" :size="24" />
                            <History v-else :size="24" />
                            {{ selectedTicket.checked_in_at ? 'Annuler Validation' : 'Valider Entrée' }}
                        </button>
                    </div>
                </div>
            </Transition>
        </div>

        <!-- Global Loading -->
        <div v-if="isLoading" class="fixed inset-0 z-200 flex items-center justify-center bg-white/40 backdrop-blur-xs">
            <Loader2 class="animate-spin text-neutral-900" :size="48" />
        </div>
    </div>
</template>

<style scoped lang="css">
.safe-top {
    padding-top: max(1rem, env(safe-area-inset-top));
}

.pb-safe {
    padding-bottom: max(2rem, env(safe-area-inset-bottom));
}

.pt-safe {
    padding-top: max(0rem, env(safe-area-inset-top));
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #e5e7eb;
    border-radius: 10px;
}

#reader {
    width: 100% !important;
    height: 100% !important;
}

#reader :deep(video) {
    object-fit: cover !important;
    width: 100% !important;
    height: 100% !important;
}

#reader :deep(img) {
    display: none !important;
}

#reader :deep(#html5-qrcode-anchor-scan-type-change) {
    display: none !important;
}
</style>
