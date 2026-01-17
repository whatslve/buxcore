<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
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
    <Head title="Create Visit" />

    <DashboardLayout>
        <!-- header -->
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-lg font-semibold">Задания просмотров</h1>

            <!-- КНОПКА НАЗАД -->
            <Link
                :href="route('cabinet.visits.index')"
                class="inline-flex items-center gap-2 rounded-md
                       border border-gray-300 bg-white px-3 py-1.5
                       text-sm font-medium text-gray-700
                       hover:bg-gray-100"
            >
                ← Назад
            </Link>
        </div>

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="max-w-3xl mx-auto py-8">
                <h2 class="text-2xl font-semibold mb-6">Create Visit</h2>

                <form
                    @submit.prevent="form.post(route('cabinet.visits.store'))"
                    class="space-y-6"
                >
                    <!-- Title -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Title</label>
                        <input v-model="form.title" type="text"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea v-model="form.description" rows="3"
                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                                  focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></textarea>
                    </div>

                    <!-- Frame -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Frame</label>
                        <input v-model.number="form.frame" type="number" min="0"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Time -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Time (seconds)</label>
                        <select v-model.number="form.time"
                                class="mt-1 block w-full rounded-md border-gray-300
                                focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option v-for="t in [5,10,15,20,30,60,80,100]" :key="t" :value="t">
                                {{ t }}
                            </option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Status</label>
                        <select v-model="form.status"
                                class="mt-1 block w-full rounded-md border-gray-300
                                focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="on">On</option>
                            <option value="off">Off</option>
                            <option value="ban">Ban</option>
                        </select>
                    </div>

                    <!-- Reward -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Reward</label>
                        <input v-model.number="form.reward" type="number" step="0.01"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Cost -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Cost</label>
                        <input v-model.number="form.cost" type="number" step="0.01"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Balance -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Balance</label>
                        <input v-model.number="form.balance" type="number" step="0.01"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Link -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Link</label>
                        <input v-model="form.link" type="url"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Max views per day -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            Max views per day
                        </label>
                        <input v-model.number="form.max_views_per_day" type="number" min="0"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- IP filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">IP Filter</label>
                        <select v-model="form.ip_filter"
                                class="mt-1 block w-full rounded-md border-gray-300
                                focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="full">Full</option>
                            <option value="mask">Mask</option>
                            <option value="off">Off</option>
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-white
                                       hover:bg-indigo-700">
                            Save Visit
                        </button>

                        <Link
                            :href="route('cabinet.visits.index')"
                            class="text-sm text-gray-600 hover:text-gray-900"
                        >
                            Cancel
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>
