<script setup>
import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const props = defineProps({
    users:   Object,
    filters: Object,
})

const search = ref(props.filters.search || '')

watch(search, (value) => {
    router.get('/users', { search: value }, {
        preserveState: true,
        replace: true,
    })
})

const deleteUser = ref(null)

const confirmDelete = () => {
    router.delete(`/users/${deleteUser.value.id}`, {
        onSuccess: () => { deleteUser.value = null },
    })
}

const roleStyles = {
    'CEO':             'bg-slate-100 text-slate-600',
    'Project Manager': 'bg-blue-100 text-blue-700',
    'Designer':        'bg-violet-100 text-violet-700',
    'Developer':       'bg-indigo-100 text-indigo-700',
    'Client':          'bg-emerald-100 text-emerald-700',
}
</script>

<template>
<DashboardLayout title="Users">

    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">Manage Users</h1>
            <p class="text-slate-400 text-sm mt-0.5">{{ users.total }} user{{ users.total !== 1 ? 's' : '' }} in the system</p>
        </div>
        <Link href="/users/create"
              class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-xl transition shadow-sm shadow-blue-200">
            + New User
        </Link>
    </div>

    <!-- Search -->
    <div class="mb-4">
        <input v-model="search" type="text" placeholder="Search by name or email…"
               class="w-full md:w-80 text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700" />
    </div>

    <!-- Empty state -->
    <div v-if="!users.data.length"
         class="bg-white rounded-2xl border border-slate-100 shadow-sm px-6 py-16 text-center text-slate-400 text-sm">
        No users found.
    </div>

    <!-- Table -->
    <div v-else class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-slate-100">
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">User</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Email</th>
                    <th class="text-left px-6 py-3.5 text-xs font-semibold text-slate-400 uppercase tracking-wide">Role</th>
                    <th class="px-6 py-3.5"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                <tr v-for="user in users.data" :key="user.id"
                    class="hover:bg-slate-50/70 transition group">

                    <!-- Avatar + name -->
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                                <span class="text-white text-xs font-bold">
                                    {{ user.name?.charAt(0)?.toUpperCase() }}
                                </span>
                            </div>
                            <span class="font-semibold text-slate-800">{{ user.name }}</span>
                        </div>
                    </td>

                    <td class="px-6 py-4 text-slate-500">{{ user.email }}</td>

                    <!-- Role badge -->
                    <td class="px-6 py-4">
                        <span class="px-2.5 py-1 rounded-full text-xs font-medium"
                              :class="roleStyles[user.role?.name] ?? 'bg-slate-100 text-slate-500'">
                            {{ user.role?.name ?? '—' }}
                        </span>
                    </td>

                    <!-- Actions -->
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2 justify-end">
                            <Link :href="`/users/${user.id}/edit`"
                                  class="px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-medium transition">
                                Edit
                            </Link>
                            <button @click="deleteUser = user"
                                    class="px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 text-xs font-medium transition">
                                Delete
                            </button>
                        </div>
                    </td>

                </tr>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex flex-wrap gap-2 mt-4">
        <Link
            v-for="link in users.links"
            :key="link.label"
            :href="link.url || ''"
            v-html="link.label"
            class="px-3 py-1.5 rounded-xl border text-xs font-medium transition"
            :class="link.active
                ? 'bg-blue-600 text-white border-blue-600'
                : 'border-slate-200 text-slate-500 hover:bg-slate-50'"
        />
    </div>

    <!-- Delete modal -->
    <div v-if="deleteUser"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/30 backdrop-blur-sm px-4">
        <div class="bg-white rounded-2xl shadow-xl p-6 w-full max-w-md">
            <div class="w-12 h-12 rounded-xl bg-red-50 flex items-center justify-center text-2xl mb-4">🗑️</div>
            <h2 class="text-lg font-bold text-slate-800 mb-1">Delete User</h2>
            <p class="text-slate-500 text-sm mb-6">
                Are you sure you want to delete
                <strong class="text-slate-700">{{ deleteUser.name }}</strong>?
                This cannot be undone.
            </p>
            <div class="flex gap-3 justify-end">
                <button @click="deleteUser = null"
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