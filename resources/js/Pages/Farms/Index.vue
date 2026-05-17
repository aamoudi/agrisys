<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

// Receive the data from Laravel Controller
defineProps({
    farms: {
        type: Array,
        required: true,
    },
});
</script>

<template>

    <Head title="My Farms" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">My Farms</h2>
                <Link :href="route('farms.create')"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700">
                    + Add New Farm
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <div v-if="farms.length === 0"
                    class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center text-gray-500">
                    You haven't added any farms yet. Click "Add New Farm" to get started.
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                    <div v-for="farm in farms.data" :key="farm.id"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200">
                        <h3 class="text-lg font-bold text-gray-900 mb-2">{{ farm.name }}</h3>

                        <div class="text-sm text-gray-600 mb-4">
                            <p><strong>Location:</strong> {{ farm.city?.name || 'No City Assigned' }}</p>
                            <p><strong>Soil Type:</strong> {{ farm.soil_type?.name || 'Unknown Soil' }}</p>
                        </div>

                        <div class="mt-4 border-t pt-4">
                            <Link :href="route('farms.show', farm.id)" class="text-blue-600 hover:underline">
                                View Details &rarr;
                            </Link>
                        </div>
                    </div>

                </div>
                <div class="mt-6 flex justify-center space-x-2">
                    <template v-for="(link, index) in farms.links" :key="index">
                        <Link v-if="link.url" :href="link.url" v-html="link.label" class="px-4 py-2 border rounded-md"
                            :class="{ 'bg-blue-600 text-white': link.active, 'bg-white text-gray-700 hover:bg-gray-50': !link.active }" />
                        <span v-else v-html="link.label"
                            class="px-4 py-2 border rounded-md text-gray-400 bg-gray-100 cursor-not-allowed"></span>
                    </template>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>