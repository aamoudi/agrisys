<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, computed } from 'vue';
import { usePage } from '@inertiajs/vue3';


// Inside your script setup block in Crops/Index.vue:
watch(() => usePage().props.flash?.success, (message) => {
    if (message) {
        alert(message); // Fires a clean success notification upon entry creation!
    }
}, { immediate: true });

const props = defineProps({
    crops: Object,
    farms: Array,
    varieties: Array,
    statuses: Array,
    types: Array,
    filters: Object
});

// ── Filter state ─────────────────────────────────────────────────
const searchForm = ref({
    search: props.filters?.search || '',
    farm_id: props.filters?.farm_id || '',
    variety_id: props.filters?.variety_id || '',
    type: props.filters?.type || '',
    status: props.filters?.status || '',
    start_date: props.filters?.start_date || '',
    end_date: props.filters?.end_date || ''
});

// Searchable custom dropdowns
const uiDropdowns = ref({ farm: false, variety: false });
const searchQueries = ref({ farm: '', variety: '' });

const filteredFarmsOpt = computed(() =>
    props.farms.filter(f =>
        f.name.toLowerCase().includes(searchQueries.value.farm.toLowerCase())
    )
);
const filteredVarietiesOpt = computed(() =>
    props.varieties.filter(v =>
        v.name.toLowerCase().includes(searchQueries.value.variety.toLowerCase())
    )
);

// Labels for selected values shown inside the dropdown trigger
const selectedFarmLabel = computed(() => {
    const found = props.farms.find(f => f.id == searchForm.value.farm_id);
    return found ? found.name : 'All Farms';
});
const selectedVarietyLabel = computed(() => {
    const found = props.varieties.find(v => v.id == searchForm.value.variety_id);
    return found ? found.name : 'All Varieties';
});

// Debounce helper
const debounce = (fn, delay) => {
    let t;
    return (...args) => { clearTimeout(t); t = setTimeout(() => fn(...args), delay); };
};

const executeFilterQuery = debounce(() => {
    router.get(route('crops.index'), searchForm.value, {
        preserveState: true,
        preserveScroll: true,
        replace: true
    });
}, 300);

watch(() => searchForm.value, () => executeFilterQuery(), { deep: true });

const clearAllFilters = () => {
    searchForm.value = { search: '', farm_id: '', variety_id: '', type: '', status: '', start_date: '', end_date: '' };
    searchQueries.value = { farm: '', variety: '' };
};

// ── Details modal ─────────────────────────────────────────────────
const isDetailsModalOpen = ref(false);
const selectedCrop = ref(null);

const openDetailsModal = (crop) => { selectedCrop.value = crop; isDetailsModalOpen.value = true; };
const closeDetailsModal = () => { selectedCrop.value = null; isDetailsModalOpen.value = false; };

// ── Delete modal ──────────────────────────────────────────────────
const isDeleteModalOpen = ref(false);
const cropToDelete = ref(null);

const confirmDelete = (crop) => { cropToDelete.value = crop; isDeleteModalOpen.value = true; };
const closeDeleteModal = () => { cropToDelete.value = null; isDeleteModalOpen.value = false; };

const executeDelete = () => {
    if (!cropToDelete.value) return;
    const name = cropToDelete.value.name;
    router.delete(route('crops.destroy', cropToDelete.value.id), {
        onSuccess: () => {
            closeDeleteModal();
            alert(`The crop "${name}" deleted successfully!`);
        }
    });
};
</script>

<template>

    <Head title="Crops Ecosystem" />

    <AuthenticatedLayout>

        <!-- ── Page header ── -->
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Crops Ecosystem
                </h2>
                <Link :href="route('crops.create')"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                    + Register New Crop
                </Link>
            </div>
        </template>

        <!-- ── Main content — matches farm py-12 wrapper exactly ── -->
        <div class="py-12 relative z-0">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <!-- ══════════════════════════════════════════════
                     FILTER PANEL
                     Row 1: Search | Farm (custom dropdown) | Variety (custom dropdown)
                     Row 2: Type   | Status                 | Date range (from – to)
                ══════════════════════════════════════════════ -->
                <div class="bg-white shadow-sm sm:rounded-lg p-5 mb-6 border border-gray-200 overflow-visible">

                    <!-- Panel header — label + clear button -->
                    <div class="flex items-center justify-between mb-4">
                        <span class="text-sm font-medium text-gray-700 flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                            </svg>
                            Filter Crops
                        </span>

                        <!-- Clear button — same size/style as Farm's Edit/Delete buttons -->
                        <button @click="clearAllFilters"
                            class="inline-flex items-center px-4 py-2 bg-red-50 text-red-700 rounded-md hover:bg-red-100 font-medium text-sm transition-colors">
                            Clear Filters
                        </button>
                    </div>

                    <!-- Row 1: Search · Farm · Variety -->
                    <div class="grid grid-cols-3 gap-4 mb-4">

                        <!-- Search -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Search</label>
                            <input v-model="searchForm.search" type="text" placeholder="Crop name..."
                                class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500" />
                        </div>

                        <!-- Farm custom searchable dropdown -->
                        <div class="relative">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Farm</label>
                            <div @click="uiDropdowns.farm = !uiDropdowns.farm; uiDropdowns.variety = false"
                                class="w-full text-sm bg-white border border-gray-300 rounded-md py-2 px-3 flex justify-between items-center cursor-pointer shadow-sm">
                                <span :class="searchForm.farm_id ? 'text-gray-900' : 'text-gray-400'">
                                    {{ selectedFarmLabel }}
                                </span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <div v-if="uiDropdowns.farm"
                                class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg">
                                <div class="p-2">
                                    <input v-model="searchQueries.farm" type="text" placeholder="Search farms..."
                                        class="w-full text-xs rounded border-gray-300 focus:ring-emerald-500 focus:border-emerald-500" />
                                </div>
                                <ul class="max-h-44 overflow-y-auto divide-y divide-gray-50 text-sm">
                                    <li @click="searchForm.farm_id = ''; uiDropdowns.farm = false"
                                        class="px-3 py-2 cursor-pointer hover:bg-gray-50 text-gray-400 italic">
                                        All Farms
                                    </li>
                                    <li v-for="f in filteredFarmsOpt" :key="f.id"
                                        @click="searchForm.farm_id = f.id; uiDropdowns.farm = false"
                                        class="px-3 py-2 cursor-pointer hover:bg-emerald-50 hover:text-emerald-700"
                                        :class="{ 'bg-emerald-50 text-emerald-700 font-medium': searchForm.farm_id == f.id }">
                                        {{ f.name }}
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- Variety custom searchable dropdown -->
                        <div class="relative">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Variety</label>
                            <div @click="uiDropdowns.variety = !uiDropdowns.variety; uiDropdowns.farm = false"
                                class="w-full text-sm bg-white border border-gray-300 rounded-md py-2 px-3 flex justify-between items-center cursor-pointer shadow-sm">
                                <span :class="searchForm.variety_id ? 'text-gray-900' : 'text-gray-400'">
                                    {{ selectedVarietyLabel }}
                                </span>
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                            <div v-if="uiDropdowns.variety"
                                class="absolute z-20 mt-1 w-full bg-white border border-gray-200 rounded-md shadow-lg">
                                <div class="p-2">
                                    <input v-model="searchQueries.variety" type="text" placeholder="Search varieties..."
                                        class="w-full text-xs rounded border-gray-300 focus:ring-emerald-500 focus:border-emerald-500" />
                                </div>
                                <ul class="max-h-44 overflow-y-auto divide-y divide-gray-50 text-sm">
                                    <li @click="searchForm.variety_id = ''; uiDropdowns.variety = false"
                                        class="px-3 py-2 cursor-pointer hover:bg-gray-50 text-gray-400 italic">
                                        All Varieties
                                    </li>
                                    <li v-for="v in filteredVarietiesOpt" :key="v.id"
                                        @click="searchForm.variety_id = v.id; uiDropdowns.variety = false"
                                        class="px-3 py-2 cursor-pointer hover:bg-emerald-50 hover:text-emerald-700"
                                        :class="{ 'bg-emerald-50 text-emerald-700 font-medium': searchForm.variety_id == v.id }">
                                        {{ v.name }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Row 2: Type · Status · Date range -->
                    <div class="grid grid-cols-3 gap-4">

                        <!-- Type -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                            <select v-model="searchForm.type"
                                class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">All Types</option>
                                <option v-for="t in types" :key="t" :value="t" class="capitalize">{{ t }}</option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Status</label>
                            <select v-model="searchForm.status"
                                class="w-full text-sm rounded-md border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500">
                                <option value="">All Statuses</option>
                                <option v-for="s in statuses" :key="s" :value="s" class="capitalize">{{ s }}</option>
                            </select>
                        </div>

                        <!-- Planting date range -->
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Planting Date Range</label>
                            <div class="flex items-center gap-2">
                                <input v-model="searchForm.start_date" type="date"
                                    class="flex-1 text-sm rounded-md border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500" />
                                <span class="text-gray-400 text-xs">to</span>
                                <input v-model="searchForm.end_date" type="date"
                                    class="flex-1 text-sm rounded-md border-gray-300 shadow-sm focus:ring-emerald-500 focus:border-emerald-500" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════
                     EMPTY STATE
                ══════════════════════════════════════════════ -->
                <div v-if="!crops.data || crops.data.length === 0"
                    class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    No crops found. Try adjusting your filters or
                    <Link :href="route('crops.create')" class="text-emerald-600 hover:underline font-medium">
                        register a new crop
                    </Link>.
                </div>

                <!-- ══════════════════════════════════════════════
                     CROP CARDS GRID
                ══════════════════════════════════════════════ -->
                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="crop in crops.data" :key="crop.id"
                        class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-200 flex flex-col justify-between">

                        <!-- Card body -->
                        <div>
                            <div class="flex items-start justify-between mb-2">
                                <div
                                    class="inline-flex items-center gap-2 px-3 py-1 bg-gray-50 border border-gray-100 rounded-lg text-sm text-gray-700 mb-4">
                                    <div v-if="crop.variety?.link" v-html="crop.variety.link"
                                        class="w-5 h-5 flex items-center justify-center"></div>
                                    <h3 class="text-lg font-bold text-gray-900">{{ crop?.name || 'Unknown' }}</h3>
                                </div>
                                <!-- Status badge -->
                                <span class="ml-2 px-2 py-0.5 rounded-full text-xs font-medium capitalize" :class="{
                                    'bg-emerald-100 text-emerald-700': crop.status === 'growing',
                                    'bg-blue-100   text-blue-700': crop.status === 'harvested',
                                    'bg-red-100    text-red-700': crop.status === 'failed',
                                    'bg-gray-100   text-gray-600': crop.status === 'fallow',
                                }">
                                    {{ crop.status }}
                                </span>
                            </div>

                            <div class="text-sm text-gray-600 mb-3 space-y-1">
                                <p><strong>Farm:</strong> {{ crop.farm?.name || 'Unassigned' }}</p>
                                <p class="flex items-center gap-1.5">
                                    <strong>Variety:</strong>
                                    <span class="flex items-center gap-1">
                                        {{ crop.variety?.name || 'Unknown' }}
                                    </span>
                                </p>
                                <p>
                                    <strong>Type:</strong>
                                    <span class="capitalize ml-1">{{ crop.type }}</span>
                                </p>
                                <p><strong>Planted:</strong> {{ crop.planting_date || 'N/A' }}</p>
                            </div>

                            <!-- Counts row -->
                            <div class="flex items-center space-x-3 mb-4">
                                <span
                                    class="flex items-center space-x-1.5 px-2.5 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-medium">
                                    <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <span>{{ crop.harvests_count ?? 0 }} Harvests</span>
                                </span>
                                <span
                                    class="flex items-center space-x-1.5 px-2.5 py-1 bg-gray-100 text-gray-700 rounded-md text-xs font-medium">
                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                    </svg>
                                    <span>{{ crop.inputs_count ?? 0 }} Inputs</span>
                                </span>
                            </div>

                            <!-- View details link — same style as Farm Index -->
                            <button @click="openDetailsModal(crop)"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium hover:underline focus:outline-none">
                                View Details &rarr;
                            </button>
                        </div>

                        <!-- Card footer — Edit / Delete — exact farm sizes -->
                        <div class="mt-4 border-t border-gray-100 pt-4 flex items-center justify-between">
                            <Link :href="route('crops.edit', crop.id)"
                                class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-md hover:bg-indigo-100 font-medium text-sm transition-colors">
                                Edit Crop
                            </Link>
                            <button @click="confirmDelete(crop)"
                                class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 font-medium text-sm transition-colors focus:outline-none">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════════════
                     PAGINATION — exact Farm Index markup
                ══════════════════════════════════════════════ -->
                <div v-if="crops.links && crops.links.length > 3" class="mt-6 flex justify-center space-x-2">
                    <template v-for="(link, index) in crops.links" :key="index">
                        <Link v-if="link.url" :href="link.url" v-html="link.label" :preserve-scroll="true"
                            class="px-4 py-2 border rounded-md" :class="{
                                'bg-blue-600 text-white': link.active,
                                'bg-white text-gray-700 hover:bg-gray-50': !link.active
                            }" />
                        <span v-else v-html="link.label"
                            class="px-4 py-2 border rounded-md text-gray-400 bg-gray-100 cursor-not-allowed" />
                    </template>
                </div>

            </div><!-- /max-w-7xl -->
        </div><!-- /py-12 -->


        <!-- ══════════════════════════════════════════════════════
             DETAILS MODAL — exact Farm Index structure
        ══════════════════════════════════════════════════════ -->
        <div v-if="isDetailsModalOpen && selectedCrop" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <!-- Backdrop -->
                <div @click="closeDetailsModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"
                    aria-hidden="true" />

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Panel -->
                <div
                    class="relative inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-xl sm:w-full">

                    <!-- Header -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900" id="modal-title">Crop Details</h3>
                        <button @click="closeDetailsModal"
                            class="text-gray-400 hover:text-gray-600 text-2xl font-bold focus:outline-none">
                            &times;
                        </button>
                    </div>

                    <!-- Body -->
                    <div class="bg-white px-6 py-6">
                        <div class="grid grid-cols-2 gap-y-6 gap-x-4 text-sm">

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Crop Name
                                </label>
                                <p class="mt-1 text-gray-900 font-medium capitalize">{{ selectedCrop.name }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Registered On
                                </label>
                                <p class="mt-1 text-gray-900">
                                    {{ new Date(selectedCrop.created_at).toLocaleDateString() }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Farm
                                </label>
                                <p class="mt-1 text-gray-900">{{ selectedCrop.farm?.name || 'Unassigned' }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Variety
                                </label>
                                <p class="mt-1 text-gray-900">{{ selectedCrop.variety?.name || 'Unknown' }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Type
                                </label>
                                <p class="mt-1 text-gray-900 capitalize">{{ selectedCrop.type }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Status
                                </label>
                                <p class="mt-1 text-gray-900 capitalize">{{ selectedCrop.status }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Date Planted
                                </label>
                                <p class="mt-1 text-gray-900">{{ selectedCrop.planting_date || 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Expected Harvest
                                </label>
                                <p class="mt-1 text-emerald-700 font-medium">
                                    {{ selectedCrop.expected_harvest_date || 'Not scheduled' }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Activity Summary
                                </label>
                                <div class="mt-2 flex flex-col space-y-2">
                                    <span class="inline-flex items-center space-x-2 text-blue-600 text-xs font-medium">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                        </svg>
                                        <span>{{ selectedCrop.harvests_count ?? 0 }} Harvest Records</span>
                                    </span>
                                    <span class="inline-flex items-center space-x-2 text-amber-600 text-xs font-medium">
                                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                        </svg>
                                        <span>{{ selectedCrop.inputs_count ?? 0 }} Input Entries</span>
                                    </span>
                                </div>
                            </div>

                            <div class="col-span-2">
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Notes
                                </label>
                                <div
                                    class="mt-1 p-3 bg-gray-50 rounded-md text-gray-700 whitespace-pre-wrap border border-gray-100">
                                    {{ selectedCrop.notes || 'No additional notes provided.' }}
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex justify-end">
                        <button @click="closeDetailsModal" type="button"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                            Close
                        </button>
                    </div>

                </div>
            </div>
        </div>


        <!-- ══════════════════════════════════════════════════════
             DELETE MODAL — exact Farm Index structure
        ══════════════════════════════════════════════════════ -->
        <div v-if="isDeleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

                <!-- Backdrop -->
                <div @click="closeDeleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"
                    aria-hidden="true" />

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <!-- Panel -->
                <div
                    class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">

                            <!-- Red warning icon -->
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>

                            <!-- Text -->
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Delete Crop
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete
                                        <span class="font-bold text-gray-800">"{{ cropToDelete?.name }}"</span>?
                                        All of its data will be permanently removed.
                                        This action cannot be undone.
                                    </p>
                                </div>
                            </div>

                        </div>
                    </div>

                    <!-- Footer buttons -->
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-row-reverse space-x-reverse space-x-3">
                        <button @click="executeDelete" type="button"
                            class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:text-sm transition">
                            Delete Crop
                        </button>
                        <button @click="closeDeleteModal" type="button"
                            class="inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:text-sm transition">
                            Cancel
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </AuthenticatedLayout>
</template>