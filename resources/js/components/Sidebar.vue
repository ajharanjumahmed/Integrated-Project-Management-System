<script setup>
import { Link, usePage, router } from '@inertiajs/vue3'
import {
    HomeIcon,
    UsersIcon,
    FolderIcon,
    ClipboardDocumentListIcon,
    ChatBubbleLeftRightIcon,
    ArrowRightStartOnRectangleIcon,
    ChartBarIcon,
} from '@heroicons/vue/24/outline'

const page = usePage()
const user = page.props.auth.user
const role = user?.role?.name

const isActive = (path) => page.url.startsWith(path)
const logout = () => router.post('/logout')

const navItems = {
    'CEO': [
        { href: '/ceo-dashboard', label: 'Dashboard', icon: ChartBarIcon, match: '/ceo-dashboard' },
        { href: '/users', label: 'Users', icon: UsersIcon, match: '/users' },
        { href: '/projects', label: 'Projects', icon: FolderIcon, match: '/projects' },
        { href: '/ceo/clients', label: 'Clients', icon: UsersIcon, match: '/ceo/clients' },
    ],
    'Project Manager': [
        { href: '/manager-dashboard', label: 'Dashboard', icon: HomeIcon, match: '/manager-dashboard' },
        { href: '/projects', label: 'Projects', icon: FolderIcon, match: '/projects' },
    ],
    'Designer': [
        { href: '/designer-dashboard', label: 'Dashboard', icon: HomeIcon, match: '/designer-dashboard' },
        { href: '/kanban', label: 'My Tasks', icon: ClipboardDocumentListIcon, match: '/kanban' },
    ],
    'Developer': [
        { href: '/developer-dashboard', label: 'Dashboard', icon: HomeIcon, match: '/developer-dashboard' },
        { href: '/kanban', label: 'My Tasks', icon: ClipboardDocumentListIcon, match: '/kanban' },
    ],
    'Client': [
        { href: '/client-dashboard', label: 'Dashboard', icon: HomeIcon, match: '/client-dashboard' },
        { href: '/client/projects', label: 'My Projects', icon: FolderIcon, match: '/client/projects' },
    ],
}

// Get the nav links for the current user's role
const links = navItems[role] ?? []
</script>

<template>
    <div class="fixed h-full w-16 md:w-64 flex flex-col bg-[#1A3263]">

        <div class="h-16 flex items-center md:px-5 px-3 shrink-0 bg-white/5 border-b border-white/10">
            <span class="hidden md:block ml-3 text-white font-bold text-2xl tracking-tight">
                IPMS
            </span>
        </div>

        <nav class="flex-1 px-2 md:px-3 py-4 space-y-0.5">

            <Link v-for="item in links" :key="item.label" :href="item.href" :class="[
                'flex items-center gap-3 px-3 py-3 rounded-lg text-sm font-medium transition-all duration-150',
                isActive(item.match)
                    ? 'bg-white text-blue-700 shadow-sm'
                    : 'text-blue-100 hover:bg-white/10 hover:text-white'
            ]">
                <component :is="item.icon" class="w-5 h-5 shrink-0" />
                <span class="hidden md:block">{{ item.label }}</span>
            </Link>

        </nav>

        <div class="shrink-0 p-3 border-t border-white/10">
            <div class="flex items-center gap-3">

                <div
                    class="w-9 h-9 rounded-xl bg-blue-400/50 border border-blue-300/30 flex items-center justify-center shrink-0">
                    <span class="text-white text-sm font-bold">
                        {{ user?.name?.charAt(0)?.toUpperCase() }}
                    </span>
                </div>

                <div class="hidden md:block flex-1 min-w-0">
                    <p class="text-white text-sm font-medium truncate">{{ user?.name }}</p>
                    <p class="text-blue-300 text-xs truncate">{{ role }}</p>
                </div>

                <button @click="logout" title="Logout"
                    class="hidden md:flex text-white/40 hover:text-white transition p-2 rounded-lg hover:bg-white/10">
                    <ArrowRightStartOnRectangleIcon class="w-5 h-5" />
                </button>

            </div>
        </div>

    </div>
</template>