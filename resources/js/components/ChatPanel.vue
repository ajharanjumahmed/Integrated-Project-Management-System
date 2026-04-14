<script setup>
import { ref, computed, nextTick, onMounted, onUnmounted } from 'vue'
import { useForm, usePage, router } from '@inertiajs/vue3'

const props = defineProps({
    // All messages for this project — parent filters by thread if needed
    messages:  { type: Array, default: () => [] },
    // The project this chat belongs to
    projectId: { type: Number, required: true },
    // Optional: filter messages to only show between specific roles.
    // e.g. ['Project Manager', 'Client'] shows only PM↔Client thread.
    // If null, shows all messages.
    threadRoles: { type: Array, default: null },
    title: { type: String, default: 'Chat' },
    placeholder: { type: String, default: 'Type a message…' },
})

const currentUser = usePage().props.auth.user
const currentRole = currentUser?.role?.name

// Filter messages by thread if threadRoles is specified.
// This lets the PM have two separate chat panels on one page:
// one showing only PM↔Client messages, another showing PM↔Team messages.
const visibleMessages = computed(() => {
    if (!props.threadRoles) return props.messages
    return props.messages.filter(m =>
        props.threadRoles.includes(m.sender?.role?.name)
    )
})

const form = useForm({ message: '' })

// Ref to the messages container — used to auto-scroll to bottom
const messagesEl = ref(null)

const scrollToBottom = () => {
    nextTick(() => {
        if (messagesEl.value) {
            messagesEl.value.scrollTop = messagesEl.value.scrollHeight
        }
    })
}

const send = () => {
    if (!form.message.trim()) return
    form.post(`/chat/${props.projectId}/messages`, {
        preserveScroll: true,
        onSuccess: () => {
            form.reset()
            scrollToBottom()
        },
    })
}

// Allow sending with Enter key (Shift+Enter for newline)
const onKeydown = (e) => {
    if (e.key === 'Enter' && !e.shiftKey) {
        e.preventDefault()
        send()
    }
}

// Format message timestamp
const formatTime = (datetime) => {
    if (!datetime) return ''
    return new Date(datetime).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })
}

const formatDay = (datetime) => {
    if (!datetime) return ''
    return new Date(datetime).toLocaleDateString([], { month: 'short', day: 'numeric' })
}

// Auto-scroll when component mounts
onMounted(() => scrollToBottom())

// ── Polling ───────────────────────────────────────────────────
// Poll every 5 seconds to fetch new messages.
// We use router.reload({ only: ['project'] }) which asks Inertia to
// re-fetch only the 'project' prop (which includes messages) without
// a full page reload. This gives a near-realtime feel without WebSockets.
let pollInterval = null

onMounted(() => {
    pollInterval = setInterval(() => {
        router.reload({ only: ['project', 'messages'], preserveScroll: true })
    }, 5000)
})

onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval)
})

// Role badge colors
const roleBadgeStyles = {
    'CEO':             'bg-slate-100 text-slate-600',
    'Project Manager': 'bg-blue-100 text-blue-700',
    'Designer':        'bg-violet-100 text-violet-700',
    'Developer':       'bg-indigo-100 text-indigo-700',
    'Client':          'bg-emerald-100 text-emerald-700',
}
</script>

<template>
<div class="flex flex-col h-full">

    <!-- Chat header -->
    <div class="px-5 py-3.5 border-b border-slate-100 shrink-0">
        <h3 class="font-semibold text-slate-800 text-md">{{ title }}</h3>
        <p class="text-xs text-slate-400 mt-1">
            Total {{ visibleMessages.length }} message{{ visibleMessages.length !== 1 ? 's' : '' }}
        </p>
    </div>

    <!-- Messages area -->
    <div
        ref="messagesEl"
        class="flex-1 overflow-y-auto px-5 py-4 space-y-3 min-h-0"
        style="max-height: 380px;"
    >
        <!-- Empty state -->
        <div v-if="!visibleMessages.length"
             class="flex items-center justify-center h-full">
            <div class="text-center text-slate-300">
                <p class="text-3xl mb-2">💬</p>
                <p class="text-xs">No messages yet. Start the conversation.</p>
            </div>
        </div>

        <!-- Message bubbles -->
        <div v-for="(msg, index) in visibleMessages" :key="msg.id">

            <!-- Date separator — show when day changes between messages -->
            <div v-if="index === 0 || formatDay(msg.created_at) !== formatDay(visibleMessages[index - 1]?.created_at)"
                 class="flex items-center gap-3 my-3">
                <div class="flex-1 h-px bg-slate-100"></div>
                <span class="text-xs text-slate-300 shrink-0">{{ formatDay(msg.created_at) }}</span>
                <div class="flex-1 h-px bg-slate-100"></div>
            </div>

            <!-- Message row — align right if sent by current user -->
            <div :class="['flex gap-2.5', msg.sender_id === currentUser.id ? 'flex-row-reverse' : 'flex-row']">

                <!-- Avatar -->
                <div class="w-7 h-7 rounded-xl shrink-0 flex items-center justify-center text-white text-xs font-bold bg-blue-600">
                    {{ msg.sender?.name?.charAt(0)?.toUpperCase() }}
                </div>

                <div :class="['max-w-[75%]', msg.sender_id === currentUser.id ? 'items-end' : 'items-start']"
                     class="flex flex-col gap-1">

                    <!-- Sender name + role + time -->
                    <div :class="['flex items-center gap-2 text-xs', msg.sender_id === currentUser.id ? 'flex-row-reverse' : '']">
                        <span class="font-medium text-slate-600">
                            {{ msg.sender_id === currentUser.id ? 'You' : msg.sender?.name }}
                        </span>
                        <span class="px-1.5 py-0.5 rounded text-xs"
                              :class="roleBadgeStyles[msg.sender?.role?.name] ?? 'bg-slate-100 text-slate-500'">
                            {{ msg.sender?.role?.name }}
                        </span>
                        <span class="text-slate-300">{{ formatTime(msg.created_at) }}</span>
                    </div>

                    <!-- Bubble -->
                    <div :class="[
                        'px-3.5 py-2.5 rounded-2xl text-sm leading-relaxed break-words',
                        msg.sender_id === currentUser.id
                            ? 'bg-blue-600 text-white rounded-tr-sm'
                            : 'bg-slate-100 text-slate-700 rounded-tl-sm'
                    ]">
                        {{ msg.message }}
                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- Input area -->
    <div class="px-5 py-3.5 border-t border-slate-100 shrink-0">
        <div class="flex gap-2">
            <textarea
                v-model="form.message"
                @keydown="onKeydown"
                :placeholder="placeholder"
                rows="1"
                class="flex-1 text-sm border border-slate-200 rounded-xl px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 resize-none"
            ></textarea>
            <button
                @click="send"
                :disabled="form.processing || !form.message.trim()"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-xl transition disabled:opacity-40 shrink-0"
            >
                Send
            </button>
        </div>
        <p class="text-xs text-slate-300 mt-1.5">Enter to send · Shift+Enter for new line</p>
    </div>

</div>
</template>