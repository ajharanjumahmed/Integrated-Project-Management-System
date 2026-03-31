<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const user = usePage().props.auth.user

const props = defineProps({
    projects:          Array,
    totalProjects:     Number,
    activeProjects:    Number,
    completedProjects: Number,
    pendingProjects:   Number,
})

const statusStyles = {
    pending:   { badge: 'bg-amber-100 text-amber-700',     dot: 'bg-amber-400' },
    active:    { badge: 'bg-blue-100 text-blue-700',       dot: 'bg-blue-500' },
    completed: { badge: 'bg-emerald-100 text-emerald-700', dot: 'bg-emerald-400' },
    cancelled: { badge: 'bg-red-100 text-red-700',         dot: 'bg-red-400' },
}

const formatDate = (d) => d
    ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
    : '—'
</script>

<template>
<DashboardLayout title="Dashboard">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">
            Welcome back, {{ user?.name?.split(' ')[0] }} 👋
        </h1>
        <p class="text-slate-400 text-sm mt-1">Here are all the projects you're managing.</p>
    </div>
    <div class="grid grid-cols-2 gap-4 mb-8 xl:grid-cols-4 gap-4">

        <div class="bg-white rounded-2xl border border-violet-100 shadow-sm px-6 py-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center text-xl">📁</div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ totalProjects }}</p>
                <p class="text-xs text-slate-400">Total Projects</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-violet-100 shadow-sm px-6 py-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center text-xl">⏳</div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ pendingProjects }}</p>
                <p class="text-xs text-slate-400">Pending Projects</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-violet-100 shadow-sm px-6 py-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center text-xl">⚡</div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ activeProjects }}</p>
                <p class="text-xs text-slate-400">Active Projects</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-violet-100 shadow-sm px-6 py-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center text-xl">✔️</div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ completedProjects }}</p>
                <p class="text-xs text-slate-400">Completed Projects</p>
            </div>
        </div>

    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
            <h2 class="text-lg font-semibold text-slate-800">My Projects</h2>
            <Link href="/projects/create"
                  class="text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 px-3 py-1.5 rounded-lg transition">
                + New Project
            </Link>
        </div>

        <div v-if="!projects?.length"
             class="px-6 py-12 text-center text-slate-400 text-sm">
            No projects assigned yet.
        </div>

        <div v-else class="divide-y divide-slate-50">
            <Link
                v-for="project in projects"
                :key="project.id"
                :href="`/projects/${project.id}`"
                class="flex items-center gap-4 px-6 py-4 hover:bg-slate-50/70 transition group"
            >
                <div class="w-2.5 h-2.5 rounded-full shrink-0"
                     :class="statusStyles[project.status]?.dot">
                </div>

                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-slate-800 text-sm group-hover:text-blue-600 transition truncate">
                        {{ project.title }}
                    </p>
                    <div class="flex items-center gap-3 mt-1 text-xs text-slate-400">
                        <span>Client: {{ project.client?.name ?? 'No client' }}</span>
                        <span class="text-slate-200">·</span>
                        <span>{{ project.tasks_count }} tasks</span>
                        <span class="text-slate-200">·</span>
                        <span>{{ project.milestones_count }} milestones</span>
                        <span class="text-slate-200">·</span>
                        <span>Due {{ formatDate(project.deadline) }}</span>
                    </div>
                </div>

                <span class="px-2.5 py-1 rounded-full text-xs font-medium capitalize shrink-0"
                      :class="statusStyles[project.status]?.badge">
                    {{ project.status }}
                </span>

            </Link>
        </div>

    </div>

</DashboardLayout>
</template>