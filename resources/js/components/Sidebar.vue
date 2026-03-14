<script setup>

import { Link, usePage } from '@inertiajs/vue3'

import {
HomeIcon,
UsersIcon,
FolderIcon,
ChartBarIcon
} from '@heroicons/vue/24/outline'

const page = usePage()

const isActive = (url) => {
    return page.url.startsWith(url)
}

</script>

<template>

<div class="h-full bg-white border-r border-gray-300 flex flex-col">

    <!-- Logo -->
    <div class="h-16 flex items-center md:px-6 px-3 border-b border-gray-300 font-bold md:text-2xl text-blue-700">
        IPMS
    </div>

    <!-- Navigation -->
    <nav class="flex-1 px-3 py-4 space-y-3">

        <Link
        href="/ceo-dashboard"
        :class="[
        'flex items-center gap-3 px-3 py-2 rounded-lg text-m transition',
        isActive('/ceo-dashboard')
        ? 'bg-blue-50 text-blue-600'
        : 'text-gray-600 hover:bg-gray-100'
        ]"
        >

            <HomeIcon class="w-5 h-5"/>

            <span class="hidden md:block">
            Dashboard
            </span>

        </Link>

        <Link
        href="/users"
        :class="[
        'flex items-center gap-3 px-3 py-2 rounded-lg text-m transition',
        isActive('/users')
        ? 'bg-blue-50 text-blue-600'
        : 'text-gray-600 hover:bg-gray-100'
        ]"
        >

            <UsersIcon class="w-5 h-5"/>

            <span class="hidden md:block">
            Users
            </span>

        </Link>

        <Link
        href="/projects"
        class="flex items-center gap-3 px-3 py-2 rounded-lg text-m text-gray-600 hover:bg-gray-100"
        >

            <FolderIcon class="w-5 h-5"/>

            <span class="hidden md:block">
            Projects
            </span>

        </Link>

        <Link
        href="/analytics"
        class="flex items-center gap-3 px-3 py-2 rounded-lg text-m text-gray-600 hover:bg-gray-100"
        >

            <ChartBarIcon class="w-5 h-5"/>

            <span class="hidden md:block">
            Analytics
            </span>

        </Link>

    </nav>

</div>

</template>