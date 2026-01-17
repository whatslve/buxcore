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
    descriptionEditor?.destroy()
    completeInfoEditor?.destroy()
})

/* -------------------- HELPERS -------------------- */

const btn = (active) =>
    `editor-btn ${active ? 'editor-btn--active' : ''}`

const cmd = (ed, fn) => ed && fn(ed.chain().focus()).run()
</script>

<template>
    <Head title="Создать задание" />

    <DashboardLayout>
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-lg font-semibold">Создать задание</h1>
            <Link
                :href="route('cabinet.quests.index')"
                class="rounded-md border px-3 py-1.5 text-sm hover:bg-gray-100"
            >
                ← Назад
            </Link>
        </div>

        <div class="bg-white rounded-lg shadow">
            <div class="max-w-3xl mx-auto py-8">
                <form
                    @submit.prevent="form.post(route('cabinet.quests.store'))"
                    class="space-y-6"
                >

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

                    <!-- EDITOR TEMPLATE -->
                    <template v-for="(editor, key) in [
            { label: 'Описание', model: 'description', ed: descriptionEditor },
            { label: 'Инструкция для пользователя', model: 'complete_info', ed: completeInfoEditor }
          ]" :key="key">

                        <div>
                            <label class="label mb-2">{{ editor.label }}</label>

                            <div class="editor-shell">
                                <!-- TOOLBAR -->
                                <div class="editor-toolbar">
                                    <select
                                        class="editor-select"
                                        @change="e => {
                      const v = +e.target.value
                      v === 0
                        ? cmd(editor.ed, c => c.setParagraph())
                        : cmd(editor.ed, c => c.toggleHeading({ level: v }))
                    }"
                                    >
                                        <option value="0">Paragraph</option>
                                        <option value="1">Heading 1</option>
                                        <option value="2">Heading 2</option>
                                        <option value="3">Heading 3</option>
                                    </select>

                                    <button :class="btn(editor.ed?.isActive('bold'))" @click.prevent="cmd(editor.ed, c => c.toggleBold())">B</button>
                                    <button :class="btn(editor.ed?.isActive('italic'))" @click.prevent="cmd(editor.ed, c => c.toggleItalic())">I</button>
                                    <button :class="btn(editor.ed?.isActive('strike'))" @click.prevent="cmd(editor.ed, c => c.toggleStrike())">S</button>
                                    <button :class="btn(editor.ed?.isActive('code'))" @click.prevent="cmd(editor.ed, c => c.toggleCode())">{ }</button>

                                    <span class="editor-sep" />

                                    <button :class="btn(editor.ed?.isActive('bulletList'))" @click.prevent="cmd(editor.ed, c => c.toggleBulletList())">•</button>
                                    <button :class="btn(editor.ed?.isActive('orderedList'))" @click.prevent="cmd(editor.ed, c => c.toggleOrderedList())">1.</button>
                                    <button :class="btn(editor.ed?.isActive('blockquote'))" @click.prevent="cmd(editor.ed, c => c.toggleBlockquote())">❝</button>

                                    <span class="editor-sep" />

                                    <button class="editor-btn" @click.prevent="cmd(editor.ed, c => c.undo())">↶</button>
                                    <button class="editor-btn" @click.prevent="cmd(editor.ed, c => c.redo())">↷</button>
                                </div>

                                <!-- CONTENT -->
                                <EditorContent
                                    :editor="editor.ed"
                                    class="editor-content"
                                />
                            </div>
                        </div>
                    </template>

                    <!-- REST (UNCHANGED) -->
                    <div>
                        <label class="label">Тип проверки</label>
                        <select v-model="form.check_type" class="input">
                            <option value="manual">Ручная</option>
                            <option value="automatic">Автоматическая</option>
                        </select>
                    </div>

                    <div class="flex gap-3">
                        <button class="btn-primary">Создать</button>
                        <Link :href="route('cabinet.quests.index')" class="btn-muted">Отмена</Link>
                    </div>

                </form>
            </div>
        </div>
    </DashboardLayout>
</template>

<style>
/* ---------- COMMON ---------- */
.label { font-size: 14px; font-weight: 500; color: #374151; }
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
