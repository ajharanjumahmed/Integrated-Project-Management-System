<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const user = usePage().props.auth.user

const props = defineProps({
    stats:       Object,
    usersByRole: Object,
    projects:    Array,
    topManagers: Array,
    topWorkers:  Array,
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

// Project status breakdown for the mini bar chart
const statusBreakdown = [
    { label: 'Active',    value: props.stats.activeProjects,    color: 'bg-blue-500'    },
    { label: 'Completed', value: props.stats.completedProjects, color: 'bg-emerald-500' },
    { label: 'Pending',   value: props.stats.pendingProjects,   color: 'bg-amber-400'   },
]

// Width % for each status bar segment
const totalProjects = props.stats.totalProjects || 1
const barWidth = (val) => ((val / totalProjects) * 100).toFixed(1) + '%'
</script>

<template>
<DashboardLayout title="Dashboard">

    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">
            Welcome back, {{ user?.name?.split(' ')[0] }} 👋
        </h1>
        <p class="text-slate-400 text-sm mt-1">Organization-wide analytics and performance overview.</p>
    </div>

    <!-- ── Top stat cards ─────────────────────── -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-violet-50 text-xl flex items-center justify-center shrink-0">👥</div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ stats.totalUsers }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Total Users</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-xl flex items-center justify-center shrink-0">📁</div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ stats.totalProjects }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Total Projects</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-blue-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-xl flex items-center justify-center shrink-0">⚡</div>
            <div>
                <p class="text-2xl font-bold text-blue-600">{{ stats.activeProjects }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Active Now</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-xl flex items-center justify-center shrink-0">✅</div>
            <div>
                <p class="text-2xl font-bold text-emerald-600">{{ stats.completedProjects }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Completed</p>
            </div>
        </div>

    </div>

    <!-- ── Revenue + Work Hours ─────────────── -->
    <div class="w-full gap-4 mb-6">

        <!-- Total revenue banner -->
        <div class="md:col-span-2 rounded-2xl p-6 relative overflow-hidden"
             style="background: linear-gradient(135deg, #1e3a5f 0%, #2563eb 100%)">
            <div class="absolute -right-6 -top-6 w-32 h-32 rounded-full bg-white/5"></div>
            <div class="absolute -right-2 -bottom-8 w-24 h-24 rounded-full bg-white/5"></div>
            <p class="text-blue-200 text-xs font-medium mb-1">Total Revenue (All Projects)</p>
            <p class="text-white text-4xl font-bold mb-7">{{ formatCurrency(stats.totalRevenue) }}</p>
            <div class="flex gap-10 ">
                <div>
                    <p class="text-blue-300 text-sm mb-1">Active</p>
                    <p class="text-white font-semibold text-sm">{{ formatCurrency(stats.activeRevenue) }}</p>
                </div>
                <div>
                    <p class="text-blue-300 text-sm mb-1">Completed</p>
                    <p class="text-white font-semibold text-sm">{{ formatCurrency(stats.completedRevenue) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ── Project status breakdown bar ─────── -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-6 py-5 mb-6">
        <h2 class="font-semibold text-slate-800 mb-3">Project Status Breakdown</h2>

        <!-- Segmented progress bar -->
        <div class="flex rounded-full overflow-hidden h-3 mb-3">
            <div v-for="seg in statusBreakdown" :key="seg.label"
                 :style="{ width: barWidth(seg.value) }"
                 :class="[seg.color, 'transition-all duration-500']"
                 :title="`${seg.label}: ${seg.value}`">
            </div>
        </div>

        <!-- Legend -->
        <div class="flex flex-wrap gap-4">
            <div v-for="seg in statusBreakdown" :key="seg.label"
                 class="flex items-center gap-1.5 text-xs text-slate-600">
                <div class="w-2.5 h-2.5 rounded-full" :class="seg.color"></div>
                {{ seg.label }} ({{ seg.value }})
            </div>
        </div>
    </div>

    <!-- ── Three column section ──────────────── -->
    <div class="grid md:grid-cols-3 gap-6 mb-6">

        <!-- Users by role -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-800">Users by Role</h2>
            </div>
            <div class="divide-y divide-slate-50">
                <div v-for="(count, role) in usersByRole" :key="role"
                     class="flex items-center justify-between px-5 py-3">
                    <span class="text-sm text-slate-700">{{ role }}</span>
                    <span class="text-sm font-bold text-slate-800">{{ count }}</span>
                </div>
            </div>
        </div>

        <!-- Top Project Managers by revenue -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-800">Top Managers</h2>
                <p class="text-xs text-slate-400 mt-0.5">By total project budget</p>
            </div>
            <div v-if="topManagers.length" class="divide-y divide-slate-50">
                <div v-for="(mgr, i) in topManagers" :key="mgr.id"
                     class="flex items-center gap-3 px-5 py-3">
                    <!-- Rank number -->
                    <span class="text-xs font-bold text-slate-300 w-4">{{ i + 1 }}</span>
                    <div class="w-7 h-7 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                        <span class="text-white text-xs font-bold">{{ mgr.name?.charAt(0) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-700 truncate">{{ mgr.name }}</p>
                        <p class="text-xs text-slate-400">{{ mgr.managed_projects_count }} project(s)</p>
                    </div>
                    <span class="text-xs font-semibold text-slate-600 shrink-0">
                        {{ formatCurrency(mgr.managed_projects_sum_budget ?? 0) }}
                    </span>
                </div>
            </div>
            <div v-else class="px-5 py-8 text-center text-slate-400 text-sm">No managers yet.</div>
        </div>

        <!-- Most active team members -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-800">Most Active</h2>
                <p class="text-xs text-slate-400 mt-0.5">Designers & developers by hours</p>
            </div>
            <div v-if="topWorkers.length" class="divide-y divide-slate-50">
                <div v-for="(worker, i) in topWorkers" :key="worker.id"
                     class="flex items-center gap-3 px-5 py-3">
                    <span class="text-xs font-bold text-slate-300 w-4">{{ i + 1 }}</span>
                    <div class="w-7 h-7 rounded-xl bg-violet-600 flex items-center justify-center shrink-0">
                        <span class="text-white text-xs font-bold">{{ worker.name?.charAt(0) }}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-700 truncate">{{ worker.name }}</p>
                        <p class="text-xs text-slate-400">{{ worker.role?.name }}</p>
                    </div>
                    <span class="text-xs font-semibold text-slate-600 shrink-0">
                        {{ ((worker.work_sessions_sum_duration ?? 0) / 60).toFixed(1) }}h
                    </span>
                </div>
            </div>
            <div v-else class="px-5 py-8 text-center text-slate-400 text-sm">No sessions logged yet.</div>
        </div>

    </div>

    <!-- ── Project earnings table ─────────────── -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <div>
                <h2 class="font-semibold text-slate-800">Project Earnings</h2>
                <p class="text-xs text-slate-400 mt-0.5">Top 10 projects by budget</p>
            </div>
            <Link href="/projects"
                  class="text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition">
                All Projects →
            </Link>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Project</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Client</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Manager</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Tasks</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-slate-400 uppercase tracking-wide">Budget</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <tr v-for="project in projects" :key="project.id"
                        class="hover:bg-slate-50/60 transition">
                        <td class="px-6 py-3.5 font-medium text-slate-800">{{ project.title }}</td>
                        <td class="px-6 py-3.5 text-slate-500">{{ project.client?.name ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-slate-500">{{ project.manager?.name ?? '—' }}</td>
                        <td class="px-6 py-3.5 text-slate-500">{{ project.tasks_count }}</td>
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
                <!-- Total row -->
                <tfoot>
                    <tr class="border-t-2 border-slate-200 bg-slate-50">
                        <td colspan="5" class="px-6 py-3.5 text-sm font-semibold text-slate-600">Total Revenue</td>
                        <td class="px-6 py-3.5 text-right text-sm font-bold text-blue-600">
                            {{ formatCurrency(stats.totalRevenue) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>

</DashboardLayout>
</template>