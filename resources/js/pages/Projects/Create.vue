<script setup>

import DashboardLayout from '@/layouts/DashboardLayout.vue'
import { useForm } from '@inertiajs/vue3'
import Multiselect from 'vue-multiselect'

const props = defineProps({
    clients: Array,
    managers: Array
})

const form = useForm({
    title: '',
    description: '',
    client_id: null,
    project_manager_id: null,
    budget: '',
    start_date: '',
    deadline: '',
    status: 'pending'
})

const submit = () => {
    form.client_id = form.client_id?.id
    form.project_manager_id = form.project_manager_id?.id
    form.post('/projects')
}

</script>


<template>

    <DashboardLayout title="Create Project">

        <div class="max-w-2xl items-center mx-auto pt-8">

            <h2 class="text-center text-3xl text-gray-800 font-bold pb-10">
                Create A New Project
            </h2>

            <div class="bg-white p-6 rounded-xl ring-1  ring-gray-300 w-2xl mb-20">

                <form @submit.prevent="submit" class="space-y-6">


                    <div>

                        <label class="block text-lg font-medium mb-3 text-gray-800">
                            Project Title
                        </label>

                        <input v-model="form.title" type="text" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-100 text-gray-600" />

                    </div>



                    <div>

                        <label class="block text-lg font-medium mb-3 text-gray-800">
                            Description
                        </label>

                        <textarea v-model="form.description" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-100 text-gray-600" />

                    </div>



                    <div>

                        <label class="block text-lg font-medium mb-3 text-gray-800">
                            Client
                        </label>

                        <Multiselect 
                            class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 text-gray-600"
                            :class="{'focus:ring focus:ring-blue-100': true}"
                            v-model="form.client_id" :options="clients" label="name" track-by="id"
                            placeholder="Search Client..." :reduce="client => client.id" />

                    </div>



                    <div>

                        <label class="block text-lg font-medium mb-3 text-gray-800">
                            Project Manager
                        </label>

                        <Multiselect 
                            class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-100 text-gray-600"
                            v-model="form.project_manager_id" :options="managers" label="name" track-by="id"
                            placeholder="Search Project Manager..." :reduce="manager => manager.id" />

                    </div>



                    <div>

                        <label class="block text-lg font-medium mb-3 text-gray-800">
                            Budget
                        </label>

                        <input v-model="form.budget" type="number" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-100 text-gray-600" />

                    </div>



                    <div class="grid md:grid-cols-2 gap-4">

                        <div>

                            <label class="block text-lg font-medium mb-3 text-gray-800">
                                Start Date
                            </label>

                            <input v-model="form.start_date" type="date" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-100 text-gray-600" />

                        </div>

                        <div>

                            <label class="block text-lg font-medium mb-3 text-gray-800">
                                Deadline
                            </label>

                            <input v-model="form.deadline" type="date" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-100 text-gray-600" />

                        </div>

                    </div>



                    <div>

                        <label class="block text-lg font-medium mb-3 text-gray-800">
                            Status
                        </label>

                        <select v-model="form.status" class="w-full bg-gray-50 border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-100 text-gray-600">

                            <option value="pending">Pending</option>
                            <option value="active">Active</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>

                        </select>

                    </div>



                    <div class="flex gap-3">

                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">
                            Create Project
                        </button>

                        <a href="/projects" class="px-4 py-2 border rounded-lg border-gray-400 text-gray-900">
                            Cancel
                        </a>

                    </div>

                </form>

            </div>

        </div>

    </DashboardLayout>

</template>


<style>

.multiselect__content-wrapper{
border-radius:10px;
border:1px solid #d1d5db;
margin-top:6px;
padding: 15px;
}

.multiselect__option--highlight{
background:#eff6ff;
color:#1d4ed8;
}

.multiselect__option--selected{
background:#dbeafe;
color:#1d4ed8;
}

.multiselect__input{
    padding: 10px;
}

</style>
