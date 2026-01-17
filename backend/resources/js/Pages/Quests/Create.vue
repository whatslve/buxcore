<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'

import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import Bold from '@tiptap/extension-bold'
import Italic from '@tiptap/extension-italic'
import BulletList from '@tiptap/extension-bullet-list'
import OrderedList from '@tiptap/extension-ordered-list'
import ListItem from '@tiptap/extension-list-item'
import History from '@tiptap/extension-history'

// Форма
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

// Общая функция создания редактора
function createEditor(valueRef) {
    return useEditor({
        extensions: [StarterKit, Bold, Italic, BulletList, OrderedList, ListItem, History],
        content: valueRef,
        onUpdate: ({ editor }) => {
            valueRef.value = editor.getHTML()
        }
    })
}

// Редакторы
const descriptionEditor = createEditor(form.description)
const completeInfoEditor = createEditor(form.complete_info)

// Функция для кнопок тулбара
function toggleBold(editor) { editor.chain().focus().toggleBold().run() }
function toggleItalic(editor) { editor.chain().focus().toggleItalic().run() }
function toggleBulletList(editor) { editor.chain().focus().toggleBulletList().run() }
function toggleOrderedList(editor) { editor.chain().focus().toggleOrderedList().run() }
function undo(editor) { editor.chain().focus().undo().run() }
function redo(editor) { editor.chain().focus().redo().run() }
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

                    <!-- Описание (Tiptap с тулбаром) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Описание</label>
                        <div class="border rounded-md">
                            <div class="p-2 border-b bg-gray-50 flex gap-2">
                                <button type="button" @click="toggleBold(descriptionEditor)">B</button>
                                <button type="button" @click="toggleItalic(descriptionEditor)">I</button>
                                <button type="button" @click="toggleBulletList(descriptionEditor)">• List</button>
                                <button type="button" @click="toggleOrderedList(descriptionEditor)">1. List</button>
                                <button type="button" @click="undo(descriptionEditor)">Undo</button>
                                <button type="button" @click="redo(descriptionEditor)">Redo</button>
                            </div>
                            <EditorContent :editor="descriptionEditor"
                                           class="p-2 min-h-[150px]"/>
                        </div>
                    </div>

                    <!-- Инструкция для пользователя (Tiptap с тулбаром) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Инструкция для пользователя</label>
                        <div class="border rounded-md">
                            <div class="p-2 border-b bg-gray-50 flex gap-2">
                                <button type="button" @click="toggleBold(completeInfoEditor)">B</button>
                                <button type="button" @click="toggleItalic(completeInfoEditor)">I</button>
                                <button type="button" @click="toggleBulletList(completeInfoEditor)">• List</button>
                                <button type="button" @click="toggleOrderedList(completeInfoEditor)">1. List</button>
                                <button type="button" @click="undo(completeInfoEditor)">Undo</button>
                                <button type="button" @click="redo(completeInfoEditor)">Redo</button>
                            </div>
                            <EditorContent :editor="completeInfoEditor"
                                           class="p-2 min-h-[150px]"/>
                        </div>
                    </div>

                    <!-- Остальные поля как в твоем компоненте -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Тип проверки</label>
                        <select v-model="form.check_type"
                                class="mt-1 block w-full rounded-md border-gray-300
                                focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="manual">Ручная</option>
                            <option value="automatic">Автоматическая</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Выбор ссылки</label>
                        <select v-model="form.link_selection_mode"
                                class="mt-1 block w-full rounded-md border-gray-300
                                focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="sequential">По порядку</option>
                            <option value="random">Случайно</option>
                        </select>
                    </div>

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

<style>
/* Стили для ProseMirror */
.ProseMirror {
    min-height: 150px;
    outline: none;
}
button {
    background: none;
    border: 1px solid #ccc;
    padding: 2px 6px;
    border-radius: 4px;
}
button:hover {
    background-color: #f0f0f0;
}
</style>
