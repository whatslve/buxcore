<script>
export default {
    name: "GRecaptcha"
}
</script>

<script setup>
import { onMounted, onBeforeUnmount, ref, defineEmits, defineExpose } from 'vue';

const props = defineProps({
    siteKey: { type: String, required: true },
    theme:   { type: String, default: 'light' },      // light | dark
    size:    { type: String, default: 'normal' },     // normal | compact
    tabindex:{ type: Number, default: 0 },
    hl:      { type: String, default: '' },           // язык (напр. 'ru'); по умолчанию авто
});

const emit = defineEmits(['verified', 'expired', 'error', 'loaded']);

const widgetId = ref(null);
const token    = ref('');
let scriptEl   = null;

/** Загружаем api.js асинхронно с onload-callback для v2 */
function loadRecaptcha() {
    return new Promise((resolve) => {
        if (window.grecaptcha?.render) return resolve();

        // глобальный onload для v2
        window.__recaptchaOnLoad = () => resolve();

        const qs = new URLSearchParams({
            onload: '__recaptchaOnLoad',
            render: 'explicit',
            ...(props.hl ? { hl: props.hl } : {}),
        });

        scriptEl = document.createElement('script');
        scriptEl.src = `https://www.google.com/recaptcha/api.js?${qs.toString()}`;
        scriptEl.async = true;
        scriptEl.defer = true;
        document.head.appendChild(scriptEl);
    });
}

/** Рендер виджета */
function renderWidget() {
    if (!window.grecaptcha?.render) return;

    widgetId.value = window.grecaptcha.render('recaptcha-container', {
        sitekey: props.siteKey,
        theme: props.theme,
        size:  props.size,
        tabindex: props.tabindex,
        callback: onSuccess,              // токен выдан
        'expired-callback': onExpired,    // токен протух
        'error-callback': onError,        // сеть/ошибка
    });

    emit('loaded', widgetId.value);
}

/** Коллбэки v2 */
function onSuccess(t) { token.value = t; emit('verified', t); }
function onExpired()  { token.value = ''; emit('expired'); }
function onError()    { token.value = ''; emit('error'); }

/** Публичные методы */
function getToken() {
    // альтернативно можно читать из `token.value`
    return window.grecaptcha?.getResponse(widgetId.value) || '';
}
function reset() {
    token.value = '';
    window.grecaptcha?.reset(widgetId.value);
}

defineExpose({ getToken, reset });

onMounted(async () => {
    await loadRecaptcha();   // ждём, пока API загрузится (асинхронно)
    renderWidget();          // явно рендерим
});

onBeforeUnmount(() => {
    // У v2 нет официального destroy; очищаем DOM-контейнер
    const el = document.getElementById('recaptcha-container');
    if (el) el.innerHTML = '';
    // (опционально) убрать <script> если добавляли его только ради этой страницы
});
</script>

<template>
    <!-- Контейнер для виджета -->
    <div id="recaptcha-container" />
</template>
