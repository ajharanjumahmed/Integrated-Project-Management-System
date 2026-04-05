<script setup>
import { useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import Multiselect from 'vue-multiselect'

const props = defineProps({
    project:  Object,
    clients:  Array,
    managers: Array,
})

const form = useForm({
    title:              props.project.title,
    description:        props.project.description ?? '',
    client_id:          props.clients.find(c => c.id === props.project.client_id) ?? null,
    project_manager_id: props.managers.find(m => m.id === props.project.project_manager_id) ?? null,
    budget:             props.project.budget ?? '',
    start_date:         props.project.start_date
                            ? String(props.project.start_date).substring(0, 10)
                            : '',
    deadline:           props.project.deadline
                            ? String(props.project.deadline).substring(0, 10)
                            : '',
    status:             props.project.status,
})

const submit = () => {
    // Extract IDs from the multiselect objects before sending
    form.transform(data => ({
        ...data,
        client_id:          data.client_id?.id ?? data.client_id,
        project_manager_id: data.project_manager_id?.id ?? data.project_manager_id,
    })).put(`/projects/${props.project.id}`)
}
</script>

<template>
<DashboardLayout title="Edit Project">

    <div class="mb-6">
        <a href="/projects" class="text-sm text-slate-400 hover:text-slate-600 transition">
            ← Back to Projects
        </a>
        <h1 class="text-2xl font-bold text-slate-800 mt-3">Edit Project</h1>
        <p class="text-slate-400 text-sm mt-1">Update the details for {{ project.title }}</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 max-w-2xl">
        <form @submit.prevent="submit" class="space-y-5">

            <!-- Title -->
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">
                    Project Title <span class="text-red-400">*</span>
                </label>
                <input v-model="form.title" type="text"
                       class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700" />
                <p v-if="form.errors.title" class="text-red-500 text-xs mt-1">{{ form.errors.title }}</p>
            </div>

            <!-- Description -->
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Description</label>
                <textarea v-model="form.description" rows="3"
                          class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 resize-none"></textarea>
            </div>

            <!-- Client -->
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">
                    Client <span class="text-red-400">*</span>
                </label>
                <Multiselect v-model="form.client_id" :options="clients"
                             label="name" track-by="id" class="text-slate-700 bg-slate-100 px-2 py-3 rounded-lg text-sm" placeholder="Search client…" />
                <p v-if="form.errors.client_id" class="text-red-500 text-xs mt-1">{{ form.errors.client_id }}</p>
            </div>

            <!-- Project Manager -->
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">
                    Project Manager <span class="text-red-400">*</span>
                </label>
                <Multiselect v-model="form.project_manager_id" :options="managers"
                             label="name" track-by="id" class="text-slate-700 bg-slate-100 px-2 py-3 rounded-lg text-sm" placeholder="Search manager…" />
                <p v-if="form.errors.project_manager_id" class="text-red-500 text-xs mt-1">{{ form.errors.project_manager_id }}</p>
            </div>

            <!-- Budget -->
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Budget ($)</label>
                <input v-model="form.budget" type="number" min="0" step="0.01"
                       class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700" />
                <p v-if="form.errors.budget" class="text-red-500 text-xs mt-1">{{ form.errors.budget }}</p>
            </div>

            <!-- Start Date + Deadline -->
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Start Date</label>
                    <input v-model="form.start_date" type="date"
                           class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700" />
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">Deadline</label>
                    <input v-model="form.deadline" type="date"
                           class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700" />
                    <p v-if="form.errors.deadline" class="text-red-500 text-xs mt-1">{{ form.errors.deadline }}</p>
                </div>
            </div>

            <!-- Status -->
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Status</label>
                <select v-model="form.status"
                        class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700">
                    <option value="pending">Pending</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex gap-3 pt-2">
                <button type="submit" :disabled="form.processing"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-xl transition disabled:opacity-50">
                    {{ form.processing ? 'Saving…' : 'Save Changes' }}
                </button>
                <a href="/projects"
                   class="px-5 py-2 rounded-xl border border-slate-200 text-slate-600 text-sm hover:bg-slate-50 transition">
                    Cancel
                </a>
            </div>

        </form>
    </div>

</DashboardLayout>
</template>

<style>
.multiselect__content-wrapper {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    margin-top: 6px;
    padding: 8px;
}
.multiselect__option--highlight {
    background: #eff6ff;
    color: #1d4ed8;
    border-radius: 8px;
}
.multiselect__option--selected {
    background: #dbeafe;
    color: #1d4ed8;
    border-radius: 8px;
}
</style>