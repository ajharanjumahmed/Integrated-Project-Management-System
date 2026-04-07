<script setup>
import { router, useForm, Link } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import { ref } from 'vue'

const props = defineProps({
    projects: Array,
    activeSession: Object,
})

const priorityStyles = {
    low: { badge: 'bg-slate-100 text-slate-500', dot: 'bg-slate-300' },
    medium: { badge: 'bg-amber-100 text-amber-700', dot: 'bg-amber-400' },
    high: { badge: 'bg-red-100 text-red-600', dot: 'bg-red-400' },
}

const projectStatusStyles = {
    pending: 'bg-amber-100 text-amber-700',
    active: 'bg-blue-100 text-blue-700',
    completed: 'bg-emerald-100 text-emerald-700',
    cancelled: 'bg-red-100 text-red-700',
}

const columns = [
    { key: 'pending', label: 'Pending', color: 'bg-sky-50', border: 'border-sky-200', dot: 'bg-slate-400' },
    { key: 'started', label: 'Started', color: 'bg-indigo-50', border: 'border-indigo-200', dot: 'bg-blue-500' },
    { key: 'completed', label: 'Completed', color: 'bg-green-50', border: 'border-green-200', dot: 'bg-emerald-500' },
]

const formatDate = (d) => {
    if (!d) return null
    return new Date(d).toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
}

// Check if deadline has passed and task isn't completed
const isOverdue = (task) => {
    if (!task.deadline || task.status === 'completed') return false
    return formatDate(new Date(task.deadline)) < formatDate(new Date())
}

// ── Status transitions ────────────────────────────────────────

const nextStatus = { pending: 'started', started: 'completed' }
const prevStatus = { started: 'pending', completed: 'started' }


const moveTask = (task, direction) => {
    const newStatus = direction === 'forward'
        ? nextStatus[task.status]
        : prevStatus[task.status]

    if (!newStatus) return

    router.patch(`/kanban/tasks/${task.id}`, {
        status: newStatus,
    }, {
        preserveScroll: true,
    })
}

// ── Task detail expand ────────────────────────────────────────
const expandedTaskId = ref(null)

// ── Submission helpers ────────────────────────────────────────

// Get the most recent submission for a task (submissions are ordered latest first)
const latestSubmission = (task) => task.submissions?.[0] ?? null

// True if task has been submitted and is awaiting PM review
const isSubmitted = (task) =>
    latestSubmission(task)?.status === 'pending_review'

// True if PM has requested a revision on this task
const hasRevision = (task) =>
    latestSubmission(task)?.status === 'revision_requested'

// ── Submission form ───────────────────────────────────────────

// Tracks which task has its submit form open
const submittingTaskId = ref(null)

const submitForm = useForm({
    file: null,
    comment: '',
})

const openSubmitForm = (taskId) => {
    submittingTaskId.value = taskId
    submitForm.reset()
}

const submitWork = (task) => {
    submitForm.post(`/tasks/${task.id}/submit`, {
        // forceFormData forces Inertia to send as multipart/form-data
        // which is required for file uploads — without this the file won't be sent
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            submittingTaskId.value = null
            submitForm.reset()
        },
    })
}

// ── Work session helpers ──────────────────────────────────────

// True if the user is currently signed in to this specific project
const isSignedInTo = (projectId) => {
    return props.activeSession?.project_id === projectId
}

// True if signed in to a DIFFERENT project (disables sign-in on others)
const isSignedInElsewhere = (projectId) => {
    return props.activeSession !== null && props.activeSession.project_id !== projectId
}

const signIn = (projectId) => {
    router.post(`/projects/${projectId}/sign-in`, {}, { preserveScroll: true })
}

const signOut = (projectId) => {
    router.post(`/projects/${projectId}/sign-out`, {}, { preserveScroll: true })
}

// Format start_time into "Signed in at HH:MM" for the active badge
const formatTime = (datetime) => {
    if (!datetime) return '—'
    let str = String(datetime).trim()
    if (!str.includes('T')) str = str.replace(' ', 'T')
    if (!str.endsWith('Z') && !str.includes('+')) str += 'Z'
    return new Date(str).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const toggleExpand = (taskId) => {
    expandedTaskId.value = expandedTaskId.value === taskId ? null : taskId
}
</script>

<template>
    <DashboardLayout title="My Kanban Board">

        <!-- ── Page header ────────────────────────── -->
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-slate-800">My Kanban Board</h1>
            <p class="text-slate-400 text-sm mt-1">
                Your tasks across all projects. Move them forward as you make progress.
            </p>
        </div>

        <!-- ── Empty state ────────────────────────── -->
        <div v-if="!projects.length"
            class="bg-white rounded-2xl border border-slate-100 shadow-sm px-6 py-20 text-center">
            <p class="text-4xl mb-3">📋</p>
            <p class="font-semibold text-slate-700">No tasks assigned yet</p>
            <p class="text-slate-400 text-sm mt-1">
                Your project manager hasn't assigned any tasks to you yet.
            </p>
        </div>

        <!-- ── One swimlane per project ───────────── -->
        <div v-else class="space-y-8">

            <div v-for="project in projects" :key="project.id">

                <!-- Project name header -->
                <div class="flex items-center gap-3 mb-3 flex-wrap">
                    <h2 class="font-bold text-slate-700 text-base">{{ project.title }}</h2>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                        :class="projectStatusStyles[project.status]">
                        {{ project.status }}
                    </span>
                    <span class="text-xs text-slate-400">
                        {{ project.pending.length + project.started.length + project.completed.length }}
                        task{{ (project.pending.length + project.started.length + project.completed.length) !== 1 ? 's'
                            : '' }}
                    </span>

                    <!-- Work session controls — pushed to the right -->
                    <div class="ml-auto flex items-center gap-2">

                        <!-- Active session badge — shown when signed in to THIS project -->
                        <span v-if="isSignedInTo(project.id)"
                            class="text-xs text-emerald-600 bg-emerald-50 border border-emerald-200 px-3 py-1 rounded-full font-medium">
                            ● Signed in since {{ formatTime(activeSession.start_time) }}
                        </span>

                        <!-- Sign In button -->
                        <button v-if="!isSignedInTo(project.id)" @click="signIn(project.id)"
                            :disabled="isSignedInElsewhere(project.id)"
                            class="text-xs font-medium px-3 py-1.5 rounded-lg transition" :class="isSignedInElsewhere(project.id)
                                ? 'bg-slate-100 text-slate-300 cursor-not-allowed'
                                : 'bg-blue-600 hover:bg-blue-700 text-white'"
                            :title="isSignedInElsewhere(project.id) ? 'Sign out of your current project first' : ''">
                            Sign In
                        </button>

                        <!-- Sign Out button -->
                        <button v-else @click="signOut(project.id)"
                            class="text-xs font-medium px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 border border-red-200 transition">
                            Sign Out
                        </button>

                    </div>
                    <!-- Chat button — links to dedicated chat page -->
                    <Link :href="`/chat/${project.id}`"
                        class="text-xs font-medium px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition flex items-center gap-1.5">
                    💬 Chat
                    </Link>
                </div>

                <!-- Total task count for this project -->
                <div class="mb-4">
                    <span class="text-xs text-slate-400">
                        Total
                        {{ project.pending.length + project.started.length + project.completed.length }}
                        task{{ (project.pending.length + project.started.length + project.completed.length) !== 1 ? 's'
                            : '' }} in this project
                    </span>
                </div>

                <!-- Three columns -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div v-for="col in columns" :key="col.key" class="rounded-2xl border p-3 min-h-48"
                        :class="[col.color, col.border]">

                        <!-- Column header -->
                        <div class="flex items-center gap-2 mb-3 px-1">
                            <div class="w-2 h-2 rounded-full" :class="col.dot"></div>
                            <span class="text-xs font-semibold text-slate-600 uppercase tracking-wide">
                                {{ col.label }}
                            </span>
                            <!-- Task count badge for this column -->
                            <span class="ml-auto text-xs font-bold text-slate-400">
                                {{ project[col.key].length }}
                            </span>
                        </div>

                        <!-- Empty column state -->
                        <div v-if="!project[col.key].length"
                            class="flex items-center justify-center h-20 text-slate-300 text-xs">
                            No tasks here
                        </div>

                        <!-- Task cards -->
                        <div v-for="task in project[col.key]" :key="task.id"
                            class="bg-white rounded-xl border shadow-sm overflow-hidden mb-2"
                            :class="hasRevision(task) ? 'border-amber-300' : 'border-slate-100'">
                            <!-- Card main row -->
                            <div class="px-3.5 py-3 cursor-pointer hover:bg-slate-50/80 transition"
                                @click="toggleExpand(task.id)">

                                <div class="flex items-start justify-between gap-2">
                                    <p class="text-sm font-medium text-slate-700 leading-snug flex-1">
                                        {{ task.title }}
                                    </p>
                                    <div class="w-2 h-2 rounded-full shrink-0 mt-1"
                                        :class="priorityStyles[task.priority]?.dot"
                                        :title="`${task.priority} priority`">
                                    </div>
                                </div>

                                <!-- Revision requested badge — high urgency indicator -->
                                <div v-if="hasRevision(task)"
                                    class="mt-2 flex items-center gap-1.5 text-xs font-semibold text-amber-700 bg-amber-50 border border-amber-200 px-2.5 py-1 rounded-lg">
                                    ⚠ Revision Requested
                                </div>

                                <!-- Meta row -->
                                <div class="flex items-center gap-2 mt-2 flex-wrap">
                                    <span v-if="task.milestone"
                                        class="text-xs text-violet-600 bg-violet-50 px-2 py-0.5 rounded-lg">
                                        {{ task.milestone.title }}
                                    </span>
                                    <span v-if="task.deadline" class="text-xs font-medium"
                                        :class="isOverdue(task) ? 'text-red-500' : 'text-slate-400'">
                                        {{ isOverdue(task) ? '⚠ ' : '' }}{{ formatDate(task.deadline) }}
                                    </span>
                                    <span class="text-xs px-2 py-0.5 rounded capitalize ml-auto"
                                        :class="priorityStyles[task.priority]?.badge">
                                        {{ task.priority }}
                                    </span>
                                </div>

                            </div>

                            <!-- Expanded detail -->
                            <div v-if="expandedTaskId === task.id"
                                class="border-t border-slate-100 px-3.5 py-3 bg-slate-50/60">

                                <!-- Description -->
                                <p v-if="task.description" class="text-xs text-slate-500 leading-relaxed mb-3">
                                    {{ task.description }}
                                </p>
                                <p v-else class="text-xs text-slate-300 italic mb-3">No description.</p>

                                <!-- PM Revision note — shown prominently when revision is requested -->
                                <div v-if="hasRevision(task) && latestSubmission(task)?.pm_note"
                                    class="mb-3 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2.5">
                                    <p class="text-xs font-semibold text-amber-700 mb-1">📝 PM Revision Note:</p>
                                    <p class="text-xs text-amber-800 leading-relaxed">
                                        {{ latestSubmission(task).pm_note }}
                                    </p>
                                </div>

                                <!-- Move buttons -->
                                <div class="flex items-center gap-2 mb-3">
                                    <button v-if="task.status !== 'pending'" @click.stop="moveTask(task, 'back')"
                                        class="flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 hover:bg-slate-100 transition">
                                        ← {{ task.status === 'started' ? 'Pending' : 'Started' }}
                                    </button>
                                    <div class="flex-1"></div>
                                    <button v-if="task.status !== 'completed'" @click.stop="moveTask(task, 'forward')"
                                        class="flex items-center gap-1.5 text-xs font-medium px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition">
                                        {{ task.status === 'pending' ? 'Start' : 'Complete' }} →
                                    </button>
                                    <span v-if="task.status === 'completed'"
                                        class="text-xs font-semibold text-emerald-600">
                                        ✓ Done
                                    </span>
                                </div>

                                <!-- Submit Work section — only on completed tasks -->
                                <div v-if="task.status === 'completed'">

                                    <!-- Already submitted — disabled state -->
                                    <div v-if="isSubmitted(task)"
                                        class="w-full text-xs font-medium py-1.5 rounded-lg bg-slate-100 text-slate-400 text-center cursor-not-allowed">
                                        ✓ Work Submitted — Awaiting Review
                                    </div>

                                    <!-- Submit form toggle -->
                                    <button v-else-if="submittingTaskId !== task.id"
                                        @click.stop="openSubmitForm(task.id)"
                                        class="w-full text-xs font-medium py-1.5 rounded-lg transition" :class="hasRevision(task)
                                            ? 'bg-amber-500 hover:bg-amber-600 text-white'
                                            : 'bg-violet-600 hover:bg-violet-700 text-white'">
                                        {{ hasRevision(task) ? '↩ Resubmit Work' : '📎 Submit Work to PM' }}
                                    </button>

                                    <!-- Submit form -->
                                    <form v-else @submit.prevent="submitWork(task)" @click.stop class="space-y-2 mt-2">

                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">
                                                Attach File
                                            </label>
                                            <input type="file" @change="submitForm.file = $event.target.files[0]"
                                                class="w-full text-xs text-slate-600 border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-violet-500" />
                                            <p v-if="submitForm.errors.file" class="text-red-500 text-xs mt-1">
                                                {{ submitForm.errors.file }}
                                            </p>
                                        </div>

                                        <div>
                                            <label class="block text-xs font-medium text-slate-600 mb-1">Comment</label>
                                            <textarea v-model="submitForm.comment" rows="2"
                                                placeholder="Any notes for the PM…"
                                                class="w-full text-xs border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-violet-500 text-slate-700 resize-none"></textarea>
                                        </div>

                                        <div class="flex gap-2">
                                            <button type="submit" :disabled="submitForm.processing"
                                                class="flex-1 py-1.5 rounded-lg bg-violet-600 hover:bg-violet-700 text-white text-xs font-medium transition disabled:opacity-50">
                                                {{ submitForm.processing ? 'Uploading…' : 'Submit' }}
                                            </button>
                                            <button type="button" @click.stop="submittingTaskId = null"
                                                class="px-3 py-1.5 rounded-lg border border-slate-200 text-slate-600 text-xs hover:bg-slate-50 transition">
                                                Cancel
                                            </button>
                                        </div>

                                    </form>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </DashboardLayout>
</template>