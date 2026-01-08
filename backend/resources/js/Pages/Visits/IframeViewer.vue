<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import axios from 'axios'

/** props от Inertia */
const props = defineProps({
    link: { type: String, required: true },
    time: { type: Number, required: true },
    visit_id: { type: Number, required: true },
})

/** reCAPTCHA site key из Vite */
const siteKey = import.meta.env.VITE_G_RECAPTCHA_SITE_KEY || ''
console.log('Site Key:', siteKey)

/** Таймер */
const timer = ref(props.time)
const showCaptcha = ref(false)

/** reCAPTCHA token */
const recaptchaToken = ref('')

/** UI */
const isSubmitting = ref(false)
const submitError = ref('')
const submitOk = ref(false)

/** Таймер */
onMounted(() => {
    const interval = setInterval(() => {
        if (timer.value > 0) {
            timer.value--
        } else {
            clearInterval(interval)
            showCaptcha.value = true
            executeRecaptcha()
        }
    }, 1000)
})

/** Выполнение reCAPTCHA v3 */
async function executeRecaptcha() {
    if (!siteKey) {
        submitError.value = 'reCAPTCHA не настроена'
        return
    }

    if (!window.grecaptcha) {
        submitError.value = 'reCAPTCHA не загружена'
        return
    }

    try {
        const token = await window.grecaptcha.execute(siteKey, { action: 'visit' })
        recaptchaToken.value = token
        submitToBackend()
    } catch (err) {
        submitError.value = 'Ошибка reCAPTCHA. Попробуйте ещё раз.'
    }
}

/** Отправка на backend */
async function submitToBackend() {
    if (!recaptchaToken.value) return

    isSubmitting.value = true
    submitError.value = ''
    submitOk.value = false

    try {
        await axios.post('/visit/finish', {
            token: recaptchaToken.value,
            visit_id: props.visit_id,
        })

        submitOk.value = true
        showCaptcha.value = false
    } catch (err) {
        submitError.value =
            err?.response?.data?.message || 'Не удалось отправить запрос'
        recaptchaToken.value = ''
    } finally {
        isSubmitting.value = false
    }
}
</script>

<template>
    <Head title="Посещение" />

    <div class="w-screen h-screen relative">
        <!-- Таймер -->
        <div
            v-if="timer > 0"
            class="absolute top-4 left-1/2 -translate-x-1/2 z-50 bg-black/70 text-white px-4 py-2 rounded-lg font-bold pointer-events-none"
        >
            {{ timer }} сек
        </div>

        <!-- iframe -->
        <iframe
            :src="link"
            class="w-full h-full border-0"
            allowfullscreen
        />

        <!-- Статус reCAPTCHA -->
        <div
            v-if="showCaptcha && submitError"
            class="absolute inset-0 z-50 bg-black/60 flex items-center justify-center p-4 text-white"
        >
            {{ submitError }}
        </div>
        <div
            v-if="submitOk"
            class="absolute top-4 right-4 z-50 bg-green-600 text-white px-4 py-2 rounded-lg"
        >
            Проверка пройдена
        </div>
        <div
            v-if="isSubmitting"
            class="absolute top-4 right-4 z-50 bg-gray-600 text-white px-4 py-2 rounded-lg"
        >
            Отправка…
        </div>
    </div>
</template>

<!-- подключение скрипта reCAPTCHA v3 -->
<script src="https://www.google.com/recaptcha/api.js?render=<?= import.meta.env.VITE_G_RECAPTCHA_SITE_KEY ?>"></script>
