<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

// меню можно тянуть из бекенда — пока хардкод
const nav = [
    { name: 'Dashboard', icon: 'grid', href: route('dashboard'), active: () => route().current('dashboard') },
    { name: 'Просмотры', icon: 'eye', href: route('visits'), active: () => route().current('visits') },
    { name: 'Кабинет рекламодателя', icon: 'briefcase', href: route('adv_dashboard'), active: () => route().current('adv_dashboard') },
]

const page = usePage()
const user = computed(() => page.props.auth?.user ?? {})
const flash = computed(() => page.props.flash ?? {})

const sidebarOpen = ref(false)
const userMenuOpen = ref(false)
const toggleSidebar = () => (sidebarOpen.value = !sidebarOpen.value)
const closeSidebar = () => (sidebarOpen.value = false)

onMounted(() => {
    // закрытие дропдаунов по ESC
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            userMenuOpen.value = false
            sidebarOpen.value = false
        }
    })
})

// небольшие иконки (без зависимостей)
const Icon = (props) => {
    const map = {
        burger: 'M4 6h16M4 12h16M4 18h16',
        close: 'M6 18L18 6M6 6l12 12',
        grid: 'M4 4h7v7H4V4zm9 0h7v7h-7V4zM4 13h7v7H4v-7zm9 7v-7h7v7h-7z',
        eye: 'M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12zm11 3a3 3 0 100-6 3 3 0 000 6z',
        briefcase: 'M10 6V4a2 2 0 012-2h4a2 2 0 012 2v2h3a1 1 0 011 1v10a3 3 0 01-3 3H6a3 3 0 01-3-3V7a1 1 0 011-1h3V4a2 2 0 012-2h4a2 2 0 012 2v2',
        bell: 'M15 17h5l-1.4-1.4A5 5 0 0117 12V9a5 5 0 10-10 0v3a5 5 0 01-1.6 3.6L4 17h5m6 0a3 3 0 11-6 0',
        chevron: 'M6 9l6 6 6-6',
        user: 'M12 12a5 5 0 100-10 5 5 0 000 10zm-9 9a9 9 0 1118 0H3z',
    }
    return (
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d={map[props.name]} />
        </svg>
    )
}
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-800">
        <!-- Topbar (mobile-first) -->
        <header class="sticky top-0 z-40 border-b bg-white">
            <div class="mx-auto flex h-14 max-w-7xl items-center justify-between px-3 sm:px-6 lg:px-8">
                <div class="flex items-center gap-2">
                    <!-- mobile burger -->
                    <button class="rounded-md p-2 hover:bg-slate-100 sm:hidden" @click="toggleSidebar" aria-label="Меню">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path v-if="!sidebarOpen" stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/>
                            <path v-else stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <!-- brand -->
                    <Link :href="route('dashboard')" class="flex items-center gap-2">
                        <span class="inline-flex h-8 w-8 items-center justify-center rounded bg-blue-600 font-semibold text-white">V</span>
                        <span class="hidden text-sm font-semibold sm:inline">Velonic</span>
                    </Link>

                    <!-- primary links (desktop) -->
                    <nav class="ml-6 hidden gap-1 sm:flex">
                        <Link
                            v-for="item in nav" :key="item.name" :href="item.href"
                            :class="[
                'rounded-md px-3 py-2 text-sm font-medium',
                item.active() ? 'bg-slate-100 text-slate-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'
              ]"
                        >{{ item.name }}</Link>
                    </nav>
                </div>

                <div class="flex items-center gap-2">
                    <button class="rounded-md p-2 hover:bg-slate-100" aria-label="Уведомления">
                        <Icon name="bell" />
                    </button>

                    <!-- user dropdown -->
                    <div class="relative">
                        <button class="flex items-center gap-2 rounded-md p-1.5 hover:bg-slate-100" @click="userMenuOpen = !userMenuOpen">
                            <img :src="`https://i.pravatar.cc/64?u=${user?.email || 'user'}`" class="h-8 w-8 rounded-full" alt="user" />
                            <span class="hidden text-sm font-medium sm:inline">{{ user?.name }}</span>
                            <svg class="hidden h-4 w-4 sm:inline" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 9l6 6 6-6" />
                            </svg>
                        </button>

                        <div
                            v-show="userMenuOpen"
                            class="absolute right-0 mt-2 w-48 overflow-hidden rounded-lg border bg-white shadow-lg"
                            @click.outside="userMenuOpen=false"
                        >
                            <Link :href="route('profile.edit')" class="block px-4 py-2 text-sm hover:bg-slate-50">Profile</Link>
                            <Link :href="route('logout')" method="post" as="button" class="block w-full px-4 py-2 text-left text-sm hover:bg-slate-50">
                                Log Out
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- sidebar (desktop static, mobile drawer) -->
        <div class="mx-auto grid max-w-7xl grid-cols-1 sm:grid-cols-[240px,1fr] lg:grid-cols-[260px,1fr]">
            <!-- overlay for mobile -->
            <div
                class="fixed inset-0 z-30 bg-slate-900/40 sm:hidden"
                v-show="sidebarOpen"
                @click="closeSidebar"
            />
            <aside
                class="fixed z-40 w-72 max-w-[85%] -translate-x-full overflow-y-auto border-r bg-white p-4 shadow-lg transition-transform duration-200 sm:static sm:z-auto sm:block sm:w-auto sm:translate-x-0 sm:border-0 sm:p-0 sm:shadow-none"
                :class="{'translate-x-0': sidebarOpen}"
            >
                <nav class="thin-scrollbar sm:sticky sm:top-[56px] sm:max-h-[calc(100vh-56px)] sm:overflow-y-auto sm:pr-4">
                    <p class="mb-2 px-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Main</p>
                    <ul class="space-y-1">
                        <li v-for="item in nav" :key="item.name">
                            <Link
                                :href="item.href"
                                class="flex items-center gap-3 rounded-md px-3 py-2 text-sm"
                                :class="item.active() ? 'bg-slate-100 font-medium text-slate-900' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900'"
                                @click="closeSidebar"
                            >
                                <svg class="h-5 w-5 opacity-70" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          :d="item.icon==='grid' ? 'M4 4h7v7H4V4zm9 0h7v7h-7V4zM4 13h7v7H4v-7zm9 7v-7h7v7h-7z'
                           :item.icon==='eye' ? 'M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12zm11 3a3 3 0 100-6 3 3 0 000 6z'
                           : 'M10 6V4a2 2 0 012-2h4a2 2 0 012 2v2h3a1 1 0 011 1v10a3 3 0 01-3 3H6a3 3 0 01-3-3V7a1 1 0 011-1h3V4a2 2 0 012-2h4a2 2 0 012 2v2'}" />
                                </svg>
                                {{ item.name }}
                            </Link>
                        </li>
                    </ul>

                    <p class="mt-8 mb-2 px-2 text-xs font-semibold uppercase tracking-wide text-slate-500">Components</p>
                    <ul class="space-y-1">
                        <li><a class="block rounded-md px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-900">Tables</a></li>
                        <li><a class="block rounded-md px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-900">Forms</a></li>
                        <li><a class="block rounded-md px-3 py-2 text-sm text-slate-600 hover:bg-slate-100 hover:text-slate-900">Charts</a></li>
                    </ul>
                </nav>
            </aside>

            <!-- content -->
            <div class="sm:border-l sm:bg-transparent">
                <!-- page header slot -->
                <header v-if="$slots.header" class="border-b bg-white">
                    <div class="px-3 py-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <!-- flashes -->
                <div v-if="flash.success" class="mx-3 mt-3 rounded border border-green-200 bg-green-50 px-4 py-2 text-sm text-green-800 sm:mx-6 lg:mx-8">
                    {{ flash.success }}
                </div>
                <div v-if="flash.error" class="mx-3 mt-3 rounded border border-red-200 bg-red-50 px-4 py-2 text-sm text-red-700 sm:mx-6 lg:mx-8">
                    {{ flash.error }}
                </div>

                <!-- page body -->
                <main class="px-3 py-4 sm:px-6 lg:px-8">
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>
