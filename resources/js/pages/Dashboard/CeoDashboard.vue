<script setup>
import { Link } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import { usePage } from '@inertiajs/vue3'

const user = usePage().props.auth.user

const props = defineProps({
    stats:          Object,
    recentProjects: Array,
})

const statusStyles = {
    pending:   { badge: 'bg-amber-100 text-amber-700',  dot: 'bg-amber-400' },
    active:    { badge: 'bg-blue-100 text-blue-700',    dot: 'bg-blue-400' },
    completed: { badge: 'bg-emerald-100 text-emerald-700', dot: 'bg-emerald-400' },
    cancelled: { badge: 'bg-red-100 text-red-700',      dot: 'bg-red-400' },
}

const formatCurrency = (val) =>
    '$' + Number(val ?? 0).toLocaleString('en-US', { minimumFractionDigits: 2 })

const statCards = [
    {
        label: 'Total Users',
        value: () => props.stats.totalUsers,
        icon: '👥',
        color: 'bg-violet-50 text-violet-600',
        border: 'border-violet-100',
    },
    {
        label: 'Total Projects',
        value: () => props.stats.totalProjects,
        icon: '📁',
        color: 'bg-blue-50 text-blue-600',
        border: 'border-blue-100',
    },
    {
        label: 'Active Projects',
        value: () => props.stats.activeProjects,
        icon: '⚡',
        color: 'bg-amber-50 text-amber-600',
        border: 'border-amber-100',
    },
    {
        label: 'Completed',
        value: () => props.stats.completedProjects,
        icon: '✅',
        color: 'bg-emerald-50 text-emerald-600',
        border: 'border-emerald-100',
    },
]
</script>

<template>
<DashboardLayout title="Dashboard">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">
            Good to see you, {{ user?.name?.split(' ')[0] }} 👋
        </h1>
        <p class="text-slate-400 text-sm mt-1">Here's your organization's overview.</p>
    </div>

    <div class="grid grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
        <div
            v-for="card in statCards"
            :key="card.label"
            class="bg-white rounded-2xl border p-5 flex items-center gap-4 shadow-sm hover:shadow-md transition-shadow"
            :class="card.border"
        >
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-2xl shrink-0"
                 :class="card.color">
                {{ card.icon }}
            </div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ card.value() }}</p>
                <p class="text-xs text-slate-400 font-medium mt-0.5">{{ card.label }}</p>
            </div>
        </div>
    </div>

    <div class="rounded-2xl p-6 mb-8 relative overflow-hidden"
         style="background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%)">

        <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-white/5"></div>
        <div class="absolute -right-4 -bottom-10 w-28 h-28 rounded-full bg-white/5"></div>

        <p class="text-blue-200 text-sm font-medium mb-2">Total Revenue</p>
        <p class="text-white text-4xl font-bold">{{ formatCurrency(stats.totalRevenue) }}</p>
        <p class="text-blue-300 text-xs mt-3">Sum of all project budgets</p>

    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h2 class="text-lg font-semibold text-slate-800">Recent Projects</h2>
                <p class="text-xs text-slate-400 mt-0.5">Latest 5 projects across the organization</p>
            </div>
            <Link href="/projects"
                  class="text-xs font-medium text-blue-600 hover:text-blue-700 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                View all →
            </Link>
        </div>

        <div v-if="!recentProjects?.length"
             class="px-6 py-12 text-center text-slate-400 text-sm">
            No projects yet. Create your first project.
        </div>

        <div v-else class="divide-y divide-slate-50">
            <div
                v-for="project in recentProjects"
                :key="project.id"
                class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50/60 transition"
            >
                <div class="w-2 h-2 rounded-full shrink-0"
                     :class="statusStyles[project.status]?.dot ?? 'bg-slate-300'">
                </div>

                <div class="flex-1 min-w-0">
                    <p class="font-medium text-slate-800 text-sm truncate">{{ project.title }}</p>
                    <p class="text-xs text-slate-400 mt-1 truncate">
                        Client: {{ project.client?.name ?? '—' }} |  Manager: {{ project.manager?.name ?? '—' }}
                    </p>
                </div>

                <div class="hidden md:block text-sm font-semibold text-slate-600 shrink-0">
                    {{ formatCurrency(project.budget) }}
                </div>

                <span class="px-2.5 py-1 rounded-full text-xs font-medium capitalize shrink-0"
                      :class="statusStyles[project.status]?.badge ?? 'bg-slate-100 text-slate-600'">
                    {{ project.status }}
                </span>

            </div>
        </div>

    </div>

</DashboardLayout>
</template>