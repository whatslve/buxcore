<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/Layouts/DashboardLayout.vue'
import { useEditor, EditorContent } from '@tiptap/vue-3'
import StarterKit from '@tiptap/starter-kit'
import { onBeforeUnmount, computed } from 'vue'

/* ---------------- FORM ---------------- */

const form = useForm({
    name: '',
    type: '',
    description: '',
    complete_info: '',
    check_type: 'manual',
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
    onUpdate: ({ editor }) => {
        form.description = editor.getHTML()
    },
})

const completeInfoEditor = useEditor({
    extensions,
    content: '',
    onUpdate: ({ editor }) => {
        form.complete_info = editor.getHTML()
    },
})

onBeforeUnmount(() => {
    descriptionEditor.value?.destroy()
    completeInfoEditor.value?.destroy()
})

/* ---------------- HELPERS ---------------- */

const isReady = ed => computed(() => !!ed.value?.view)

function run(ed, command) {
    const editor = ed.value
    if (!editor || !editor.view) return
    command(editor.chain().focus()).run()
}

function btn(active) {
    return `editor-btn ${active ? 'editor-btn--active' : ''}`
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

                <!-- EDITORS -->
                <template
                    v-for="(editor, i) in [
            { label: 'Описание', ed: descriptionEditor },
            { label: 'Инструкция', ed: completeInfoEditor },
          ]"
                    :key="i"
                >
                    <div>
                        <label class="label mb-2">{{ editor.label }}</label>

                        <div class="editor-shell">

                            <!-- TOOLBAR -->
                            <div class="editor-toolbar">
                                <select
                                    class="editor-select"
                                    :disabled="!editor.ed?.value?.view"
                                    @change="e => {
                    const v = +e.target.value
                    v === 0
                      ? run(editor.ed, c => c.setParagraph())
                      : run(editor.ed, c => c.toggleHeading({ level: v }))
                  }"
                                >
                                    <option value="0">Text</option>
                                    <option value="1">H1</option>
                                    <option value="2">H2</option>
                                    <option value="3">H3</option>
                                </select>

                                <button :class="btn(editor.ed?.value?.isActive('bold'))"
                                        @click.prevent="run(editor.ed, c => c.toggleBold())">B</button>

                                <button :class="btn(editor.ed?.value?.isActive('italic'))"
                                        @click.prevent="run(editor.ed, c => c.toggleItalic())">I</button>

                                <button :class="btn(editor.ed?.value?.isActive('strike'))"
                                        @click.prevent="run(editor.ed, c => c.toggleStrike())">S</button>

                                <span class="editor-sep" />

                                <button :class="btn(editor.ed?.value?.isActive('bulletList'))"
                                        @click.prevent="run(editor.ed, c => c.toggleBulletList())">•</button>

                                <button :class="btn(editor.ed?.value?.isActive('orderedList'))"
                                        @click.prevent="run(editor.ed, c => c.toggleOrderedList())">1.</button>

                                <button :class="btn(editor.ed?.value?.isActive('blockquote'))"
                                        @click.prevent="run(editor.ed, c => c.toggleBlockquote())">❝</button>

                                <span class="editor-sep" />

                                <button class="editor-btn"
                                        @click.prevent="run(editor.ed, c => c.undo())">↶</button>

                                <button class="editor-btn"
                                        @click.prevent="run(editor.ed, c => c.redo())">↷</button>
                            </div>

                            <!-- CONTENT -->
                            <EditorContent
                                :editor="editor.ed"
                                class="editor-content"
                            />
                        </div>
                    </div>
                </template>

                <!-- CHECK TYPE -->
                <div>
                    <label class="label">Тип проверки</label>
                    <select v-model="form.check_type" class="input">
                        <option value="manual">Ручная</option>
                        <option value="automatic">Автоматическая</option>
                    </select>
                </div>

                <!-- ACTIONS -->
                <div class="flex gap-3">
                    <button class="btn-primary">Создать</button>
                    <Link class="btn-muted" :href="route('cabinet.quests.index')">Отмена</Link>
                </div>

            </form>
        </div>
    </DashboardLayout>
</template>

<style>
/* ---------- BASE ---------- */
.label { font-size: 14px; font-weight: 500; }
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
.editor-card {
    background: #fff;
    padding: 32px;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0,0,0,.08);
}

.editor-shell {
    border: 1px solid #d1d5db;
    border-radius: 10px;
    overflow: hidden;
}

.editor-toolbar {
    display: flex;
    gap: 6px;
    padding: 8px;
    background: #f9fafb;
    border-bottom: 1px solid #e5e7eb;
}

.editor-btn {
    padding: 4px 8px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: #fff;
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
}

.editor-sep {
    width: 1px;
    background: #d1d5db;
    margin: 0 4px;
}

.editor-content {
    padding: 16px;
    min-height: 160px;
}

.ProseMirror {
    outline: none;
}
</style>
