<script setup>

import DashboardLayout from '@/layouts/DashboardLayout.vue';

const props = defineProps({
    projects: Array
})

</script>

<template>

    <DashboardLayout title="Projects">

        <div class="flex justify-between items-center mb-6">

            <h1 class="text-3xl font-bold text-gray-700">Manage Projects</h1>

            <a href="/projects/create" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                Create Project
            </a>

        </div>

        <div class="bg-white rounded-xl overflow-hidden mt-10 shadow-sm">

            <table class="w-full text-sm">

                <thead class="bg-blue-600 text-left text-white uppercase">

                    <tr>

                        <th class="p-4">Project</th>
                        <th class="p-4">Client</th>
                        <th class="p-4">Manager</th>
                        <th class="p-4">Budget</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Deadline</th>
                        <th class="p-4">Actions</th>

                    </tr>

                </thead>

                <tbody>

                    <tr v-for="project in projects" :key="project.id" class="border-t border-gray-200 hover:bg-gray-50 transition">

                        <td class="p-4 font-medium text-gray-700">

                            {{ project.title }}

                        </td>

                        <td class="p-4 text-gray-700">

                            {{ project.client?.name }}

                        </td>

                        <td class="p-4 text-gray-700">

                            {{ project.manager?.name }}

                        </td>

                        <td class="p-4 text-gray-700">

                            ${{ project.budget }}

                        </td>

                        <td class="p-4 text-gray-700">

                            <span class="px-4 py-2 rounded-full text-xs" :class="{
                                'bg-yellow-100 text-yellow-700': project.status === 'pending',
                                'bg-blue-100 text-blue-700': project.status === 'active',
                                'bg-green-100 text-green-700': project.status === 'completed',
                                'bg-red-100 text-red-700': project.status === 'cancelled'
                            }">

                                {{ project.status }}

                            </span>

                        </td>

                        <td class="p-4 text-gray-700">

                            {{ project.deadline }}

                        </td>

                        <td class="p-4 flex gap-3">

                            <a :href="`/projects/${project.id}/edit`" class="px-6 py-2 bg-blue-500 text-white text-s rounded">
                                Edit
                            </a>

                        </td>

                    </tr>

                </tbody>

            </table>

        </div>

    </DashboardLayout>

</template>