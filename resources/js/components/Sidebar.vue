<script setup>

import { Link, usePage } from '@inertiajs/vue3'

const page = usePage()

const isActive = (url) => {
    return page.url.startsWith(url)
}

</script>

<template>

<div class="w-60 h-screen bg-blue-600 text-white flex flex-col">

    <div class="p-6 text-2xl font-bold border-b border-indigo-400">
        IPMS
    </div>

    <nav class="flex-1 p-4 space-y-2">

        <Link
            href="/ceo-dashboard"
            :class="[
                'block px-4 py-2 rounded transition',
                isActive('/ceo-dashboard')
                    ? 'bg-white text-black'
                    : 'hover:bg-blue-50 hover:text-gray-900'
            ]"
        >
            Dashboard
        </Link>

        <Link
            href="/users"
            :class="[
                'block px-4 py-2 rounded transition',
                isActive('/users')
                    ? 'bg-white text-black'
                    : 'hover:bg-blue-50 hover:text-gray-900'
            ]"
        >
            Users
        </Link>

        <Link
            href="/projects"
            :class="[
                'block px-4 py-2 rounded transition',
                isActive('/projects')
                    ? 'bg-white text-black'
                    : 'hover:bg-blue-50 hover:text-gray-900'
            ]"
        >
            Projects
        </Link>

        <Link
            href="/clients"
            :class="[
                'block px-4 py-2 rounded transition',
                isActive('/clients')
                    ? 'bg-white text-black'
                    : 'hover:bg-blue-50 hover:text-gray-900'
            ]"
        >
            Clients
        </Link>

        <Link
            href="/analytics"
            :class="[
                'block px-4 py-2 rounded transition',
                isActive('/analytics')
                    ? 'bg-white text-black'
                    : 'hover:bg-blue-50 hover:text-gray-900'
            ]"
        >
            Analytics
        </Link>

    </nav>

</div>

</template>