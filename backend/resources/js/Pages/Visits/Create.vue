<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3'
import NavLink from '@/Components/NavLink.vue';
import DashboardLayout from "../../Layouts/DashboardLayout.vue";
const form = useForm({
    title: '',
    description: '',
    frame: 0,
    time: 5,
    status: 'off',
    reward: 0,
    cost: 0,
    views: 0,
    balance: 0,
    link: '',
    max_views_per_day: 0,
    ip_filter: 'off',
})

</script>

<template>
    <Head title="Dashboard" />
    <DashboardLayout>
        <h1 class="mb-4 text-lg font-semibold">Задания просмотров</h1>
        <div
            class="overflow-hidden bg-white shadow-sm sm:rounded-lg"
        >
            <div class="max-w-3xl mx-auto py-8">
                <h1 class="text-2xl font-semibold mb-6">Create Visit</h1>

                <form @submit.prevent="form.post(route('adv.visits.store'))" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input v-model="form.title" type="text" id="title"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea v-model="form.description" id="description" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                    </div>

                    <!-- Frame -->
                    <div>
                        <label for="frame" class="block text-sm font-medium text-gray-700">Frame</label>
                        <input v-model.number="form.frame" type="number" id="frame" min="0"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Time -->
                    <div>
                        <label for="time" class="block text-sm font-medium text-gray-700">Time (seconds)</label>
                        <select v-model.number="form.time" id="time"
                                class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option v-for="t in [5,10,15,20,30,60,80,100]" :key="t" :value="t">{{ t }}</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select v-model="form.status" id="status"
                                class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="on">On</option>
                            <option value="off">Off</option>
                            <option value="ban">Ban</option>
                        </select>
                    </div>

                    <!-- Reward -->
                    <div>
                        <label for="reward" class="block text-sm font-medium text-gray-700">Reward</label>
                        <input v-model.number="form.reward" type="number" step="0.01" id="reward"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Cost -->
                    <div>
                        <label for="cost" class="block text-sm font-medium text-gray-700">Cost</label>
                        <input v-model.number="form.cost" type="number" step="0.01" id="cost"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Balance -->
                    <div>
                        <label for="balance" class="block text-sm font-medium text-gray-700">Balance</label>
                        <input v-model.number="form.balance" type="number" step="0.01" id="balance"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Link -->
                    <div>
                        <label for="link" class="block text-sm font-medium text-gray-700">Link</label>
                        <input v-model="form.link" type="url" id="link"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Max views per day -->
                    <div>
                        <label for="max_views_per_day" class="block text-sm font-medium text-gray-700">Max views per day</label>
                        <input v-model.number="form.max_views_per_day" type="number" min="0" id="max_views_per_day"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- IP filter -->
                    <div>
                        <label for="ip_filter" class="block text-sm font-medium text-gray-700">IP Filter</label>
                        <select v-model="form.ip_filter" id="ip_filter"
                                class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="full">Full</option>
                            <option value="mask">Mask</option>
                            <option value="off">Off</option>
                        </select>
                    </div>

                    <!-- Submit -->
                    <div>
                        <button type="submit"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                            Save Visit
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </DashboardLayout>
</template>
