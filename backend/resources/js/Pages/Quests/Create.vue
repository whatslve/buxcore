<script setup>
/*
  Create.vue — рабочий компонент с TipTap (Vue3 + Inertia).
  Требования:
    npm install @tiptap/vue-3 @tiptap/starter-kit
  ВАЖНО: не импортируй отдельные расширения, которые уже входят в StarterKit,
  иначе будут дубликаты (bold, italic, lists, history, и т.п.).
*/

import { Head, Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import { onBeforeUnmount } from 'vue'

// Форма (оставил все поля как у тебя)
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

// Инициализация редакторов — ТОЛЬКО StarterKit
const descriptionEditor = useEditor({
    extensions: [StarterKit],
    content: '',
    onUpdate: ({ editor }) => {
        // синхронизируем HTML с формой
        form.description = editor.getHTML()
    },
})

const completeInfoEditor = useEditor({
    extensions: [StarterKit],
    content: '',
    onUpdate: ({ editor }) => {
        form.complete_info = editor.getHTML()
    },
})

// Безопасные команды для тулбара (проверяют, что редактор существует)
function toggleBold(edRef) {
    const ed = edRef?.value ?? edRef
    if (!ed) return
    ed.chain().focus().toggleBold().run()
}
function toggleItalic(edRef) {
    const ed = edRef?.value ?? edRef
    if (!ed) return
    ed.chain().focus().toggleItalic().run()
}
function toggleBulletList(edRef) {
    const ed = edRef?.value ?? edRef
    if (!ed) return
    ed.chain().focus().toggleBulletList().run()
}
function toggleOrderedList(edRef) {
    const ed = edRef?.value ?? edRef
    if (!ed) return
    ed.chain().focus().toggleOrderedList().run()
}
function undo(edRef) {
    const ed = edRef?.value ?? edRef
    if (!ed) return
    if (ed.can().undo) ed.chain().focus().undo().run()
}
function redo(edRef) {
    const ed = edRef?.value ?? edRef
    if (!ed) return
    if (ed.can().redo) ed.chain().focus().redo().run()
}

// helpers для активного состояния кнопок
function isActive(edRef, name) {
    const ed = edRef?.value ?? edRef
    if (!ed) return false
    try { return ed.isActive(name) } catch { return false }
}

// корректно уничтожаем редакторы при размонтировании
onBeforeUnmount(() => {
    // useEditor возвращает ref с методами на самом ref в некоторых версиях,
    // но безопасно пробуем оба варианта
    if (descriptionEditor?.destroy) {
        try { descriptionEditor.destroy() } catch {}
    }
    if (descriptionEditor?.value?.destroy) {
        try { descriptionEditor.value.destroy() } catch {}
    }

    if (completeInfoEditor?.destroy) {
        try { completeInfoEditor.destroy() } catch {}
    }
    if (completeInfoEditor?.value?.destroy) {
        try { completeInfoEditor.value.destroy() } catch {}
    }
})

// DEBUG: временные логи — убрать позже
console.log('descriptionEditor', descriptionEditor)
console.log('completeInfoEditor', completeInfoEditor)
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
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Тип -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Тип</label>
                        <input v-model="form.type" type="text"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Описание (редактор) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Описание</label>

                        <div class="border rounded-md">
                            <!-- тулбар -->
                            <div class="p-2 border-b bg-gray-50 flex gap-2 flex-wrap">
                                <button type="button" @click="toggleBold(descriptionEditor)"
                                        :class="{'bg-gray-200': isActive(descriptionEditor, 'bold')}"
                                        class="px-2 py-1 border rounded text-xs">B</button>

                                <button type="button" @click="toggleItalic(descriptionEditor)"
                                        :class="{'bg-gray-200': isActive(descriptionEditor, 'italic')}"
                                        class="px-2 py-1 border rounded text-xs">I</button>

                                <button type="button" @click="toggleBulletList(descriptionEditor)"
                                        :class="{'bg-gray-200': isActive(descriptionEditor, 'bulletList')}"
                                        class="px-2 py-1 border rounded text-xs">• List</button>

                                <button type="button" @click="toggleOrderedList(descriptionEditor)"
                                        :class="{'bg-gray-200': isActive(descriptionEditor, 'orderedList')}"
                                        class="px-2 py-1 border rounded text-xs">1. List</button>

                                <button type="button" @click="undo(descriptionEditor)" class="px-2 py-1 border rounded text-xs">Undo</button>
                                <button type="button" @click="redo(descriptionEditor)" class="px-2 py-1 border rounded text-xs">Redo</button>
                            </div>

                            <!-- содержимое редактора -->
                            <div class="p-2">
                                <div v-if="descriptionEditor">
                                    <EditorContent :editor="descriptionEditor" class="min-h-[150px] prose" />
                                </div>
                                <div v-else class="text-sm text-gray-400 py-6">Инициализация редактора...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Инструкция для пользователя (редактор) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Инструкция для пользователя</label>

                        <div class="border rounded-md">
                            <div class="p-2 border-b bg-gray-50 flex gap-2 flex-wrap">
                                <button type="button" @click="toggleBold(completeInfoEditor)"
                                        :class="{'bg-gray-200': isActive(completeInfoEditor, 'bold')}"
                                        class="px-2 py-1 border rounded text-xs">B</button>

                                <button type="button" @click="toggleItalic(completeInfoEditor)"
                                        :class="{'bg-gray-200': isActive(completeInfoEditor, 'italic')}"
                                        class="px-2 py-1 border rounded text-xs">I</button>

                                <button type="button" @click="toggleBulletList(completeInfoEditor)"
                                        :class="{'bg-gray-200': isActive(completeInfoEditor, 'bulletList')}"
                                        class="px-2 py-1 border rounded text-xs">• List</button>

                                <button type="button" @click="toggleOrderedList(completeInfoEditor)"
                                        :class="{'bg-gray-200': isActive(completeInfoEditor, 'orderedList')}"
                                        class="px-2 py-1 border rounded text-xs">1. List</button>

                                <button type="button" @click="undo(completeInfoEditor)" class="px-2 py-1 border rounded text-xs">Undo</button>
                                <button type="button" @click="redo(completeInfoEditor)" class="px-2 py-1 border rounded text-xs">Redo</button>
                            </div>

                            <div class="p-2">
                                <div v-if="completeInfoEditor">
                                    <EditorContent :editor="completeInfoEditor" class="min-h-[150px] prose" />
                                </div>
                                <div v-else class="text-sm text-gray-400 py-6">Инициализация редактора...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Остальные поля (как у тебя) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Тип проверки</label>
                        <select v-model="form.check_type"
                                class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="manual">Ручная</option>
                            <option value="automatic">Автоматическая</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Выбор ссылки</label>
                        <select v-model="form.link_selection_mode"
                                class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
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
                                class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="all">Показывать всем</option>
                            <option value="my_referrals">Мои рефералы</option>
                            <option value="no_refer">Не показывать рефералам</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Таймер запуска</label>
                        <input type="datetime-local" v-model="form.start_at"
                               class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
                            Создать
                        </button>
                        <Link :href="route('cabinet.quests.index')" class="text-sm text-gray-600 hover:text-gray-900">Отмена</Link>
                    </div>

                    <!-- debug: вывести форму (можно убрать) -->
                    <pre class="bg-gray-50 p-2 text-xs mt-2 overflow-auto">{{ form }}</pre>

                </form>
            </div>
        </div>
    </DashboardLayout>
</template>

<style>
/* Минимальные стили для ProseMirror и тулбара */
.ProseMirror {
    min-height: 150px;
    outline: none;
}
button {
    cursor: pointer;
}
</style>
