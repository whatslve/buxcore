<script setup>
import { Head, Link } from '@inertiajs/vue3'
import Pagination from '@/Components/Pagination.vue'
import DashboardLayout from "../Layouts/DashboardLayout.vue"

const props = defineProps({
    visits: { type: Object, required: true }
})
</script>

<template>
    <Head title="Задания просмотров" />
    <DashboardLayout>
        <h1 class="mb-6 text-xl font-semibold text-graphite">
            Задания просмотров
        </h1>

        <!-- список карточек -->
        <ul v-if="props.visits.data.length" role="list" class="space-y-4">
            <li
                v-for="v in props.visits.data"
                :key="v.id"
                class="rounded-xl bg-white shadow hover:shadow-md transition border border-gray-100 overflow-hidden"
            >
                <div class="p-5">
                    <!-- Заголовок -->
                    <h2 class="text-lg font-semibold text-graphite mb-2">
                        {{ v.title }}
                    </h2>

                    <!-- Описание -->
                    <p class="text-sm text-gray-600">
                        {{ v.description }}
                    </p>

                    <!-- Статус + просмотры -->
                    <div class="mt-4 flex items-center justify-between">
      <span
          :class="[
          'px-2 py-1 rounded-full text-xs font-medium',
          v.status === 'on'  ? 'bg-mint text-graphite' :
          v.status === 'ban' ? 'bg-red-100 text-red-700' :
                               'bg-gray-100 text-gray-700'
        ]"
      >
        {{ v.status }}
      </span>
                        <span class="text-sm text-gray-700 font-medium">
        👁 {{ v.views }}
      </span>
                    </div>
                </div>

                <!-- Действие -->
                <div class="px-5 py-4 border-t border-gray-100 bg-gray-50 flex justify-end">
                    <Link
                        :href="route('visits.perform', v.id)"
                        class="inline-flex items-center gap-2 rounded-lg
             bg-graphite px-4 py-2 text-sm font-medium text-mint
             shadow hover:bg-mint hover:text-graphite transition"
                    >
                        Выполнить
                    </Link>
                </div>
            </li>

        </ul>

        <!-- если пусто -->
        <div v-else class="text-center py-12 text-gray-500 text-sm">
            НЕТ ЗАДАНИЙ ПРОСМОТРОВ
        </div>

        <!-- пагинация -->
        <div class="mt-6">
            <Pagination :links="visits.links" />
        </div>
    </DashboardLayout>
</template>
