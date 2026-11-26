<script setup lang="ts">
import { router } from '@inertiajs/vue3';
import {
    Search,
    X,
    Check,
    ChevronRight,
    Loader2,
    History,
    User,
    AlertCircle,
    CheckCircle2,
    XCircle,
    Scan,
    List,
    Clock,
    UserCheck,
    Ticket,
    Info,
    TicketCheck,
    PackageCheck,
    Package,
    TicketSlash,
} from '@lucide/vue';
import { Html5Qrcode } from 'html5-qrcode';
import { ref, computed, onUnmounted, nextTick, onMounted, watch } from 'vue';

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
        status:
            | 'success'
            | 'already_scanned'
            | 'invalid'
            | 'unauthorized'
            | 'error'
            | 'cancelled_checkin';
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
const scanDelayActive = ref(false);
const lastScanResult = ref<{
    status:
        | 'success'
        | 'already_scanned'
        | 'invalid'
        | 'unauthorized'
        | 'error'
        | 'cancelled_checkin';
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

const isPopupOpen = computed(
    () => !!lastScanResult.value || !!selectedTicket.value,
);

// Stop/Start scanner based on popup and loading state
watch([isPopupOpen, isLoading], ([isOpen, isLoad], [wasOpen, wasLoad]) => {
    if (isOpen || isLoad) {
        stopScanner();
    } else if (
        !isOpen &&
        !isLoad &&
        (wasOpen || wasLoad) &&
        activeTab.value === 'scan'
    ) {
        // Re-start with a delay after closing popup or finishing load
        scanDelayActive.value = true;
        setTimeout(() => {
            if (
                !isPopupOpen.value &&
                !isLoading.value &&
                activeTab.value === 'scan'
            ) {
                startScanner();
            } else {
                scanDelayActive.value = false;
            }
        }, 750); // Reduced delay: 750ms
    }
});

// Persistence: Load from local storage
onMounted(() => {
    const savedHistory = localStorage.getItem(
        `checkin_history_${props.checkinList.id}`,
    );

    if (savedHistory) {
        try {
            sessionHistory.value = JSON.parse(savedHistory);
        } catch (e) {
            console.error('Failed to parse history', e);
        }
    }

    if (activeTab.value === 'scan') {
        startScanner();
    }
});

// Persistence: Save to local storage when history changes
watch(
    sessionHistory,
    (newHistory) => {
        localStorage.setItem(
            `checkin_history_${props.checkinList.id}`,
            JSON.stringify(newHistory.slice(0, 100)),
        ); // Keep last 100
    },
    { deep: true },
);

// Sync local stats when props update
watch(
    () => props.stats,
    (newStats) => {
        localStats.value = { ...newStats };
    },
    { deep: true },
);

// React to scan results
watch(
    [() => props.scan_result, () => props.flash?.scan_result],
    ([newResult, flashResult]) => {
        const result = newResult || flashResult;

        if (result) {
            lastScanResult.value = {
                ...result,
                timestamp: Date.now(),
            };

            if (result.ticket) {
                // Add to history (unshift to keep recent first)
                sessionHistory.value.unshift({
                    ...result.ticket,
                    action_type: result.status,
                    timestamp: Date.now(),
                });

                // Update search results if ticket is present
                const index = searchResults.value.findIndex(
                    (t) => t.id === result.ticket.id,
                );

                if (index !== -1) {
                    searchResults.value[index] = { ...result.ticket };
                }

                // Update selected ticket if it's the one being modified
                if (
                    selectedTicket.value &&
                    selectedTicket.value.id === result.ticket.id
                ) {
                    selectedTicket.value = { ...result.ticket };
                }
            }
        }
    },
    { immediate: true },
);

// API Search Logic
let searchTimeout: any = null;
watch(searchQuery, (newQuery) => {
    if (searchTimeout) {
        clearTimeout(searchTimeout);
    }

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
        const response = await fetch(
            `${baseUrl}?q=${encodeURIComponent(searchQuery.value)}`,
        );
        const data = await response.json();
        searchResults.value = data;
    } catch (error) {
        console.error('Search error', error);
    } finally {
        isSearching.value = false;
    }
};

const startScanner = async () => {
    if (isScanning.value || isPopupOpen.value || isLoading.value) {
        return;
    }

    await nextTick();
    html5QrCode.value = new Html5Qrcode('reader');
    isScanning.value = true;
    scanDelayActive.value = true; // Block processing while starting

    try {
        await html5QrCode.value.start(
            { facingMode: 'environment' },
            {
                fps: 25,
                qrbox: (viewfinderWidth, viewfinderHeight) => {
                    const minEdge = Math.min(viewfinderWidth, viewfinderHeight);

                    return { width: minEdge * 0.9, height: minEdge * 0.9 };
                },
            },
            (decodedText) => handleScan(decodedText),
            () => {},
        );
        // Reduced extra delay after camera is ready: 250ms
        setTimeout(() => {
            scanDelayActive.value = false;
        }, 250);
    } catch (err) {
        console.error('Scanner error', err);
        isScanning.value = false;
        scanDelayActive.value = false;
    }
};

const stopScanner = async () => {
    if (html5QrCode.value && isScanning.value) {
        try {
            await html5QrCode.value.stop();
        } catch (e) {}

        html5QrCode.value = null;
        isScanning.value = false;
    }
};

const handleScan = (publicId: string) => {
    if (isLoading.value || isPopupOpen.value || scanDelayActive.value) {
        return;
    }

    if (
        lastScanResult.value &&
        lastScanResult.value.ticket?.public_id === publicId &&
        Date.now() - lastScanResult.value.timestamp < 2000
    ) {
        return;
    }

    isLoading.value = true;
    const scanUrl = props.isPublic
        ? `/checkin/${props.checkinList.public_url_token}/scan`
        : `/checkin/list/${props.checkinList.id}/scan`;
    router.post(
        scanUrl,
        { public_id: publicId },
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                isLoading.value = false;
            },
        },
    );
};

const ticketUrl = (ticketId: number, action: 'toggle' | 'status') =>
    props.isPublic
        ? `/checkin/${props.checkinList.public_url_token}/tickets/${ticketId}/${action}`
        : `/checkin/list/${props.checkinList.id}/tickets/${ticketId}/${action}`;

const toggleTicketStatus = (ticket: any) => {
    isLoading.value = true;
    router.post(
        ticketUrl(ticket.id, 'toggle'),
        {},
        {
            preserveState: true,
            preserveScroll: true,
            onFinish: () => {
                isLoading.value = false;
            },
        },
    );
};

const openTicketDetails = async (ticket: any) => {
    selectedTicket.value = { ...ticket };
    isModalLoading.value = true;

    // Fetch fresh status to ensure we show the latest state
    try {
        const response = await fetch(ticketUrl(ticket.id, 'status'), {
            headers: { Accept: 'application/json' },
        });

        if (!response.ok) {
            throw new Error(`HTTP ${response.status}`);
        }

        const data = await response.json();

        if (selectedTicket.value && selectedTicket.value.id === ticket.id) {
            selectedTicket.value = data;

            // Also update in search results if present
            const index = searchResults.value.findIndex(
                (t) => t.id === ticket.id,
            );

            if (index !== -1) {
                searchResults.value[index] = { ...data };
            }
        }
    } catch (e) {
        console.error('Failed to fetch fresh ticket status', e);
    } finally {
        isModalLoading.value = false;
    }
};

watch(activeTab, (newTab) => {
    if (newTab === 'scan' && !isPopupOpen.value && !isLoading.value) {
        startScanner();
    } else {
        stopScanner();
    }
});

onMounted(() => {
    if (activeTab.value === 'scan') {
        startScanner();
    }
});
onUnmounted(() => stopScanner());

const getStatusColor = (status: string) => {
    switch (status) {
        case 'success':
            return 'bg-green-500';
        case 'already_scanned':
            return 'bg-amber-500';
        case 'invalid':
            return 'bg-red-500';
        case 'cancelled_checkin':
            return 'bg-red-500';
        case 'unauthorized':
            return 'bg-red-600';
        default:
            return 'bg-neutral-500';
    }
};

const getStatusIcon = (status: string) => {
    switch (status) {
        case 'success':
            return CheckCircle2;
        case 'already_scanned':
            return AlertCircle;
        case 'invalid':
            return XCircle;
        case 'cancelled_checkin':
            return X;
        case 'unauthorized':
            return AlertCircle;
        default:
            return Info;
    }
};

const getActionLabel = (status: string) => {
    switch (status) {
        case 'success':
            return 'Validé';
        case 'already_scanned':
            return 'Déjà scanné';
        case 'invalid':
            return 'Invalide';
        case 'cancelled_checkin':
            return 'Annulé';
        case 'unauthorized':
            return 'Non autorisé';
        default:
            return status;
    }
};

const scanPercentage = computed(() => {
    if (localStats.value.total === 0) {
        return 0;
    }

    return Math.round(
        (localStats.value.scanned / localStats.value.total) * 100,
    );
});
</script>

<template>
    <div
        class="fixed inset-0 flex flex-col overflow-hidden bg-white font-sans text-neutral-900 antialiased"
    >
        <!-- HEADER : Compact Brutalist Horizontal Dashboard -->
        <header
            v-show="activeTab === 'scan'"
            class="safe-top z-50 shrink-0 border-b-4 border-black bg-white"
        >
            <div class="flex flex-col gap-3 px-6 py-4">
                <div class="flex items-end justify-between gap-4">
                    <div class="min-w-0">
                        <p
                            class="mb-0.5 text-[8px] font-black tracking-[0.3em] text-neutral-400 uppercase"
                        >
                            {{ props.checkinList.event_title }}
                        </p>
                        <h1
                            class="truncate font-chillax text-2xl leading-none font-black text-black uppercase"
                        >
                            {{ props.checkinList.name }}
                        </h1>
                    </div>
                    <div class="flex shrink-0 flex-col items-end">
                        <span
                            class="text-2xl leading-none font-black text-black"
                            >{{ scanPercentage }}%</span
                        >
                    </div>
                </div>

                <!-- Progress Bar Container -->
                <div class="flex flex-col gap-1.5">
                    <div
                        class="h-6 w-full overflow-hidden rounded-xl border-2 border-black bg-neutral-100 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]"
                    >
                        <div
                            class="h-full border-r-2 border-black bg-emerald-500 transition-all duration-1000 ease-out"
                            :style="{ width: `${scanPercentage}%` }"
                        ></div>
                    </div>
                    <!-- Label Below Bar -->
                    <div class="text-center">
                        <span
                            class="text-[8px] font-black tracking-[0.2em] text-neutral-400 uppercase"
                        >
                            {{ localStats.scanned }} /
                            {{ localStats.total }} tickets scannés
                        </span>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="relative flex-1 overflow-hidden bg-black">
            <!-- Scanner Tab -->
            <div
                v-show="activeTab === 'scan'"
                class="absolute inset-0 flex flex-col bg-zinc-900"
            >
                <div id="reader" class="flex-1"></div>
            </div>

            <!-- History Tab -->
            <div
                v-show="activeTab === 'history'"
                class="absolute inset-0 flex flex-col overflow-y-auto bg-zinc-800"
            >
                <!-- Header Section -->
                <div class="border-b-2 border-black bg-zinc-900 px-6 py-6">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <h2
                                class="font-chillax text-2xl font-black tracking-tight text-white uppercase"
                            >
                                Historique
                            </h2>
                            <p
                                class="mt-0.5 text-[8px] font-bold tracking-widest text-zinc-500 uppercase"
                            >
                                Actions récentes de la session
                            </p>
                        </div>
                        <button
                            v-if="sessionHistory.length > 0"
                            @click="sessionHistory = []"
                            class="rounded-xl border-2 border-black bg-red-600 px-4 py-2 text-[10px] font-black tracking-widest text-white uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all active:translate-x-0.5 active:translate-y-0.5 active:shadow-none"
                        >
                            Effacer
                        </button>
                    </div>
                </div>

                <!-- History List -->
                <div class="flex-1">
                    <div
                        v-for="(item, index) in sessionHistory"
                        :key="index"
                        @click="openTicketDetails(item)"
                        class="flex items-center gap-4 border-b border-black bg-white p-4 transition-all odd:bg-neutral-100 active:scale-[0.98]"
                    >
                        <!-- Status Icon Container -->
                        <div
                            :class="[
                                getStatusColor(item.action_type),
                                'flex h-12 w-12 shrink-0 items-center justify-center rounded-xl border-2 border-black text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]',
                            ]"
                        >
                            <component
                                :is="getStatusIcon(item.action_type)"
                                :size="20"
                                stroke-width="3"
                            />
                        </div>

                        <!-- Content -->
                        <div class="min-w-0 flex-1">
                            <div
                                class="mb-0.5 flex items-center justify-between gap-2"
                            >
                                <h4
                                    class="truncate text-lg leading-none font-black text-neutral-900"
                                >
                                    {{ item.buyer_name }}
                                </h4>
                                <span
                                    class="shrink-0 font-mono text-[10px] font-black text-neutral-400"
                                >
                                    {{
                                        new Date(
                                            item.timestamp,
                                        ).toLocaleTimeString([], {
                                            hour: '2-digit',
                                            minute: '2-digit',
                                        })
                                    }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span
                                    :class="[
                                        getStatusColor(item.action_type),
                                        'rounded border border-black/10 px-1.5 py-0.5 text-[8px] font-black tracking-widest text-white uppercase',
                                    ]"
                                >
                                    {{ getActionLabel(item.action_type) }}
                                </span>
                                <p
                                    class="truncate text-xs font-bold text-neutral-500"
                                >
                                    {{ item.reservable_name }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Empty State -->
                    <div
                        v-if="sessionHistory.length === 0"
                        class="flex flex-col items-center justify-center py-32 text-center"
                    >
                        <div
                            class="mb-6 flex h-24 w-24 items-center justify-center rounded-[2.5rem] border-4 border-zinc-800 bg-zinc-900 shadow-2xl"
                        >
                            <Clock class="text-zinc-600" :size="40" />
                        </div>
                        <p
                            class="mb-2 text-xl font-black tracking-widest text-white uppercase"
                        >
                            Aucun scan récent
                        </p>
                        <p
                            class="mx-auto max-w-62.5 text-lg font-bold text-zinc-500"
                        >
                            Les billets scannés ou modifiés apparaîtront ici.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Search Tab : Redesigned List with Floating Bottom Input -->
            <div
                v-show="activeTab === 'search'"
                class="absolute inset-0 flex flex-col overflow-hidden bg-zinc-800"
            >
                <!-- Header Section (Matches History) -->
                <div
                    class="shrink-0 border-b-2 border-black bg-zinc-900 px-6 py-6"
                >
                    <div class="flex flex-col">
                        <h2
                            class="font-chillax text-2xl font-black tracking-tight text-white uppercase"
                        >
                            Recherche
                        </h2>
                        <p
                            class="mt-0.5 text-[8px] font-bold tracking-widest text-zinc-500 uppercase"
                        >
                            Trouver par nom, email ou ID
                        </p>
                    </div>
                </div>

                <div class="custom-scrollbar flex-1 overflow-y-auto p-0 pb-48">
                    <div
                        v-if="isSearching"
                        class="flex flex-col items-center justify-center py-12 text-white"
                    >
                        <Loader2 class="mb-4 animate-spin" :size="32" />
                        <p class="text-sm font-black tracking-widest uppercase">
                            Recherche en cours...
                        </p>
                    </div>

                    <div
                        v-else-if="!searchQuery"
                        class="py-12 text-center text-white"
                    >
                        <div
                            class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl border-2 border-zinc-800 bg-zinc-900 shadow-xl"
                        >
                            <Search class="text-zinc-600" :size="28" />
                        </div>
                        <p
                            class="mb-1 text-lg font-black tracking-widest text-white uppercase"
                        >
                            Prêt à scanner ?
                        </p>
                        <p
                            class="mx-auto max-w-50 text-sm font-bold text-zinc-500"
                        >
                            Tapez un nom ou un identifiant.
                        </p>
                    </div>

                    <template v-else>
                        <div
                            v-for="ticket in searchResults"
                            :key="ticket.id"
                            @click="openTicketDetails(ticket)"
                            class="flex flex-col gap-4 border-b-2 border-black bg-white p-5 transition-all odd:bg-neutral-100 active:scale-[0.98]"
                        >
                            <!-- Main Info -->
                            <div class="flex items-center gap-4">
                                <div
                                    :class="[
                                        ticket.checked_in_at
                                            ? 'bg-green-500 shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]'
                                            : 'bg-neutral-100',
                                        'flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl border-2 border-black transition-all',
                                    ]"
                                >
                                    <template
                                        v-if="
                                            ticket.reservable_type ===
                                            'TicketType'
                                        "
                                    >
                                        <TicketCheck
                                            v-if="ticket.checked_in_at"
                                            class="text-white"
                                            :size="28"
                                            stroke-width="2.5"
                                        />
                                        <Ticket
                                            v-else
                                            class="text-neutral-400"
                                            :size="28"
                                        />
                                    </template>
                                    <template v-else>
                                        <PackageCheck
                                            v-if="ticket.checked_in_at"
                                            class="text-white"
                                            :size="28"
                                            stroke-width="2.5"
                                        />
                                        <Package
                                            v-else
                                            class="text-neutral-400"
                                            :size="28"
                                        />
                                    </template>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <h4
                                        class="mb-1 truncate text-xl leading-none font-black text-neutral-900"
                                    >
                                        {{ ticket.buyer_name }}
                                    </h4>
                                    <p
                                        class="truncate text-sm font-bold text-neutral-500"
                                    >
                                        {{ ticket.buyer_email }}
                                    </p>
                                </div>
                            </div>

                            <div class="flex flex-col gap-3">
                                <div
                                    class="flex items-center justify-between gap-3 rounded-xl border border-black/5 bg-neutral-900/5 p-3"
                                >
                                    <div class="flex flex-col">
                                        <span
                                            class="text-sm leading-tight font-black text-neutral-900"
                                            >{{ ticket.reservable_name }}</span
                                        >
                                    </div>
                                    <div
                                        v-if="ticket.price_name"
                                        class="flex flex-col items-end"
                                    >
                                        <span
                                            class="rounded border border-black bg-white px-2 py-0.5 text-[8px] font-black tracking-widest text-neutral-700 uppercase shadow-[1px_1px_0px_0px_rgba(0,0,0,1)]"
                                        >
                                            {{ ticket.price_name }}
                                        </span>
                                    </div>
                                </div>

                                <div
                                    class="w-full rounded-xl border-2 border-black bg-neutral-900 py-2 text-center shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]"
                                >
                                    <span
                                        class="font-mono text-sm font-black tracking-widest text-white"
                                    >
                                        {{ ticket.public_id }}
                                    </span>
                                </div>

                                <!-- Timestamp -->
                                <div
                                    v-if="ticket.checked_in_at"
                                    class="flex items-center justify-center gap-2 rounded-xl border-2 border-black bg-green-500 py-2 text-white shadow-[2px_2px_0px_0px_rgba(0,0,0,1)]"
                                >
                                    <Clock :size="16" stroke-width="3" />
                                    <span
                                        class="text-[10px] font-black tracking-widest uppercase"
                                        >Validé à
                                        {{
                                            new Date(
                                                ticket.checked_in_at,
                                            ).toLocaleTimeString([], {
                                                hour: '2-digit',
                                                minute: '2-digit',
                                            })
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>

                        <div
                            v-if="searchQuery && searchResults.length === 0"
                            class="flex flex-col items-center py-20 text-center text-white"
                        >
                            <div
                                class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-2xl border-2 border-zinc-800 bg-zinc-900 shadow-xl"
                            >
                                <TicketSlash class="text-zinc-600" :size="28" />
                            </div>
                            <p
                                class="mb-1 text-lg font-black tracking-widest text-white uppercase"
                            >
                                Aucun ticket
                            </p>
                            <p
                                class="mx-auto max-w-50 text-sm font-bold text-zinc-500"
                            >
                                Essayez avec un autre nom ou email.
                            </p>
                        </div>
                    </template>
                </div>

                <!-- Floating Bottom Input -->
                <div
                    class="absolute right-0 bottom-0 left-0 z-20 rounded-t-lg bg-linear-to-b from-transparent via-black/60 to-black p-4 py-8"
                >
                    <div class="group relative">
                        <Search
                            class="absolute top-1/2 left-5 -translate-y-1/2 text-neutral-900 transition-colors"
                            :size="20"
                        />
                        <input
                            v-model="searchQuery"
                            type="text"
                            placeholder="Rechercher..."
                            class="w-full rounded-2xl border-2 border-black bg-white py-4 pr-10 pl-12 text-lg font-bold text-neutral-900 placeholder:text-neutral-500 focus:outline-none"
                        />
                        <button
                            v-if="searchQuery"
                            @click="searchQuery = ''"
                            class="absolute top-1/2 right-5 -translate-y-1/2 text-neutral-500 hover:text-neutral-900"
                        >
                            <X :size="20" />
                        </button>
                    </div>
                </div>
            </div>
        </main>

        <!-- Bottom Navigation -->
        <nav class="pb-safe z-50 border-t-4 border-black bg-zinc-900 px-4 pt-2">
            <div class="mx-auto flex max-w-lg items-center justify-between">
                <button
                    @click="activeTab = 'search'"
                    :class="[
                        activeTab === 'search' ? 'text-white' : 'text-zinc-500',
                    ]"
                    class="group flex flex-1 flex-col items-center gap-1 py-1 transition-all"
                >
                    <div
                        :class="[
                            activeTab === 'search'
                                ? 'border-black bg-white text-black shadow-[2px_2px_0px_0px_rgba(255,255,255,0.2)]'
                                : 'border-transparent bg-transparent text-zinc-500',
                            'rounded-xl border-2 p-2 transition-all group-active:scale-90',
                        ]"
                    >
                        <List
                            :size="20"
                            :stroke-width="activeTab === 'search' ? 4 : 2"
                        />
                    </div>
                    <span
                        class="text-[8px] font-black tracking-widest uppercase"
                        >Liste</span
                    >
                </button>

                <button
                    @click="activeTab = 'scan'"
                    class="relative -top-6 mx-6 rounded-2xl border-4 border-black bg-emerald-500 p-6 text-black shadow-[6px_6px_0px_0px_rgba(0,0,0,1)] transition-all active:translate-x-1 active:translate-y-1 active:shadow-none"
                >
                    <Scan :size="32" stroke-width="4" />
                </button>

                <button
                    @click="activeTab = 'history'"
                    :class="[
                        activeTab === 'history'
                            ? 'text-white'
                            : 'text-zinc-500',
                    ]"
                    class="group flex flex-1 flex-col items-center gap-1 py-1 transition-all"
                >
                    <div
                        :class="[
                            activeTab === 'history'
                                ? 'border-black bg-white text-black shadow-[2px_2px_0px_0px_rgba(255,255,255,0.2)]'
                                : 'border-transparent bg-transparent text-zinc-500',
                            'rounded-xl border-2 p-2 transition-all group-active:scale-90',
                        ]"
                    >
                        <Clock
                            :size="20"
                            :stroke-width="activeTab === 'history' ? 4 : 2"
                        />
                    </div>
                    <span
                        class="text-[8px] font-black tracking-widest uppercase"
                        >Historique</span
                    >
                </button>
            </div>
        </nav>

        <!-- FULLSCREEN SCAN RESULT OVERLAY -->
        <Transition
            enter-active-class="transition duration-400 ease-out"
            enter-from-class="scale-110 opacity-0"
            enter-to-class="scale-100 opacity-100"
            leave-active-class="transition duration-200 ease-in"
            leave-from-class="scale-100 opacity-100"
            leave-to-class="scale-95 opacity-0"
        >
            <div
                v-if="lastScanResult"
                :class="[
                    getStatusColor(lastScanResult.status),
                    'fixed inset-0 z-150 flex flex-col overflow-hidden p-6 transition-colors duration-500',
                ]"
            >
                <div
                    class="flex flex-1 flex-col items-center justify-center text-center"
                >
                    <!-- Status Icon -->
                    <div
                        class="mb-6 rounded-4xl border-2 border-white bg-white/20 p-6 text-white shadow-[4px_4px_0px_0px_rgba(0,0,0,0.2)] backdrop-blur-md"
                    >
                        <component
                            :is="getStatusIcon(lastScanResult.status)"
                            :size="80"
                            stroke-width="4"
                        />
                    </div>

                    <!-- Message -->
                    <h2
                        class="mb-6 text-3xl leading-none font-black tracking-tight text-white uppercase drop-shadow-lg"
                    >
                        {{ lastScanResult.message }}
                    </h2>

                    <!-- Ticket Details -->
                    <template v-if="lastScanResult.ticket">
                        <p
                            class="mb-2 w-full truncate text-4xl leading-none font-black text-white drop-shadow-md"
                        >
                            {{ lastScanResult.ticket.buyer_name }}
                        </p>
                        <p
                            class="mb-8 text-lg font-black tracking-widest text-white/70 uppercase"
                        >
                            {{ lastScanResult.ticket.reservable_name }}
                        </p>

                        <div
                            class="rounded-2xl border-2 border-black bg-white px-8 py-4 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]"
                        >
                            <span
                                class="font-mono text-xl font-black tracking-[0.2em] text-black"
                                >{{ lastScanResult.ticket.public_id }}</span
                            >
                        </div>
                    </template>
                </div>

                <!-- Actions -->
                <div class="mt-auto space-y-4">
                    <button
                        @click="lastScanResult = null"
                        class="w-full rounded-2xl border-2 border-black bg-white py-6 text-2xl font-black tracking-widest text-black uppercase shadow-[4px_4px_0px_0px_rgba(0,0,0,1)] transition-all active:translate-x-1 active:translate-y-1 active:shadow-none"
                    >
                        Suivant
                    </button>

                    <button
                        v-if="
                            lastScanResult.status === 'already_scanned' &&
                            lastScanResult.ticket
                        "
                        @click="
                            toggleTicketStatus(lastScanResult.ticket);
                            lastScanResult = null;
                        "
                        class="w-full rounded-xl border-2 border-white bg-black/20 py-4 text-xs font-black tracking-widest text-white uppercase shadow-[2px_2px_0px_0px_rgba(0,0,0,0.1)] transition-all active:translate-x-0.5 active:translate-y-0.5 active:shadow-none"
                    >
                        Annuler la validation
                    </button>
                </div>
            </div>
        </Transition>

        <!-- TICKET DETAIL MODAL -->
        <div
            v-if="selectedTicket"
            class="fixed inset-0 z-100 flex flex-col justify-end"
        >
            <!-- Backdrop: Fades in/out independently -->
            <Transition
                enter-active-class="transition duration-300 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-200 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
                appear
            >
                <div
                    class="absolute inset-0 bg-neutral-900/80 backdrop-blur-md"
                    @click="selectedTicket = null"
                ></div>
            </Transition>

            <!-- Content: Slides up/down -->
            <Transition
                enter-active-class="transition duration-400 cubic-bezier(0.16, 1, 0.3, 1)"
                enter-from-class="translate-y-full"
                enter-to-class="translate-y-0"
                leave-active-class="transition duration-300 ease-in"
                leave-from-class="translate-y-0"
                leave-to-class="translate-y-full"
                appear
            >
                <div
                    class="relative flex max-h-[92vh] flex-col overflow-hidden rounded-t-3xl border-t-4 border-black bg-white shadow-2xl"
                >
                    <!-- Header with Close -->
                    <div
                        class="flex items-center justify-between border-b-2 border-black bg-zinc-900 px-6 py-5"
                    >
                        <div class="flex flex-col">
                            <h2
                                class="font-chillax text-2xl font-black tracking-tight text-white uppercase"
                            >
                                Détails
                            </h2>
                            <p
                                class="mt-0.5 text-[8px] font-bold tracking-widest text-zinc-500 uppercase"
                            >
                                Informations du ticket
                            </p>
                        </div>
                        <button
                            @click="selectedTicket = null"
                            class="flex h-10 w-10 items-center justify-center rounded-xl border-2 border-black bg-white text-black shadow-[2px_2px_0px_0px_rgba(0,0,0,1)] transition-all active:translate-x-0.5 active:translate-y-0.5 active:shadow-none"
                        >
                            <X :size="20" stroke-width="4" />
                        </button>
                    </div>

                    <div class="custom-scrollbar flex-1 overflow-y-auto p-6">
                        <div
                            v-if="isModalLoading"
                            class="flex flex-col items-center justify-center py-12"
                        >
                            <Loader2
                                class="mb-2 animate-spin text-neutral-900"
                                :size="32"
                            />
                            <p
                                class="text-sm font-black tracking-widest text-neutral-900 uppercase"
                            >
                                Mise à jour...
                            </p>
                        </div>

                        <template v-else>
                            <div
                                class="mt-2 mb-8 flex items-start justify-between"
                            >
                                <div class="min-w-0 pr-4">
                                    <h2
                                        class="mb-2 text-3xl leading-[0.9] font-black wrap-break-word text-neutral-900"
                                    >
                                        {{ selectedTicket.buyer_name }}
                                    </h2>
                                    <p
                                        class="text-sm font-bold break-all text-neutral-500"
                                    >
                                        {{ selectedTicket.buyer_email }}
                                    </p>
                                </div>
                                <div
                                    :class="[
                                        selectedTicket.checked_in_at
                                            ? 'bg-green-500 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]'
                                            : 'bg-neutral-900 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]',
                                        'shrink-0 rounded-2xl border-2 border-black p-5 transition-all',
                                    ]"
                                >
                                    <template
                                        v-if="
                                            selectedTicket.reservable_type ===
                                            'TicketType'
                                        "
                                    >
                                        <TicketCheck
                                            v-if="selectedTicket.checked_in_at"
                                            class="text-white"
                                            :size="32"
                                            stroke-width="3"
                                        />
                                        <Ticket
                                            v-else
                                            class="text-white"
                                            :size="32"
                                        />
                                    </template>
                                    <template v-else>
                                        <PackageCheck
                                            v-if="selectedTicket.checked_in_at"
                                            class="text-white"
                                            :size="32"
                                            stroke-width="3"
                                        />
                                        <Package
                                            v-else
                                            class="text-white"
                                            :size="32"
                                        />
                                    </template>
                                </div>
                            </div>

                            <div class="mb-8 grid gap-4">
                                <div
                                    class="flex flex-col gap-3 rounded-2xl border-2 border-black bg-neutral-100 p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]"
                                >
                                    <div
                                        class="flex items-center justify-between"
                                    >
                                        <div>
                                            <p
                                                class="mb-0.5 text-[8px] font-black tracking-[0.2em] text-neutral-400 uppercase"
                                            >
                                                {{
                                                    selectedTicket.reservable_type ===
                                                    'TicketType'
                                                        ? 'Type de Billet'
                                                        : 'Supplément Extra'
                                                }}
                                            </p>
                                            <p
                                                class="text-xl leading-tight font-black text-neutral-900 uppercase"
                                            >
                                                {{
                                                    selectedTicket.reservable_name
                                                }}
                                            </p>
                                        </div>
                                        <div
                                            class="rounded-lg border border-black bg-white p-2"
                                        >
                                            <Ticket
                                                class="text-neutral-900"
                                                :size="20"
                                                stroke-width="3"
                                            />
                                        </div>
                                    </div>

                                    <div
                                        v-if="selectedTicket.price_name"
                                        class="flex items-center justify-between border-t border-black/5 pt-4"
                                    >
                                        <div>
                                            <p
                                                class="mb-0.5 text-[8px] font-black tracking-[0.2em] text-neutral-400 uppercase"
                                            >
                                                Tarif appliqué
                                            </p>
                                            <p
                                                class="text-sm font-black text-neutral-700 uppercase"
                                            >
                                                {{ selectedTicket.price_name }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <div
                                    class="flex flex-col gap-0.5 rounded-2xl border-2 border-black bg-neutral-900 p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]"
                                >
                                    <p
                                        class="text-[8px] font-black tracking-[0.3em] text-zinc-500 uppercase"
                                    >
                                        Identifiant Unique
                                    </p>
                                    <p
                                        class="font-mono text-xl font-black tracking-widest text-white"
                                    >
                                        {{ selectedTicket.public_id }}
                                    </p>
                                </div>

                                <div
                                    v-if="selectedTicket.checked_in_at"
                                    class="flex items-center gap-4 rounded-2xl border-2 border-black bg-green-500 p-6 shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]"
                                >
                                    <div
                                        class="rounded-xl border border-black bg-white p-3 text-green-600"
                                    >
                                        <Check :size="24" stroke-width="4" />
                                    </div>
                                    <div class="min-w-0">
                                        <p
                                            class="text-sm font-black tracking-tight text-white uppercase"
                                        >
                                            Entrée Validée
                                        </p>
                                        <p
                                            class="text-xs font-bold text-green-100"
                                        >
                                            {{
                                                new Date(
                                                    selectedTicket.checked_in_at,
                                                ).toLocaleDateString('fr-FR', {
                                                    day: 'numeric',
                                                    month: 'long',
                                                })
                                            }}
                                            à
                                            {{
                                                new Date(
                                                    selectedTicket.checked_in_at,
                                                ).toLocaleTimeString('fr-FR', {
                                                    hour: '2-digit',
                                                    minute: '2-digit',
                                                })
                                            }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="border-t-2 border-black bg-zinc-50 p-6">
                        <button
                            @click="
                                toggleTicketStatus(selectedTicket);
                                selectedTicket = null;
                            "
                            :disabled="isModalLoading"
                            :class="[
                                'flex w-full items-center justify-center gap-3 rounded-2xl border-2 border-black py-5 text-lg font-black tracking-widest uppercase transition-all active:translate-x-1 active:translate-y-1 active:shadow-none',
                                selectedTicket?.checked_in_at
                                    ? 'bg-red-500 text-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]'
                                    : 'bg-emerald-500 text-white shadow-[4px_4px_0px_0px_rgba(0,0,0,1)]',
                                isModalLoading
                                    ? 'cursor-not-allowed opacity-50 grayscale'
                                    : '',
                            ]"
                        >
                            <template v-if="!isModalLoading">
                                <UserCheck
                                    v-if="!selectedTicket?.checked_in_at"
                                    :size="24"
                                    stroke-width="3"
                                />
                                <History v-else :size="24" stroke-width="3" />
                                {{
                                    selectedTicket?.checked_in_at
                                        ? "Annuler l'Entrée"
                                        : "Valider l'Entrée"
                                }}
                            </template>
                            <Loader2 v-else class="animate-spin" :size="24" />
                        </button>
                    </div>
                </div>
            </Transition>
        </div>

        <!-- Global Loading -->
        <div
            v-if="isLoading"
            class="fixed inset-0 z-200 flex items-center justify-center bg-white/40 backdrop-blur-xs"
        >
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
