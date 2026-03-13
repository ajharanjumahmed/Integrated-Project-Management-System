<script setup>

import { usePage } from '@inertiajs/vue3'

const user = usePage().props.auth.user

</script>

<template>

<div class="flex justify-between items-center bg-white shadow px-6 py-4">

    <h1 class="text-xl font-semibold text-gray-600">
        Dashboard
    </h1>

    <div class="flex items-center gap-4">

        <span class="text-gray-600">
            {{ user.name }}
        </span>

        <img
            src="https://ui-avatars.com/api/?name=User"
            class="w-8 h-8 rounded-full"
        />

    </div>

</div>

</template>