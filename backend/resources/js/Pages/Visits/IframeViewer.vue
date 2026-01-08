<script setup>
import { ref, onMounted } from 'vue'
import { Head } from '@inertiajs/vue3'
import axios from 'axios'

/** Props от Inertia */
const props = defineProps({
    link: { type: String, required: true },
    time: { type: Number, required: true },
    visit_id: { type: Number, required: true },
})

/** Таймер */
const timer = ref(props.time)
const showCaptcha = ref(false)

/** reCAPTCHA v3 */
const siteKey = import.meta.env.VITE_G_RECAPTCHA_SITE_KEY || ''
const recaptchaToken = ref('')

/** UI */
const isSubmitting = ref(false)
const submitError = ref('')
const submitOk = ref(false)

/** Таймер запускается при монтировании */
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

/** Вызов reCAPTCHA v3 */
async function executeRecaptcha() {
    if (!siteKey) {
        submitError.value = 'reCAPTCHA не настроена'
        return
    }

    try {
        recaptchaToken.value = await window.grecaptcha.execute(siteKey, { action: 'visit' })
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
        submitError.value = err?.response?.data?.message || 'Не удалось отправить запрос'
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
        ></iframe>

        <!-- reCAPTCHA overlay -->
        <div
            v-if="showCaptcha && siteKey"
            class="absolute inset-0 z-50 bg-black/60 flex items-center justify-center p-4"
        >
            <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg text-center">
                <h2 class="text-xl font-semibold mb-4">Подтвердите, что вы не робот</h2>
                <p v-if="submitError" class="mt-4 text-sm text-red-600">{{ submitError }}</p>
                <p v-if="submitOk" class="mt-4 text-sm text-green-600">Проверка пройдена</p>
                <p v-if="isSubmitting" class="mt-4 text-sm text-gray-500">Отправка…</p>
            </div>
        </div>

        <div
            v-if="showCaptcha && !siteKey"
            class="absolute inset-0 z-50 bg-black/60 flex items-center justify-center text-white"
        >
            reCAPTCHA не настроена
        </div>
    </div>
</template>

<script src="https://www.google.com/recaptcha/api.js?render=YOUR_SITE_KEY"></script>
