<script setup>

import { usePage } from '@inertiajs/vue3'

const user = usePage().props.auth.user

const props = defineProps({
    title: String
})

</script>

<template>

<div class="h-16 bg-white border-b border-gray-300 flex items-center justify-between px-6">

    <h1 class="text-lg font-semibold text-gray-800">
        {{ title }}
    </h1>

    <div class="flex items-center gap-4">

        <span class="text-sm text-gray-700">
            {{ user.name }}
        </span>

        <img
        src="https://ui-avatars.com/api/?name=User"
        class="w-8 h-8 rounded-full"
        />

    </div>

</div>

</template>