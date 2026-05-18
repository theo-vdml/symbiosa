<script setup lang="ts">
    import { ref, computed, onUnmounted, nextTick, onMounted, watch } from 'vue';
    import { router } from '@inertiajs/vue3';
    import {
        Search, X, Check, ChevronRight, Loader2, History, User, AlertCircle,
        CheckCircle2, XCircle, Scan, List, Clock, UserCheck, Ticket,
        Info,
        TicketCheck,
        PackageCheck,
        Package,
        TicketSlash
    } from '@lucide/vue';
    import { Html5Qrcode } from "html5-qrcode";

    const props = defineProps<{
        checkinList: {
            id: number;
            name: string;
            event_title: string;
            public_url_token?: string;
        };
        stats: {
            total: number;
            scanned: number;
        };
        isPublic: boolean;
        scan_result?: {
            status: 'success' | 'already_scanned' | 'invalid' | 'unauthorized' | 'error' | 'cancelled_checkin';
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
        status: 'success' | 'already_scanned' | 'invalid' | 'unauthorized' | 'error' | 'cancelled_checkin';
        message: string;
        ticket?: any;
        timestamp: number;
    } | null>(null);

    const isLoading = ref(false);
    const isSearching = ref(false);
    const isModalLoading = ref(false);
    const searchResults = ref<any[]>([]);
    const selectedTicket = ref<any>(null);
    const sessionHistory = ref<Array<any>>([]);

    // Persistence: Load from local storage
    onMounted(() => {
        const savedHistory = localStorage.getItem(`checkin_history_${props.checkinList.id}`);
        if (savedHistory) {
            try {
                sessionHistory.value = JSON.parse(savedHistory);
            } catch (e) {
                console.error("Failed to parse history", e);
            }
        }
        if (activeTab.value === 'scan') startScanner();
    });

    // Persistence: Save to local storage when history changes
    watch(sessionHistory, (newHistory) => {
        localStorage.setItem(`checkin_history_${props.checkinList.id}`, JSON.stringify(newHistory.slice(0, 100))); // Keep last 100
    }, { deep: true });

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
                // Add to history (unshift to keep recent first)
                sessionHistory.value.unshift({
                    ...result.ticket,
                    action_type: result.status,
                    timestamp: Date.now()
                });

                // Update search results if ticket is present
                const index = searchResults.value.findIndex(t => t.id === result.ticket.id);
                if (index !== -1) {
                    searchResults.value[index] = { ...result.ticket };
                }

                // Update selected ticket if it's the one being modified
                if (selectedTicket.value && selectedTicket.value.id === result.ticket.id) {
                    selectedTicket.value = { ...result.ticket };
                }
            }
        }
    }, { immediate: true });

    // API Search Logic
    let searchTimeout: any = null;
    watch(searchQuery, (newQuery) => {
        if (searchTimeout) clearTimeout(searchTimeout);
        if (!newQuery || newQuery.length < 2) {
            searchResults.value = [];
            return;
        }
        isSearching.value = true;
        searchTimeout = setTimeout(fetchResults, 400);
    });

    const fetchResults = async () => {
        try {
            const baseUrl = props.isPublic
                ? `/checkin/${props.checkinList.public_url_token}/search`
                : `/checkin/list/${props.checkinList.id}/search`;
            const response = await fetch(`${baseUrl}?q=${encodeURIComponent(searchQuery.value)}`);
            const data = await response.json();
            searchResults.value = data;
        } catch (error) {
            console.error("Search error", error);
        } finally {
            isSearching.value = false;
        }
    };

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

    const openTicketDetails = async (ticket: any) => {
        selectedTicket.value = { ...ticket };
        isModalLoading.value = true;
        // Fetch fresh status to ensure we show the latest state
        try {
            const response = await fetch(`/checkin/tickets/${ticket.id}/status`);
            const data = await response.json();
            if (selectedTicket.value && selectedTicket.value.id === ticket.id) {
                selectedTicket.value = data;

                // Also update in search results if present
                const index = searchResults.value.findIndex(t => t.id === ticket.id);
                if (index !== -1) {
                    searchResults.value[index] = { ...data };
                }
            }
        } catch (e) {
            console.error("Failed to fetch fresh ticket status", e);
        } finally {
            isModalLoading.value = false;
        }
    };

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
            case 'cancelled_checkin': return 'bg-red-500';
            case 'unauthorized': return 'bg-red-600';
            default: return 'bg-neutral-500';
        }
    };

    const getStatusIcon = (status: string) => {
        switch (status) {
            case 'success': return CheckCircle2;
            case 'already_scanned': return AlertCircle;
            case 'invalid': return XCircle;
            case 'cancelled_checkin': return X;
            case 'unauthorized': return AlertCircle;
            default: return Info;
        }
    };

    const getActionLabel = (status: string) => {
        switch (status) {
            case 'success': return 'Validé';
            case 'already_scanned': return 'Déjà scanné';
            case 'invalid': return 'Invalide';
            case 'cancelled_checkin': return 'Annulé';
            case 'unauthorized': return 'Non autorisé';
            default: return status;
        }
    };

    const scanPercentage = computed(() => {
        if (localStats.value.total === 0) return 0;
        return Math.round((localStats.value.scanned / localStats.value.total) * 100);
    });
</script>

<template>
    <div class="fixed inset-0 bg-white text-neutral-900 font-sans antialiased overflow-hidden flex flex-col">
        <!-- HEADER : Compact Brutalist Horizontal Dashboard -->
        <header v-show="activeTab === 'scan'" class="safe-top bg-white border-b-4 border-black z-50 shrink-0">
            <div class="px-6 py-4 flex flex-col gap-3">
                <div class="flex items-end justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-[8px] font-black uppercase tracking-[0.3em] text-neutral-400 mb-0.5">
                            {{ props.checkinList.event_title }}
                        </p>
                        <h1 class="font-chillax text-2xl font-black text-black leading-none uppercase truncate">
                            {{ props.checkinList.name }}
                        </h1>
                    </div>
                    <div class="shrink-0 flex flex-col items-end">
                        <span class="text-2xl font-black text-black leading-none">{{ scanPercentage }}%</span>
                    </div>
                </div>

                <!-- Progress Bar Container -->
                <div class="flex flex-col gap-1.5">
                    <div
                        class="h-6 w-full bg-neutral-100 border-2 border-black rounded-xl overflow-hidden shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                        <div class="h-full bg-emerald-500 border-r-2 border-black transition-all duration-1000 ease-out"
                            :style="{ width: `${scanPercentage}%` }"></div>
                    </div>
                    <!-- Label Below Bar -->
                    <div class="text-center">
                        <span class="text-[8px] font-black uppercase tracking-[0.2em] text-neutral-400">
                            {{ localStats.scanned }} / {{ localStats.total }} tickets scannés
                        </span>
                    </div>
                </div>
            </div>
        </header>


        <!-- Main Content Area -->
        <main class="flex-1 relative overflow-hidden bg-black">
            <!-- Scanner Tab -->
            <div v-show="activeTab === 'scan'" class="absolute inset-0 flex flex-col bg-zinc-900">
                <div id="reader" class="flex-1"></div>
            </div>

            <!-- History Tab -->
            <div v-show="activeTab === 'history'" class="absolute inset-0 flex flex-col overflow-y-auto bg-zinc-800">
                <!-- Header Section -->
                <div class="px-6 py-6 bg-zinc-900 border-b-2 border-black">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <h2 class="font-chillax text-2xl font-black text-white uppercase tracking-tight">Historique
                            </h2>
                            <p class="text-zinc-500 font-bold uppercase tracking-widest text-[8px] mt-0.5">Actions
                                récentes
                                de la session</p>
                        </div>
                        <button v-if="sessionHistory.length > 0" @click="sessionHistory = []"
                            class="text-[10px] font-black uppercase tracking-widest text-white bg-red-600 px-4 py-2 rounded-xl border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all active:translate-x-0.5 active:translate-y-0.5 active:shadow-none">
                            Effacer
                        </button>
                    </div>
                </div>

                <!-- History List -->
                <div class="flex-1">
                    <div v-for="(item, index) in sessionHistory" :key="index" @click="openTicketDetails(item)"
                        class="bg-white border-b border-black p-4 flex items-center gap-4 active:scale-[0.98] transition-all odd:bg-neutral-100">

                        <!-- Status Icon Container -->
                        <div
                            :class="[getStatusColor(item.action_type), 'shrink-0 w-12 h-12 rounded-xl text-white flex items-center justify-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] border-2 border-black']">
                            <component :is="getStatusIcon(item.action_type)" :size="20" stroke-width="3" />
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-0.5">
                                <h4 class="font-black text-neutral-900 text-lg truncate leading-none">
                                    {{ item.buyer_name }}
                                </h4>
                                <span class="shrink-0 font-mono text-[10px] font-black text-neutral-400">
                                    {{ new Date(item.timestamp).toLocaleTimeString([], {
                                        hour: '2-digit', minute:
                                            '2-digit'
                                    }) }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    :class="[getStatusColor(item.action_type), 'px-1.5 py-0.5 rounded text-[8px] font-black uppercase tracking-widest text-white border border-black/10']">
                                    {{ getActionLabel(item.action_type) }}
                                </span>
                                <p class="text-xs text-neutral-500 font-bold truncate">
                                    {{ item.reservable_name }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div v-if="sessionHistory.length === 0"
                        class="py-32 text-center flex flex-col items-center justify-center">
                        <div
                            class="w-24 h-24 rounded-[2.5rem] bg-zinc-900 border-4 border-zinc-800 flex items-center justify-center mb-6 shadow-2xl">
                            <Clock class="text-zinc-600" :size="40" />
                        </div>
                        <p class="font-black uppercase text-xl tracking-widest text-white mb-2">Aucun scan récent</p>
                        <p class="text-zinc-500 font-bold max-w-[250px] mx-auto text-lg">Les billets scannés ou modifiés
                            apparaîtront ici.</p>
                    </div>
                </div>
            </div>

            <!-- Search Tab : Redesigned List with Floating Bottom Input -->
            <div v-show="activeTab === 'search'" class="absolute inset-0 flex flex-col overflow-hidden bg-zinc-800">
                <!-- Header Section (Matches History) -->
                <div class="px-6 py-6 bg-zinc-900 border-b-2 border-black shrink-0">
                    <div class="flex flex-col">
                        <h2 class="font-chillax text-2xl font-black text-white uppercase tracking-tight">Recherche</h2>
                        <p class="text-zinc-500 font-bold uppercase tracking-widest text-[8px] mt-0.5">Trouver par nom,
                            email
                            ou ID</p>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-0 pb-48 custom-scrollbar">
                    <div v-if="isSearching" class="py-12 flex flex-col items-center justify-center text-white">
                        <Loader2 class="animate-spin mb-4" :size="32" />
                        <p class="font-black uppercase text-sm tracking-widest">Recherche en cours...</p>
                    </div>

                    <div v-else-if="!searchQuery" class="py-12 text-center text-white">
                        <div
                            class="w-16 h-16 rounded-2xl bg-zinc-900 border-2 border-zinc-800 flex items-center justify-center mb-4 shadow-xl mx-auto">
                            <Search class="text-zinc-600" :size="28" />
                        </div>
                        <p class="font-black uppercase text-lg tracking-widest text-white mb-1">Prêt à scanner ?</p>
                        <p class="text-zinc-500 font-bold max-w-[200px] mx-auto text-sm">Tapez un nom ou un
                            identifiant.</p>
                    </div>

                    <template v-else>
                        <div v-for="ticket in searchResults" :key="ticket.id" @click="openTicketDetails(ticket)"
                            class="bg-white border-b-2 border-black p-5 flex flex-col gap-4 active:scale-[0.98] transition-all odd:bg-neutral-100">

                            <!-- Main Info -->
                            <div class="flex items-center gap-4">
                                <div
                                    :class="[ticket.checked_in_at ? 'bg-green-500 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]' : 'bg-neutral-100', 'shrink-0 w-14 h-14 rounded-2xl border-2 border-black flex items-center justify-center transition-all']">
                                    <template v-if="ticket.reservable_type === 'TicketType'">
                                        <TicketCheck v-if="ticket.checked_in_at" class="text-white" :size="28"
                                            stroke-width="2.5" />
                                        <Ticket v-else class="text-neutral-400" :size="28" />
                                    </template>
                                    <template v-else>
                                        <PackageCheck v-if="ticket.checked_in_at" class="text-white" :size="28"
                                            stroke-width="2.5" />
                                        <Package v-else class="text-neutral-400" :size="28" />
                                    </template>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4 class="font-black text-xl leading-none truncate text-neutral-900 mb-1">
                                        {{ ticket.buyer_name }}
                                    </h4>
                                    <p class="text-neutral-500 font-bold text-sm truncate">{{ ticket.buyer_email }}</p>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3">
                                <div
                                    class="bg-neutral-900/5 rounded-xl p-3 flex items-center justify-between gap-3 border border-black/5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-black text-neutral-900 leading-tight">{{
                                            ticket.reservable_name
                                            }}</span>
                                    </div>
                                    <div v-if="ticket.price_name" class="flex flex-col items-end">
                                        <span
                                            class="text-[8px] font-black uppercase tracking-widest text-neutral-700 bg-white px-2 py-0.5 rounded border border-black shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]">
                                            {{ ticket.price_name }}
                                        </span>
                                    </div>
                                </div>

                                <div
                                    class="w-full py-2 bg-neutral-900 rounded-xl text-center border-2 border-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                    <span class="font-mono text-sm font-black text-white tracking-widest">
                                        {{ ticket.public_id }}
                                    </span>
                                </div>

                                <!-- Timestamp -->
                                <div v-if="ticket.checked_in_at"
                                    class="flex items-center justify-center gap-2 py-2 bg-green-500 rounded-xl border-2 border-black text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]">
                                    <Clock :size="16" stroke-width="3" />
                                    <span class="text-[10px] font-black uppercase tracking-widest">Validé à {{ new
                                        Date(ticket.checked_in_at).toLocaleTimeString([], {
                                            hour: '2-digit', minute: '2-digit'
                                        })
                                    }}</span>
                                </div>
                            </div>
                        </div>

                        <div v-if="searchQuery && searchResults.length === 0"
                            class="py-20 text-center text-white flex flex-col items-center">
                            <div
                                class="w-16 h-16 rounded-2xl bg-zinc-900 border-2 border-zinc-800 flex items-center justify-center mb-4 shadow-xl mx-auto">
                                <TicketSlash class="text-zinc-600" :size="28" />
                            </div>
                            <p class="font-black uppercase text-lg tracking-widest text-white mb-1">Aucun ticket</p>
                            <p class="text-zinc-500 font-bold max-w-[200px] mx-auto text-sm">Essayez avec un autre nom
                                ou email.</p>
                        </div>
                    </template>
                </div>

                <!-- Floating Bottom Input -->
                <div
                    class="absolute bottom-0 left-0 right-0 z-20 bg-linear-to-b from-transparent via-black/60 to-black p-4 py-8 rounded-t-lg">
                    <div class="relative group">
                        <Search class="absolute left-5 top-1/2 -translate-y-1/2 text-neutral-900 transition-colors"
                            :size="20" />
                        <input v-model="searchQuery" type="text" placeholder="Rechercher..."
                            class="w-full bg-white border-2 border-black rounded-2xl pl-12 pr-10 py-4 text-neutral-900 placeholder:text-neutral-500 focus:outline-none text-lg font-bold" />
                        <button v-if="searchQuery" @click="searchQuery = ''"
                            class="absolute right-5 top-1/2 -translate-y-1/2 text-neutral-500 hover:text-neutral-900">
                            <X :size="20" />
                        </button>
                    </div>
                </div>
            </div>
        </main>

        <!-- Bottom Navigation -->
        <nav class="bg-zinc-900 border-t-4 border-black px-4 pt-2 pb-safe z-50">
            <div class="flex items-center justify-between max-w-lg mx-auto">
                <button @click="activeTab = 'search'" :class="[activeTab === 'search' ? 'text-white' : 'text-zinc-500']"
                    class="flex flex-col items-center gap-1 py-1 flex-1 transition-all group">
                    <div
                        :class="[activeTab === 'search' ? 'bg-white text-black border-black shadow-[2px_2px_0px_0px_rgba(255,255,255,0.2)]' : 'bg-transparent text-zinc-500 border-transparent', 'p-2 rounded-xl border-2 transition-all group-active:scale-90']">
                        <List :size="20" :stroke-width="activeTab === 'search' ? 4 : 2" />
                    </div>
                    <span class="text-[8px] font-black uppercase tracking-widest">Liste</span>
                </button>

                <button @click="activeTab = 'scan'"
                    class="mx-6 relative -top-6 bg-emerald-500 text-black p-6 rounded-2xl shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all border-4 border-black">
                    <Scan :size="32" stroke-width="4" />
                </button>

                <button @click="activeTab = 'history'"
                    :class="[activeTab === 'history' ? 'text-white' : 'text-zinc-500']"
                    class="flex flex-col items-center gap-1 py-1 flex-1 transition-all group">
                    <div
                        :class="[activeTab === 'history' ? 'bg-white text-black border-black shadow-[2px_2px_0px_0px_rgba(255,255,255,0.2)]' : 'bg-transparent text-zinc-500 border-transparent', 'p-2 rounded-xl border-2 transition-all group-active:scale-90']">
                        <Clock :size="20" :stroke-width="activeTab === 'history' ? 4 : 2" />
                    </div>
                    <span class="text-[8px] font-black uppercase tracking-widest">Historique</span>
                </button>
            </div>
        </nav>

        <!-- FULLSCREEN SCAN RESULT OVERLAY -->
        <Transition enter-active-class="transition duration-400 ease-out" enter-from-class="scale-110 opacity-0"
            enter-to-class="scale-100 opacity-100" leave-active-class="transition duration-200 ease-in"
            leave-from-class="scale-100 opacity-100" leave-to-class="scale-95 opacity-0">
            <div v-if="lastScanResult"
                :class="[getStatusColor(lastScanResult.status), 'fixed inset-0 z-150 flex flex-col p-6 overflow-hidden transition-colors duration-500 border-[6px] border-black']">
                <div class="flex-1 flex flex-col items-center justify-center text-center">
                    <!-- Status Icon -->
                    <div
                        class="mb-6 p-6 rounded-[2rem] bg-white/20 backdrop-blur-md border-2 border-white shadow-[4px_4px_0px_0px_rgba(0,0,0,0.2)] text-white">
                        <component :is="getStatusIcon(lastScanResult.status)" :size="80" stroke-width="4" />
                    </div>

                    <!-- Message -->
                    <h2
                        class="text-3xl font-black uppercase tracking-tight mb-6 text-white leading-none drop-shadow-lg">
                        {{
                            lastScanResult.message }}</h2>

                    <!-- Ticket Details -->
                    <template v-if="lastScanResult.ticket">
                        <p class="text-4xl font-black text-white mb-2 truncate w-full leading-none drop-shadow-md">{{
                            lastScanResult.ticket.buyer_name }}</p>
                        <p class="text-lg font-black text-white/70 uppercase tracking-widest mb-8">{{
                            lastScanResult.ticket.reservable_name }}</p>

                        <div
                            class="bg-white py-4 px-8 rounded-2xl border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                            <span class="font-mono text-xl font-black text-black tracking-[0.2em]">{{
                                lastScanResult.ticket.public_id }}</span>
                        </div>
                    </template>
                </div>

                <!-- Actions -->
                <div class="mt-auto space-y-4">
                    <button @click="lastScanResult = null"
                        class="w-full py-6 bg-white text-black border-2 border-black rounded-2xl text-2xl font-black uppercase tracking-widest shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] active:translate-x-1 active:translate-y-1 active:shadow-none transition-all">
                        Suivant
                    </button>

                    <button v-if="lastScanResult.status === 'already_scanned' && lastScanResult.ticket"
                        @click="toggleTicketStatus(lastScanResult.ticket); lastScanResult = null"
                        class="w-full py-4 bg-black/20 text-white border-2 border-white rounded-xl text-xs font-black uppercase tracking-widest shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all">
                        Annuler la validation
                    </button>
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
                    class="relative bg-white rounded-t-3xl shadow-2xl flex flex-col max-h-[92vh] border-t-4 border-black overflow-hidden">

                    <!-- Header with Close -->
                    <div class="px-6 py-5 bg-zinc-900 border-b-2 border-black flex items-center justify-between">
                        <div class="flex flex-col">
                            <h2 class="font-chillax text-2xl font-black text-white uppercase tracking-tight">Détails
                            </h2>
                            <p class="text-zinc-500 font-bold uppercase tracking-widest text-[8px] mt-0.5">Informations
                                du
                                ticket</p>
                        </div>
                        <button @click="selectedTicket = null"
                            class="w-10 h-10 rounded-xl bg-white border-2 border-black flex items-center justify-center text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] active:translate-x-0.5 active:translate-y-0.5 active:shadow-none transition-all">
                            <X :size="20" stroke-width="4" />
                        </button>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar p-6">
                        <div v-if="isModalLoading" class="py-12 flex flex-col items-center justify-center">
                            <Loader2 class="animate-spin text-neutral-900 mb-2" :size="32" />
                            <p class="font-black uppercase text-sm tracking-widest text-neutral-900">Mise à jour...</p>
                        </div>

                        <template v-else>
                            <div class="flex items-start justify-between mb-8 mt-2">
                                <div class="min-w-0 pr-4">
                                    <h2 class="text-3xl font-black text-neutral-900 leading-[0.9] mb-2 wrap-break-word">
                                        {{
                                            selectedTicket.buyer_name }}</h2>
                                    <p class="text-neutral-500 font-bold text-sm break-all">{{
                                        selectedTicket.buyer_email }}
                                    </p>
                                </div>
                                <div
                                    :class="[selectedTicket.checked_in_at ? 'bg-green-500 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]' : 'bg-neutral-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]', 'shrink-0 p-5 rounded-2xl border-2 border-black transition-all']">
                                    <template v-if="selectedTicket.reservable_type === 'TicketType'">
                                        <TicketCheck v-if="selectedTicket.checked_in_at" class="text-white" :size="32"
                                            stroke-width="3" />
                                        <Ticket v-else class="text-white" :size="32" />
                                    </template>
                                    <template v-else>
                                        <PackageCheck v-if="selectedTicket.checked_in_at" class="text-white" :size="32"
                                            stroke-width="3" />
                                        <Package v-else class="text-white" :size="32" />
                                    </template>
                                </div>
                            </div>

                            <div class="grid gap-4 mb-8">
                                <div
                                    class="bg-neutral-100 rounded-2xl p-6 border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col gap-3">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p
                                                class="text-[8px] font-black uppercase tracking-[0.2em] text-neutral-400 mb-0.5">
                                                {{ selectedTicket.reservable_type === 'TicketType' ? 'Type de Billet' :
                                                    'Supplément Extra'
                                                }}
                                            </p>
                                            <p class="text-xl font-black text-neutral-900 leading-tight uppercase">{{
                                                selectedTicket.reservable_name }}</p>
                                        </div>
                                        <div class="bg-white p-2 rounded-lg border border-black">
                                            <Ticket class="text-neutral-900" :size="20" stroke-width="3" />
                                        </div>
                                    </div>

                                    <div v-if="selectedTicket.price_name"
                                        class="pt-4 border-t border-black/5 flex items-center justify-between">
                                        <div>
                                            <p
                                                class="text-[8px] font-black uppercase tracking-[0.2em] text-neutral-400 mb-0.5">
                                                Tarif appliqué
                                            </p>
                                            <p class="text-sm font-black text-neutral-700 uppercase">{{
                                                selectedTicket.price_name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="bg-neutral-900 rounded-2xl p-6 border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] flex flex-col gap-0.5">
                                    <p class="text-[8px] font-black uppercase tracking-[0.3em] text-zinc-500">
                                        Identifiant Unique
                                    </p>
                                    <p class="font-mono text-xl font-black text-white tracking-[0.1em]">
                                        {{ selectedTicket.public_id }}
                                    </p>
                                </div>

                                <div v-if="selectedTicket.checked_in_at"
                                    class="bg-green-500 rounded-2xl p-6 flex items-center gap-4 border-2 border-black shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]">
                                    <div class="bg-white text-green-600 p-3 rounded-xl border border-black">
                                        <Check :size="24" stroke-width="4" />
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-black text-white uppercase text-sm tracking-tight">Entrée Validée
                                        </p>
                                        <p class="text-green-100 font-bold text-xs">
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
                        </template>
                    </div>

                    <div class="p-6 bg-zinc-50 border-t-2 border-black">
                        <button @click="toggleTicketStatus(selectedTicket); selectedTicket = null"
                            :disabled="isModalLoading" :class="[
                                'w-full py-5 rounded-2xl text-lg font-black uppercase tracking-widest transition-all active:translate-x-1 active:translate-y-1 active:shadow-none border-2 border-black flex items-center justify-center gap-3',
                                selectedTicket?.checked_in_at
                                    ? 'bg-red-500 text-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]'
                                    : 'bg-emerald-500 text-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]',
                                isModalLoading ? 'opacity-50 grayscale cursor-not-allowed' : ''
                            ]">
                            <template v-if="!isModalLoading">
                                <UserCheck v-if="!selectedTicket?.checked_in_at" :size="24" stroke-width="3" />
                                <History v-else :size="24" stroke-width="3" />
                                {{ selectedTicket?.checked_in_at ? 'Annuler l\'Entrée' : 'Valider l\'Entrée' }}
                            </template>
                            <Loader2 v-else class="animate-spin" :size="24" />
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
    padding-bottom: max(1rem, env(safe-area-inset-bottom));
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
