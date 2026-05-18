<script setup lang="ts">
    import { ref, computed, onUnmounted, nextTick, onMounted, watch } from 'vue';
    import { router } from '@inertiajs/vue3';
    import {
        Search, X, Check, ChevronRight, Loader2, History, User, AlertCircle,
        CheckCircle2, XCircle, Scan, List, Clock, UserCheck, Ticket,
        Info,
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
            default: return 'bg-zinc-500';
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
    <div class="fixed inset-0 bg-white text-zinc-900 font-sans antialiased overflow-hidden flex flex-col">
        <!-- HEADER : Larger, with Integrated Progress -->
        <header class="safe-top bg-white border-b border-zinc-100 z-50">
            <div class="px-6 py-6 flex flex-col gap-4">
                <div class="flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-400 mb-0.5 truncate">{{
                            props.checkinList.event_title }}</p>
                        <h1 class="font-chillax text-2xl font-black truncate leading-tight">{{ props.checkinList.name }}
                        </h1>
                    </div>
                    <div class="shrink-0 text-right">
                        <span class="text-3xl font-black tabular-nums tracking-tighter">{{ scanPercentage }}%</span>
                    </div>
                </div>

                <!-- Progress Bar Integrated in Header -->
                <div class="flex flex-col gap-2">
                    <div class="h-3 bg-zinc-50 rounded-full overflow-hidden border border-zinc-100/50">
                        <div class="h-full bg-zinc-900 transition-all duration-1000 ease-out"
                            :style="{ width: `${scanPercentage}%` }"></div>
                    </div>
                    <div
                        class="flex justify-between items-center text-[10px] font-black uppercase tracking-widest text-zinc-400">
                        <span>{{ localStats.scanned }} Validés</span>
                        <span>Total: {{ localStats.total }}</span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 relative overflow-hidden bg-neutral-600">
            <!-- Scanner Tab : Completely raw camera -->
            <div v-show="activeTab === 'scan'" class="absolute inset-0 flex flex-col bg-black">
                <div id="reader" class="flex-1"></div>
            </div>

            <!-- History Tab -->
            <div v-show="activeTab === 'history'" class="absolute inset-0 flex flex-col p-6 overflow-y-auto pt-safe">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-chillax text-2xl font-black">Historique</h2>
                    <button @click="sessionHistory = []"
                        class="text-[10px] font-black uppercase tracking-widest text-zinc-400">Effacer</button>
                </div>
                <div class="space-y-2">
                    <div v-for="item in sessionHistory" :key="item.id + item.scan_timestamp"
                        @click="openTicketDetails(item)"
                        class="bg-white border border-zinc-200/50 rounded-2xl p-4 flex items-center gap-4 active:scale-95 transition-all shadow-sm">
                        <div
                            :class="[getStatusColor(item.scan_status), 'shrink-0 w-10 h-10 rounded-xl text-white flex items-center justify-center']">
                            <component :is="getStatusIcon(item.scan_status)" :size="20" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="font-bold truncate text-sm leading-tight">{{ item.buyer_name }}</h4>
                            <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-tight mt-0.5">
                                {{ item.reservable_name }} <span class="mx-1">•</span> {{ new
                                    Date(item.scan_timestamp).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
                                }}
                            </p>
                        </div>
                    </div>
                    <div v-if="sessionHistory.length === 0" class="py-20 text-center text-zinc-300">
                        <Clock class="mx-auto mb-4 opacity-10" :size="48" />
                        <p class="font-black uppercase text-xs tracking-widest">Aucun scan récent</p>
                    </div>
                </div>
            </div>

            <!-- Search Tab : Redesigned List with Floating Bottom Input -->
            <div v-show="activeTab === 'search'" class="absolute inset-0 flex flex-col overflow-hidden">
                <div class="flex-1 overflow-y-auto space-y-4 p-6 pb-64 custom-scrollbar">
                    <div v-for="ticket in filteredTickets" :key="ticket.id" @click="openTicketDetails(ticket)"
                        class="bg-white border-2 border-zinc-900 rounded-[2.5rem] p-8 flex flex-col gap-6 active:scale-[0.98] transition-all shadow-xl">
                        <!-- Badge ID & Category -->
                        <div class="flex items-center justify-between">
                            <span :class="[
                                ticket.reservable_type === 'TicketType' ? 'bg-zinc-900 text-white' : 'bg-amber-400 text-black border border-amber-500',
                                'px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest'
                            ]">
                                {{ ticket.reservable_type === 'TicketType' ? 'Billet' : 'Extra' }}
                            </span>
                            <span class="font-mono text-sm font-black text-zinc-400 tracking-widest">{{ ticket.public_id
                                }}</span>
                        </div>

                        <!-- Main Info -->
                        <div class="flex items-center gap-6">
                            <div
                                :class="[ticket.checked_in_at ? 'bg-green-500 text-white shadow-lg shadow-green-500/20' : 'bg-zinc-100 text-zinc-300', 'shrink-0 w-20 h-20 rounded-3xl flex items-center justify-center transition-all']">
                                <UserCheck v-if="ticket.checked_in_at" :size="40" stroke-width="2.5" />
                                <User v-else :size="40" />
                            </div>
                            <div class="min-w-0 flex-1">
                                <h4 class="font-black text-3xl leading-none truncate text-zinc-900 mb-2">{{
                                    ticket.buyer_name }}</h4>
                                <p class="text-zinc-500 font-bold text-lg truncate">{{ ticket.buyer_email }}</p>
                            </div>
                        </div>

                        <!-- Data Grid -->
                        <div class="grid grid-cols-1 gap-3">
                            <div
                                class="flex items-center justify-between bg-zinc-50 p-5 rounded-2xl border border-zinc-100">
                                <span class="text-xs font-black uppercase tracking-widest text-zinc-400">Produit</span>
                                <span class="text-xl font-black text-zinc-900">{{ ticket.reservable_name }}</span>
                            </div>
                            <div v-if="ticket.price_name"
                                class="flex items-center justify-between bg-zinc-50 p-5 rounded-2xl border border-zinc-100">
                                <span class="text-xs font-black uppercase tracking-widest text-zinc-400">Tarif</span>
                                <span class="text-xl font-black text-zinc-900">{{ ticket.price_name }}</span>
                            </div>
                        </div>

                        <!-- Timestamp -->
                        <div v-if="ticket.checked_in_at"
                            class="flex items-center justify-center gap-3 py-4 bg-green-50 rounded-2xl border border-green-100 text-green-700">
                            <Clock :size="20" stroke-width="3" />
                            <span class="text-sm font-black uppercase tracking-widest">Validé à {{ new
                                Date(ticket.checked_in_at).toLocaleTimeString([], { hour: '2-digit', minute:'2-digit'})
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
                        <Search class="absolute left-6 top-1/2 -translate-y-1/2 text-zinc-900 transition-colors"
                            :size="24" />
                        <input v-model="searchQuery" type="text" placeholder="Rechercher id, nom, email, ..."
                            class="w-full bg-white border-4 border-black rounded-[2.5rem] pl-16 pr-6 py-6 text-zinc-900 placeholder:text-zinc-500 focus:outline-none text-xl font-bold" />
                        <button v-if="searchQuery" @click="searchQuery = ''"
                            class="absolute right-6 top-1/2 -translate-y-1/2 text-zinc-500 hover:text-zinc-900">
                            <X :size="24" />
                        </button>
                    </div>
                </div>
            </div>
        </main>

        <!-- Bottom Navigation -->
        <nav class="bg-white border-t border-zinc-100 px-8 pt-3 pb-safe z-50">
            <div class="flex items-center justify-between max-w-lg mx-auto">
                <button @click="activeTab = 'search'"
                    :class="[activeTab === 'search' ? 'text-zinc-900' : 'text-zinc-300']"
                    class="flex flex-col items-center gap-1.5 py-2 flex-1 transition-all">
                    <List :size="24" :stroke-width="activeTab === 'search' ? 3 : 2" />
                    <span class="text-[9px] font-black uppercase tracking-[0.1em]">Liste</span>
                </button>

                <button @click="activeTab = 'scan'"
                    class="mx-8 relative -top-6 bg-zinc-900 text-white p-6 rounded-[2.5rem] shadow-2xl shadow-zinc-900/40 active:scale-90 transition-all border-[8px] border-white">
                    <Scan :size="32" stroke-width="3" />
                </button>

                <button @click="activeTab = 'history'"
                    :class="[activeTab === 'history' ? 'text-zinc-900' : 'text-zinc-300']"
                    class="flex flex-col items-center gap-1.5 py-2 flex-1 transition-all">
                    <Clock :size="24" :stroke-width="activeTab === 'history' ? 3 : 2" />
                    <span class="text-[9px] font-black uppercase tracking-[0.1em]">Historique</span>
                </button>
            </div>
        </nav>

        <!-- FULLSCREEN SCAN RESULT OVERLAY -->
        <Transition enter-active-class="transition duration-400 ease-out" enter-from-class="scale-125 opacity-0"
            enter-to-class="scale-100 opacity-100" leave-active-class="transition duration-200 ease-in"
            leave-from-class="scale-100 opacity-100" leave-to-class="scale-90 opacity-0">
            <div v-if="lastScanResult" class="fixed inset-0 z-[100] flex flex-col p-8 overflow-hidden">
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
                        class="w-full py-7 bg-white text-zinc-900 rounded-[3rem] text-2xl font-black uppercase tracking-[0.1em] shadow-2xl active:scale-95 transition-all">OK,
                        suivant</button>
                    <button v-if="lastScanResult.status === 'already_scanned' && lastScanResult.ticket"
                        @click="toggleTicketStatus(lastScanResult.ticket); lastScanResult = null"
                        class="w-full py-5 bg-black/10 text-white border border-white/10 rounded-3xl text-[10px] font-black uppercase tracking-widest">Annuler
                        la validation</button>
                </div>
            </div>
        </Transition>

        <!-- TICKET DETAIL MODAL -->
        <Transition enter-active-class="transition duration-300 ease-out" enter-from-class="translate-y-full"
            enter-to-class="translate-y-0" leave-active-class="transition duration-200 ease-in"
            leave-from-class="translate-y-0" leave-to-class="translate-y-full">
            <div v-if="selectedTicket" class="fixed inset-0 z-[100] flex flex-col justify-end">
                <div class="absolute inset-0 bg-zinc-900/60 backdrop-blur-sm" @click="selectedTicket = null"></div>
                <div class="relative bg-white rounded-t-[4rem] p-10 pb-16 shadow-2xl flex flex-col max-h-[95vh]">
                    <div class="w-16 h-2 bg-zinc-100 rounded-full mx-auto mb-10"></div>
                    <div class="flex items-start justify-between mb-10">
                        <div class="min-w-0 pr-6">
                            <h2 class="text-4xl font-black text-zinc-900 leading-[0.9] mb-3">{{
                                selectedTicket.buyer_name }}
                            </h2>
                            <p class="text-zinc-400 font-bold text-lg">{{ selectedTicket.buyer_email }}</p>
                        </div>
                        <div
                            :class="[selectedTicket.checked_in_at ? 'bg-green-500 text-white shadow-lg shadow-green-500/20' : 'bg-zinc-50 text-zinc-200', 'p-6 rounded-[2.5rem] transition-all']">
                            <UserCheck v-if="selectedTicket.checked_in_at" :size="40" />
                            <User v-else :size="40" />
                        </div>
                    </div>
                    <div class="grid gap-4 mb-12">
                        <div
                            class="bg-zinc-50 rounded-[2rem] p-8 border border-zinc-100 flex items-center justify-between">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-400 mb-1">Produit
                                </p>
                                <p class="text-2xl font-black">{{ selectedTicket.reservable_name }}</p>
                            </div>
                            <Ticket class="text-zinc-200" :size="32" />
                        </div>
                        <div class="bg-zinc-900 rounded-[2rem] p-8 flex items-center justify-between shadow-xl">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-zinc-500 mb-1">ID Public
                                </p>
                                <p class="font-mono text-2xl font-black text-white tracking-[0.2em]">{{
                                    selectedTicket.public_id
                                    }}</p>
                            </div>
                        </div>
                        <div v-if="selectedTicket.checked_in_at"
                            class="bg-green-50 rounded-[2rem] p-6 flex items-center gap-5 border border-green-100">
                            <div class="bg-green-500 text-white p-3 rounded-2xl">
                                <Check :size="24" stroke-width="4" />
                            </div>
                            <div>
                                <p class="font-black text-green-900 uppercase text-xs tracking-tight">Ticket Validé</p>
                                <p class="text-green-600/60 text-[11px] font-bold">Le {{ new
                                    Date(selectedTicket.checked_in_at).toLocaleString('fr-FR', {
                                        dateStyle: 'medium',
                                        timeStyle:
                                            'short'
                                    }) }}</p>
                            </div>
                        </div>
                    </div>
                    <button @click="toggleTicketStatus(selectedTicket); selectedTicket = null"
                        :class="['w-full py-7 rounded-[2.5rem] text-xl font-black uppercase tracking-widest transition-all active:scale-[0.98] shadow-2xl', selectedTicket.checked_in_at ? 'bg-zinc-100 text-zinc-400' : 'bg-zinc-900 text-white']">
                        {{ selectedTicket.checked_in_at ? 'Annuler Validation' : 'Valider maintenant' }}
                    </button>
                    <button @click="selectedTicket = null"
                        class="mt-6 text-zinc-300 text-[10px] font-black uppercase tracking-widest text-center">Fermer</button>
                </div>
            </div>
        </Transition>
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
