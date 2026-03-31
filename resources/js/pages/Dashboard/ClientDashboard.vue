<script setup>
import { usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const user  = usePage().props.auth.user

const props = defineProps({
    projects: Array,
})

const statusStyles = {
    pending:   { badge: 'bg-amber-100 text-amber-700',     bar: 'bg-amber-400' },
    active:    { badge: 'bg-blue-100 text-blue-700',       bar: 'bg-blue-500' },
    completed: { badge: 'bg-emerald-100 text-emerald-700', bar: 'bg-emerald-500' },
    cancelled: { badge: 'bg-red-100 text-red-700',         bar: 'bg-red-400' },
}

const milestoneStatusStyles = {
    pending:   'bg-slate-100 text-slate-500',
    running:   'bg-blue-100 text-blue-600',
    completed: 'bg-emerald-100 text-emerald-600',
}

const formatDate = (d) => d
    ? new Date(d).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' })
    : '—'

// % of milestones marked completed
const progress = (project) => {
    const total = project.milestones?.length ?? 0
    if (!total) return 0
    const done  = project.milestones.filter(m => m.status === 'completed').length
    return Math.round((done / total) * 100)
}
</script>

<template>
<DashboardLayout title="My Projects">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">
            Hi {{ user?.name?.split(' ')[0] }}, track your projects here 📊
        </h1>
        <p class="text-slate-400 text-sm mt-1">You can see real-time progress on all your projects below.</p>
    </div>

    <!-- Empty state -->
    <div v-if="!projects?.length"
         class="bg-white rounded-2xl border border-slate-100 shadow-sm px-6 py-20 text-center">
        <p class="text-4xl mb-3">📭</p>
        <p class="font-semibold text-slate-700">No projects yet</p>
        <p class="text-slate-400 text-sm mt-1">Your project manager will add you to a project soon.</p>
    </div>

    <!-- Project cards -->
    <div v-else class="space-y-5">
        <div v-for="project in projects" :key="project.id" class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 flex items-start justify-between gap-4 border-b border-slate-100">
                <div>
                    <h2 class="font-semibold text-slate-800">{{ project.title }}</h2>
                    <p class="text-xs text-slate-400 mt-1">
                        Manager: <span class="text-slate-600">{{ project.manager?.name ?? '—' }}</span>
                        &nbsp;·&nbsp;
                        Deadline: <span class="text-slate-600">{{ formatDate(project.deadline) }}</span>
                    </p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-xs font-semibold capitalize shrink-0"
                      :class="statusStyles[project.status]?.badge">
                    {{ project.status }}
                </span>
            </div>

            <div class="px-6 py-4">

                <!-- Progress bar -->
                <div class="mb-4">
                    <div class="flex justify-between items-center mb-1.5">
                        <span class="text-xs font-medium text-slate-500">Milestone Progress</span>
                        <span class="text-xs font-bold text-slate-700">{{ progress(project) }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                        <div class="h-2 rounded-full transition-all duration-500"
                             :class="statusStyles[project.status]?.bar ?? 'bg-blue-500'"
                             :style="{ width: progress(project) + '%' }">
                        </div>
                    </div>
                </div>

                <!-- Milestone list -->
                <div v-if="project.milestones?.length" class="space-y-2">
                    <div v-for="m in project.milestones" :key="m.id"
                         class="flex items-center justify-between text-sm py-1">
                        <span class="text-slate-700 text-xs">{{ m.title }}</span>
                        <span class="px-2 py-0.5 rounded-full text-xs capitalize"
                              :class="milestoneStatusStyles[m.status]">
                            {{ m.status }}
                        </span>
                    </div>
                </div>

                <p v-else class="text-xs text-slate-400 italic">No milestones defined yet.</p>

            </div>

        </div>
    </div>

</DashboardLayout>
</template>