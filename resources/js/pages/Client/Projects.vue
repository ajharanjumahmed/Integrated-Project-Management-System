<script setup>
import { Link } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const props = defineProps({
    projects: Array,
})

const statusStyles = {
    pending:   { badge: 'bg-amber-100 text-amber-700',     bar: 'bg-amber-400' },
    active:    { badge: 'bg-blue-100 text-blue-700',       bar: 'bg-blue-500'  },
    completed: { badge: 'bg-emerald-100 text-emerald-700', bar: 'bg-emerald-500' },
    cancelled: { badge: 'bg-red-100 text-red-700',         bar: 'bg-red-400'   },
}

const milestoneStatusStyles = {
    pending:   'bg-slate-100 text-slate-500',
    running:   'bg-blue-100 text-blue-600',
    completed: 'bg-emerald-100 text-emerald-700',
}

const formatDate = (d) => d
    ? new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
    : '—'

const progress = (project) => {
    const total = project.milestones?.length ?? 0
    if (!total) return 0
    return Math.round(project.milestones.filter(m => m.status === 'completed').length / total * 100)
}

const pendingReviews = (project) =>
    project.tasks?.reduce((acc, t) =>
        acc + (t.submissions?.filter(s => s.client_status === 'pending').length ?? 0)
    , 0) ?? 0
</script>

<template>
<DashboardLayout title="My Projects">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">My Projects</h1>
        <p class="text-slate-400 text-sm mt-1">Click into any project to track progress and review submissions.</p>
    </div>

    <div v-if="!projects?.length"
         class="bg-white rounded-2xl border border-slate-100 shadow-sm px-6 py-20 text-center">
        <p class="text-4xl mb-3">📭</p>
        <p class="font-semibold text-slate-700">No projects yet</p>
        <p class="text-slate-400 text-sm mt-1">Your project manager will add you to a project soon.</p>
    </div>

    <div v-else class="space-y-5">
        <div v-for="project in projects" :key="project.id"
             class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

            <div class="px-6 py-4 flex items-start justify-between gap-4 border-b border-slate-100">
                <div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <h2 class="font-semibold text-slate-800">{{ project.title }}</h2>
                        <span v-if="pendingReviews(project) > 0"
                              class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-600">
                            {{ pendingReviews(project) }} pending review
                        </span>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">
                        Manager: {{ project.manager?.name ?? '—' }} ·
                        Deadline: {{ formatDate(project.deadline) }}
                    </p>
                </div>
                <div class="flex items-center gap-2 shrink-0">
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold capitalize"
                          :class="statusStyles[project.status]?.badge">
                        {{ project.status }}
                    </span>
                    <Link :href="`/client/projects/${project.id}`"
                          class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium transition">
                        View →
                    </Link>
                </div>
            </div>

            <div class="px-6 py-4">
                <div class="mb-3">
                    <div class="flex justify-between text-xs text-slate-500 mb-1.5">
                        <span>Milestone Progress</span>
                        <span class="font-bold text-slate-700">{{ progress(project) }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="h-2 rounded-full transition-all"
                             :class="statusStyles[project.status]?.bar"
                             :style="{ width: progress(project) + '%' }">
                        </div>
                    </div>
                </div>
                <div v-if="project.milestones?.length" class="space-y-1.5">
                    <div v-for="m in project.milestones" :key="m.id"
                         class="flex items-center justify-between">
                        <span class="text-xs text-slate-600">{{ m.title }}</span>
                        <span class="px-2 py-0.5 rounded-full text-xs capitalize"
                              :class="milestoneStatusStyles[m.status]">
                            {{ m.status }}
                        </span>
                    </div>
                </div>
                <p v-else class="text-xs text-slate-400 italic">No milestones yet.</p>
            </div>

        </div>
    </div>

</DashboardLayout>
</template>