<script setup>

import Navbar from '@/components/Navbar.vue'
import Sidebar from '@/components/Sidebar.vue'

</script>

<template>

<div class="flex">

    <Sidebar />

    <div class="flex-1 bg-gray-100 min-h-screen">

        <Navbar />

        <main class="p-8">
            <slot />
        </main>

    </div>

</div>

</template>