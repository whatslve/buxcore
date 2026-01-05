<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    links: Array
})
</script>

<template>
    <div class="flex space-x-2 mt-4">
        <template v-for="link in links" :key="link.url">
            <div v-if="link.url === null" v-html="link.label" class="px-3 py-1 text-gray-400" />
            <Link
                v-else
                :href="link.url"
                v-html="link.label"
                :class="[
          'px-3 py-1 border rounded',
          link.active ? 'bg-blue-500 text-white' : 'bg-white text-gray-700 hover:bg-gray-100'
        ]"
            />
        </template>
    </div>
</template>
