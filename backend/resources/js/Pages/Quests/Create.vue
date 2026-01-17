<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'

const form = useForm({
    name: '',
    type: '',
    description: '',
    complete_info: '',
    check_type: 'manual',
    link_selection_mode: 'sequential',

    quest_type_id: null,
    daily_limit: 0,
    repeat_after_hours: null,
    execution_time_hours: 120,
    min_experience: 0,

    white_list_only: false,
    allow_parallel: false,
    is_adult: false,
    is_soft_install: false,
    show_screenshot_help: false,
    check_is_proofed_email: false,
    check_is_proofed_phone: false,

    referral_filter: 'all',
    start_at: null,
})

// Tiptap редактор для description
const descriptionEditor = useEditor({
    extensions: [StarterKit],
    content: form.description,
    onUpdate: ({ editor }) => {
        form.description = editor.getHTML()
    }
})

// Tiptap редактор для complete_info
const completeInfoEditor = useEditor({
    extensions: [StarterKit],
    content: form.complete_info,
    onUpdate: ({ editor }) => {
        form.complete_info = editor.getHTML()
    }
})
</script>

<template>
    <Head title="Создать задание" />
    <DashboardLayout>
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-lg font-semibold">Создать задание</h1>
            <Link
                :href="route('cabinet.quests.index')"
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
                <form @submit.prevent="form.post(route('cabinet.quests.store'))" class="space-y-6">

                    <!-- Название -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Название</label>
                        <input v-model="form.name" type="text"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Тип -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Тип</label>
                        <input v-model="form.type" type="text"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Описание -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Описание</label>
                        <EditorContent :editor="descriptionEditor" class="mt-1 border border-gray-300 rounded-md p-2 min-h-[150px]" />
                    </div>

                    <!-- Поле complete_info -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Инструкция для пользователя</label>
                        <EditorContent :editor="completeInfoEditor" class="mt-1 border border-gray-300 rounded-md p-2 min-h-[150px]" />
                    </div>

                    <!-- Остальные поля остаются как есть -->
                    <!-- Check Type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Тип проверки</label>
                        <select v-model="form.check_type"
                                class="mt-1 block w-full rounded-md border-gray-300
                                focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="manual">Ручная</option>
                            <option value="automatic">Автоматическая</option>
                        </select>
                    </div>

                    <!-- Link selection mode -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Выбор ссылки</label>
                        <select v-model="form.link_selection_mode"
                                class="mt-1 block w-full rounded-md border-gray-300
                                focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="sequential">По порядку</option>
                            <option value="random">Случайно</option>
                        </select>
                    </div>

                    <!-- Флаги -->
                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="form.is_adult" />
                            Для взрослых
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="form.is_soft_install" />
                            Требует установки ПО
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="form.show_screenshot_help" />
                            Показать помощь по скриншотам
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="form.white_list_only" />
                            Только для белого списка
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="form.allow_parallel" />
                            Разрешить параллельное выполнение
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="form.check_is_proofed_email" />
                            Проверенный Email
                        </label>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" v-model="form.check_is_proofed_phone" />
                            Проверенный телефон
                        </label>
                    </div>

                    <!-- Referral Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Реферальный фильтр</label>
                        <select v-model="form.referral_filter"
                                class="mt-1 block w-full rounded-md border-gray-300
                                focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="all">Показывать всем</option>
                            <option value="my_referrals">Мои рефералы</option>
                            <option value="no_refer">Не показывать рефералам</option>
                        </select>
                    </div>

                    <!-- Start at -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Таймер запуска</label>
                        <input type="datetime-local" v-model="form.start_at"
                               class="mt-1 block w-full rounded-md border-gray-300
                               focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-white
                                       hover:bg-indigo-700">
                            Создать
                        </button>
                        <Link
                            :href="route('cabinet.quests.index')"
                            class="text-sm text-gray-600 hover:text-gray-900"
                        >
                            Отмена
                        </Link>
                    </div>

                </form>
            </div>
        </div>
    </DashboardLayout>
</template>
