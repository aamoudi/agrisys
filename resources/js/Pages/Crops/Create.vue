<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';


// Props passed down from CropController@create
const props = defineProps({
    farms: Array,       // User-owned farms list: id, name
    varieties: Array,   // Lookup varieties list: id, name
    statuses: Array,    // ['growing', 'harvested', 'failed', 'fallow']
    types: Array        // ['annual', 'perennial']
});

// Form state matching exactly the fields required by backend schemas
const form = useForm({
    name: '',
    farm_id: '',
    variety_cd: '',
    type: '',
    status: 'growing', // Default status for new crops
    planting_date: '',
    expected_harvest_date: '',
    notes: ''
});

// Submit execution mapping back to resource routes
const submit = () => {
    form.post(route('crops.store'));
};
</script>

<template>

    <Head title="Register New Crop" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Crops Management</h2>
        </template>

        <div class="py-12 bg-gray-100 min-h-[calc(100vh-65px)]">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-xl border border-gray-100">

                    <div class="p-6 bg-white border-b border-gray-100 flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Cultivation Ledger</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Register and link a new crop lifecycle crop to your
                                designated farm land.</p>
                        </div>
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                            New Crop Segment
                        </span>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-6 bg-white">

                        <div v-if="form.hasErrors" class="p-4 bg-red-50 border-l-4 border-red-500 rounded-r-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-red-800">Please correct the highlighted
                                        processing
                                        errors below.</h3>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Crop Batch Name <span
                                        class="text-red-500">*</span></label>
                                <input v-model="form.name" type="text" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    placeholder="e.g., North Field Organic Tomatoes" />
                                <div v-if="form.errors.name" class="text-xs text-red-600 mt-1 font-medium">{{
                                    form.errors.name
                                }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Assign To Farm Plot <span
                                        class="text-red-500">*</span></label>
                                <select v-model="form.farm_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="" disabled>-- Choose target location --</option>
                                    <option v-for="farm in farms" :key="farm.id" :value="farm.id">
                                        {{ farm.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.farm_id" class="text-xs text-red-600 mt-1 font-medium">
                                    {{ form.errors.farm_id }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Crop Variety <span
                                        class="text-red-500">*</span></label>
                                <select v-model="form.variety_cd" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="" disabled>-- Select certified taxonomy --</option>
                                    <option v-for="variety in varieties" :key="variety.id" :value="variety.id">
                                        {{ variety.name }}
                                    </option>
                                </select>
                                <div v-if="form.errors.variety_cd" class="text-xs text-red-600 mt-1 font-medium">{{
                                    form.errors.variety_cd }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Cultivation Lifecycle Type <span
                                        class="text-red-500">*</span></label>
                                <select v-model="form.type" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option value="" disabled>-- Select lifecycle nature --</option>
                                    <option v-for="t in types" :key="t" :value="t">
                                        {{ t.charAt(0).toUpperCase() + t.slice(1) }}
                                    </option>
                                </select>
                                <div v-if="form.errors.type" class="text-xs text-red-600 mt-1 font-medium">{{
                                    form.errors.type
                                }}</div>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Initial Status <span
                                        class="text-red-500">*</span></label>
                                <select v-model="form.status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    <option v-for="s in statuses" :key="s" :value="s">
                                        {{ s.charAt(0).toUpperCase() + s.slice(1) }}
                                    </option>
                                </select>
                                <div v-if="form.errors.status" class="text-xs text-red-600 mt-1 font-medium">{{
                                    form.errors.status }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Planting Date <span
                                        class="text-red-500">*</span></label>
                                <input v-model="form.planting_date" type="date" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                <div v-if="form.errors.planting_date" class="text-xs text-red-600 mt-1 font-medium">{{
                                    form.errors.planting_date }}</div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Expected Harvest Date</label>
                                <input v-model="form.expected_harvest_date" type="date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
                                <div v-if="form.errors.expected_harvest_date"
                                    class="text-xs text-red-600 mt-1 font-medium">{{
                                        form.errors.expected_harvest_date }}</div>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Agronomist Field Notes</label>
                            <textarea v-model="form.notes" rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Add soil treatment context, irrigation patterns, fertilizer plans, or seed lot variations..."></textarea>
                            <div v-if="form.errors.notes" class="text-xs text-red-600 mt-1 font-medium">{{
                                form.errors.notes }}
                            </div>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                            <Link :href="route('crops.index')"
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-150">
                                Cancel
                            </Link>

                            <button type="submit" :disabled="form.processing"
                                class="inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50 transition duration-150">
                                {{ form.processing ? 'Saving...' : 'Save Crop' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>