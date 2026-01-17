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

// Редактор для description
const descriptionEditor = useEditor({
    extensions: [
        StarterKit,
        Bold,
        Italic,
        BulletList,
        OrderedList,
        ListItem,
        History
    ],
    content: '',
    onUpdate: ({ editor }) => {
        form.description = editor.getHTML()
    }
})

// Редактор для complete_info
const completeInfoEditor = useEditor({
    extensions: [
        StarterKit,
        Bold,
        Italic,
        BulletList,
        OrderedList,
        ListItem,
        History
    ],
    content: '',
    onUpdate: ({ editor }) => {
        form.complete_info = editor.getHTML()
    }
})

// DEBUG: проверить инициализацию
console.log('descriptionEditor:', descriptionEditor)
console.log('completeInfoEditor:', completeInfoEditor)
</script>

<template>
    <Head title="Создать задание" />
    <DashboardLayout>
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-lg font-semibold">Создать задание</h1>
            <Link :href="route('cabinet.quests.index')"
                  class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100">
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

                    <!-- Описание (Tiptap) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Описание</label>
                        <div v-if="descriptionEditor">
                            <!-- Панель инструментов -->
                            <div class="flex gap-2 mb-2">
                                <button type="button" @click="descriptionEditor.chain().focus().toggleBold().run()"
                                        :class="{'font-bold bg-gray-200': descriptionEditor.isActive('bold')}"
                                        class="px-2 py-1 border rounded">B</button>
                                <button type="button" @click="descriptionEditor.chain().focus().toggleItalic().run()"
                                        :class="{'italic bg-gray-200': descriptionEditor.isActive('italic')}"
                                        class="px-2 py-1 border rounded">I</button>
                                <button type="button" @click="descriptionEditor.chain().focus().toggleBulletList().run()"
                                        :class="{'bg-gray-200': descriptionEditor.isActive('bulletList')}" class="px-2 py-1 border rounded">• List</button>
                                <button type="button" @click="descriptionEditor.chain().focus().toggleOrderedList().run()"
                                        :class="{'bg-gray-200': descriptionEditor.isActive('orderedList')}" class="px-2 py-1 border rounded">1. List</button>
                            </div>
                            <EditorContent :editor="descriptionEditor"
                                           class="mt-1 border border-gray-300 rounded-md p-2 min-h-[150px]"/>
                        </div>
                    </div>

                    <!-- Инструкция для пользователя (Tiptap) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Инструкция для пользователя</label>
                        <div v-if="completeInfoEditor">
                            <div class="flex gap-2 mb-2">
                                <button type="button" @click="completeInfoEditor.chain().focus().toggleBold().run()"
                                        :class="{'font-bold bg-gray-200': completeInfoEditor.isActive('bold')}"
                                        class="px-2 py-1 border rounded">B</button>
                                <button type="button" @click="completeInfoEditor.chain().focus().toggleItalic().run()"
                                        :class="{'italic bg-gray-200': completeInfoEditor.isActive('italic')}"
                                        class="px-2 py-1 border rounded">I</button>
                                <button type="button" @click="completeInfoEditor.chain().focus().toggleBulletList().run()"
                                        :class="{'bg-gray-200': completeInfoEditor.isActive('bulletList')}" class="px-2 py-1 border rounded">• List</button>
                                <button type="button" @click="completeInfoEditor.chain().focus().toggleOrderedList().run()"
                                        :class="{'bg-gray-200': completeInfoEditor.isActive('orderedList')}" class="px-2 py-1 border rounded">1. List</button>
                            </div>
                            <EditorContent :editor="completeInfoEditor"
                                           class="mt-1 border border-gray-300 rounded-md p-2 min-h-[150px]"/>
                        </div>
                    </div>

                    <!-- Остальные поля (чекбоксы, селекты и т.д.) оставляем как у тебя -->
                    <div class="flex items-center gap-3">
                        <button type="submit"
                                class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                            Создать
                        </button>
                        <Link :href="route('cabinet.quests.index')" class="text-sm text-gray-600 hover:text-gray-900">
                            Отмена
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </DashboardLayout>
</template>

<style>
/* Стили для редактора TipTap */
.ProseMirror {
    min-height: 150px;
    outline: none;
    padding: 0.5rem;
}
.ProseMirror p {
    margin: 0.5rem 0;
}
button {
    cursor: pointer;
}
</style>
