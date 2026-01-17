<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import { onBeforeUnmount } from 'vue'

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

// Инициализация редакторов (только StarterKit — без дубликатов)
const descriptionEditor = useEditor({
    extensions: [StarterKit],
    content: '',
    onUpdate: ({ editor }) => form.description = editor.getHTML(),
})

const completeInfoEditor = useEditor({
    extensions: [StarterKit],
    content: '',
    onUpdate: ({ editor }) => form.complete_info = editor.getHTML(),
})

// Toolbar helpers (без ошибок если редактор ещё не готов)
const getEditor = (ed) => ed?.value ?? ed
const exec = (ed, fn) => { const e = getEditor(ed); if (!e) return; e.chain().focus()[fn]().run() }
const execWithArg = (ed, fn, arg) => { const e = getEditor(ed); if (!e) return; e.chain().focus()[fn](arg).run() }

function toggleBold(ed) { exec(ed, 'toggleBold') }
function toggleItalic(ed) { exec(ed, 'toggleItalic') }
function toggleBulletList(ed) { exec(ed, 'toggleBulletList') }
function toggleOrderedList(ed) { exec(ed, 'toggleOrderedList') }
function undo(ed) { const e = getEditor(ed); if (!e) return; try { e.chain().focus().undo().run() } catch {} }
function redo(ed) { const e = getEditor(ed); if (!e) return; try { e.chain().focus().redo().run() } catch {} }
function isActive(ed, name) { const e = getEditor(ed); if (!e) return false; try { return e.isActive(name) } catch { return false } }

// Clean up
onBeforeUnmount(() => {
    if (descriptionEditor?.destroy) try { descriptionEditor.destroy() } catch {}
    if (descriptionEditor?.value?.destroy) try { descriptionEditor.value.destroy() } catch {}
    if (completeInfoEditor?.destroy) try { completeInfoEditor.destroy() } catch {}
    if (completeInfoEditor?.value?.destroy) try { completeInfoEditor.value.destroy() } catch {}
})

// DEBUG (убрать позже)
console.log('descriptionEditor', descriptionEditor)
console.log('completeInfoEditor', completeInfoEditor)
</script>

<template>
    <Head title="Создать задание" />
    <DashboardLayout>
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-lg font-semibold">Создать задание</h1>
            <Link :href="route('cabinet.quests.index')" class="inline-flex items-center gap-2 rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-100">
                ← Назад
            </Link>
        </div>

        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
            <div class="max-w-3xl mx-auto py-8">
                <form @submit.prevent="form.post(route('cabinet.quests.store'))" class="space-y-6">

                    <!-- Название -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Название</label>
                        <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Тип -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Тип</label>
                        <input v-model="form.type" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- Описание -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Описание</label>

                        <div class="border rounded-md">
                            <!-- toolbar -->
                            <div class="px-2 py-1 flex flex-wrap gap-2 items-center bg-gray-50 border-b">
                                <button type="button" @click="toggleBold(descriptionEditor)"
                                        :aria-pressed="isActive(descriptionEditor, 'bold')"
                                        :class="{'bg-gray-200': isActive(descriptionEditor, 'bold')}"
                                        class="flex items-center gap-1 px-2 py-1 rounded text-xs border">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden><path d="M6 4h7a4 4 0 010 8H6zM6 12h8a4 4 0 010 8H6z"/></svg>
                                </button>

                                <button type="button" @click="toggleItalic(descriptionEditor)"
                                        :aria-pressed="isActive(descriptionEditor, 'italic')"
                                        :class="{'bg-gray-200': isActive(descriptionEditor, 'italic')}"
                                        class="flex items-center gap-1 px-2 py-1 rounded text-xs border">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 4h6M8 20h6M14 4l-4 16"/></svg>
                                </button>

                                <button type="button" @click="toggleBulletList(descriptionEditor)"
                                        :class="{'bg-gray-200': isActive(descriptionEditor, 'bulletList')}"
                                        class="flex items-center gap-1 px-2 py-1 rounded text-xs border">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden><path d="M7 6a1 1 0 110-2 1 1 0 010 2zm0 8a1 1 0 110-2 1 1 0 010 2zm0 8a1 1 0 110-2 1 1 0 010 2zM10 5h10v2H10zm0 8h10v2H10zm0 8h10v2H10z"/></svg>
                                </button>

                                <button type="button" @click="toggleOrderedList(descriptionEditor)"
                                        :class="{'bg-gray-200': isActive(descriptionEditor, 'orderedList')}"
                                        class="flex items-center gap-1 px-2 py-1 rounded text-xs border">
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor" aria-hidden><path d="M7 4h2v2H7zM4 4h2v2H4zM10 5h10v2H10zM4 12h2v2H4zM7 11h2v2H7zM10 13h10v2H10zM4 20h2v2H4zM7 19h2v2H7zM10 21h10v2H10z"/></svg>
                                </button>

                                <div class="ml-2 border-l pl-2 flex gap-2">
                                    <button type="button" @click="undo(descriptionEditor)" class="px-2 py-1 rounded text-xs border">↶</button>
                                    <button type="button" @click="redo(descriptionEditor)" class="px-2 py-1 rounded text-xs border">↷</button>
                                </div>
                            </div>

                            <!-- editor -->
                            <div class="p-3">
                                <div v-if="descriptionEditor">
                                    <EditorContent :editor="descriptionEditor" class="min-h-[160px] prose max-w-none" />
                                </div>
                                <div v-else class="text-sm text-gray-400 py-6">Инициализация редактора...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Инструкция для пользователя -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Инструкция для пользователя</label>

                        <div class="border rounded-md">
                            <div class="px-2 py-1 flex flex-wrap gap-2 items-center bg-gray-50 border-b">
                                <button type="button" @click="toggleBold(completeInfoEditor)"
                                        :aria-pressed="isActive(completeInfoEditor, 'bold')"
                                        :class="{'bg-gray-200': isActive(completeInfoEditor, 'bold')}"
                                        class="px-2 py-1 rounded text-xs border">B</button>

                                <button type="button" @click="toggleItalic(completeInfoEditor)"
                                        :aria-pressed="isActive(completeInfoEditor, 'italic')"
                                        :class="{'bg-gray-200': isActive(completeInfoEditor, 'italic')}"
                                        class="px-2 py-1 rounded text-xs border">I</button>

                                <button type="button" @click="toggleBulletList(completeInfoEditor)"
                                        :class="{'bg-gray-200': isActive(completeInfoEditor, 'bulletList')}"
                                        class="px-2 py-1 rounded text-xs border">• List</button>

                                <button type="button" @click="toggleOrderedList(completeInfoEditor)"
                                        :class="{'bg-gray-200': isActive(completeInfoEditor, 'orderedList')}"
                                        class="px-2 py-1 rounded text-xs border">1. List</button>

                                <div class="ml-2 border-l pl-2 flex gap-2">
                                    <button type="button" @click="undo(completeInfoEditor)" class="px-2 py-1 rounded text-xs border">↶</button>
                                    <button type="button" @click="redo(completeInfoEditor)" class="px-2 py-1 rounded text-xs border">↷</button>
                                </div>
                            </div>

                            <div class="p-3">
                                <div v-if="completeInfoEditor">
                                    <EditorContent :editor="completeInfoEditor" class="min-h-[160px] prose max-w-none" />
                                </div>
                                <div v-else class="text-sm text-gray-400 py-6">Инициализация редактора...</div>
                            </div>
                        </div>
                    </div>

                    <!-- остальные поля (как было) -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Тип проверки</label>
                        <select v-model="form.check_type" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="manual">Ручная</option>
                            <option value="automatic">Автоматическая</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Выбор ссылки</label>
                        <select v-model="form.link_selection_mode" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="sequential">По порядку</option>
                            <option value="random">Случайно</option>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_adult" /> Для взрослых</label>
                        <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_soft_install" /> Требует установки ПО</label>
                        <label class="flex items-center gap-2"><input type="checkbox" v-model="form.show_screenshot_help" /> Показать помощь по скриншотам</label>
                        <label class="flex items-center gap-2"><input type="checkbox" v-model="form.white_list_only" /> Только для белого списка</label>
                        <label class="flex items-center gap-2"><input type="checkbox" v-model="form.allow_parallel" /> Разрешить параллельное выполнение</label>
                        <label class="flex items-center gap-2"><input type="checkbox" v-model="form.check_is_proofed_email" /> Проверенный Email</label>
                        <label class="flex items-center gap-2"><input type="checkbox" v-model="form.check_is_proofed_phone" /> Проверенный телефон</label>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Реферальный фильтр</label>
                        <select v-model="form.referral_filter" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="all">Показывать всем</option>
                            <option value="my_referrals">Мои рефералы</option>
                            <option value="no_refer">Не показывать рефералам</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Таймер запуска</label>
                        <input type="datetime-local" v-model="form.start_at" class="mt-1 block w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- actions -->
                    <div class="flex items-center gap-3">
                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Создать</button>
                        <Link :href="route('cabinet.quests.index')" class="text-sm text-gray-600 hover:text-gray-900">Отмена</Link>
                    </div>

                    <!-- debug -->
                    <pre class="bg-gray-50 p-2 text-xs mt-2 overflow-auto">{{ form }}</pre>

                </form>
            </div>
        </div>
    </DashboardLayout>
</template>

<style>
.ProseMirror { min-height: 160px; outline: none; }
.ProseMirror :where(p, ul, ol) { margin: .4rem 0; }
button { cursor: pointer; }
</style>
