<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import axios from 'axios'
import RecaptchaV2 from '@/Components/GRecaptcha.vue'

const props = defineProps({
    link: { type: String, required: true },
    time: { type: Number, required: true },
    visit_id: { type: Number, required: true }
})

/** ENV */
const API_BASE = import.meta.env.VITE_API_BASE || 'http://192.168.1.135:80'
const SITE_KEY = import.meta.env.VITE_G_RECAPTCHA_SITE_KEY // <-- ваш site key для v2

/** Таймер */
const timer = ref(props.time)
const showCaptcha = ref(false)

/** reCAPTCHA */
const recaptchaToken = ref('')
const captchaRef = ref(null)

/** UI состояния */
const isSubmitting = ref(false)
const submitError = ref('')
const submitOk = ref(false)

onMounted(() => {
    const interval = setInterval(async () => {
        if (timer.value > 0) {
            timer.value--
        } else {
            clearInterval(interval)

            // Настроим axios и (если нужно) инициализируем sanctum cookie
            axios.defaults.baseURL = API_BASE
            try {
                // Опционально, если у вас Laravel Sanctum
                await axios.get('/sanctum/csrf-cookie')
            } catch (e) {
                // не критично, если не используете Sanctum
            }

            // Показываем капчу
            showCaptcha.value = true
        }
    }, 1000)
})

/** Как только капча пройдена — авто-отправка на бэкенд */
function onVerified(token) {
    recaptchaToken.value = token
    submitError.value = ''
    submitToBackend()
}

function onExpired() {
    recaptchaToken.value = ''
}

function onError() {
    recaptchaToken.value = ''
    submitError.value = 'Ошибка проверки reCAPTCHA. Попробуйте ещё раз.'
}

/** Отправка на бек: /api/test/check */
async function submitToBackend() {
    if (!recaptchaToken.value) return
    isSubmitting.value = true
    submitError.value = ''
    submitOk.value = false

    try {
        const payload = {
            token: recaptchaToken.value,
            visit_id: props.visit_id
        }
        const { data } = await axios.post('/api/visit/finish', payload)

        // Успешно
        submitOk.value = true
        showCaptcha.value = false
        // при необходимости — сбросить/перерендерить капчу:
        try { captchaRef.value?.reset?.() } catch {}

        // можете дальше делать переход/логирование и т.п.
        // console.log('Ответ бэка:', data)

    } catch (err) {
        submitError.value =
            err?.response?.data?.message ||
            err?.message ||
            'Не удалось отправить запрос'
        // Позволим пользователю повторить попытку, капчу обычно придётся решить снова
        try { captchaRef.value?.reset?.() } catch {}
        recaptchaToken.value = ''
    } finally {
        isSubmitting.value = false
    }
}
</script>

<template>
    <Head title="Посещение" />
    <div class="w-screen h-screen relative">
        <!-- Таймер сверху -->
        <div
            v-if="timer > 0"
            class="absolute top-4 left-1/2 -translate-x-1/2 z-50 bg-black bg-opacity-70 text-white px-4 py-2 rounded-lg text-lg font-bold"
        >
            {{ timer }} сек
        </div>

        <!-- Основной контент -->
        <iframe
            :src="link"
            class="w-full h-full border-0"
            allowfullscreen
        ></iframe>

        <!-- Оверлей с капчей после таймера -->
        <div
            v-if="showCaptcha"
            class="absolute inset-0 z-50 bg-black/60 flex items-center justify-center p-4"
        >
            <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg">
                <h2 class="text-xl font-semibold mb-4">Подтвердите, что вы не робот</h2>

                <RecaptchaV2
                    ref="captchaRef"
                    :siteKey="SITE_KEY"
                    theme="light"
                    @verified="onVerified"
                    @expired="onExpired"
                    @error="onError"
                />

                <div class="mt-4 text-sm text-red-600" v-if="submitError">
                    {{ submitError }}
                </div>
                <div class="mt-4 text-sm text-green-600" v-if="submitOk">
                    Проверка пройдена — запрос отправлен.
                </div>

                <div class="mt-4 text-sm text-gray-500" v-if="isSubmitting">
                    Отправка данных…
                </div>
            </div>
        </div>
    </div>
</template>
