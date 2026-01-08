<script setup>
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import axios from 'axios'

const props = defineProps({
    link: { type: String, required: true },
    time: { type: Number, required: true },
    visit_id: { type: Number, required: true },
})

const timer = ref(props.time)
const showCaptcha = ref(false)
const recaptchaToken = ref('')
const isSubmitting = ref(false)
const submitError = ref('')
const submitOk = ref(false)
const siteKey = import.meta.env.VITE_G_RECAPTCHA_SITE_KEY || ''

// Таймер
onMounted(() => {
    const interval = setInterval(() => {
        if (timer.value > 0) timer.value--
        else {
            clearInterval(interval)
            showCaptcha.value = true
            loadRecaptcha()
        }
    }, 1000)
})

// Динамическая загрузка скрипта
function loadRecaptcha() {
    if (!siteKey) {
        submitError.value = 'reCAPTCHA не настроена'
        return
    }

    if (!window.grecaptcha) {
        const script = document.createElement('script')
        script.src = `https://www.google.com/recaptcha/api.js?render=${siteKey}`
        script.async = true
        script.defer = true
        script.onload = () => executeRecaptcha()
        document.head.appendChild(script)
    } else {
        executeRecaptcha()
    }
}

// Генерация токена v3
async function executeRecaptcha() {
    try {
        await window.grecaptcha.ready()
        const token = await window.grecaptcha.execute(siteKey, { action: 'visit' })
        if (!token) throw new Error('Пустой токен reCAPTCHA')
        recaptchaToken.value = token
        submitToBackend()
    } catch (err) {
        console.error('reCAPTCHA error', err)
        submitError.value = 'Ошибка reCAPTCHA. Попробуйте ещё раз.'
    }
}

// Отправка на бэкенд
async function submitToBackend() {
    if (!recaptchaToken.value) return
    isSubmitting.value = true
    submitError.value = ''
    submitOk.value = false

    try {
        const res = await axios.post(`/visits-records/${props.visit_id}/finish`, {
            token: recaptchaToken.value
        })
        submitOk.value = true
        showCaptcha.value = false
    } catch (err) {
        submitError.value = err?.response?.data?.message || 'Не удалось отправить запрос'
        recaptchaToken.value = ''
    } finally {
        isSubmitting.value = false
    }
}

// Повторная проверка (если ошибка)
function retryCaptcha() {
    submitError.value = ''
    recaptchaToken.value = ''
    executeRecaptcha()
}
</script>

<template>
    <Head title="Посещение" />
    <div class="w-screen h-screen relative">
        <!-- Таймер -->
        <div v-if="timer > 0" class="absolute top-4 left-1/2 -translate-x-1/2 z-50 bg-black/70 text-white px-4 py-2 rounded-lg font-bold pointer-events-none">
            {{ timer }} сек
        </div>

        <!-- Основной iframe -->
        <iframe :src="link" class="w-full h-full border-0" allowfullscreen></iframe>

        <!-- reCAPTCHA overlay -->
        <div v-if="showCaptcha && siteKey" class="absolute inset-0 z-50 bg-black/60 flex items-center justify-center p-4">
            <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg text-center">
                <h2 class="text-xl font-semibold mb-4">Подтвердите, что вы не робот</h2>

                <p v-if="submitError" class="mt-4 text-sm text-red-600">{{ submitError }}</p>
                <p v-if="submitOk" class="mt-4 text-sm text-green-600">Проверка пройдена</p>
                <p v-if="isSubmitting" class="mt-4 text-sm text-gray-500">Отправка…</p>

                <button
                    v-if="submitError && !isSubmitting"
                    @click="retryCaptcha"
                    class="mt-4 px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Повторить проверку
                </button>
            </div>
        </div>

        <!-- Overlay если ключ не настроен -->
        <div v-if="showCaptcha && !siteKey" class="absolute inset-0 z-50 bg-black/60 flex items-center justify-center text-white">
            reCAPTCHA не настроена
        </div>
    </div>
</template>
