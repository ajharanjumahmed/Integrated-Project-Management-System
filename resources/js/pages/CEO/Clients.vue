<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const props = defineProps({
    clients: Array,
})

const search = ref('')

const filtered = () => props.clients.filter(c =>
    c.name.toLowerCase().includes(search.value.toLowerCase()) ||
    c.email.toLowerCase().includes(search.value.toLowerCase()) ||
    (c.client_profile?.company_name ?? '').toLowerCase().includes(search.value.toLowerCase())
)

const formatDate = (d) => d
    ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
    : '—'
</script>

<template>
<DashboardLayout title="Client List">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">All Clients</h1>
            <p class="text-slate-400 text-sm mt-0.5">{{ clients.length }} client(s) in the system</p>
        </div>
        <input v-model="search" type="text" placeholder="Search clients…"
               class="text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 w-64" />
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

        <div v-if="!filtered().length"
             class="px-6 py-12 text-center text-slate-400 text-sm">
            No clients found.
        </div>

        <table v-else class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Client</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Email</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Total Projects</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Completed Projects</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Active Projects</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Joined</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <tr v-for="client in filtered()" :key="client.id"
                    class="hover:bg-slate-50/60 transition">
                    <td class="px-6 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                                <span class="text-white text-xs font-bold">{{ client.name?.charAt(0)?.toUpperCase() }}</span>
                            </div>
                            <span class="font-medium text-slate-800">{{ client.name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-3.5 text-slate-500">{{ client.email }}</td>
                    <td class="px-6 py-3.5 text-slate-500">{{ client.client_projects_count }}</td>
                    <td class="px-6 py-3.5 text-slate-500">{{ client.completed_projects_count }}</td>
                    <td class="px-6 py-3.5 text-slate-500">{{ client.active_projects_count }}</td>
                    <td class="px-6 py-3.5 text-slate-500">{{ formatDate(client.created_at) }}</td>
                </tr>
            </tbody>
        </table>

    </div>

</DashboardLayout>
</template>