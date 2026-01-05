<script setup>
import {Head} from '@inertiajs/vue3'
import {ref, onMounted} from 'vue'
import axios from "axios";

let props = defineProps({
    link: {
        type: String,
        required: true
    },
})
let time = ref(3);
onMounted(() => {
    const interval = setInterval(() => {
        if (time.value > 0) {
            time.value--
        } else {
            clearInterval(interval)
            window.location.href = props.link
        }
    }, 1000)
})
</script>

<template>
    <Head title="Посещение"/>
    <div class="w-screen h-screen">
        <div
            v-if="time > 0"
            class="absolute top-4 left-1/2 -translate-x-1/2 z-50 bg-black bg-opacity-70 text-white px-4 py-2 rounded-lg text-lg font-bold"
        >
            через {{ time }} сек будет переход на сайт
        </div>
    </div>
</template>
