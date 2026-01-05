<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import {Head} from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';

</script>

<template>
    <Head title="Dashboard"/>

    <DashboardLayout>
        <!-- Заголовок -->
        <h1 class="mb-4 text-lg font-semibold">Data Tables</h1>
        <div class="overflow-x-auto rounded-lg bg-white shadow">
            <table class="min-w-full text-left text-sm">
                <thead class="bg-slate-50">
                <tr>
                    <th class="px-6 py-3">Name</th>
                    <th class="px-6 py-3">Position</th>
                    <th class="px-6 py-3">Office</th>
                    <th class="px-6 py-3">Age</th>
                    <th class="px-6 py-3">Start date</th>
                    <th class="px-6 py-3">Salary</th>
                </tr>
                </thead>
                <tbody class="divide-y">
                <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 font-medium">Airi Satou</td>
                    <td class="px-6 py-3">Accountant</td>
                    <td class="px-6 py-3">Tokyo</td>
                    <td class="px-6 py-3">33</td>
                    <td class="px-6 py-3">2008/11/28</td>
                    <td class="px-6 py-3">$162,700</td>
                </tr>
                <!-- ... остальные строки ... -->
                </tbody>
            </table>
        </div>

    </DashboardLayout>
</template>
