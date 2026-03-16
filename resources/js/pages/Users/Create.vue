<script setup>

import { useForm } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const props = defineProps({
    roles: Array
})

const form = useForm({
    name: '',
    email: '',
    password: '',
    role_id: ''
})

const submit = () => {
    form.post('/users')
}

</script>

<template>

<DashboardLayout title="Create User">

<div class="w-full flex flex-col items-center">
    <div class="text-3xl font-bold text-gray-700 pb-10 pt-10">
        <h2>Create A New User</h2>
    </div>
    <div class="bg-white p-6 rounded-xl ring-1  ring-gray-300 w-2xl">

        <form @submit.prevent="submit" class="space-y-6">

            <div>
                <label class="block text-lg font-medium mb-2 text-gray-800">
                    Name
                </label>

                <input
                    v-model="form.name"
                    type="text"
                    class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-100 text-gray-600"
                />

                <p v-if="form.errors.name" class="text-red-500 text-sm mt-1">
                    {{ form.errors.name }}
                </p>
            </div>

            <div>
                <label class="block text-lg font-medium mb-2 text-gray-800">
                    Email
                </label>

                <input
                    v-model="form.email"
                    type="email"
                    class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-100 text-gray-600"
                />

                <p v-if="form.errors.email" class="text-red-500 text-sm mt-1">
                    {{ form.errors.email }}
                </p>
            </div>

            <div>
                <label class="block text-lg font-medium mb-2 text-gray-800">
                    Password
                </label>

                <input
                    v-model="form.password"
                    type="password"
                    class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-100 text-gray-600"
                />

                <p v-if="form.errors.password" class="text-red-500 text-sm mt-1">
                    {{ form.errors.password }}
                </p>
            </div>

            <div>
                <label class="block text-lg font-medium mb-2 text-gray-800">
                    Role
                </label>

                <select
                    v-model="form.role_id"
                    class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-100 text-gray-600"
                >

                    <option value="">Select Role</option>

                    <option
                        v-for="role in roles"
                        :key="role.id"
                        :value="role.id"
                    >
                        {{ role.name }}
                    </option>

                </select>

                <p v-if="form.errors.role_id" class="text-red-500 text-sm mt-1">
                    {{ form.errors.role_id }}
                </p>
            </div>

            <div class="flex gap-3">

                <button
                    type="submit"
                    class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700"
                    :disabled="form.processing"
                >
                    Create User
                </button>

                <a
                    href="/users"
                    class="px-4 py-2 border border-gray-400 rounded-lg text-gray-900"
                >
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

</DashboardLayout>

</template>