<script setup>
import { ref } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const props = defineProps({
    project: Object,
})

const statusStyles = {
    pending: 'bg-amber-100 text-amber-700',
    active: 'bg-blue-100 text-blue-700',
    completed: 'bg-emerald-100 text-emerald-700',
    cancelled: 'bg-red-100 text-red-700',
}

const milestoneStatusDot = {
    pending: 'bg-slate-300',
    running: 'bg-blue-500',
    completed: 'bg-emerald-500',
}

const milestoneStatusStyles = {
    pending: 'bg-slate-100 text-slate-500',
    running: 'bg-blue-100 text-blue-600',
    completed: 'bg-emerald-100 text-emerald-700',
}

// Client status badge styles
const clientStatusStyles = {
    pending: 'bg-amber-100 text-amber-700',
    approved: 'bg-emerald-100 text-emerald-700',
    revision_requested: 'bg-red-100 text-red-600',
}

const formatDate = (d) => d
    ? new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })
    : '—'

const formatCurrency = (val) =>
    '$' + Number(val ?? 0).toLocaleString('en-US', { minimumFractionDigits: 2 })

// Collect all submissions sent to client across all tasks
const allSubmissions = props.project.tasks
    ?.flatMap(task =>
        (task.submissions ?? []).map(s => ({
            ...s,
            task_title: task.title,
        }))
    ) ?? []

const pendingSubmissions = allSubmissions.filter(s => s.client_status === 'pending')
const reviewedSubmissions = allSubmissions.filter(s => s.client_status !== 'pending')

// Milestone completion %
const completedMilestones = props.project.milestones?.filter(m => m.status === 'completed').length ?? 0
const totalMilestones = props.project.milestones?.length ?? 0
const milestoneProgress = totalMilestones
    ? Math.round((completedMilestones / totalMilestones) * 100)
    : 0

// ── Revision modal ────────────────────────────────────────────
const revisionTarget = ref(null)
const revisionNote = ref('')

const approveSubmission = (id) => {
    router.post(`/client/submissions/${id}/approve`, {}, { preserveScroll: true })
}

const submitRevision = () => {
    router.post(`/client/submissions/${revisionTarget.value.id}/revision`, {
        client_note: revisionNote.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            revisionTarget.value = null
            revisionNote.value = ''
        },
    })
}
</script>

<template>
    <DashboardLayout :title="project.title">

        <!-- Back -->
        <Link href="/client-dashboard"
            class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-slate-600 transition mb-4">
            ← Back to My Projects
        </Link>

        <!-- Header -->
        <div class="flex items-start justify-between gap-4 mb-6 flex-wrap">
            <div>
                <div class="flex items-center gap-3 flex-wrap">
                    <h1 class="text-2xl font-bold text-slate-800">{{ project.title }}</h1>
                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold capitalize"
                        :class="statusStyles[project.status]">
                        {{ project.status }}
                    </span>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-5">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-xl flex items-center justify-center shrink-0">🧑‍💼</div>
                <div>
                    <p class="text-lg font-semibold text-slate-800">{{ project.manager?.name ?? '—' }}</p>
                    <p class="text-xs text-slate-400 mt-1">Project Manager</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-xl flex items-center justify-center shrink-0">🗓️</div>
                <div>
                    <p class="text-lg font-semibold text-slate-800">{{ formatDate(project.deadline)}}</p>
                    <p class="text-xs text-slate-400 mt-1">Project Deadline</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-xl flex items-center justify-center shrink-0">💰</div>
                <div>
                    <p class="text-lg font-semibold text-slate-800">{{ formatCurrency(project.budget)}}</p>
                    <p class="text-xs text-slate-400 mt-1">Project Budget</p>
                </div>
            </div>
        </div>

        <!-- Progress bar -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-6 py-4 mb-6">
            <div class="flex justify-between items-center mb-2">
                <p class="text-sm font-semibold text-slate-700">Milestone Progress</p>
                <p class="text-sm font-bold text-blue-600">{{ milestoneProgress }}%</p>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                <div class="h-2.5 rounded-full bg-blue-500 transition-all duration-500"
                    :style="{ width: milestoneProgress + '%' }">
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-2">
                {{ completedMilestones }} of {{ totalMilestones }} milestones completed
            </p>
        </div>

        <!-- Two columns: Milestones + Pending Submissions -->
        <div class="grid md:grid-cols-2 gap-6 mb-6">

            <!-- Milestones -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800">Project Milestones</h2>
                </div>
                <div v-if="project.milestones?.length" class="divide-y divide-slate-50">
                    <div v-for="m in project.milestones" :key="m.id" class="flex items-center gap-3 px-5 py-3.5">
                        <div class="w-2.5 h-2.5 rounded-full shrink-0" :class="milestoneStatusDot[m.status]">
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-700 truncate">{{ m.title }}</p>
                            <p class="text-xs text-slate-400">Due: {{ formatDate(m.deadline) }}</p>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium capitalize"
                            :class="milestoneStatusStyles[m.status]">
                            {{ m.status }}
                        </span>
                    </div>
                </div>
                <div v-else class="px-5 py-10 text-center text-slate-400 text-sm">
                    No milestones defined yet.
                </div>
            </div>

            <!-- Pending Submissions -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100">
                    <h2 class="font-semibold text-slate-800">Awaiting Your Review</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Work submitted by the team for your approval</p>
                </div>

                <div v-if="pendingSubmissions.length" class="divide-y divide-slate-50">
                    <div v-for="sub in pendingSubmissions" :key="sub.id" class="px-5 py-4">

                        <!-- Task name + submitter -->
                        <p class="text-sm font-semibold text-slate-700 mb-0.5">{{ sub.task_title }}</p>
                        <p class="text-xs text-slate-400 mb-2">
                            Submitted by {{ sub.submitter?.name }} · {{ formatDate(sub.created_at) }}
                        </p>

                        <!-- Comment -->
                        <p v-if="sub.comment" class="text-xs text-slate-500 italic mb-2">
                            "{{ sub.comment }}"
                        </p>

                        <!-- Download -->
                        <a :href="`/storage/${sub.file_path}`" :download="sub.file_path.split('/').pop()"
                            class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download File
                        </a>

                        <!-- Action buttons — change based on client_status -->
                        <div class="flex gap-2">

                            <!-- Already reviewed — show status instead of buttons -->
                            <template v-if="sub.client_status !== 'pending'">
                                <span class="w-full text-center text-xs font-medium py-1.5 rounded-lg" :class="{
                                    'bg-emerald-100 text-emerald-700': sub.client_status === 'approved',
                                    'bg-amber-100 text-amber-700': sub.client_status === 'revision_requested',
                                }">
                                    {{ sub.client_status === 'approved' ? '✓ You approved this' : '↩ Revision requested'}}
                                </span>
                            </template>

                            <!-- Pending — show action buttons -->
                            <template v-else>
                                <button @click="approveSubmission(sub.id)"
                                    class="flex-1 text-xs font-medium py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition">
                                    ✓ Approve
                                </button>
                                <button @click="revisionTarget = sub; revisionNote = ''"
                                    class="flex-1 text-xs font-medium py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white transition">
                                    Request Changes
                                </button>
                            </template>

                        </div>

                    </div>
                </div>

                <div v-else class="px-5 py-10 text-center text-slate-400 text-sm">
                    <p class="text-2xl mb-2">📬</p>
                    No submissions awaiting your review.
                </div>
            </div>

        </div>

        <!-- Previously reviewed submissions -->
        <div v-if="reviewedSubmissions.length"
            class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-800">Previously Reviewed</h2>
            </div>

            <div class="divide-y divide-slate-50">
                <div v-for="sub in reviewedSubmissions" :key="sub.id" class="flex items-center gap-4 px-6 py-4">

                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-700 truncate">{{ sub.task_title }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ sub.submitter?.name }} · {{ formatDate(sub.created_at) }}
                        </p>
                        <!-- Show client's own revision note if they requested changes -->
                        <p v-if="sub.client_note"
                            class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-1.5 mt-2">
                            Your note: {{ sub.client_note }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        <a :href="`/storage/${sub.file_path}`" :download="sub.file_path.split('/').pop()"
                            class="text-xs text-blue-600 hover:underline">
                            Download
                        </a>
                        <!-- PM's own file (if PM uploaded one when forwarding) -->
                        <a v-if="sub.pm_to_client_file" :href="`/storage/${sub.pm_to_client_file}`"
                            :download="sub.pm_to_client_file.split('/').pop()"
                            class="inline-flex items-center gap-1.5 text-xs font-medium text-violet-600 bg-violet-50 hover:bg-violet-100 px-3 py-1.5 rounded-lg transition mt-2 ml-8">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"
                                stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            PM's File
                        </a>
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium capitalize"
                            :class="clientStatusStyles[sub.client_status]">
                            {{ sub.client_status.replace('_', ' ') }}
                        </span>
                    </div>

                </div>
            </div>

        </div>

        <!-- Revision modal -->
        <div v-if="revisionTarget"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
                <h2 class="text-lg font-bold text-slate-800 mb-1">Request Changes</h2>
                <p class="text-slate-400 text-sm mb-4">
                    Describe what you'd like changed for
                    <strong class="text-slate-700">{{ revisionTarget.task_title }}</strong>.
                </p>
                <textarea v-model="revisionNote" rows="3"
                    placeholder="e.g. Please change the color scheme and adjust the layout…"
                    class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 resize-none mb-4"></textarea>
                <div class="flex gap-3 justify-end">
                    <button @click="revisionTarget = null"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button @click="submitRevision" :disabled="!revisionNote.trim()"
                        class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium transition disabled:opacity-50">
                        Send Feedback
                    </button>
                </div>
            </div>
        </div>

    </DashboardLayout>
</template>