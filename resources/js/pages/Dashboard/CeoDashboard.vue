<script setup>

import DashboardLayout from '@/layouts/DashboardLayout.vue';

defineProps({
    totalUsers: Number,
    totalProjects: Number,
    activeProjects: Number,
    completedProjects: Number,
    totalRevenue: Number
})

</script>

<template>

<DashboardLayout>

<h1 class="text-3xl text-gray-900 font-bold mb-8">CEO Dashboard</h1>

<div class="grid grid-cols-5 gap-6">

    <div class="p-6 bg-blue-600 text-white rounded shadow">
        <p>Total Users</p>
        <h2 class="text-3xl font-bold">{{ totalUsers }}</h2>
    </div>

    <div class="p-6 bg-blue-600 text-white rounded shadow">
        <p>Total Projects</p>
        <h2 class="text-3xl font-bold">{{ totalProjects }}</h2>
    </div>

    <div class="p-6 bg-blue-600 text-white rounded shadow">
        <p>Active Projects</p>
        <h2 class="text-3xl font-bold">{{ activeProjects }}</h2>
    </div>

    <div class="p-6 bg-blue-600 text-white rounded shadow">
        <p>Completed Projects</p>
        <h2 class="text-3xl font-bold">{{ completedProjects }}</h2>
    </div>

    <div class="p-6 bg-blue-600 text-white rounded shadow">
        <p>Total Revenue</p>
        <h2 class="text-3xl font-bold">${{ totalRevenue }}</h2>
    </div>

</div>

</DashboardLayout>

</template>