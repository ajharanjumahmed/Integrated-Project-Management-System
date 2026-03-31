<script setup>
import Sidebar from '@/components/Sidebar.vue'
import Navbar from '@/components/Navbar.vue'

defineProps({
    title: String
})
</script>

<template>
<div class="flex min-h-screen bg-slate-50">

    <div class="w-16 md:w-64 shrink-0">
        <Sidebar />
    </div>

    <div class="flex-1 flex flex-col min-w-0">
        <Navbar :title="title" />
        <main class="flex-1 p-6 md:p-8">
            <slot />
        </main>
    </div>

</div>
</template>