<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref } from 'vue'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const props = defineProps({
    projects: Array,
})


const statusStyles = {
    pending:   'bg-amber-100 text-amber-700',
    active:    'bg-blue-100 text-blue-700',
    completed: 'bg-emerald-100 text-emerald-700',
    cancelled: 'bg-red-100 text-red-700',
}

const formatDate = (d) => d
    ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
    : '—'

const formatCurrency = (val) =>
    '$' + Number(val ?? 0).toLocaleString('en-US', { minimumFractionDigits: 2 })


const deleteTarget = ref(null)

const confirmDelete = () => {
    router.delete(`/projects/${deleteTarget.value.id}`, {
        onSuccess: () => { deleteTarget.value = null },
    })
}
</script>

<template>
<DashboardLayout title="Projects">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Projects</h1>
            <p class="text-slate-400 text-sm mt-0.5">{{ projects.length }} total project{{ projects.length !== 1 ? 's' : '' }}</p>
        </div>
        <Link href="/projects/create"
              class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition shadow-sm shadow-blue-200">
            + New Project
        </Link>
    </div>

    <div v-if="!projects.length"
         class="bg-white rounded-2xl border border-slate-100 shadow-sm px-6 py-20 text-center">
        <p class="text-4xl mb-3">📁</p>
        <p class="font-semibold text-slate-700">No projects yet</p>
        <p class="text-slate-400 text-sm mt-1">Create your first project to get started.</p>
    </div>

    <div v-else class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

        <table class="w-full text-sm">

            <thead>
                <tr class="border-b border-slate-100">
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Project</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Client</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Manager</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Budget</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Deadline</th>
                    <th class="px-6 py-3.5"></th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-50">
                <tr v-for="project in projects" :key="project.id"
                    class="hover:bg-slate-50/70 transition group">

                    <td class="px-6 py-4">
                        <Link :href="`/projects/${project.id}`"
                              class="font-semibold text-slate-800 group-hover:text-blue-600 transition">
                            {{ project.title }}
                        </Link>
                        <p v-if="project.description"
                           class="text-xs text-slate-400 mt-0.5 truncate max-w-xs">
                            {{ project.description }}
                        </p>
                    </td>

                    <td class="px-6 py-4 text-slate-600">
                        {{ project.client?.name ?? '—' }}
                    </td>

                    <td class="px-6 py-4 text-slate-600">
                        {{ project.manager?.name ?? '—' }}
                    </td>

                    <td class="px-6 py-4 text-slate-600 font-medium">
                        {{ formatCurrency(project.budget) }}
                    </td>

                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium capitalize"
                              :class="statusStyles[project.status]">
                            {{ project.status }}
                        </span>
                    </td>

                    <td class="px-6 py-4 text-slate-500 text-xs">
                        {{ formatDate(project.deadline) }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 justify-end">

                            <Link :href="`/projects/${project.id}`"
                                  class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-medium transition">
                                View
                            </Link>

                            <Link :href="`/projects/${project.id}/edit`"
                                  class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-medium transition">
                                Edit
                            </Link>

                            <button @click="deleteTarget = project"
                                    class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 text-xs font-medium transition">
                                Delete
                            </button>

                        </div>
                    </td>

                </tr>
            </tbody>

        </table>

    </div>

    <div v-if="deleteTarget"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4">

        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">

            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-2xl mb-4">
                🗑️
            </div>

            <h2 class="text-lg font-bold text-slate-800 mb-1">Delete Project</h2>

            <p class="text-slate-500 text-sm mb-6">
                Are you sure you want to delete
                <strong class="text-slate-700">{{ deleteTarget.name }}</strong>?
                This will also delete all tasks, milestones, and messages in this project.
                This cannot be undone.
            </p>

            <div class="flex gap-3 justify-end">
                <button @click="deleteTarget = null"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition">
                    Cancel
                </button>
                <button @click="confirmDelete"
                        class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition">
                    Yes, Delete
                </button>
            </div>

        </div>

    </div>

</DashboardLayout>
</template>