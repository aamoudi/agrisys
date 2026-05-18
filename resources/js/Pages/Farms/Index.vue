<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    farms: {
        type: Object,
        required: true,
    },
});

// --- DETAILS MODAL STATE ---
const isModalOpen = ref(false);
const selectedFarm = ref(null);

const openDetailsModal = (farm) => {
    selectedFarm.value = farm;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    selectedFarm.value = null;
};

// --- DELETE MODAL STATE ---
const isDeleteModalOpen = ref(false);
const farmToDelete = ref(null);

const confirmDelete = (farm) => {
    farmToDelete.value = farm;
    isDeleteModalOpen.value = true;
};

const executeDelete = () => {
    if (farmToDelete.value) {
        // Save the name before resetting the state so it is available for the alert
        const deletedFarmName = farmToDelete.value.name;

        router.delete(route('farms.destroy', farmToDelete.value.id), {
            onSuccess: () => {
                closeDeleteModal();
                // Custom browser alert matching your exact request
                alert(`The farm "${deletedFarmName}" deleted successfully!`);
            },
        });
    }
};

const closeDeleteModal = () => {
    isDeleteModalOpen.value = false;
    farmToDelete.value = null;
};
</script>

<template>

    <Head title="My Farms" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Farms</h2>
                <Link :href="route('farms.create')"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition-colors">
                    + Add New Farm
                </Link>
            </div>
        </template>

        <div class="py-12 relative z-0">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div v-if="!farms.data || farms.data.length === 0"
                    class="bg-white shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    You haven't added any farms yet. Click "Add New Farm" to get started.
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div v-for="farm in farms.data" :key="farm.id"
                        class="bg-white shadow-sm sm:rounded-lg p-6 border border-gray-200 flex flex-col justify-between">

                        <div>
                            <h3 class="text-lg font-bold text-gray-900 mb-2">{{ farm.name }}</h3>

                            <div class="text-sm text-gray-600 mb-3 space-y-1">
                                <p><strong>Location:</strong> {{ farm.city?.name || 'No City Assigned' }}</p>
                                <p><strong>Soil Type:</strong> {{ farm.soil_type?.name || 'Unknown Soil' }}</p>
                            </div>

                            <div class="flex items-center space-x-3 mb-4">
                                <Link :href="route().has('workers.index') ? route('workers.index') : '#'" 
                                    class="flex items-center space-x-1.5 px-2.5 py-1 bg-gray-100 text-gray-700 hover:bg-blue-50 hover:text-blue-700 rounded-md text-xs font-medium transition-colors group">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                    </svg>
                                    <span>{{ farm.workers_count ?? 0 }} Workers</span>
                                </Link>

                                <Link :href="route().has('crops.index') ? route('crops.index') : '#'" 
                                    class="flex items-center space-x-1.5 px-2.5 py-1 bg-gray-100 text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-md text-xs font-medium transition-colors group">
                                    <svg class="w-4 h-4 text-gray-400 group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <span>{{ farm.crops_count ?? 0 }} Crops</span>
                                </Link>
                            </div>

                            <button @click="openDetailsModal(farm)"
                                class="text-blue-600 hover:text-blue-800 text-sm font-medium hover:underline focus:outline-none">
                                View Details &rarr;
                            </button>
                        </div>

                        <div class="mt-4 border-t border-gray-100 pt-4 flex items-center justify-between">
                            <Link :href="route('farms.edit', farm.id)"
                                class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-md hover:bg-indigo-100 font-medium text-sm transition-colors">
                                Edit Farm
                            </Link>

                            <button @click="confirmDelete(farm)"
                                class="inline-flex items-center px-3 py-1.5 bg-red-50 text-red-700 rounded-md hover:bg-red-100 font-medium text-sm transition-colors focus:outline-none">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex justify-center space-x-2">
                    <template v-for="(link, index) in farms.links" :key="index">
                        <Link v-if="link.url" :href="link.url" v-html="link.label"
                            class="px-4 py-2 border rounded-md transition-colors"
                            :class="{ 'bg-blue-600 text-white border-blue-600': link.active, 'bg-white text-gray-700 hover:bg-gray-50': !link.active }" />
                        <span v-else v-html="link.label"
                            class="px-4 py-2 border rounded-md text-gray-400 bg-gray-50 cursor-not-allowed"></span>
                    </template>
                </div>

            </div>
        </div>

        <div v-if="isModalOpen && selectedFarm" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
                <div @click="closeModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"
                    aria-hidden="true">
                </div>
                <div
                    class="relative inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-xl sm:w-full">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                        <h3 class="text-lg font-bold text-gray-900" id="modal-title">Farm Details</h3>
                        <button @click="closeModal"
                            class="text-gray-400 hover:text-gray-600 text-2xl font-bold focus:outline-none">&times;</button>
                    </div>
                    <div class="bg-white px-6 py-6">
                        <div class="grid grid-cols-2 gap-y-6 gap-x-4 text-sm">
                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Farm
                                    Name</label>
                                <p class="mt-1 text-gray-900 font-medium">{{ selectedFarm.name }}</p>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Registered
                                    On</label>
                                <p class="mt-1 text-gray-900">{{ new Date(selectedFarm.created_at).toLocaleDateString()
                                    }}</p>
                            </div>
                            <div>
                                <label
                                    class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Location
                                    (City)</label>
                                <p class="mt-1 text-gray-900">{{ selectedFarm.city?.name || 'Not mapped' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Soil
                                    Profile</label>
                                <p class="mt-1 text-gray-900">{{ selectedFarm.soil_type?.name || 'Unassigned' }}</p>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Size
                                    (Hectares)</label>
                                <p class="mt-1 text-gray-900">{{ selectedFarm.size_hectares ? selectedFarm.size_hectares
                                    + ' ha'
                                    : 'N/A' }}</p>
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Operational Summary</label>
                                <div class="mt-2 flex flex-col space-y-2">
                                    <Link :href="route().has('workers.index') ? route('workers.index') : '#'" class="inline-flex items-center space-x-2 text-blue-600 hover:underline text-xs font-medium">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                        <span>{{ selectedFarm.workers_count ?? 0 }} Registered Workers</span>
                                    </Link>
                                    <Link :href="route().has('crops.index') ? route('crops.index') : '#'" class="inline-flex items-center space-x-2 text-emerald-600 hover:underline text-xs font-medium">
                                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        <span>{{ selectedFarm.crops_count ?? 0 }} Active Crops</span>
                                    </Link>
                                </div>
                            </div>

                            <div class="col-span-2">
                                <label
                                    class="block text-xs font-semibold text-gray-400 uppercase tracking-wider">Notes</label>
                                <div
                                    class="mt-1 p-3 bg-gray-50 rounded-md text-gray-700 whitespace-pre-wrap border border-gray-100">
                                    {{ selectedFarm.notes || 'No additional notes provided.' }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-6 py-3 border-t border-gray-200 flex justify-end">
                        <button @click="closeModal" type="button"
                            class="px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="isDeleteModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div @click="closeDeleteModal" class="fixed inset-0 bg-gray-900 bg-opacity-50 transition-opacity"
                    aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div
                    class="relative inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div
                                class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Delete Farm
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Are you sure you want to delete <span class="font-bold text-gray-800">"{{
                                            farmToDelete?.name }}"</span>? All of its data will be permanently removed.
                                        This
                                        action cannot be undone.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-row-reverse space-x-reverse space-x-3">
                        <button @click="executeDelete" type="button"
                            class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:text-sm transition">
                            Delete Farm
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