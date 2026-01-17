<script setup>
/*
  Simple-style Tiptap editor UI for "description" and "complete_info"
  Uses ONLY: @tiptap/vue-3 and @tiptap/starter-kit (no duplicate extensions).
*/

import { Head, Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import { onBeforeUnmount } from 'vue'

// form fields (as before)
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

// create editors (StarterKit only)
const descriptionEditor = useEditor({
    extensions: [StarterKit],
    content: '',
    onUpdate: ({ editor }) => {
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

// safe helper to call chain commands
const getEd = (ed) => ed?.value ?? ed
function safeChain(ed, fn) {
    const e = getEd(ed)
    if (!e || !e.chain) return
    try { fn(e.chain().focus()) }
    catch (err) { /* ignore */ }
}

// toolbar actions
function toggleBold(ed) { safeChain(ed, c => c.toggleBold().run()) }
function toggleItalic(ed) { safeChain(ed, c => c.toggleItalic().run()) }
function toggleStrike(ed) { safeChain(ed, c => c.toggleStrike().run()) }
function toggleCode(ed) { safeChain(ed, c => c.toggleCode().run()) } // inline code
function toggleCodeBlock(ed) { safeChain(ed, c => c.toggleCodeBlock().run()) } // code block
function toggleBlockquote(ed) { safeChain(ed, c => c.toggleBlockquote().run()) }
function toggleBulletList(ed) { safeChain(ed, c => c.toggleBulletList().run()) }
function toggleOrderedList(ed) { safeChain(ed, c => c.toggleOrderedList().run()) }
function setHeading(ed, level) { safeChain(ed, c => c.toggleHeading({ level }).run()) }
function setParagraph(ed) { safeChain(ed, c => c.setParagraph().run()) }
function undo(ed) { safeChain(ed, c => c.undo().run()) }
function redo(ed) { safeChain(ed, c => c.redo().run()) }
function insertHorizontalRule(ed) { safeChain(ed, c => c.setHorizontalRule().run()) } // might not be available in very old starter-kit

// active state check
function isActive(ed, name, opts = {}) {
    const e = getEd(ed)
    if (!e) return false
    try { return e.isActive(name, opts) } catch { return false }
}

// cleanup
onBeforeUnmount(() => {
    try { descriptionEditor?.destroy?.() } catch {}
    try { descriptionEditor?.value?.destroy?.() } catch {}
    try { completeInfoEditor?.destroy?.() } catch {}
    try { completeInfoEditor?.value?.destroy?.() } catch {}
})

// debug (remove later)
console.log('desc editor', descriptionEditor)
console.log('complete editor', completeInfoEditor)
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

                    <!-- name/type -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Название</label>
                        <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Тип</label>
                        <input v-model="form.type" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"/>
                    </div>

                    <!-- DESCRIPTION: simple-editor style -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Описание</label>

                        <div class="border rounded-md bg-white">
                            <!-- menu -->
                            <div class="flex items-center gap-2 flex-wrap px-2 py-1 bg-gray-50 border-b">
                                <!-- block type select -->
                                <div class="relative">
                                    <select
                                        @change="(e)=>{ const v=+e.target.value; if (v===0) setParagraph(descriptionEditor); else setHeading(descriptionEditor, v) }"
                                        class="text-xs rounded border px-2 py-1"
                                    >
                                        <option value="0">Paragraph</option>
                                        <option value="1">Heading 1</option>
                                        <option value="2">Heading 2</option>
                                        <option value="3">Heading 3</option>
                                    </select>
                                </div>

                                <!-- formatting buttons -->
                                <div class="flex gap-2 ml-2">
                                    <button type="button" @click="toggleBold(descriptionEditor)"
                                            :class="{'bg-gray-200': isActive(descriptionEditor,'bold')}"
                                            class="px-2 py-1 rounded border text-xs">B</button>

                                    <button type="button" @click="toggleItalic(descriptionEditor)"
                                            :class="{'bg-gray-200': isActive(descriptionEditor,'italic')}"
                                            class="px-2 py-1 rounded border text-xs">I</button>

                                    <button type="button" @click="toggleStrike(descriptionEditor)"
                                            :class="{'bg-gray-200': isActive(descriptionEditor,'strike')}"
                                            class="px-2 py-1 rounded border text-xs line-through">S</button>

                                    <button type="button" @click="toggleCode(descriptionEditor)"
                                            :class="{'bg-gray-200': isActive(descriptionEditor,'code')}"
                                            class="px-2 py-1 rounded border text-xs">inline code</button>

                                    <button type="button" @click="toggleCodeBlock(descriptionEditor)"
                                            :class="{'bg-gray-200': isActive(descriptionEditor,'codeBlock')}"
                                            class="px-2 py-1 rounded border text-xs">code block</button>

                                    <button type="button" @click="toggleBlockquote(descriptionEditor)"
                                            :class="{'bg-gray-200': isActive(descriptionEditor,'blockquote')}"
                                            class="px-2 py-1 rounded border text-xs">❝</button>

                                    <button type="button" @click="toggleBulletList(descriptionEditor)"
                                            :class="{'bg-gray-200': isActive(descriptionEditor,'bulletList')}"
                                            class="px-2 py-1 rounded border text-xs">• list</button>

                                    <button type="button" @click="toggleOrderedList(descriptionEditor)"
                                            :class="{'bg-gray-200': isActive(descriptionEditor,'orderedList')}"
                                            class="px-2 py-1 rounded border text-xs">1. list</button>

                                    <button type="button" @click="insertHorizontalRule(descriptionEditor)"
                                            class="px-2 py-1 rounded border text-xs">—</button>

                                </div>

                                <div class="ml-auto flex gap-2">
                                    <button type="button" @click="undo(descriptionEditor)" class="px-2 py-1 rounded border text-xs">↶</button>
                                    <button type="button" @click="redo(descriptionEditor)" class="px-2 py-1 rounded border text-xs">↷</button>
                                </div>
                            </div>

                            <!-- editor area -->
                            <div class="p-3">
                                <div v-if="descriptionEditor">
                                    <EditorContent :editor="descriptionEditor" class="min-h-[160px] prose max-w-none" />
                                </div>
                                <div v-else class="text-sm text-gray-400 py-6">Инициализация редактора...</div>
                            </div>
                        </div>
                    </div>

                    <!-- COMPLETE_INFO: same UI -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Инструкция для пользователя</label>

                        <div class="border rounded-md bg-white">
                            <div class="flex items-center gap-2 flex-wrap px-2 py-1 bg-gray-50 border-b">
                                <div>
                                    <select
                                        @change="(e)=>{ const v=+e.target.value; if (v===0) setParagraph(completeInfoEditor); else setHeading(completeInfoEditor, v) }"
                                        class="text-xs rounded border px-2 py-1"
                                    >
                                        <option value="0">Paragraph</option>
                                        <option value="1">Heading 1</option>
                                        <option value="2">Heading 2</option>
                                        <option value="3">Heading 3</option>
                                    </select>
                                </div>

                                <div class="flex gap-2 ml-2">
                                    <button type="button" @click="toggleBold(completeInfoEditor)"
                                            :class="{'bg-gray-200': isActive(completeInfoEditor,'bold')}"
                                            class="px-2 py-1 rounded border text-xs">B</button>

                                    <button type="button" @click="toggleItalic(completeInfoEditor)"
                                            :class="{'bg-gray-200': isActive(completeInfoEditor,'italic')}"
                                            class="px-2 py-1 rounded border text-xs">I</button>

                                    <button type="button" @click="toggleStrike(completeInfoEditor)"
                                            :class="{'bg-gray-200': isActive(completeInfoEditor,'strike')}"
                                            class="px-2 py-1 rounded border text-xs line-through">S</button>

                                    <button type="button" @click="toggleCode(completeInfoEditor)"
                                            :class="{'bg-gray-200': isActive(completeInfoEditor,'code')}"
                                            class="px-2 py-1 rounded border text-xs">inline code</button>

                                    <button type="button" @click="toggleCodeBlock(completeInfoEditor)"
                                            :class="{'bg-gray-200': isActive(completeInfoEditor,'codeBlock')}"
                                            class="px-2 py-1 rounded border text-xs">code block</button>

                                    <button type="button" @click="toggleBlockquote(completeInfoEditor)"
                                            :class="{'bg-gray-200': isActive(completeInfoEditor,'blockquote')}"
                                            class="px-2 py-1 rounded border text-xs">❝</button>

                                    <button type="button" @click="toggleBulletList(completeInfoEditor)"
                                            :class="{'bg-gray-200': isActive(completeInfoEditor,'bulletList')}"
                                            class="px-2 py-1 rounded border text-xs">• list</button>

                                    <button type="button" @click="toggleOrderedList(completeInfoEditor)"
                                            :class="{'bg-gray-200': isActive(completeInfoEditor,'orderedList')}"
                                            class="px-2 py-1 rounded border text-xs">1. list</button>

                                    <button type="button" @click="insertHorizontalRule(completeInfoEditor)"
                                            class="px-2 py-1 rounded border text-xs">—</button>
                                </div>

                                <div class="ml-auto flex gap-2">
                                    <button type="button" @click="undo(completeInfoEditor)" class="px-2 py-1 rounded border text-xs">↶</button>
                                    <button type="button" @click="redo(completeInfoEditor)" class="px-2 py-1 rounded border text-xs">↷</button>
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

                    <!-- keep the rest fields unchanged -->
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

                    <div class="flex items-center gap-3">
                        <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Создать</button>
                        <Link :href="route('cabinet.quests.index')" class="text-sm text-gray-600 hover:text-gray-900">Отмена</Link>
                    </div>

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
