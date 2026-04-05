<script setup>
import { ref, computed } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const props = defineProps({
    project: Object,
    availableMembers: Array,
})

// ── Style maps ────────────────────────────────────────────────

const statusStyles = {
    pending: { badge: 'bg-amber-100 text-amber-700', dot: 'bg-amber-400' },
    active: { badge: 'bg-blue-100 text-blue-700', dot: 'bg-blue-500' },
    completed: { badge: 'bg-emerald-100 text-emerald-700', dot: 'bg-emerald-500' },
    cancelled: { badge: 'bg-red-100 text-red-700', dot: 'bg-red-400' },
}

const milestoneStatusStyles = {
    pending: 'bg-slate-100 text-slate-500',
    running: 'bg-blue-100 text-blue-600',
    completed: 'bg-emerald-100 text-emerald-700',
}

const milestoneStatusDot = {
    pending: 'bg-slate-300',
    running: 'bg-blue-500',
    completed: 'bg-emerald-500',
}

const taskStatusStyles = {
    pending: 'bg-slate-100 text-slate-600',
    started: 'bg-blue-100 text-blue-700',
    completed: 'bg-emerald-100 text-emerald-700',
}

const priorityStyles = {
    low: 'bg-slate-100 text-slate-500',
    medium: 'bg-amber-100 text-amber-700',
    high: 'bg-red-100 text-red-600',
}

const priorityDot = {
    low: 'bg-slate-300',
    medium: 'bg-amber-400',
    high: 'bg-red-400',
}

const formatTime = (datetime) => {
    if (!datetime) return '—'
    let str = String(datetime).trim()
    if (!str.includes('T')) str = str.replace(' ', 'T')
    if (!str.endsWith('Z') && !str.includes('+')) str += 'Z'
    return new Date(str).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

// ── PM Submission review ──────────────────────────────────────

const approveSubmission = (id) => {
    router.post(`/submissions/${id}/approve`, {}, { preserveScroll: true })
}

// Revision modal (PM → designer)
const revisionTarget = ref(null)
const revisionNote = ref('')

const openRevisionModal = (submission) => {
    revisionTarget.value = submission
    revisionNote.value = ''
}

const submitRevision = () => {
    router.post(`/submissions/${revisionTarget.value.id}/revision`, {
        pm_note: revisionNote.value,
    }, {
        preserveScroll: true,
        onSuccess: () => { revisionTarget.value = null; revisionNote.value = '' },
    })
}

// Submit to client form
const clientSubmitTarget = ref(null)   // which submission is being forwarded
const clientSubmitComment = ref('')
const clientSubmitFile = ref(null)
const clientSubmitting = ref(false)

const openClientSubmitForm = (submission) => {
    clientSubmitTarget.value = submission
    clientSubmitComment.value = ''
    clientSubmitFile.value = null
}

const submitToClient = () => {
    const form = new FormData()
    form.append('pm_to_client_comment', clientSubmitComment.value)
    if (clientSubmitFile.value) form.append('pm_to_client_file', clientSubmitFile.value)

    clientSubmitting.value = true
    router.post(`/submissions/${clientSubmitTarget.value.id}/submit-to-client`, form, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            clientSubmitTarget.value = null
            clientSubmitting.value = false
        },
        onError: () => { clientSubmitting.value = false },
    })
}

// Resubmit to client (after client revision request)
const resubmitTarget = ref(null)
const resubmitComment = ref('')
const resubmitFile = ref(null)
const resubmitting = ref(false)

const openResubmitForm = (submission) => {
    resubmitTarget.value = submission
    resubmitComment.value = ''
    resubmitFile.value = null
}

const resubmitToClient = () => {
    const form = new FormData()
    form.append('pm_to_client_comment', resubmitComment.value)
    if (resubmitFile.value) form.append('pm_to_client_file', resubmitFile.value)

    resubmitting.value = true
    router.post(`/submissions/${resubmitTarget.value.id}/resubmit-to-client`, form, {
        forceFormData: true,
        preserveScroll: true,
        onSuccess: () => {
            resubmitTarget.value = null
            resubmitting.value = false
        },
        onError: () => { resubmitting.value = false },
    })
}

const formatDate = (d) => d
    ? new Date(d).toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' })
    : '—'

const formatCurrency = (val) =>
    '$' + Number(val ?? 0).toLocaleString('en-US', { minimumFractionDigits: 2 })

// ── Computed stats ────────────────────────────────────────────

const totalTasks = computed(() => props.project.tasks?.length ?? 0)
const completedTasks = computed(() => props.project.tasks?.filter(t => t.status === 'completed').length ?? 0)
const totalMembers = computed(() => props.project.members?.length ?? 0)
const totalMilestones = computed(() => props.project.milestones?.length ?? 0)
const completedMilestones = computed(() => props.project.milestones?.filter(m => m.status === 'completed').length ?? 0)

const taskProgress = computed(() => {
    if (!totalTasks.value) return 0
    return Math.round((completedTasks.value / totalTasks.value) * 100)
})

const milestoneProgress = computed(() => {
    if (!totalMilestones.value) return 0
    return Math.round((completedMilestones.value / totalMilestones.value) * 100)
})

// ── Team member form ──────────────────────────────────────────

const showAddMember = ref(false)

const memberForm = useForm({
    user_id: '',
    role: '',
})

const submitMember = () => {
    memberForm.post(`/projects/${props.project.id}/members`, {
        onSuccess: () => {
            showAddMember.value = false
            memberForm.reset()
        },
    })
}

const removeMember = (userId) => {
    router.delete(`/projects/${props.project.id}/members/${userId}`, {
        preserveScroll: true,
    })
}

// ── Milestone create form ─────────────────────────────────────

const showAddMilestone = ref(false)

const milestoneForm = useForm({
    title: '',
    deadline: '',
    status: 'pending',
})

const submitMilestone = () => {
    milestoneForm.post(`/projects/${props.project.id}/milestones`, {
        onSuccess: () => {
            showAddMilestone.value = false
            milestoneForm.reset()
        },
    })
}

// ── Milestone edit ────────────────────────────────────────────

const editingMilestoneId = ref(null)

const editMilestoneForm = useForm({
    title: '',
    deadline: '',
    status: '',
})

const startEditMilestone = (milestone) => {
    editingMilestoneId.value = milestone.id
    editMilestoneForm.title = milestone.title
    editMilestoneForm.deadline = milestone.deadline
        ? String(milestone.deadline).substring(0, 10)
        : ''
    editMilestoneForm.status = milestone.status
}

const cancelEditMilestone = () => {
    editingMilestoneId.value = null
    editMilestoneForm.reset()
}

const submitEditMilestone = (milestone) => {
    editMilestoneForm.put(`/projects/${props.project.id}/milestones/${milestone.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            editingMilestoneId.value = null
            editMilestoneForm.reset()
        },
    })
}

// ── Milestone delete ──────────────────────────────────────────

const deleteMilestoneTarget = ref(null)

const confirmDeleteMilestone = () => {
    router.delete(`/projects/${props.project.id}/milestones/${deleteMilestoneTarget.value.id}`, {
        preserveScroll: true,
        onSuccess: () => { deleteMilestoneTarget.value = null },
    })
}

// ── Task create form ──────────────────────────────────────────

const showAddTask = ref(false)

const taskForm = useForm({
    title: '',
    description: '',
    assigned_to: '',
    milestone_id: '',
    priority: 'medium',
    status: 'pending',
    deadline: '',
})

const submitTask = () => {
    taskForm.post(`/projects/${props.project.id}/tasks`, {
        onSuccess: () => {
            showAddTask.value = false
            taskForm.reset()
            // Reset priority/status back to their defaults after reset()
            // because reset() clears everything including defaults
            taskForm.priority = 'medium'
            taskForm.status = 'pending'
        },
    })
}

// ── Task edit ─────────────────────────────────────────────────

// Same pattern as milestone editing — only one task editable at a time
const editingTaskId = ref(null)

const editTaskForm = useForm({
    title: '',
    description: '',
    assigned_to: '',
    milestone_id: '',
    priority: '',
    status: '',
    deadline: '',
})

const startEditTask = (task) => {
    editingTaskId.value = task.id
    editTaskForm.title = task.title
    editTaskForm.description = task.description ?? ''
    editTaskForm.assigned_to = task.assigned_to
    // milestone_id can be null if task has no milestone
    editTaskForm.milestone_id = task.milestone_id ?? ''
    editTaskForm.priority = task.priority
    editTaskForm.status = task.status
    editTaskForm.deadline = task.deadline
        ? String(task.deadline).substring(0, 10)
        : ''
}

const cancelEditTask = () => {
    editingTaskId.value = null
    editTaskForm.reset()
}

const submitEditTask = (task) => {
    editTaskForm.put(`/projects/${props.project.id}/tasks/${task.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            editingTaskId.value = null
            editTaskForm.reset()
        },
    })
}

// ── Task delete ───────────────────────────────────────────────

const deleteTaskTarget = ref(null)

const confirmDeleteTask = () => {
    router.delete(`/projects/${props.project.id}/tasks/${deleteTaskTarget.value.id}`, {
        preserveScroll: true,
        onSuccess: () => { deleteTaskTarget.value = null },
    })
}
</script>

<template>
    <DashboardLayout :title="project.title">

        <!-- ── Back + Header ──────────────────────── -->
        <div class="mb-6">

            <Link href="/projects"
                class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-slate-600 transition mb-4">
                ← Back to Projects
            </Link>

            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
                <div class="flex-1 min-w-0">
                    <div class="flex items-center gap-3 flex-wrap">
                        <h1 class="text-2xl font-bold text-slate-800 truncate">{{ project.title }}</h1>
                        <span class="px-2.5 py-1 rounded-full text-xs font-semibold capitalize"
                            :class="statusStyles[project.status]?.badge">
                            {{ project.status }}
                        </span>
                    </div>
                    <p class="text-slate-400 text-sm mt-1">
                        {{ project.description || 'No description provided.' }}
                    </p>
                </div>
                <Link :href="`/projects/${project.id}/edit`"
                    class="shrink-0 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition shadow-sm shadow-blue-200">
                    Edit Project
                </Link>
            </div>

        </div>

        <!-- ── Info bar ───────────────────────────── -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-4 py-3">
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Client</p>
                <p class="text-sm font-semibold text-slate-700">{{ project.client?.name ?? '—' }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-4 py-3">
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Manager</p>
                <p class="text-sm font-semibold text-slate-700">{{ project.manager?.name ?? '—' }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-4 py-3">
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Budget</p>
                <p class="text-sm font-semibold text-slate-700">{{ formatCurrency(project.budget) }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-4 py-3">
                <p class="text-xs text-slate-400 font-medium uppercase tracking-wide mb-1">Deadline</p>
                <p class="text-sm font-semibold text-slate-700">{{ formatDate(project.deadline) }}</p>
            </div>
        </div>

        <!-- ── Stat cards ─────────────────────────── -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-xl shrink-0">👥</div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ totalMembers }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Team Members</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-violet-50 flex items-center justify-center text-xl shrink-0">🏁
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ totalMilestones }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Milestones</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center text-xl shrink-0">📋</div>
                <div>
                    <p class="text-2xl font-bold text-slate-800">{{ totalTasks }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Total Tasks</p>
                </div>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-xl shrink-0">✅
                </div>
                <div>
                    <p class="text-2xl font-bold text-emerald-600">{{ completedTasks }}</p>
                    <p class="text-xs text-slate-400 mt-0.5">Tasks Done</p>
                </div>
            </div>
        </div>

        <!-- ── Progress bars ──────────────────────── -->
        <div class="grid md:grid-cols-2 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-6 py-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-semibold text-slate-700">Milestone Progress</p>
                    <p class="text-sm font-bold text-blue-600">{{ milestoneProgress }}%</p>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                    <div class="h-2.5 rounded-full bg-blue-500 transition-all duration-500"
                        :style="{ width: milestoneProgress + '%' }"></div>
                </div>
                <p class="text-xs text-slate-400 mt-2">{{ completedMilestones }} of {{ totalMilestones }} completed</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm px-6 py-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-sm font-semibold text-slate-700">Task Progress</p>
                    <p class="text-sm font-bold text-blue-600">{{ taskProgress }}%</p>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-2.5 overflow-hidden">
                    <div class="h-2.5 rounded-full bg-blue-500 transition-all duration-500"
                        :style="{ width: taskProgress + '%' }"></div>
                </div>
                <p class="text-xs text-slate-400 mt-2">{{ completedTasks }} of {{ totalTasks }} completed</p>
            </div>
        </div>

        <!-- ── Team + Milestones ──────────────────── -->
        <div class="grid md:grid-cols-2 gap-6 mb-6">

            <!-- Team Members -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <div>
                        <h2 class="font-semibold text-slate-800">Team Members</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Designers & developers on this project</p>
                    </div>
                    <button @click="showAddMember = !showAddMember"
                        class="text-xs font-medium px-3 py-1.5 rounded-lg transition"
                        :class="showAddMember ? 'bg-slate-100 text-slate-600' : 'bg-blue-600 text-white hover:bg-blue-700'">
                        {{ showAddMember ? '✕ Cancel' : '+ Add Member' }}
                    </button>
                </div>
                <div v-show="showAddMember" class="px-5 py-4 bg-slate-50 border-b border-slate-100">
                    <form @submit.prevent="submitMember" class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Select Person</label>
                            <select v-model="memberForm.user_id"
                                class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700">
                                <option value="" disabled>Choose a designer or developer…</option>
                                <option v-for="m in availableMembers" :key="m.id" :value="m.id">
                                    {{ m.name }} ({{ m.role?.name }})
                                </option>
                            </select>
                            <p v-if="memberForm.errors.user_id" class="text-red-500 text-xs mt-1">{{
                                memberForm.errors.user_id }}</p>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Project Role</label>
                            <select v-model="memberForm.role"
                                class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700">
                                <option value="" disabled>Choose role…</option>
                                <option value="designer">Designer</option>
                                <option value="developer">Developer</option>
                            </select>
                            <p v-if="memberForm.errors.role" class="text-red-500 text-xs mt-1">{{ memberForm.errors.role
                                }}</p>
                        </div>
                        <p v-if="availableMembers.length === 0" class="text-xs text-slate-400 italic">
                            All designers and developers are already on this project.
                        </p>
                        <button type="submit"
                            :disabled="memberForm.processing || !memberForm.user_id || !memberForm.role"
                            class="w-full py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ memberForm.processing ? 'Adding…' : 'Add to Project' }}
                        </button>
                    </form>
                </div>
                <div v-if="project.members?.length" class="divide-y divide-slate-50">
                    <div v-for="member in project.members" :key="member.id"
                        class="flex items-center justify-between px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                                <span class="text-white text-xs font-bold">{{ member.name?.charAt(0)?.toUpperCase()
                                    }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-slate-700">{{ member.name }}</p>
                                <p class="text-xs text-slate-400 capitalize">{{ member.pivot?.role }}</p>
                            </div>
                        </div>
                        <button @click="removeMember(member.id)"
                            class="text-xs text-slate-400 bg-slate-100 hover:bg-red-50 hover:text-red-400 transition px-2 py-1 rounded-lg">
                            Remove
                        </button>
                    </div>
                </div>
                <div v-else class="px-5 py-10 text-center text-slate-400 text-sm">
                    <p class="text-2xl mb-2">👤</p>
                    No team members assigned yet.
                </div>
            </div>

            <!-- Milestones -->
            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                    <div>
                        <h2 class="font-semibold text-slate-800">Milestones</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Project phases and delivery points</p>
                    </div>
                    <button @click="showAddMilestone = !showAddMilestone"
                        class="text-xs font-medium px-3 py-1.5 rounded-lg transition"
                        :class="showAddMilestone ? 'bg-slate-100 text-slate-600' : 'bg-blue-600 text-white hover:bg-blue-700'">
                        {{ showAddMilestone ? '✕ Cancel' : '+ Add Milestone' }}
                    </button>
                </div>

                <div v-show="showAddMilestone" class="px-5 py-4 bg-slate-50 border-b border-slate-100">
                    <form @submit.prevent="submitMilestone" class="space-y-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">
                                Title <span class="text-red-400">*</span>
                            </label>
                            <input v-model="milestoneForm.title" type="text"
                                placeholder="e.g. Design Handoff, Beta Launch…"
                                class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 placeholder-slate-300" />
                            <p v-if="milestoneForm.errors.title" class="text-red-500 text-xs mt-1">{{
                                milestoneForm.errors.title }}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Deadline</label>
                                <input v-model="milestoneForm.deadline" type="date"
                                    class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700" />
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                                <select v-model="milestoneForm.status"
                                    class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700">
                                    <option value="pending">Pending</option>
                                    <option value="running">Running</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>
                        </div>
                        <button type="submit" :disabled="milestoneForm.processing"
                            class="w-full py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ milestoneForm.processing ? 'Saving…' : 'Add Milestone' }}
                        </button>
                    </form>
                </div>

                <div v-if="project.milestones?.length" class="divide-y divide-slate-50">
                    <div v-for="milestone in project.milestones" :key="milestone.id">

                        <!-- View row -->
                        <div v-if="editingMilestoneId !== milestone.id"
                            class="flex items-center justify-between px-5 py-3.5 group hover:bg-slate-50/60 transition">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <div class="w-2.5 h-2.5 rounded-full shrink-0"
                                    :class="milestoneStatusDot[milestone.status]"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-slate-700 truncate">{{ milestone.title }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">Due: {{ formatDate(milestone.deadline) }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 shrink-0">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium capitalize"
                                    :class="milestoneStatusStyles[milestone.status]">
                                    {{ milestone.status }}
                                </span>
                                <button @click="startEditMilestone(milestone)"
                                    class="text-xs text-slate-300 hover:text-blue-500 px-2 py-1 rounded-lg hover:bg-blue-50 transition opacity-0 group-hover:opacity-100">
                                    Edit
                                </button>
                                <button @click="deleteMilestoneTarget = milestone"
                                    class="text-xs text-slate-300 hover:text-red-400 px-2 py-1 rounded-lg hover:bg-red-50 transition opacity-0 group-hover:opacity-100">
                                    Delete
                                </button>
                            </div>
                        </div>

                        <!-- Edit row -->
                        <div v-else class="px-5 py-4 bg-blue-50/40 border-l-2 border-blue-400">
                            <p class="text-xs font-semibold text-blue-600 mb-3 uppercase tracking-wide">Editing
                                Milestone</p>
                            <form @submit.prevent="submitEditMilestone(milestone)" class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Title</label>
                                    <input v-model="editMilestoneForm.title" type="text"
                                        class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700" />
                                    <p v-if="editMilestoneForm.errors.title" class="text-red-500 text-xs mt-1">{{
                                        editMilestoneForm.errors.title }}</p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Deadline</label>
                                        <input v-model="editMilestoneForm.deadline" type="date"
                                            class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700" />
                                    </div>
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                                        <select v-model="editMilestoneForm.status"
                                            class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700">
                                            <option value="pending">Pending</option>
                                            <option value="running">Running</option>
                                            <option value="completed">Completed</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <button type="submit" :disabled="editMilestoneForm.processing"
                                        class="flex-1 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition disabled:opacity-50">
                                        {{ editMilestoneForm.processing ? 'Saving…' : 'Save Changes' }}
                                    </button>
                                    <button type="button" @click="cancelEditMilestone"
                                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm hover:bg-slate-50 transition">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>

                <div v-else class="px-5 py-10 text-center text-slate-400 text-sm">
                    <p class="text-2xl mb-2">🏁</p>
                    No milestones yet. Add one to track project phases.
                </div>
            </div>

        </div>

        <!-- ── Tasks ──────────────────────────────── -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">

            <!-- Panel header -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
                <div>
                    <h2 class="font-semibold text-slate-800">Tasks</h2>
                    <p class="text-xs text-slate-400 mt-0.5">All tasks assigned within this project</p>
                </div>
                <button @click="showAddTask = !showAddTask"
                    class="text-xs font-medium px-3 py-1.5 rounded-lg transition"
                    :class="showAddTask ? 'bg-slate-100 text-slate-600' : 'bg-blue-600 text-white hover:bg-blue-700'">
                    {{ showAddTask ? '✕ Cancel' : '+ Add Task' }}
                </button>
            </div>

            <!-- ── Add Task form ──────────────────── -->
            <div v-show="showAddTask" class="px-6 py-5 bg-slate-50 border-b border-slate-100">

                <!-- Warning shown if there are no team members yet -->
                <!-- You can't assign a task if nobody is on the project -->
                <div v-if="!project.members?.length"
                    class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-xl px-4 py-3">
                    ⚠ You need to add team members before creating tasks.
                </div>

                <form v-else @submit.prevent="submitTask" class="space-y-4">

                    <!-- Title -->
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">
                            Task Title <span class="text-red-400">*</span>
                        </label>
                        <input v-model="taskForm.title" type="text" placeholder="e.g. Design homepage wireframe…"
                            class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 placeholder-slate-300" />
                        <p v-if="taskForm.errors.title" class="text-red-500 text-xs mt-1">{{ taskForm.errors.title }}
                        </p>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Description</label>
                        <textarea v-model="taskForm.description" rows="2"
                            placeholder="Optional details about this task…"
                            class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 placeholder-slate-300 resize-none"></textarea>
                    </div>

                    <!-- Assign to + Milestone -->
                    <div class="grid grid-cols-2 gap-3">

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">
                                Assign To <span class="text-red-400">*</span>
                            </label>
                            <!-- Only project members appear here — not all users in the system -->
                            <select v-model="taskForm.assigned_to"
                                class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700">
                                <option value="" disabled>Choose team member…</option>
                                <option v-for="member in project.members" :key="member.id" :value="member.id">
                                    {{ member.name }} ({{ member.pivot?.role }})
                                </option>
                            </select>
                            <p v-if="taskForm.errors.assigned_to" class="text-red-500 text-xs mt-1">{{
                                taskForm.errors.assigned_to }}</p>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Milestone</label>
                            <select v-model="taskForm.milestone_id"
                                class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700">
                                <!-- Empty option = no milestone linked -->
                                <option value="">No milestone</option>
                                <option v-for="ms in project.milestones" :key="ms.id" :value="ms.id">
                                    {{ ms.title }}
                                </option>
                            </select>
                        </div>

                    </div>

                    <!-- Priority + Status + Deadline -->
                    <div class="grid grid-cols-3 gap-3">

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Priority</label>
                            <select v-model="taskForm.priority"
                                class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700">
                                <option value="low">Low</option>
                                <option value="medium">Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                            <select v-model="taskForm.status"
                                class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700">
                                <option value="pending">Pending</option>
                                <option value="started">Started</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-slate-600 mb-1">Deadline</label>
                            <input v-model="taskForm.deadline" type="date"
                                class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700" />
                        </div>

                    </div>

                    <button type="submit" :disabled="taskForm.processing"
                        class="w-full py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                        {{ taskForm.processing ? 'Creating…' : 'Create Task' }}
                    </button>

                </form>

            </div>

            <!-- ── Task list ──────────────────────── -->
            <div v-if="!project.tasks?.length" class="px-6 py-10 text-center text-slate-400 text-sm">
                <p class="text-2xl mb-2">📋</p>
                No tasks yet. Add the first task above.
            </div>

            <div v-else class="divide-y divide-slate-50">

                <div v-for="task in project.tasks" :key="task.id">

                    <!-- ── Task view row ──────────── -->
                    <div v-if="editingTaskId !== task.id"
                        class="flex items-center gap-4 px-6 py-4 group hover:bg-slate-50/60 transition">

                        <!-- Priority dot -->
                        <div class="w-2.5 h-2.5 rounded-full shrink-0" :class="priorityDot[task.priority]"></div>

                        <!-- Task info -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-700 truncate">{{ task.title }}</p>
                            <div class="flex items-center gap-3 mt-0.5 text-xs text-slate-400 flex-wrap">
                                <span>{{ task.assignee?.name ?? '—' }}</span>
                                <span v-if="task.milestone" class="text-slate-300">·</span>
                                <span v-if="task.milestone">{{ task.milestone.title }}</span>
                                <span class="text-slate-300">·</span>
                                <span>Due {{ formatDate(task.deadline) }}</span>
                            </div>
                        </div>

                        <!-- Priority badge -->
                        <span class="px-2 py-0.5 rounded text-xs capitalize hidden md:block shrink-0"
                            :class="priorityStyles[task.priority]">
                            {{ task.priority }}
                        </span>

                        <!-- Status badge -->
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium capitalize shrink-0"
                            :class="taskStatusStyles[task.status]">
                            {{ task.status }}
                        </span>

                        <!-- Edit + Delete — appear on hover -->
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition shrink-0">
                            <button @click="startEditTask(task)"
                                class="text-xs text-slate-300 hover:text-blue-500 px-2 py-1 rounded-lg hover:bg-blue-50 transition">
                                Edit
                            </button>
                            <button @click="deleteTaskTarget = task"
                                class="text-xs text-slate-300 hover:text-red-400 px-2 py-1 rounded-lg hover:bg-red-50 transition">
                                Delete
                            </button>
                        </div>

                    </div>

                    <!-- ── Task edit row ──────────── -->
                    <!-- Replaces the view row inline when this task is being edited -->
                    <div v-else class="px-6 py-5 bg-blue-50/40 border-l-2 border-blue-400">

                        <p class="text-xs font-semibold text-blue-600 mb-3 uppercase tracking-wide">Editing Task</p>

                        <form @submit.prevent="submitEditTask(task)" class="space-y-4">

                            <!-- Title -->
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Title</label>
                                <input v-model="editTaskForm.title" type="text"
                                    class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700" />
                                <p v-if="editTaskForm.errors.title" class="text-red-500 text-xs mt-1">{{
                                    editTaskForm.errors.title }}</p>
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-xs font-medium text-slate-600 mb-1">Description</label>
                                <textarea v-model="editTaskForm.description" rows="2"
                                    class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 resize-none">
                            </textarea>
                            </div>

                            <!-- Assign to + Milestone -->
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Assign To</label>
                                    <select v-model="editTaskForm.assigned_to"
                                        class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700">
                                        <option value="" disabled>Choose team member…</option>
                                        <option v-for="member in project.members" :key="member.id" :value="member.id">
                                            {{ member.name }} ({{ member.pivot?.role }})
                                        </option>
                                    </select>
                                    <p v-if="editTaskForm.errors.assigned_to" class="text-red-500 text-xs mt-1">{{
                                        editTaskForm.errors.assigned_to }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Milestone</label>
                                    <select v-model="editTaskForm.milestone_id"
                                        class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700">
                                        <option value="">No milestone</option>
                                        <option v-for="ms in project.milestones" :key="ms.id" :value="ms.id">
                                            {{ ms.title }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Priority + Status + Deadline -->
                            <div class="grid grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Priority</label>
                                    <select v-model="editTaskForm.priority"
                                        class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700">
                                        <option value="low">Low</option>
                                        <option value="medium">Medium</option>
                                        <option value="high">High</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                                    <select v-model="editTaskForm.status"
                                        class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700">
                                        <option value="pending">Pending</option>
                                        <option value="started">Started</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Deadline</label>
                                    <input v-model="editTaskForm.deadline" type="date"
                                        class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700" />
                                </div>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" :disabled="editTaskForm.processing"
                                    class="flex-1 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium transition disabled:opacity-50">
                                    {{ editTaskForm.processing ? 'Saving…' : 'Save Changes' }}
                                </button>
                                <button type="button" @click="cancelEditTask"
                                    class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm hover:bg-slate-50 transition">
                                    Cancel
                                </button>
                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </div>

        <!-- ── Work Session History ───────────────── -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mt-6">

            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-800">Work Session History</h2>
                <p class="text-xs text-slate-400 mt-0.5">Login and logout times for all team members</p>
            </div>

            <div v-if="!project.work_sessions?.length" class="px-6 py-10 text-center text-slate-400 text-sm">
                <p class="text-2xl mb-2">🕐</p>
                No work sessions recorded yet.
            </div>

            <div v-else class="divide-y divide-slate-50">
                <div v-for="session in project.work_sessions" :key="session.id"
                    class="flex items-center gap-4 px-6 py-3.5">

                    <!-- User avatar -->
                    <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                        <span class="text-white text-xs font-bold">
                            {{ session.user?.name?.charAt(0)?.toUpperCase() }}
                        </span>
                    </div>

                    <!-- Name + dates -->
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-700">{{ session.user?.name }}</p>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ formatDate(session.start_time) }}
                        </p>
                    </div>

                    <!-- Sign in time -->
                    <div class="text-center hidden md:block">
                        <p class="text-xs text-slate-400">Signed In</p>
                        <p class="text-sm font-medium text-slate-700">
                            {{ formatTime(session.start_time) }}
                        </p>
                    </div>

                    <!-- Sign out time -->
                    <div class="text-center hidden md:block">
                        <p class="text-xs text-slate-400">Signed Out</p>
                        <p class="text-sm font-medium text-slate-700">
                            {{ session.end_time
                                ? formatTime(session.end_time)
                                : '—' }}
                        </p>
                    </div>

                    <!-- Duration badge -->
                    <div class="shrink-0">
                        <span v-if="session.duration"
                            class="px-2.5 py-1 rounded-full text-xs font-medium bg-blue-50 text-blue-600">
                            {{ session.duration }} min
                        </span>
                        <!-- Active session — still in progress -->
                        <span v-else
                            class="px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-50 text-emerald-600 animate-pulse">
                            Active
                        </span>
                    </div>

                </div>
            </div>

        </div>

        <!-- ── Submissions Review (PM) ────────────── -->
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden mt-6">

            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="font-semibold text-slate-800">Submissions</h2>
                <p class="text-xs text-slate-400 mt-0.5">Work submitted by your team for review</p>
            </div>

            <template v-if="project.tasks?.some(t => t.submissions?.length)">

                <div v-for="task in project.tasks.filter(t => t.submissions?.length)" :key="task.id"> 

                    <!-- Task header -->
                    <div class="px-6 py-2.5 bg-slate-100 border-b border-slate-100">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">
                            Task: {{ task.title }}
                        </p>
                    </div>

                    <div v-for="submission in task.submissions" :key="submission.id"
                        class="px-6 py-6 border-b border-slate-200">

                        <div class="flex items-start justify-between gap-4 flex-wrap">

                            <!-- Left: submitter info + files -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <div
                                        class="w-6 h-6 rounded-lg bg-blue-600 flex items-center justify-center shrink-0">
                                        <span class="text-white text-xs font-bold">
                                            {{ submission.submitter?.name?.charAt(0)?.toUpperCase() }}
                                        </span>
                                    </div>
                                    <span class="text-sm font-medium text-slate-700">{{
                                        submission.submitter?.name}}</span>
                                    <span class="text-xs text-slate-400">{{ formatDate(submission.created_at) }}</span>
                                </div>

                                <p v-if="submission.comment"
                                    class="text-xs text-semibold text-slate-700 ml-8 mb-3 mt-3">
                                    Note: <span class="text-xs text-slate-500 italic mb-3 mt-3"> {{ submission.comment
                                        }} </span>
                                </p>

                                <!-- Designer's file -->
                                <a :href="`/storage/${submission.file_path}`"
                                    :download="submission.file_path.split('/').pop()"
                                    class="inline-flex items-center gap-1.5 text-xs font-medium text-blue-600 bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg transition ml-8 mb-1 mt-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none"
                                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Designer's File
                                </a>


                            </div>

                            <!-- Right: status + actions -->
                            <div class="flex items-end gap-3 shrink-0">

                                <!-- PM review status -->
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium" :class="{
                                    'bg-amber-100 text-amber-700': submission.status === 'pending_review',
                                    'bg-emerald-100 text-emerald-700': submission.status === 'approved',
                                    'bg-red-100 text-red-600': submission.status === 'revision_requested',
                                }">
                                    {{ submission.status.replace(/_/g, ' ')
                                    .replace(/\b\w/g, char => char.toUpperCase()) }}
                                </span>

                                <!-- Client status (if sent to client) -->
                                <span v-if="submission.client_submitted"
                                    class="px-2.5 py-1 rounded-full text-xs font-medium" :class="{
                                        'bg-blue-100 text-blue-700': submission.client_status === 'pending',
                                        'bg-emerald-100 text-emerald-700': submission.client_status === 'approved',
                                        'bg-red-100 text-red-600': submission.client_status === 'revision_requested',
                                    }">
                                    Client: {{submission.client_status
                                        .replace(/_/g, ' ')
                                    .replace(/\b\w/g, char => char.toUpperCase()) }}
                                </span>

                                <!-- PM review actions — only for pending_review -->
                                <div v-if="submission.status === 'pending_review'" class="flex gap-2">
                                    <button @click="approveSubmission(submission.id)"
                                        class="text-xs font-medium px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition">
                                        Accept
                                    </button>
                                    <button @click="openRevisionModal(submission)"
                                        class="text-xs font-medium px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white transition">
                                        Request Revision
                                    </button>
                                </div>

                                <!-- Submit to client form toggle — only after PM accepts and not yet sent -->
                                <button v-if="submission.status === 'approved' && !submission.client_submitted"
                                    @click="openClientSubmitForm(submission)"
                                    class="text-xs font-medium px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition">
                                    Submit to Client →
                                </button>

                                <!-- Resubmit to client — only when client requested revision -->
                                <button v-if="submission.client_status === 'revision_requested'"
                                    @click="openResubmitForm(submission)"
                                    class="text-xs font-medium px-3 py-1.5 rounded-lg bg-violet-600 hover:bg-violet-700 text-white transition">
                                    Resubmit to Client
                                </button>

                            </div>

                            <!-- Client revision note (shown when client requested changes) -->
                            <div v-if="submission.client_status === 'revision_requested' && submission.client_note"
                                class="ml-8 bg-red-50 border border-red-200 rounded-xl px-3 py-2 w-full">
                                <p class="text-xs font-semibold text-red-600 mb-1">⚠ Client Revision Request:</p>
                                <p class="text-xs text-red-700">{{ submission.client_note }}</p>
                            </div>

                            <!-- PM revision note (shown to PM as reminder) -->
                            <p v-if="submission.pm_note"
                                class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-lg px-3 py-1.5 ml-8 mb-2  w-full">
                                Your revision note: {{ submission.pm_note }}
                            </p>

                        </div>

                        <!-- Submit to client inline form -->
                        <div v-if="clientSubmitTarget?.id === submission.id"
                            class="mt-4 border-t border-slate-100 pt-4 bg-blue-50/40 rounded-xl p-4">
                            <p class="text-xs font-semibold text-blue-700 mb-3 uppercase tracking-wide">
                                Submit to Client
                            </p>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Your Message to
                                        Client</label>
                                    <textarea v-model="clientSubmitComment" rows="2"
                                        placeholder="Optional note for the client…"
                                        class="w-full text-xs border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 resize-none">
                            </textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">
                                        Attach File (optional — client will also see designer's file)
                                    </label>
                                    <input type="file" @change="clientSubmitFile = $event.target.files[0]"
                                        class="w-full text-xs border border-slate-200 rounded-xl px-3 py-2 bg-white" />
                                </div>
                                <div class="flex gap-2">
                                    <button @click="submitToClient" :disabled="clientSubmitting"
                                        class="flex-1 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium transition disabled:opacity-50">
                                        {{ clientSubmitting ? 'Sending…' : 'Send to Client' }}
                                    </button>
                                    <button @click="clientSubmitTarget = null"
                                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs hover:bg-slate-50 transition">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Resubmit to client inline form -->
                        <div v-if="resubmitTarget?.id === submission.id"
                            class="mt-4 border-t border-slate-100 pt-4 bg-violet-50/40 rounded-xl p-4">
                            <p class="text-xs font-semibold text-violet-700 mb-3 uppercase tracking-wide">
                                Resubmit to Client
                            </p>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Updated Message</label>
                                    <textarea v-model="resubmitComment" rows="2" placeholder="Explain what was changed…"
                                        class="w-full text-xs border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-violet-500 text-slate-700 resize-none">
                            </textarea>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Updated File
                                        (optional)</label>
                                    <input type="file" @change="resubmitFile = $event.target.files[0]"
                                        class="w-full text-xs border border-slate-200 rounded-xl px-3 py-2 bg-white" />
                                </div>
                                <div class="flex gap-2">
                                    <button @click="resubmitToClient" :disabled="resubmitting"
                                        class="flex-1 py-2 rounded-xl bg-violet-600 hover:bg-violet-700 text-white text-xs font-medium transition disabled:opacity-50">
                                        {{ resubmitting ? 'Sending…' : 'Resubmit' }}
                                    </button>
                                    <button @click="resubmitTarget = null"
                                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-xs hover:bg-slate-50 transition">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

            </template>

            <div v-else class="px-6 py-10 text-center text-slate-400 text-sm">
                <p class="text-2xl mb-2">📬</p>
                No submissions yet.
            </div>

        </div>

        <!-- PM → Designer revision modal -->
        <div v-if="revisionTarget"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
                <h2 class="text-lg font-bold text-slate-800 mb-1">Request Revision from Designer</h2>
                <p class="text-slate-400 text-sm mb-4">Explain what needs to change.</p>
                <textarea v-model="revisionNote" rows="3" placeholder="e.g. Please adjust the font size…"
                    class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 resize-none mb-4">
        </textarea>
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

        <!-- ── Revision request modal ─────────────── -->
        <div v-if="revisionTarget"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
                <h2 class="text-lg font-bold text-slate-800 mb-1">Request Revision</h2>
                <p class="text-slate-400 text-sm mb-4">
                    Explain what needs to change so the designer can revise the work.
                </p>
                <textarea v-model="revisionNote" rows="3"
                    placeholder="e.g. Please adjust the font size and update the color palette…"
                    class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 resize-none mb-4"></textarea>
                <div class="flex gap-3 justify-end">
                    <button @click="revisionTarget = null; revisionNote = ''"
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

        <!-- ── Delete Milestone modal ─────────────── -->
        <div v-if="deleteMilestoneTarget"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
                <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-2xl mb-4">🗑️</div>
                <h2 class="text-lg font-bold text-slate-800 mb-1">Delete Milestone</h2>
                <p class="text-slate-500 text-sm mb-3">
                    Are you sure you want to delete
                    <strong class="text-slate-700">{{ deleteMilestoneTarget.title }}</strong>?
                </p>
                <p class="text-xs text-amber-700 bg-amber-50 border border-amber-100 rounded-xl px-3 py-2 mb-6">
                    ⚠ Tasks linked to this milestone will not be deleted — they will become unlinked.
                </p>
                <div class="flex gap-3 justify-end">
                    <button @click="deleteMilestoneTarget = null"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button @click="confirmDeleteMilestone"
                        class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition">
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>

        <!-- ── Delete Task modal ──────────────────── -->
        <div v-if="deleteTaskTarget"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4">
            <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
                <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-2xl mb-4">🗑️</div>
                <h2 class="text-lg font-bold text-slate-800 mb-1">Delete Task</h2>
                <p class="text-slate-500 text-sm mb-6">
                    Are you sure you want to delete
                    <strong class="text-slate-700">{{ deleteTaskTarget.title }}</strong>?
                    This cannot be undone.
                </p>
                <div class="flex gap-3 justify-end">
                    <button @click="deleteTaskTarget = null"
                        class="px-4 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm font-medium hover:bg-slate-50 transition">
                        Cancel
                    </button>
                    <button @click="confirmDeleteTask"
                        class="px-4 py-2 rounded-xl bg-red-600 hover:bg-red-700 text-white text-sm font-medium transition">
                        Yes, Delete
                    </button>
                </div>
            </div>
        </div>

    </DashboardLayout>
</template>