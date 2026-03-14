<script setup>

import Sidebar from '@/components/Sidebar.vue';
import Navbar from '@/components/Navbar.vue';

const props = defineProps({
    title: String
})

</script>

<template>

<div class="flex bg-gray-50 min-h-screen">

    <div class="w-16 md:w-64">
        <Sidebar />
    </div>

    <div class="flex-1 flex flex-col">

        <Navbar :title="title" />

        <main class="p-6">
            <slot />
        </main>

    </div>

</div>

</template>