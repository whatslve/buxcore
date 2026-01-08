<script setup>
import { Head } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import axios from 'axios'
import RecaptchaV2 from '@/Components/GRecaptcha.vue'

/** props от Inertia */
const props = defineProps({
    link: { type: String, required: true },
    time: { type: Number, required: true },
    visit_id: { type: Number, required: true },
})

/** reCAPTCHA site key из Vite */
const siteKey = import.meta.env.VITE_G_RECAPTCHA_SITE_KEY || ''
console.log(siteKey);
/** Таймер */
const timer = ref(props.time)
const showCaptcha = ref(false)

/** reCAPTCHA */
const recaptchaToken = ref('')
const captchaRef = ref(null)

/** UI */
const isSubmitting = ref(false)
const submitError = ref('')
const submitOk = ref(false)

/** Запуск таймера */
onMounted(() => {
    const interval = setInterval(() => {
        if (timer.value > 0) {
            timer.value--
        } else {
            clearInterval(interval)
            showCaptcha.value = true
        }
    }, 1000)
})

/** reCAPTCHA callbacks */
function onVerified(token) {
    recaptchaToken.value = token
    submitToBackend()
}

function onExpired() {
    recaptchaToken.value = ''
}

function onError() {
    submitError.value = 'Ошибка reCAPTCHA. Попробуйте ещё раз.'
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
        captchaRef.value?.reset?.()
    } catch (err) {
        submitError.value =
            err?.response?.data?.message || 'Не удалось отправить запрос'
        captchaRef.value?.reset?.()
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

        <!-- reCAPTCHA -->
        <div
            v-if="showCaptcha && siteKey"
            class="absolute inset-0 z-50 bg-black/60 flex items-center justify-center p-4"
        >
            <div class="bg-white rounded-xl p-6 w-full max-w-md shadow-lg">
                <h2 class="text-xl font-semibold mb-4">
                    Подтвердите, что вы не робот
                </h2>

                <RecaptchaV2
                    ref="captchaRef"
                    :siteKey="siteKey"
                    @verified="onVerified"
                    @expired="onExpired"
                    @error="onError"
                />

                <p v-if="submitError" class="mt-4 text-sm text-red-600">
                    {{ submitError }}
                </p>
                <p v-if="submitOk" class="mt-4 text-sm text-green-600">
                    Проверка пройдена
                </p>
                <p v-if="isSubmitting" class="mt-4 text-sm text-gray-500">
                    Отправка…
                </p>
            </div>
        </div>

        <!-- если вдруг ключа нет -->
        <div
            v-if="showCaptcha && !siteKey"
            class="absolute inset-0 z-50 bg-black/60 flex items-center justify-center text-white"
        >
            reCAPTCHA не настроена
        </div>
    </div>
</template>
