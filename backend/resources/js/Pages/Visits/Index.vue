<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import {Head} from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import {Link} from '@inertiajs/vue3'

defineProps({
    visits: Array
})
</script>

<template>
    <Head title="Dashboard"/>
    <DashboardLayout>
        <!-- Заголовок -->
        <h1 class="mb-4 text-lg font-semibold">Задания просмотров</h1>
        <div class="pb-12">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <Link :href="route('adv.visits.create')"
                          class="inline-flex items-center gap-2 ml-2 rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                             stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Создать
                    </Link>
                </div>
            </div>
        </div>
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <h1 class="text-xl font-semibold mb-4">Список заданий</h1>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col"
                                class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                Title
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">
                                Description
                            </th>
                            <th scope="col"
                                class="px-6 py-3 text-right text-sm font-medium text-gray-500 uppercase tracking-wider">
                                Действия
                            </th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200 bg-white">
                        <!-- Если есть визиты -->
                        <tr v-for="visit in visits" :key="visit.id" v-if="visits.length > 0">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ visit.title }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ visit.description }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <Link
                                    :href="route('adv.visits.edit', visit.id)"
                                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                                >
                                    Редактировать
                                </Link>
                            </td>
                        </tr>

                        <!-- Если нет визитов -->
                        <tr v-else>
                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                Нет заданий
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </DashboardLayout>
</template>
