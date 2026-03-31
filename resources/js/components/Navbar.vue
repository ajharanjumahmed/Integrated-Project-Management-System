<script setup>
import { usePage } from '@inertiajs/vue3'
import { BellIcon } from '@heroicons/vue/24/outline'

const user  = usePage().props.auth.user
const flash = usePage().props.flash

defineProps({ title: String })
</script>

<template>
<!-- Clean white top bar. No heavy border — just a subtle shadow. -->
<div class="h-16 bg-white shadow-[0_1px_3px_rgba(0,0,0,0.06)] flex items-center justify-between px-6 md:px-8 shrink-0">

    <!-- Page title — passed from each page via DashboardLayout -->
    <div>
        <h1 class="text-lg font-semibold text-slate-800">{{ title }}</h1>
    </div>

    <!-- Right side: notification bell + user chip -->
    <div class="flex items-center gap-3">

        <!-- Notification bell (placeholder — we'll wire this up later) -->
        <button class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 flex items-center justify-center transition text-slate-500">
            <BellIcon class="w-4 h-4" />
        </button>

        <!-- User chip: avatar + name + role badge -->
        <!-- This whole block is a visual identity element in the top right -->
        <div class="flex items-center gap-2.5 pl-3 border-l border-slate-100">

            <!-- Coloured initial avatar — matches the sidebar -->
            <div class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center">
                <span class="text-white text-sm font-bold">
                    {{ user?.name?.charAt(0)?.toUpperCase() }}
                </span>
            </div>

            <!-- Name + role — hidden on small screens -->
            <div class="hidden md:block">
                <p class="text-sm font-semibold text-slate-700 leading-tight">{{ user?.name }}</p>
                <p class="text-xs text-slate-400 leading-tight">{{ user?.role?.name }}</p>
            </div>

        </div>

    </div>

</div>

<!-- ── Flash messages ───────────────────────────────────────── -->
<!-- These appear below the navbar and auto-fade using CSS animation.
     They're shown when a controller does redirect()->with('success', '...') -->
<div v-if="flash?.success"
     class="mx-6 md:mx-8 mt-4 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium flex items-center gap-2">
    <span class="text-emerald-500">✓</span>
    {{ flash.success }}
</div>

<div v-if="flash?.error"
     class="mx-6 md:mx-8 mt-4 px-4 py-3 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-medium flex items-center gap-2">
    <span class="text-red-400">✕</span>
    {{ flash.error }}
</div>
</template>