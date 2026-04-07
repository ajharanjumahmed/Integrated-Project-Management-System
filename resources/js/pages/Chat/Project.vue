<script setup>
import { Link } from '@inertiajs/vue3'
import DashboardLayout from '@/layouts/DashboardLayout.vue'
import ChatPanel from '@/components/ChatPanel.vue'

const props = defineProps({
    project:  Object,
    messages: Array,
})
</script>

<template>
<DashboardLayout :title="`Chat — ${project.title}`">

    <Link href="/kanban"
          class="inline-flex items-center gap-1.5 text-sm text-slate-400 hover:text-slate-600 transition mb-4">
        ← Back to Kanban
    </Link>

    <div class="flex items-center gap-3 mb-6">
        <h1 class="text-2xl font-bold text-slate-800">{{ project.title }}</h1>
        <span class="text-xs text-slate-400">Project Chat</span>
    </div>

    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden"
         style="height: 560px;">
        <ChatPanel
            :messages="messages"
            :project-id="project.id"
            title="Dedicated Project Chat"
            placeholder="Write your message here..."
            style="height: 100%;"
        />
    </div>

</DashboardLayout>
</template>