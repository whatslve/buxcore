<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    quests: Array
});
</script>

<template>
    <Head title="Задания"/>
    <DashboardLayout>
        <div class="mb-4 flex justify-between items-center">
            <h1 class="text-lg font-semibold">Задания</h1>
            <Link :href="route('quests.create')"
                  class="inline-flex items-center gap-2 rounded-md bg-green-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700">
                Создать
            </Link>
        </div>

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6 text-gray-900">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Название</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Тип</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Ссылок</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Лимит / Повтор</th>
                        <th class="px-6 py-3 text-left text-sm font-medium text-gray-500 uppercase tracking-wider">Оплата</th>
                        <th class="px-6 py-3 text-right text-sm font-medium text-gray-500 uppercase tracking-wider">Действия</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="quest in quests" :key="quest.id">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ quest.name }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ quest.type }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ quest.links.length }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ quest.limits?.daily_limit ?? '—' }} / {{ quest.limits?.repeat_after_hours ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ quest.payment?.user_payment ?? '—' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                            <Link :href="route('quests.edit', quest.id)"
                                  class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm font-medium text-white shadow-sm hover:bg-indigo-700">
                                Редактировать
                            </Link>
                        </td>
                    </tr>

                    <tr v-if="quests.length === 0">
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">Нет заданий</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </DashboardLayout>
</template>
