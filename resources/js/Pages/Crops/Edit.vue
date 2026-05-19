<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';

const props = defineProps({
    crop: Object,
    farms: Array,
    varieties: Array,
    statuses: Array,
    types: Array
});

// Initialize form reactive state mapped precisely against active record statistics
const form = useForm({
    name: props.crop.name,
    farm_id: props.crop.farm_id,
    variety_cd: props.crop.variety_cd,
    type: props.crop.type,
    status: props.crop.status,
    planting_date: props.crop.planting_date,
    expected_harvest_date: props.crop.expected_harvest_date || '',
    notes: props.crop.notes || ''
});

const submit = () => {
    form.put(route('crops.update', props.crop.id));
};
</script>

<template>

    <Head :title="`Modify Log: ${crop.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Edit Crop Log: {{ crop.name }}</h2>
        </template>

        <div class="py-12 bg-gray-100 min-h-[calc(100vh-65px)]">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-200">
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-medium text-gray-900">Cultivation Specifications</h3>
                        <p class="text-xs text-gray-500 mt-1">Modify historical ledger info, status levels, or farm area
                            allocations below.</p>
                    </div>

                    <form @submit.prevent="submit" class="p-6 space-y-5">

                        <div v-if="form.errors.error"
                            class="p-4 bg-red-50 border-l-4 border-red-500 text-red-700 text-sm rounded-md">
                            {{ form.errors.error }}
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Crop Ledger Identifier /
                                    Name</label>
                                <input type="text" v-model="form.name" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                                    placeholder="e.g. Autumn Wheat Plots" />
                                <span v-if="form.errors.name" class="text-xs text-red-600 mt-1 block">{{
                                    form.errors.name
                                    }}</span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Assigned Land Plot</label>
                                <select v-model="form.farm_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <option value="" disabled>Choose target sector...</option>
                                    <option v-for="farm in farms" :key="farm.id" :value="farm.id">{{ farm.name }}
                                    </option>
                                </select>
                                <span v-if="form.errors.farm_id" class="text-xs text-red-600 mt-1 block">{{
                                    form.errors.farm_id
                                    }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Botanical Variety</label>
                                <select v-model="form.variety_cd" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <option value="" disabled>Select variety taxonomy...</option>
                                    <option v-for="variety in varieties" :key="variety.id" :value="variety.id">{{
                                        variety.name
                                        }}</option>
                                </select>
                                <span v-if="form.errors.variety_cd" class="text-xs text-red-600 mt-1 block">{{
                                    form.errors.variety_cd }}</span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Lifecycle Class</label>
                                <select v-model="form.type" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <option value="" disabled>Choose lifecycle...</option>
                                    <option v-for="t in types" :key="t" :value="t" class="capitalize">{{ t }}</option>
                                </select>
                                <span v-if="form.errors.type" class="text-xs text-red-600 mt-1 block">{{
                                    form.errors.type
                                    }}</span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Status Layer</label>
                                <select v-model="form.status" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <option value="" disabled>Current status...</option>
                                    <option v-for="s in statuses" :key="s" :value="s" class="capitalize">{{ s }}
                                    </option>
                                </select>
                                <span v-if="form.errors.status" class="text-xs text-red-600 mt-1 block">{{
                                    form.errors.status
                                    }}</span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Initial Seeding / Planting
                                    Date</label>
                                <input type="date" v-model="form.planting_date" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" />
                                <span v-if="form.errors.planting_date" class="text-xs text-red-600 mt-1 block">{{
                                    form.errors.planting_date }}</span>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Expected Harvest Timeline</label>
                                <input type="date" v-model="form.expected_harvest_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" />
                                <span v-if="form.errors.expected_harvest_date"
                                    class="text-xs text-red-600 mt-1 block">{{
                                        form.errors.expected_harvest_date }}</span>
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Additional Field Log
                                Commentary</label>
                            <textarea v-model="form.notes" rows="4"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"
                                placeholder="Add specific soil logs, fertilizing summaries, or progress remarks..."></textarea>
                            <span v-if="form.errors.notes" class="text-xs text-red-600 mt-1 block">{{ form.errors.notes
                                }}</span>
                        </div>

                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                            <Link :href="route('crops.index')"
                                class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition duration-150">
                                Cancel
                            </Link>

                            <button type="submit" :disabled="form.processing"
                                class="inline-flex justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 disabled:opacity-50 transition duration-150">
                                {{ form.processing ? 'Modifying...' : 'Save Logs' }}
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>