<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import { onBeforeUnmount } from 'vue'

/* ---------------- FORM ---------------- */

const form = useForm({
    name: '',
    type: '',
    description: '',
    complete_info: '',

    // limits / targeting / payment-ish (вернул поля)
    quest_type_id: null,
    daily_limit: 0,
    repeat_after_hours: null,
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

    // old ones kept minimal
    check_type: 'manual',
    link_selection_mode: 'sequential',
})

/* ---------------- EDITOR EXTENSIONS ---------------- */

const extensions = [
    StarterKit.configure({
        heading: { levels: [1, 2, 3] },
        history: true,
    }),
]

/* ---------------- EDITORS ---------------- */

const descriptionEditor = useEditor({
    extensions,
    content: '',
    onUpdate: ({ editor }) => (form.description = editor.getHTML()),
})

const completeInfoEditor = useEditor({
    extensions,
    content: '',
    onUpdate: ({ editor }) => (form.complete_info = editor.getHTML()),
})

onBeforeUnmount(() => {
    // useEditor returns a ref; always access .value
    if (descriptionEditor?.value?.destroy) descriptionEditor.value.destroy()
    if (completeInfoEditor?.value?.destroy) completeInfoEditor.value.destroy()
})

/* ---------------- HELPERS ---------------- */

// unwrap ref to Editor instance
function editorInstance(edRef) {
    if (!edRef) return null
    return edRef.value ?? edRef
}

// safe check for ready view
function isReady(edRef) {
    const ed = editorInstance(edRef)
    return !!(ed && ed.view)
}

// safe isActive
function isActiveSafe(edRef, name, opts = {}) {
    const ed = editorInstance(edRef)
    if (!ed) return false
    try {
        return ed.isActive(name, opts)
    } catch {
        return false
    }
}

// safe runner — pass a function that receives chain builder (c) and modifies it,
// e.g. c => c.toggleBold()
function runSafe(edRef, fn) {
    const ed = editorInstance(edRef)
    if (!ed || !ed.view) return
    try {
        const chain = ed.chain().focus()
        fn(chain)
        chain.run()
    } catch {
        // ignore runtime errors from chain if any
    }
}

function btnClass(active) {
    return active ? 'editor-btn editor-btn--active' : 'editor-btn'
}
</script>

<template>
    <Head title="Создать задание" />

    <DashboardLayout>
        <div class="mb-6 flex justify-between items-center">
            <h1 class="text-lg font-semibold">Создать задание</h1>
            <Link :href="route('cabinet.quests.index')" class="btn-muted">← Назад</Link>
        </div>

        <div class="editor-card">
            <form @submit.prevent="form.post(route('cabinet.quests.store'))" class="space-y-6">

                <!-- NAME -->
                <div>
                    <label class="label">Название</label>
                    <input v-model="form.name" class="input" />
                </div>

                <!-- TYPE -->
                <div>
                    <label class="label">Тип</label>
                    <input v-model="form.type" class="input" />
                </div>

                <!-- DESCRIPTION EDITOR -->
                <div>
                    <label class="label mb-2">Описание</label>
                    <div class="editor-shell">
                        <div class="editor-toolbar">
                            <select
                                class="editor-select"
                                :disabled="!isReady(descriptionEditor)"
                                @change="e => {
                  const v = +e.target.value
                  if (v === 0) runSafe(descriptionEditor, c => c.setParagraph())
                  else runSafe(descriptionEditor, c => c.toggleHeading({ level: v }))
                }"
                            >
                                <option value="0">Text</option>
                                <option value="1">H1</option>
                                <option value="2">H2</option>
                                <option value="3">H3</option>
                            </select>

                            <button
                                :class="btnClass(isActiveSafe(descriptionEditor,'bold'))"
                                @click.prevent="runSafe(descriptionEditor, c => c.toggleBold())"
                                :disabled="!isReady(descriptionEditor)"
                                type="button"
                            >B</button>

                            <button
                                :class="btnClass(isActiveSafe(descriptionEditor,'italic'))"
                                @click.prevent="runSafe(descriptionEditor, c => c.toggleItalic())"
                                :disabled="!isReady(descriptionEditor)"
                                type="button"
                            >I</button>

                            <button
                                :class="btnClass(isActiveSafe(descriptionEditor,'strike'))"
                                @click.prevent="runSafe(descriptionEditor, c => c.toggleStrike())"
                                :disabled="!isReady(descriptionEditor)"
                                type="button"
                            >S</button>

                            <button
                                :class="btnClass(isActiveSafe(descriptionEditor,'code'))"
                                @click.prevent="runSafe(descriptionEditor, c => c.toggleCode())"
                                :disabled="!isReady(descriptionEditor)"
                                type="button"
                            >{ }</button>

                            <span class="editor-sep" />

                            <button
                                :class="btnClass(isActiveSafe(descriptionEditor,'bulletList'))"
                                @click.prevent="runSafe(descriptionEditor, c => c.toggleBulletList())"
                                :disabled="!isReady(descriptionEditor)"
                                type="button"
                            >•</button>

                            <button
                                :class="btnClass(isActiveSafe(descriptionEditor,'orderedList'))"
                                @click.prevent="runSafe(descriptionEditor, c => c.toggleOrderedList())"
                                :disabled="!isReady(descriptionEditor)"
                                type="button"
                            >1.</button>

                            <button
                                :class="btnClass(isActiveSafe(descriptionEditor,'blockquote'))"
                                @click.prevent="runSafe(descriptionEditor, c => c.toggleBlockquote())"
                                :disabled="!isReady(descriptionEditor)"
                                type="button"
                            >❝</button>

                            <span class="editor-sep" />

                            <button class="editor-btn" @click.prevent="runSafe(descriptionEditor, c => c.undo())" :disabled="!isReady(descriptionEditor)" type="button">↶</button>
                            <button class="editor-btn" @click.prevent="runSafe(descriptionEditor, c => c.redo())" :disabled="!isReady(descriptionEditor)" type="button">↷</button>
                        </div>

                        <EditorContent :editor="descriptionEditor" class="editor-content" />
                    </div>
                </div>

                <!-- COMPLETE_INFO EDITOR -->
                <div>
                    <label class="label mb-2">Инструкция для пользователя</label>
                    <div class="editor-shell">
                        <div class="editor-toolbar">
                            <select
                                class="editor-select"
                                :disabled="!isReady(completeInfoEditor)"
                                @change="e => {
                  const v = +e.target.value
                  if (v === 0) runSafe(completeInfoEditor, c => c.setParagraph())
                  else runSafe(completeInfoEditor, c => c.toggleHeading({ level: v }))
                }"
                            >
                                <option value="0">Text</option>
                                <option value="1">H1</option>
                                <option value="2">H2</option>
                                <option value="3">H3</option>
                            </select>

                            <button :class="btnClass(isActiveSafe(completeInfoEditor,'bold'))" @click.prevent="runSafe(completeInfoEditor, c => c.toggleBold())" :disabled="!isReady(completeInfoEditor)" type="button">B</button>
                            <button :class="btnClass(isActiveSafe(completeInfoEditor,'italic'))" @click.prevent="runSafe(completeInfoEditor, c => c.toggleItalic())" :disabled="!isReady(completeInfoEditor)" type="button">I</button>
                            <button :class="btnClass(isActiveSafe(completeInfoEditor,'strike'))" @click.prevent="runSafe(completeInfoEditor, c => c.toggleStrike())" :disabled="!isReady(completeInfoEditor)" type="button">S</button>
                            <button :class="btnClass(isActiveSafe(completeInfoEditor,'code'))" @click.prevent="runSafe(completeInfoEditor, c => c.toggleCode())" :disabled="!isReady(completeInfoEditor)" type="button">{ }</button>

                            <span class="editor-sep" />

                            <button :class="btnClass(isActiveSafe(completeInfoEditor,'bulletList'))" @click.prevent="runSafe(completeInfoEditor, c => c.toggleBulletList())" :disabled="!isReady(completeInfoEditor)" type="button">•</button>

                            <button :class="btnClass(isActiveSafe(completeInfoEditor,'orderedList'))" @click.prevent="runSafe(completeInfoEditor, c => c.toggleOrderedList())" :disabled="!isReady(completeInfoEditor)" type="button">1.</button>

                            <button :class="btnClass(isActiveSafe(completeInfoEditor,'blockquote'))" @click.prevent="runSafe(completeInfoEditor, c => c.toggleBlockquote())" :disabled="!isReady(completeInfoEditor)" type="button">❝</button>

                            <span class="editor-sep" />

                            <button class="editor-btn" @click.prevent="runSafe(completeInfoEditor, c => c.undo())" :disabled="!isReady(completeInfoEditor)" type="button">↶</button>
                            <button class="editor-btn" @click.prevent="runSafe(completeInfoEditor, c => c.redo())" :disabled="!isReady(completeInfoEditor)" type="button">↷</button>
                        </div>

                        <EditorContent :editor="completeInfoEditor" class="editor-content" />
                    </div>
                </div>

                <!-- OTHER FIELDS (restored) -->
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
                </div>

                <div class="grid grid-cols-2 gap-4">
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

                <!-- ACTIONS -->
                <div class="flex items-center gap-3">
                    <button class="btn-primary" type="submit">Создать</button>
                    <Link :href="route('cabinet.quests.index')" class="btn-muted">Отмена</Link>
                </div>

                <!-- debug (удалить если не нужен) -->
                <pre class="bg-gray-50 p-2 text-xs mt-2 overflow-auto">{{ form }}</pre>
            </form>
        </div>
    </DashboardLayout>
</template>

<style>
/* ---------- BASE ---------- */
.label { font-size: 14px; font-weight: 500; display:block; margin-bottom:6px; }
.input {
    width: 100%;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    padding: 8px 10px;
}
.btn-primary {
    background: #4f46e5;
    color: #fff;
    padding: 8px 16px;
    border-radius: 6px;
}
.btn-muted { color: #6b7280; }

/* ---------- EDITOR ---------- */
.editor-card { background: #fff; padding: 24px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,.06); }
.editor-shell { border: 1px solid #d1d5db; border-radius: 8px; overflow: hidden; margin-bottom: 6px; }
.editor-toolbar { display:flex; gap:6px; padding:8px; background:#f9fafb; border-bottom:1px solid #e5e7eb; align-items:center; }
.editor-btn { padding:4px 8px; border:1px solid #d1d5db; border-radius:6px; background:#fff; cursor:pointer; }
.editor-btn[disabled] { opacity: .5; cursor: not-allowed; }
.editor-btn--active { background:#e5e7eb; font-weight:600; }
.editor-select { border:1px solid #d1d5db; border-radius:6px; padding:4px 6px; }
.editor-sep { width:1px; background:#d1d5db; height:20px; margin:0 6px; }
.editor-content { padding:14px; min-height:160px; }
.ProseMirror { outline:none; }
</style>
