<!-- resources/js/Pages/Visits/Edit.vue -->
<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import NavLink from '@/Components/NavLink.vue';
// приходят из контроллера: Inertia::render('Visits/Edit', ['visit' => $visit])
const props = defineProps({
    visit: {
        type: Object,
        required: true,
    },
})

// заполняем форму начальными значениями из props.visit
const form = useForm({
    title: props.visit.title ?? '',
    description: props.visit.description ?? '',
    frame: Number(props.visit.frame ?? 0),
    time: Number(props.visit.time ?? 5),
    status: props.visit.status ?? 'off',
    reward: Number(props.visit.reward ?? 0),
    cost: Number(props.visit.cost ?? 0),
    views: Number(props.visit.views ?? 0),
    balance: Number(props.visit.balance ?? 0),
    link: props.visit.link ?? '',
    max_views_per_day: Number(props.visit.max_views_per_day ?? 0),
    ip_filter: props.visit.ip_filter ?? 'off',
})

const submit = () => {
    form.put(route('adv.visits.update', props.visit.id), {
        preserveScroll: true,
    })
}
</script>

<template>
    <Head title="Edit Visit" />
    <DashboardLayout>
        <!-- Заголовок -->
        <h1 class="mb-4 text-lg font-semibold">Редактирование задания просмотра</h1>
        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="mb-6 flex items-center justify-between">
                    <Link
                        :href="route('adv.visits.index')"
                        class="rounded-md bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200"
                    >
                        ← Назад к списку
                    </Link>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Title -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
                        <input
                            v-model="form.title"
                            type="text"
                            id="title"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                         focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        />
                        <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                        <textarea
                            v-model="form.description"
                            id="description"
                            rows="3"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                         focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        ></textarea>
                        <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                    </div>

                    <!-- Frame -->
                    <div>
                        <label for="frame" class="block text-sm font-medium text-gray-700">Frame</label>
                        <input
                            v-model.number="form.frame"
                            type="number"
                            id="frame"
                            min="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                         focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        />
                        <p v-if="form.errors.frame" class="mt-1 text-sm text-red-600">{{ form.errors.frame }}</p>
                    </div>

                    <!-- Time -->
                    <div>
                        <label for="time" class="block text-sm font-medium text-gray-700">Time (seconds)</label>
                        <select
                            v-model.number="form.time"
                            id="time"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option v-for="t in [5,10,15,20,30,60,80,100]" :key="t" :value="t">{{ t }}</option>
                        </select>
                        <p v-if="form.errors.time" class="mt-1 text-sm text-red-600">{{ form.errors.time }}</p>
                    </div>

                    <!-- Status -->
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
                        <select
                            v-model="form.status"
                            id="status"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="on">On</option>
                            <option value="off">Off</option>
                            <option value="ban">Ban</option>
                        </select>
                        <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</p>
                    </div>

                    <!-- Reward -->
                    <div>
                        <label for="reward" class="block text-sm font-medium text-gray-700">Reward</label>
                        <input
                            v-model.number="form.reward"
                            type="number"
                            step="0.01"
                            id="reward"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                         focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        />
                        <p v-if="form.errors.reward" class="mt-1 text-sm text-red-600">{{ form.errors.reward }}</p>
                    </div>

                    <!-- Cost -->
                    <div>
                        <label for="cost" class="block text-sm font-medium text-gray-700">Cost</label>
                        <input
                            v-model.number="form.cost"
                            type="number"
                            step="0.01"
                            id="cost"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                         focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        />
                        <p v-if="form.errors.cost" class="mt-1 text-sm text-red-600">{{ form.errors.cost }}</p>
                    </div>

                    <!-- Views -->
                    <div>
                        <label for="views" class="block text-sm font-medium text-gray-700">Views</label>
                        <input
                            v-model.number="form.views"
                            type="number"
                            min="0"
                            id="views"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                         focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        />
                        <p v-if="form.errors.views" class="mt-1 text-sm text-red-600">{{ form.errors.views }}</p>
                    </div>

                    <!-- Balance -->
                    <div>
                        <label for="balance" class="block text-sm font-medium text-gray-700">Balance</label>
                        <input
                            v-model.number="form.balance"
                            type="number"
                            step="0.01"
                            id="balance"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                         focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        />
                        <p v-if="form.errors.balance" class="mt-1 text-sm text-red-600">{{ form.errors.balance }}</p>
                    </div>

                    <!-- Link -->
                    <div>
                        <label for="link" class="block text-sm font-medium text-gray-700">Link</label>
                        <input
                            v-model="form.link"
                            type="url"
                            id="link"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                         focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        />
                        <p v-if="form.errors.link" class="mt-1 text-sm text-red-600">{{ form.errors.link }}</p>
                    </div>

                    <!-- Max views per day -->
                    <div>
                        <label for="max_views_per_day" class="block text-sm font-medium text-gray-700">Max views per day</label>
                        <input
                            v-model.number="form.max_views_per_day"
                            type="number"
                            min="0"
                            id="max_views_per_day"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                         focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        />
                        <p v-if="form.errors.max_views_per_day" class="mt-1 text-sm text-red-600">
                            {{ form.errors.max_views_per_day }}
                        </p>
                    </div>

                    <!-- IP filter -->
                    <div>
                        <label for="ip_filter" class="block text-sm font-medium text-gray-700">IP Filter</label>
                        <select
                            v-model="form.ip_filter"
                            id="ip_filter"
                            class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                        >
                            <option value="full">Full</option>
                            <option value="mask">Mask</option>
                            <option value="off">Off</option>
                        </select>
                        <p v-if="form.errors.ip_filter" class="mt-1 text-sm text-red-600">{{ form.errors.ip_filter }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 disabled:opacity-50"
                        >
                            Сохранить изменения
                        </button>

                        <Link
                            :href="route('adv.visits.index')"
                            class="text-sm text-gray-600 hover:text-gray-800"
                        >
                            Отмена
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </DashboardLayout>

</template>
