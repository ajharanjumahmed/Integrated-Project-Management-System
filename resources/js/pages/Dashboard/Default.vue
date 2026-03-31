<script setup>
import { usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const user = usePage().props.auth.user
</script>

<template>
<DashboardLayout title="Dashboard">
    <div class="flex items-center justify-center min-h-[60vh]">
        <div class="text-center max-w-sm">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 border border-amber-100 flex items-center justify-center text-3xl mx-auto mb-5">
                ⚠️
            </div>
            <h2 class="text-xl font-bold text-slate-800 mb-2">No role assigned</h2>
            <p class="text-slate-400 text-sm leading-relaxed">
                Hello <strong class="text-slate-600">{{ user?.name }}</strong>, your account exists
                but has no role assigned yet. Please contact your system administrator.
            </p>
        </div>
    </div>
</DashboardLayout>
</template>