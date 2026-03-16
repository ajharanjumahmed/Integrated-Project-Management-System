<script setup>

import { Link, router } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const props = defineProps({
    users: Object,
    filters: Object
})

const search = ref(props.filters.search || '')

watch(search, (value) => {
    router.get('/users', { search: value }, {
        preserveState: true,
        replace: true
    })
})

const deleteUser = (id) => {
    if(confirm('Are you sure you want to delete this user?')){
        router.delete(`/users/${id}`)
    }
}

</script>

<template>

<DashboardLayout title="Users">

<div class="flex flex-col gap-6">

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <h1 class="text-3xl font-bold text-gray-700">
            Manage Users
        </h1>

        <Link
            href="/users/create"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 w-fit"
        >
            Create User
        </Link>

    </div>

    <input
        v-model="search"
        type="text"
        placeholder="Search users..."
        class="bg-white border border-gray-300 rounded px-4 py-2 w-full md:w-80 text-gray-900"
    />

    <div class="hidden md:block bg-white rounded shadow-sm overflow-x-auto">

        <table class="w-full text-left">

            <thead class="bg-blue-600">
                <tr>
                    <th class="p-5 text-white">Name</th>
                    <th class="p-5 text-white">Email</th>
                    <th class="p-5 text-white">Role</th>
                    <th class="p-5 text-white">Actions</th>
                </tr>
            </thead>

            <tbody>

                <tr
                    v-for="user in users.data"
                    :key="user.id"
                    class="border-t border-gray-200"
                >

                    <td class="pl-5 text-gray-700">{{ user.name }}</td>
                    <td class="pl-5 text-gray-700">{{ user.email }}</td>
                    <td class="pl-5 text-gray-700">{{ user.role?.name }}</td>

                    <td class="pl-5 pt-4 pb-4 flex gap-3 ">

                        <Link
                            :href="`/users/${user.id}/edit`"
                            class="px-6 py-2 bg-blue-500 text-white text-s rounded"
                        >
                            Edit
                        </Link>

                        <button
                            @click="deleteUser(user.id)"
                            class="px-8 py-2 bg-red-500 text-white rounded"
                        >
                            Delete
                        </button>

                    </td>

                </tr>

            </tbody>

        </table>

    </div>

    <div class="grid gap-4 md:hidden">

        <div
            v-for="user in users.data"
            :key="user.id"
            class="bg-white p-4 rounded shadow flex flex-col gap-2"
        >

            <div class="font-semibold text-gray-500">
                {{ user.name }}
            </div>

            <div class="text-gray-500 text-sm">
                {{ user.email }}
            </div>

            <div class="text-sm text-gray-500">
                Role: {{ user.role?.name }}
            </div>

            <div class="flex gap-4 mt-2">

                <Link
                    :href="`/users/${user.id}/edit`"
                    class="text-blue-600"
                >
                    Edit
                </Link>

                <button
                    @click="deleteUser(user.id)"
                    class="text-red-600"
                >
                    Delete
                </button>

            </div>

        </div>

    </div>

    <div class="flex flex-wrap gap-2">

        <Link
            v-for="link in users.links"
            :key="link.label"
            :href="link.url || ''"
            v-html="link.label"
            class="px-3 py-1 border border-gray-400 rounded text-sm text-gray-700"
            :class="{ 'bg-blue-600 text-white': link.active }"
        />

    </div>

</div>

</DashboardLayout>

</template>