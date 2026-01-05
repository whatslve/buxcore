<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

// Inertia props
const page = usePage()
const user = computed(() => page.props.auth?.user ?? {})
const flash = computed(() => page.props.flash ?? {})

// UI state
const sidebarOpen = ref(false)     // mobile drawer state
const userMenuOpen = ref(false)    // profile dropdown
const expanded = ref({ adv: false })

// Главные пункты (без вложенности)
const navMainFlat = [
    { name: 'Dashboard', routeName: 'dashboard', icon: 'grid' },
    { name: 'Просмотры', routeName: 'visits', icon: 'eye' },
]

// Вложенная группа «Кабинет рекламодателя»
const advGroup = {
    key: 'adv',
    title: 'Кабинет рекламодателя',
    icon: 'briefcase',
    children: [
        { name: 'Настройки', routeName: 'adv_dashboard' },
        { name: 'Просмотры', routeName: 'adv.visits.index' },
        { name: 'Editable Tables', routeName: 'dashboard' }, // пример
    ],
}

// icons
const iconPath = (name) => ({
    grid: 'M4 4h7v7H4V4zm9 0h7v7h-7V4zM4 13h7v7H4v-7zm9 7v-7h7v7h-7z',
    eye: 'M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12zm11 3a3 3 0 100-6 3 3 0 000 6z',
    briefcase: 'M10 6V4a2 2 0 012-2h4a2 2 0 012 2v2h3a1 1 0 011 1v10a3 3 0 01-3 3H6a3 3 0 01-3-3V7a1 1 0 011-1h3V4a2 2 0 012-2h4a2 2 0 012 2v2',
    bell: 'M15 17h5l-1.4-1.4A5 5 0 0117 12V9a5 5 0 10-10 0v3a5 5 0 01-1.6 3.6L4 17h5m6 0a3 3 0 11-6 0',
    chevron: 'M6 9l6 6 6-6',
    close: 'M6 18L18 6M6 6l12 12',
    burger: 'M4 6h16M4 12h16M4 18h16',
}[name])

const toggleSidebar = () => (sidebarOpen.value = !sidebarOpen.value)
const closeSidebar = () => (sidebarOpen.value = false)

const isActive = (name) => {
    try { return route().current(name) } catch { return false }
}

const syncExpandedWithRoute = () => {
    expanded.value.adv = advGroup.children.some(c => isActive(c.routeName))
}

onMounted(() => {
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            sidebarOpen.value = false
            userMenuOpen.value = false
        }
    })
    syncExpandedWithRoute()
})

watch(() => page.url, () => {
    syncExpandedWithRoute()
    closeSidebar() // на мобиле закрываем drawer после перехода
})
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-800">
        <!-- Topbar -->
        <header class="sticky top-0 z-40 flex h-14 items-center justify-between bg-white px-3 shadow sm:px-6 lg:ml-60">
            <!-- burger / close (mobile) -->
            <button class="rounded-md p-2 hover:bg-slate-100 lg:hidden" aria-label="Меню" @click="toggleSidebar">
                <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path :d="sidebarOpen ? iconPath('close') : iconPath('burger')" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>

            <!-- brand -->
            <Link :href="route('dashboard')" class="flex items-center gap-2">
                <span class="inline-flex h-8 w-8 items-center justify-center rounded bg-blue-600 text-white font-semibold">V</span>
                <span class="text-base font-semibold">Velonic</span>
            </Link>

            <!-- right: bell + profile dropdown -->
            <div class="flex items-center gap-2">
                <button class="rounded-md p-2 hover:bg-slate-100" aria-label="Уведомления">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path :d="iconPath('bell')" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>

                <div class="relative">
                    <button class="flex items-center gap-2 rounded-md p-1.5 hover:bg-slate-100" @click="userMenuOpen = !userMenuOpen">
                        <img :src="`https://i.pravatar.cc/64?u=${user?.email || 'user'}`" class="h-8 w-8 rounded-full" alt="user" />
                        <span class="hidden text-sm font-medium sm:inline">{{ user?.name }}</span>
                        <svg class="hidden h-4 w-4 sm:inline" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path :d="iconPath('chevron')" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <!-- click-outside для дропа -->
                    <div v-show="userMenuOpen" class="fixed inset-0 z-40" @click="userMenuOpen=false"></div>

                    <div v-show="userMenuOpen" class="absolute right-0 z-50 mt-2 w-48 overflow-hidden rounded-lg bg-white shadow-lg">
                        <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm hover:bg-slate-50">Profile</Link>
                        <Link :href="route('logout')" method="post" as="button" class="block w-full px-4 py-2 text-left text-sm hover:bg-slate-50">
                            Log Out
                        </Link>
                    </div>
                </div>
            </div>
        </header>
        <!-- DESKTOP aside (всегда виден) -->
        <aside class="fixed inset-y-0 left-0 z-30 hidden w-60 overflow-y-auto bg-white p-4 shadow-md lg:block">
            <nav class="space-y-1">
                <p class="mb-2 px-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Main</p>

                <Link
                    v-for="item in navMainFlat" :key="item.name"
                    :href="route(item.routeName)"
                    class="flex items-center gap-3 rounded-md px-3 py-2 text-sm"
                    :class="isActive(item.routeName) ? 'bg-slate-100 font-medium text-slate-900 shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
                >
                    <svg class="h-5 w-5 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path :d="iconPath(item.icon)" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    {{ item.name }}
                </Link>

                <!-- Вложенная группа -->
                <div class="rounded-md">
                    <button
                        class="flex w-full items-center justify-between rounded-md px-3 py-2 text-left text-sm"
                        :class="[
              advGroup.children.some(c => isActive(c.routeName))
                ? 'bg-slate-100 text-slate-900 font-medium shadow-sm'
                : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
            ]"
                        @click="expanded.adv = !expanded.adv"
                    >
            <span class="flex items-center gap-3">
              <svg class="h-5 w-5 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path :d="iconPath(advGroup.icon)" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
              {{ advGroup.title }}
            </span>
                        <svg class="h-4 w-4 transition" :class="{ 'rotate-180': expanded.adv }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path :d="iconPath('chevron')" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <transition name="fade" mode="out-in">
                        <ul v-show="expanded.adv" class="ml-9 space-y-1 py-1">
                            <li v-for="child in advGroup.children" :key="child.name">
                                <Link
                                    :href="route(child.routeName)"
                                    class="block rounded-md px-3 py-1.5 text-sm"
                                    :class="isActive(child.routeName) ? 'bg-blue-50 text-blue-700 font-semibold shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
                                >
                                    {{ child.name }}
                                </Link>
                            </li>
                        </ul>
                    </transition>
                </div>
            </nav>
        </aside>

        <!-- MOBILE drawer container: overlay + aside; клики по свободной зоне закрывают меню -->
        <div v-show="sidebarOpen" class="fixed inset-0 z-40 flex lg:hidden">
            <!-- сам aside -->
            <aside
                class="relative w-60 max-w-[80%] -translate-x-full transform bg-white p-4 shadow-lg transition-transform duration-200"
                :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            >
                <!-- кнопка закрытия внутри aside -->
                <div class="mb-2 flex items-center justify-between">
                    <span class="px-2 text-sm font-semibold text-slate-600">Меню</span>
                    <button class="rounded-md p-2 hover:bg-slate-100" aria-label="Закрыть" @click="closeSidebar">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path :d="iconPath('close')" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>

                <nav class="space-y-1">
                    <p class="mb-2 px-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Main</p>

                    <Link
                        v-for="item in navMainFlat" :key="item.name"
                        :href="route(item.routeName)"
                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm"
                        :class="isActive(item.routeName) ? 'bg-slate-100 font-medium text-slate-900 shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
                        @click="closeSidebar"
                    >
                        <svg class="h-5 w-5 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path :d="iconPath(item.icon)" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        {{ item.name }}
                    </Link>

                    <!-- Вложенная группа -->
                    <div class="rounded-md">
                        <button
                            class="flex w-full items-center justify-between rounded-md px-3 py-2 text-left text-sm"
                            :class="[
                advGroup.children.some(c => isActive(c.routeName))
                  ? 'bg-slate-100 text-slate-900 font-medium shadow-sm'
                  : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
              ]"
                            @click="expanded.adv = !expanded.adv"
                        >
              <span class="flex items-center gap-3">
                <svg class="h-5 w-5 opacity-70" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                  <path :d="iconPath(advGroup.icon)" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                {{ advGroup.title }}
              </span>
                            <svg class="h-4 w-4 transition" :class="{ 'rotate-180': expanded.adv }" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path :d="iconPath('chevron')" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>

                        <transition name="fade" mode="out-in">
                            <ul v-show="expanded.adv" class="ml-9 space-y-1 py-1">
                                <li v-for="child in advGroup.children" :key="child.name">
                                    <Link
                                        :href="route(child.routeName)"
                                        class="block rounded-md px-3 py-1.5 text-sm"
                                        :class="isActive(child.routeName) ? 'bg-blue-50 text-blue-700 font-semibold shadow-sm' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
                                        @click="closeSidebar"
                                    >
                                        {{ child.name }}
                                    </Link>
                                </li>
                            </ul>
                        </transition>
                    </div>
                </nav>
            </aside>
            <!-- свободная зона -->
            <div class="flex-1 bg-slate-900/40" @click="closeSidebar"></div>
        </div>

        <!-- Content -->
        <main class="pt-4 lg:ml-60">
            <header v-if="$slots.header" class="bg-white shadow">
                <div class="px-3 py-4 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <div v-if="flash.success" class="mx-3 mt-3 rounded bg-green-50 px-4 py-2 text-sm text-green-800 shadow sm:mx-6 lg:mx-8">
                {{ flash.success }}
            </div>
            <div v-if="flash.error" class="mx-3 mt-3 rounded bg-red-50 px-4 py-2 text-sm text-red-700 shadow sm:mx-6 lg:mx-8">
                {{ flash.error }}
            </div>

            <div class="px-3 py-4 sm:px-6 lg:px-8">
                <slot />
            </div>
        </main>
    </div>
</template>

<style>
.fade-enter-active, .fade-leave-active { transition: opacity .15s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
