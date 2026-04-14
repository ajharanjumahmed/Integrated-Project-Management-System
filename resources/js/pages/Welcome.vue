<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { dashboard, login } from '@/routes';

withDefaults(
    defineProps<{ canRegister: boolean }>(),
    { canRegister: true }
);
</script>

<template>
<div class="min-h-screen bg-slate-50">
    <Head title="IPMS — Integrated Project Management" />

    <!-- ── Navbar ──────────────────────────────────────────── -->
    <header class="bg-white border-b border-slate-100 px-6 md:px-12 h-16 flex items-center justify-between sticky top-0 z-10">

        <!-- Logo -->
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center">
                <span class="text-white font-black text-xs">IP</span>
            </div>
            <span class="font-bold text-slate-800 text-lg tracking-tight">IPMS</span>
        </div>

        <!-- Nav actions -->
        <div class="flex items-center gap-3">
            <Link v-if="$page.props.auth.user" :href="dashboard()"
                  class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition">
                Go to Dashboard →
            </Link>
            <template v-else>
                <Link :href="login()"
                      class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-xl transition shadow-sm shadow-blue-200">
                    Log In
                </Link>
            </template>
        </div>

    </header>

    <!-- ── Hero ────────────────────────────────────────────── -->
    <section class="max-w-5xl mx-auto px-6 pt-20 pb-16 text-center">

        <!-- Badge -->
        <div class="inline-flex items-center gap-2 bg-blue-50 border border-blue-100 text-blue-600 text-xs font-semibold px-3 py-1.5 rounded-full mb-6">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span>
            Built for modern teams
        </div>

        <h1 class="text-4xl md:text-6xl font-bold text-slate-800 leading-tight mb-6">
            Manage Projects<br class="hidden md:block" />
            <span class="text-blue-600">Smarter & Faster</span>
        </h1>

        <p class="text-slate-500 text-lg max-w-2xl mx-auto mb-10">
            A centralized platform for project managers, designers, developers, and clients
            to collaborate, track progress, and deliver work — all in one place.
        </p>

        <div class="flex items-center justify-center gap-3">
            <Link v-if="$page.props.auth.user" :href="dashboard()"
                  class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-sm shadow-blue-200">
                Go to Dashboard →
            </Link>
            <template v-else>
                <Link :href="login()"
                      class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-sm shadow-blue-200">
                    Get Started →
                </Link>
            </template>
        </div>

    </section>

    <!-- ── Stats strip ──────────────────────────────────────── -->
    <section class="max-w-4xl mx-auto px-6 mb-16">
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm grid grid-cols-2 md:grid-cols-4 divide-x divide-slate-100">
            <div class="px-6 py-5 text-center">
                <p class="text-2xl font-bold text-blue-600">5</p>
                <p class="text-xs text-slate-400 mt-1">User Roles</p>
            </div>
            <div class="px-6 py-5 text-center">
                <p class="text-2xl font-bold text-violet-600">∞</p>
                <p class="text-xs text-slate-400 mt-1">Projects</p>
            </div>
            <div class="px-6 py-5 text-center">
                <p class="text-2xl font-bold text-emerald-600">Real-time</p>
                <p class="text-xs text-slate-400 mt-1">Chat</p>
            </div>
            <div class="px-6 py-5 text-center">
                <p class="text-2xl font-bold text-amber-500">Full</p>
                <p class="text-xs text-slate-400 mt-1">Analytics</p>
            </div>
        </div>
    </section>

    <!-- ── Features ────────────────────────────────────────── -->
    <section class="max-w-5xl mx-auto px-6 pb-20">

        <h2 class="text-2xl font-bold text-slate-800 text-center mb-2">Everything your team needs</h2>
        <p class="text-slate-400 text-sm text-center mb-10">One system. Every role. Full visibility.</p>

        <div class="grid md:grid-cols-3 gap-4">

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-2xl flex items-center justify-center mb-4">📋</div>
                <h3 class="font-semibold text-slate-800 mb-1">Task Management</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Create, assign, and track tasks with priority levels, deadlines, and a Kanban board for your team.
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-violet-50 text-2xl flex items-center justify-center mb-4">🏁</div>
                <h3 class="font-semibold text-slate-800 mb-1">Milestone Tracking</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Break projects into phases, monitor milestone completion, and keep every stakeholder informed.
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-2xl flex items-center justify-center mb-4">💬</div>
                <h3 class="font-semibold text-slate-800 mb-1">Built-in Chat</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    PM talks to the client, PM talks to the team — separate threads per project, all in one place.
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-2xl flex items-center justify-center mb-4">📎</div>
                <h3 class="font-semibold text-slate-800 mb-1">Submission & Review</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Designers submit work, PMs review and forward to clients, clients approve or request revisions.
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-2xl flex items-center justify-center mb-4">⏱</div>
                <h3 class="font-semibold text-slate-800 mb-1">Time Tracking</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Designers and developers sign in and out of projects. PMs see full session history.
                </p>
            </div>

            <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 hover:shadow-md transition">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-2xl flex items-center justify-center mb-4">📊</div>
                <h3 class="font-semibold text-slate-800 mb-1">CEO Analytics</h3>
                <p class="text-sm text-slate-500 leading-relaxed">
                    Organization-wide earnings, project breakdowns, top performers, and activity insights.
                </p>
            </div>

        </div>

    </section>

    <!-- ── Role section ─────────────────────────────────────── -->
    <section class="max-w-5xl mx-auto px-6 pb-24">

        <h2 class="text-2xl font-bold text-slate-800 text-center mb-2">Built for every role</h2>
        <p class="text-slate-400 text-sm text-center mb-10">Each user sees exactly what they need — nothing more.</p>

        <div class="grid grid-cols-2 md:grid-cols-5 gap-3">

            <div v-for="role in [
                { name: 'CEO',             icon: '👔', color: 'bg-slate-100 text-slate-700',   desc: 'Analytics & oversight' },
                { name: 'Project Manager', icon: '🗂',  color: 'bg-blue-100 text-blue-700',    desc: 'Plan & coordinate' },
                { name: 'Designer',        icon: '🎨', color: 'bg-violet-100 text-violet-700', desc: 'Create & submit' },
                { name: 'Developer',       icon: '💻', color: 'bg-indigo-100 text-indigo-700', desc: 'Build & deliver' },
                { name: 'Client',          icon: '🤝', color: 'bg-emerald-100 text-emerald-700', desc: 'Review & approve' },
            ]" :key="role.name"
               class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 text-center hover:shadow-md transition">
                <div class="text-2xl mb-2">{{ role.icon }}</div>
                <span class="inline-block px-2 py-0.5 rounded-full text-xs font-semibold mb-2"
                      :class="role.color">
                    {{ role.name }}
                </span>
                <p class="text-xs text-slate-400">{{ role.desc }}</p>
            </div>

        </div>

    </section>

    <!-- ── Footer ──────────────────────────────────────────── -->
    <footer class="border-t border-slate-100 bg-white py-6 text-center">
        <div class="flex items-center justify-center gap-2 mb-2">
            <div class="w-6 h-6 rounded-md bg-blue-600 flex items-center justify-center">
                <span class="text-white font-black text-xs">IP</span>
            </div>
            <span class="font-bold text-slate-700 text-sm">IPMS</span>
        </div>
        <p class="text-xs text-slate-400">© 2026 IPMS. All rights reserved.</p>
    </footer>

</div>
</template>