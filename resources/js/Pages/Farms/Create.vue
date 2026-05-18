<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    countries: Array,
    cities: Array,
    soilTypes: Array
});

const form = useForm({
    name: '',
    country_id: '',   // Kept strictly to manage frontend state filtering
    city_id: '',      // Sent to backend
    soil_type_cd: '', // Sent to backend
    size_hectares: '',// Sent to backend
    notes: '',// Sent to backend
});

// Cascading Filter: Only show cities belonging to the selected country
const filteredCities = computed(() => {
    if (!form.country_id) return [];
    return props.cities.filter(city => city.country_id === parseInt(form.country_id));
});

const submit = () => {
    form.post(route('farms.store'));
};
</script>

<template>

    <Head title="Register New Farm" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Farms Management</h2>
        </template>

        <div class="py-12 bg-gray-100 min-h-[calc(100vh-65px)]">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                    <h3 class="text-lg font-medium text-gray-900 mb-6">Register a New Farm Plot</h3>

                    <form @submit.prevent="submit" class="space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Farm Name</label>
                            <input v-model="form.name" type="text"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                placeholder="Enter farm name" />
                            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Country</label>
                                <select v-model="form.country_id" @change="form.city_id = ''"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Select Country</option>
                                    <option v-for="country in countries" :key="country.id" :value="country.id">{{
                                        country.name
                                        }}</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">City</label>
                                <select v-model="form.city_id" :disabled="!form.country_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm disabled:bg-gray-100">
                                    <option value="">Select City</option>
                                    <option v-for="city in filteredCities" :key="city.id" :value="city.id">{{ city.name
                                        }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Soil Profile
                                    Classification</label>
                                <select v-model="form.soil_type_cd"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="">Select Soil Type</option>
                                    <option v-for="type in soilTypes" :key="type.id" :value="type.id">{{ type.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Size (Hectares)</label>
                                <input v-model="form.size_hectares" type="number" step="0.01" min="0"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="e.g. 10.55" />
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Additional Notes</label>
                            <textarea v-model="form.notes" rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Any special instructions or details about this farm..."></textarea>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                            <Link :href="route('farms.index')"
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                                Cancel
                            </Link>

                            <button type="submit" :disabled="form.processing"
                                class="inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50 transition duration-150">
                                {{ form.processing ? 'Saving...' : 'Save Farm' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>