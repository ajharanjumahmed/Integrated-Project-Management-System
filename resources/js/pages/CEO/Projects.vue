<script setup>
import { ref } from 'vue'
import { Link } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const props = defineProps({
    projects: Array,
    totalRevenue: Number,
})

const search      = ref('')
const filterStatus = ref('')

const filtered = () => props.projects.filter(p => {
    const matchSearch = p.title.toLowerCase().includes(search.value.toLowerCase()) ||
        (p.client?.name ?? '').toLowerCase().includes(search.value.toLowerCase())
    const matchStatus = !filterStatus.value || p.status === filterStatus.value
    return matchSearch && matchStatus
})

const formatCurrency = (val) =>
    '$' + Number(val ?? 0).toLocaleString('en-US', { minimumFractionDigits: 2 })

const formatDate = (d) => d
    ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
    : '—'

const statusStyles = {
    pending:   'bg-amber-100 text-amber-700',
    active:    'bg-blue-100 text-blue-700',
    completed: 'bg-emerald-100 text-emerald-700',
    cancelled: 'bg-red-100 text-red-700',
}
</script>

<template>
<DashboardLayout title="All Projects">

    <div class="flex items-center justify-between gap-4 mb-6 flex-wrap">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">All Projects</h1>
            <p class="text-slate-400 text-sm mt-0.5">
                {{ projects.length }} projects · Total: {{ formatCurrency(totalRevenue) }}
            </p>
        </div>
        <div class="flex gap-2">
            <input v-model="search" type="text" placeholder="Search…"
                   class="text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 w-48" />
            <select v-model="filterStatus"
                    class="text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700">
                <option value="">All statuses</option>
                <option value="pending">Pending</option>
                <option value="active">Active</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <div v-if="!filtered().length"
             class="px-6 py-12 text-center text-slate-400 text-sm">
            No projects match your filters.
        </div>
        <table v-else class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Project</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Client</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Manager</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Deadline</th>
                    <th class="text-left px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                    <th class="text-right px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Budget</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <tr v-for="project in filtered()" :key="project.id"
                    class="hover:bg-slate-50/60 transition">
                    <td class="px-6 py-3.5 font-medium text-slate-800">
                        <Link :href="`/projects/${project.id}`"
                              class="hover:text-blue-600 transition">
                            {{ project.title }}
                        </Link>
                    </td>
                    <td class="px-6 py-3.5 text-slate-500">{{ project.client?.name ?? '—' }}</td>
                    <td class="px-6 py-3.5 text-slate-500">{{ project.manager?.name ?? '—' }}</td>
                    <td class="px-6 py-3.5 text-slate-500">{{ formatDate(project.deadline) }}</td>
                    <td class="px-6 py-3.5">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium capitalize"
                              :class="statusStyles[project.status]">
                            {{ project.status }}
                        </span>
                    </td>
                    <td class="px-6 py-3.5 text-right font-semibold text-slate-700">
                        {{ formatCurrency(project.budget) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

</DashboardLayout>
</template>