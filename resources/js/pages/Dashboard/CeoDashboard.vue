<script setup>

import DashboardLayout from '@/layouts/DashboardLayout.vue';
import { usePage } from '@inertiajs/vue3'

const user = usePage().props.auth.user

defineProps({
    totalUsers: Number,
    totalProjects: Number,
    activeProjects: Number,
    completedProjects: Number,
    totalRevenue: Number
})

</script>

<template>

<DashboardLayout title="Dashboard">

<h1 class="text-3xl font-bold mb-8 text-gray-700">
    Welcome Back, {{ user.name }}!
</h1>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">

<div class="bg-white p-5 rounded-xl  border border-gray-300">
<p class="text-sm text-gray-500">Total Users</p>
<h2 class="text-2xl font-bold mt-1 text-gray-800">{{ totalUsers }}</h2>
</div>

<div class="bg-white p-5 rounded-xl border border-gray-300">
<p class="text-sm text-gray-500">Total Projects</p>
<h2 class="text-2xl font-bold mt-1 text-gray-800">{{ totalProjects }}</h2>
</div>

<div class="bg-white p-5 rounded-xl border border-gray-300">
<p class="text-sm text-gray-500">Active Projects</p>
<h2 class="text-2xl font-bold mt-1 text-gray-800">{{ activeProjects }}</h2>
</div>

<div class="bg-white p-5 rounded-xl border border-gray-300">
<p class="text-sm text-gray-500">Completed Projects</p>
<h2 class="text-2xl font-bold mt-1 text-gray-800">{{ completedProjects }}</h2>
</div>

<div class="bg-white p-5 rounded-xl border border-gray-300">
<p class="text-sm text-gray-500">Revenue</p>
<h2 class="text-2xl font-bold mt-1 text-gray-800">${{ totalRevenue }}</h2>
</div>

</div>

</DashboardLayout>

</template>