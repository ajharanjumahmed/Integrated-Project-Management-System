<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'

const user  = usePage().props.auth.user

const props = defineProps({
    stats: Object,
})
</script>

<template>
<DashboardLayout title="Dashboard">

    <div class="mb-8">
        <h1 class="text-2xl font-bold text-slate-800">
            Welcome, {{ user?.name?.split(' ')[0] }} 👋
        </h1>
        <p class="text-slate-400 text-sm mt-1">Here's a summary of your projects.</p>
    </div>

    <!-- Stat cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">

        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-xl flex items-center justify-center shrink-0">📁</div>
            <div>
                <p class="text-2xl font-bold text-slate-800">{{ stats.totalProjects }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Total Projects</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-blue-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-xl flex items-center justify-center shrink-0">⚡</div>
            <div>
                <p class="text-2xl font-bold text-blue-600">{{ stats.activeProjects }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Active</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-emerald-100 shadow-sm p-5 flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-xl flex items-center justify-center shrink-0">✅</div>
            <div>
                <p class="text-2xl font-bold text-emerald-600">{{ stats.completedProjects }}</p>
                <p class="text-xs text-slate-400 mt-0.5">Completed</p>
            </div>
        </div>

        <!-- Pending reviews — red if > 0 to signal urgency -->
        <div class="bg-white rounded-2xl shadow-sm p-5 flex items-center gap-4"
             :class="stats.pendingReviews > 0 ? 'border border-red-200' : 'border border-slate-100'">
            <div class="w-10 h-10 rounded-xl text-xl flex items-center justify-center shrink-0"
                 :class="stats.pendingReviews > 0 ? 'bg-red-50' : 'bg-slate-50'">
                📬
            </div>
            <div>
                <p class="text-2xl font-bold"
                   :class="stats.pendingReviews > 0 ? 'text-red-600' : 'text-slate-800'">
                    {{ stats.pendingReviews }}
                </p>
                <p class="text-xs text-slate-400 mt-0.5">Pending Reviews</p>
            </div>
        </div>

    </div>

    <!-- CTA to My Projects -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex items-center justify-between">
        <div>
            <p class="font-semibold text-slate-800">View Your Projects</p>
            <p class="text-sm text-slate-400 mt-0.5">See progress, milestones, and review submitted work.</p>
        </div>
        <Link href="/client/projects"
              class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition shrink-0">
            My Projects →
        </Link>
    </div>

</DashboardLayout>
</template>