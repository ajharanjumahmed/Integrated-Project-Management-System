<script setup>
import { usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const user  = usePage().props.auth.user

const props = defineProps({
    projects: Array,
    tasks:    Array,
    activeTasks: Number,
    completedTasks: Number,
    pendingTasks: Number,
})

const priorityStyles = {
    low:    'bg-slate-100 text-slate-500',
    medium: 'bg-amber-100 text-amber-700',
    high:   'bg-red-100 text-red-700',
}

const taskStatusStyles = {
    pending:   'bg-slate-100 text-slate-600',
    started:   'bg-blue-100 text-blue-700',
    completed: 'bg-emerald-100 text-emerald-700',
}

const formatDate = (d) => d
    ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
    : '—'

const isOverdue = (d) => d &&  formatDate(new Date()) > formatDate(d)

</script>

<template>
<DashboardLayout title="Dashboard">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">
            Hey {{ user?.name?.split(' ')[0] }}, here's your work 🎨
        </h1>
        <p class="text-slate-400 text-sm mt-1">Your active tasks and project assignments.</p>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-8 xl:grid-cols-4 gap-4">

        <div class="bg-white rounded-2xl border border-violet-100 shadow-sm px-6 py-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center text-xl">📁</div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ projects.length }}</p>
                <p class="text-xs text-slate-400">Projects</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-blue-100 shadow-sm px-6 py-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-xl">⚡</div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ activeTasks }}</p>
                <p class="text-xs text-slate-400">Active Tasks</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-blue-100 shadow-sm px-6 py-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-xl">⏳</div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ pendingTasks }}</p>
                <p class="text-xs text-slate-400">Pending Tasks</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-blue-100 shadow-sm px-6 py-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-xl">✔️</div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ completedTasks }}</p>
                <p class="text-xs text-slate-400">Completed Tasks</p>
            </div>
        </div>

    </div>

    <div class="grid md:grid-cols-2 gap-6">

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-800">My Tasks</h2>
                <p class="text-xs text-slate-400 mt-1">All pending & in-progress tasks</p>
            </div>

            <div v-if="!tasks.length"
                 class="px-5 py-10 text-center text-slate-400 text-sm">
                No tasks assigned yet.
            </div>

            <div v-else class="divide-y divide-slate-50">
                <div v-for="task in tasks" :key="task.id" class="px-5 py-3.5">

                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">{{ task.title }}</p>
                            <p class="text-xs text-slate-400 mt-0.5 truncate">{{ task.project?.title }}</p>
                        </div>
                        <span class="px-2 py-0.5 rounded-full text-xs font-medium capitalize shrink-0"
                              :class="taskStatusStyles[task.status]">
                            {{ task.status }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 mt-2">
                        <span class="px-2 py-0.5 rounded text-xs capitalize"
                              :class="priorityStyles[task.priority]">
                            {{ task.priority }}
                        </span>
                        <span class="text-xs font-medium"
                              :class="isOverdue(task.deadline) ? 'text-red-500' : 'text-slate-400'">
                            {{ isOverdue(task.deadline) ? '⚠ Overdue · ' : '' }}Due {{ formatDate(task.deadline) }}
                        </span>
                    </div>

                </div>
            </div>

        </div>

        <!-- My Projects -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-800">My Projects</h2>
            </div>

            <div v-if="!projects.length"
                 class="px-5 py-10 text-center text-slate-400 text-sm">
                Not assigned to any projects yet.
            </div>

            <div v-else class="divide-y divide-slate-50">
                <div v-for="project in projects" :key="project.id"
                     class="px-5 py-4 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-800">{{ project.title }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ project.tasks_count }} task(s) assigned to you
                        </p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-700 capitalize">
                        {{ project.pivot?.role }}
                    </span>
                </div>
            </div>

        </div>

    </div>

</DashboardLayout>
</template>