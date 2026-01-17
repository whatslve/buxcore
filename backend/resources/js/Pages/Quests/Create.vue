<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import { onBeforeUnmount } from 'vue'

/* -------------------- FORM -------------------- */
const form = useForm({
    name: '',
    type: '',
    description: '',
    complete_info: '',

    // limits / targeting / payment-ish
    quest_type_id: null,
    daily_limit: 0,
    repeat_after_hours: null, // minutes or hours per your logic
    execution_time_hours: 120,
    min_experience: 0,
    white_list_only: false,
    allow_parallel: false,

    // flags
    is_adult: false,
    is_soft_install: false,
    show_screenshot_help: false,
    check_is_proofed_email: false,
    check_is_proofed_phone: false,

    // referral / start
    referral_filter: 'all',
    start_at: null,
})

/* -------------------- EDITORS -------------------- */
const baseExtensions = [
    StarterKit.configure({
        history: true,
        heading: { levels: [1, 2, 3] },
    }),
]

const descriptionEditor = useEditor({
    extensions: baseExtensions,
    content: '',
    onUpdate: ({ editor }) => (form.description = editor.getHTML()),
})

const completeInfoEditor = useEditor({
    extensions: baseExtensions,
    content: '',
    onUpdate: ({ editor }) => (form.complete_info = editor.getHTML()),
})

onBeforeUnmount(() => {
    try { descriptionEditor?.destroy() } catch {}
    try { descriptionEditor?.value?.destroy() } catch {}
    try { completeInfoEditor?.destroy() } catch {}
    try { completeInfoEditor?.value?.destroy() } catch {}
})

/* -------------------- HELPERS -------------------- */
// unwrap ref or return instance
function getEditor(ed) {
    return ed?.value ?? ed ?? null
}

// run a chain safely: pass function that receives chain (c) and calls methods, must include .run()
function runChain(ed, fn) {
    const e = getEditor(ed)
    if (!e || !e.chain) return
    try {
        fn(e.chain().focus())
    } catch (err) {
        // ignore
    }
}

// safe isActive
function isActiveSafe(ed, name, opts = {}) {
    const e = getEditor(ed)
    if (!e) return false
    try { return e.isActive(name, opts) } catch { return false }
}

/* toolbar actions wrappers */
const toggleBold = (ed) => runChain(ed, c => c.toggleBold().run())
const toggleItalic = (ed) => runChain(ed, c => c.toggleItalic().run())
const toggleStrike = (ed) => runChain(ed, c => c.toggleStrike().run())
const toggleCode = (ed) => runChain(ed, c => c.toggleCode().run())
const toggleCodeBlock = (ed) => runChain(ed, c => c.toggleCodeBlock().run())
const toggleBlockquote = (ed) => runChain(ed, c => c.toggleBlockquote().run())
const toggleBulletList = (ed) => runChain(ed, c => c.toggleBulletList().run())
const toggleOrderedList = (ed) => runChain(ed, c => c.toggleOrderedList().run())
const setHeading = (ed, lvl) => runChain(ed, c => c.toggleHeading({ level: lvl }).run())
const setParagraph = (ed) => runChain(ed, c => c.setParagraph().run())
const undo = (ed) => runChain(ed, c => c.undo().run())
const redo = (ed) => runChain(ed, c => c.redo().run())
const insertHorizontalRule = (ed) => runChain(ed, c => c.setHorizontalRule().run()) // may be no-op in some versions

</script>

<template>
    <Head title="Создать задание" />
    <DashboardLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-lg font-semibold">Создать задание</h1>
            <Link :href="route('cabinet.quests.index')" class="rounded-md border px-3 py-1.5 text-sm hover:bg-gray-100">← Назад</Link>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="max-w-3xl mx-auto py-8">
                <form @submit.prevent="form.post(route('cabinet.quests.store'))" class="space-y-6">

                    <!-- Название -->
                    <div>
                        <label class="label">Название</label>
                        <input v-model="form.name" class="input" />
                    </div>

                    <!-- Тип -->
                    <div>
                        <label class="label">Тип</label>
                        <input v-model="form.type" class="input" />
                    </div>

                    <!-- DESCRIPTION -->
                    <div>
                        <label class="label mb-2">Описание</label>
                        <div class="editor-shell">
                            <div class="editor-toolbar">
                                <select class="editor-select" @change="e => { const v=+e.target.value; v===0 ? setParagraph(descriptionEditor) : setHeading(descriptionEditor, v) }">
                                    <option value="0">Paragraph</option>
                                    <option value="1">Heading 1</option>
                                    <option value="2">Heading 2</option>
                                    <option value="3">Heading 3</option>
                                </select>

                                <button :class="['editor-btn', isActiveSafe(descriptionEditor,'bold') ? 'editor-btn--active' : '']" @click.prevent="toggleBold(descriptionEditor)">B</button>
                                <button :class="['editor-btn', isActiveSafe(descriptionEditor,'italic') ? 'editor-btn--active' : '']" @click.prevent="toggleItalic(descriptionEditor)">I</button>
                                <button :class="['editor-btn', isActiveSafe(descriptionEditor,'strike') ? 'editor-btn--active' : '']" @click.prevent="toggleStrike(descriptionEditor)">S</button>
                                <button :class="['editor-btn', isActiveSafe(descriptionEditor,'code') ? 'editor-btn--active' : '']" @click.prevent="toggleCode(descriptionEditor)">{ }</button>

                                <span class="editor-sep"></span>

                                <button :class="['editor-btn', isActiveSafe(descriptionEditor,'bulletList') ? 'editor-btn--active' : '']" @click.prevent="toggleBulletList(descriptionEditor)">•</button>
                                <button :class="['editor-btn', isActiveSafe(descriptionEditor,'orderedList') ? 'editor-btn--active' : '']" @click.prevent="toggleOrderedList(descriptionEditor)">1.</button>
                                <button :class="['editor-btn', isActiveSafe(descriptionEditor,'blockquote') ? 'editor-btn--active' : '']" @click.prevent="toggleBlockquote(descriptionEditor)">❝</button>

                                <span class="editor-sep"></span>

                                <button class="editor-btn" @click.prevent="undo(descriptionEditor)">↶</button>
                                <button class="editor-btn" @click.prevent="redo(descriptionEditor)">↷</button>

                                <div class="ml-auto"></div>
                            </div>

                            <div class="editor-content">
                                <div v-if="descriptionEditor">
                                    <EditorContent :editor="descriptionEditor" class="prose max-w-none" />
                                </div>
                                <div v-else class="text-sm text-gray-400 py-6">Инициализация редактора...</div>
                            </div>
                        </div>
                    </div>

                    <!-- COMPLETE_INFO -->
                    <div>
                        <label class="label mb-2">Инструкция для пользователя</label>
                        <div class="editor-shell">
                            <div class="editor-toolbar">
                                <select class="editor-select" @change="e => { const v=+e.target.value; v===0 ? setParagraph(completeInfoEditor) : setHeading(completeInfoEditor, v) }">
                                    <option value="0">Paragraph</option>
                                    <option value="1">Heading 1</option>
                                    <option value="2">Heading 2</option>
                                    <option value="3">Heading 3</option>
                                </select>

                                <button :class="['editor-btn', isActiveSafe(completeInfoEditor,'bold') ? 'editor-btn--active' : '']" @click.prevent="toggleBold(completeInfoEditor)">B</button>
                                <button :class="['editor-btn', isActiveSafe(completeInfoEditor,'italic') ? 'editor-btn--active' : '']" @click.prevent="toggleItalic(completeInfoEditor)">I</button>
                                <button :class="['editor-btn', isActiveSafe(completeInfoEditor,'strike') ? 'editor-btn--active' : '']" @click.prevent="toggleStrike(completeInfoEditor)">S</button>
                                <button :class="['editor-btn', isActiveSafe(completeInfoEditor,'code') ? 'editor-btn--active' : '']" @click.prevent="toggleCode(completeInfoEditor)">{ }</button>

                                <span class="editor-sep"></span>

                                <button :class="['editor-btn', isActiveSafe(completeInfoEditor,'bulletList') ? 'editor-btn--active' : '']" @click.prevent="toggleBulletList(completeInfoEditor)">•</button>
                                <button :class="['editor-btn', isActiveSafe(completeInfoEditor,'orderedList') ? 'editor-btn--active' : '']" @click.prevent="toggleOrderedList(completeInfoEditor)">1.</button>
                                <button :class="['editor-btn', isActiveSafe(completeInfoEditor,'blockquote') ? 'editor-btn--active' : '']" @click.prevent="toggleBlockquote(completeInfoEditor)">❝</button>

                                <span class="editor-sep"></span>

                                <button class="editor-btn" @click.prevent="undo(completeInfoEditor)">↶</button>
                                <button class="editor-btn" @click.prevent="redo(completeInfoEditor)">↷</button>

                                <div class="ml-auto"></div>
                            </div>

                            <div class="editor-content">
                                <div v-if="completeInfoEditor">
                                    <EditorContent :editor="completeInfoEditor" class="prose max-w-none" />
                                </div>
                                <div v-else class="text-sm text-gray-400 py-6">Инициализация редактора...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Limits & flags restored -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="label">Макс в сутки</label>
                            <input v-model.number="form.daily_limit" type="number" min="0" class="input" />
                        </div>
                        <div>
                            <label class="label">Повтор через (ч)</label>
                            <select v-model.number="form.repeat_after_hours" class="input">
                                <option :value="null">Нет</option>
                                <option :value="1">1</option>
                                <option :value="5">5</option>
                                <option :value="24">24</option>
                            </select>
                        </div>

                        <div>
                            <label class="label">Время на выполнение (ч)</label>
                            <input v-model.number="form.execution_time_hours" type="number" min="1" class="input" />
                        </div>

                        <div>
                            <label class="label">Мин. опыт</label>
                            <input v-model.number="form.min_experience" type="number" min="0" class="input" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <label class="flex items-center gap-2"><input type="checkbox" v-model="form.white_list_only" /> Только белый список</label>
                        <label class="flex items-center gap-2"><input type="checkbox" v-model="form.allow_parallel" /> Разрешить параллельное</label>
                        <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_adult" /> Для взрослых</label>
                        <label class="flex items-center gap-2"><input type="checkbox" v-model="form.is_soft_install" /> Требует ПО</label>
                        <label class="flex items-center gap-2"><input type="checkbox" v-model="form.show_screenshot_help" /> Помощь по скриншотам</label>
                        <label class="flex items-center gap-2"><input type="checkbox" v-model="form.check_is_proofed_email" /> Проверенный Email</label>
                        <label class="flex items-center gap-2"><input type="checkbox" v-model="form.check_is_proofed_phone" /> Проверенный телефон</label>
                    </div>

                    <div>
                        <label class="label">Реферальный фильтр</label>
                        <select v-model="form.referral_filter" class="input">
                            <option value="all">Показывать всем</option>
                            <option value="my_referrals">Мои рефералы</option>
                            <option value="no_refer">Не показывать рефералам</option>
                        </select>
                    </div>

                    <div>
                        <label class="label">Таймер запуска</label>
                        <input type="datetime-local" v-model="form.start_at" class="input" />
                    </div>

                    <!-- actions -->
                    <div class="flex items-center gap-3">
                        <button class="btn-primary" type="submit">Создать</button>
                        <Link :href="route('cabinet.quests.index')" class="btn-muted">Отмена</Link>
                    </div>

                    <!-- debug (убрать когда всё ок) -->
                    <pre class="bg-gray-50 p-2 text-xs mt-2 overflow-auto">{{ form }}</pre>

                </form>
            </div>
        </div>
    </DashboardLayout>
</template>

<style>
/* ---------- COMMON ---------- */
.label { font-size: 14px; font-weight: 500; color: #374151; display:block; margin-bottom:6px; }
.input {
    width: 100%;
    border-radius: 6px;
    border: 1px solid #d1d5db;
    padding: 8px 10px;
}
.btn-primary {
    background: #4f46e5;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
}
.btn-muted { color: #6b7280; }

/* ---------- EDITOR ---------- */
.editor-shell {
    border: 1px solid #d1d5db;
    border-radius: 8px;
    overflow: hidden;
}
.editor-toolbar {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}
.editor-btn {
    border: 1px solid #d1d5db;
    background: white;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 13px;
    cursor: pointer;
}
.editor-btn--active {
    background: #e5e7eb;
    font-weight: 600;
}
.editor-select {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 4px 6px;
    font-size: 13px;
}
.editor-sep {
    width: 1px;
    height: 18px;
    background: #d1d5db;
    margin: 0 4px;
}
.editor-content {
    padding: 14px;
    min-height: 160px;
}
.ProseMirror { outline: none; }
</style>
