<script setup lang="ts">
import { Form, Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
import { register } from '@/routes';
import { store } from '@/routes/login';
import { request } from '@/routes/password';

defineProps<{
    status?: string;
    canResetPassword: boolean;
    canRegister: boolean;
}>();
</script>

<template>
<div class="min-h-screen bg-slate-50 flex">
    <Head title="Log In — IPMS" />

    <!-- ── Left panel — branding ───────────────────────────── -->
    <div class="hidden lg:flex lg:w-1/2 flex-col justify-between p-12"
         style="background: linear-gradient(160deg, #1e3a5f 0%, #2563eb 100%)">

        <!-- Logo -->
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center">
                <span class="text-white font-black text-xs">IP</span>
            </div>
            <span class="text-white font-bold text-lg tracking-tight">IPMS</span>
        </div>

        <!-- Center copy -->
        <div>
            <h2 class="text-4xl font-bold text-white leading-snug mb-4">
                Your projects.<br />Your team.<br />One platform.
            </h2>
            <p class="text-blue-200 text-sm leading-relaxed max-w-sm">
                Manage tasks, track milestones, review submissions, and communicate — all from a single dashboard.
            </p>
        </div>

        <!-- Role pills at bottom -->
        <div class="flex flex-wrap gap-2">
            <span v-for="role in ['CEO', 'Project Manager', 'Designer', 'Developer', 'Client']"
                  :key="role"
                  class="px-3 py-1 rounded-full text-xs font-medium bg-white/10 text-white border border-white/20">
                {{ role }}
            </span>
        </div>

    </div>

    <!-- ── Right panel — form ───────────────────────────────── -->
    <div class="flex-1 flex items-center justify-center px-6 py-12">

        <div class="w-full max-w-sm">

            <!-- Mobile logo -->
            <div class="flex items-center gap-2 mb-8 lg:hidden">
                <div class="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center">
                    <span class="text-white font-black text-xs">IP</span>
                </div>
                <span class="font-bold text-slate-800 text-lg">IPMS</span>
            </div>

            <!-- Heading -->
            <h1 class="text-2xl font-bold text-slate-800 mb-1">Welcome back</h1>
            <p class="text-slate-400 text-sm mb-8">Log in to your IPMS account to continue.</p>

            <!-- Status message (e.g. password reset success) -->
            <div v-if="status"
                 class="mb-6 px-4 py-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm font-medium">
                {{ status }}
            </div>

            <!-- Form -->
            <Form
                v-bind="store.form()"
                :reset-on-success="['password']"
                v-slot="{ errors, processing }"
                class="flex flex-col gap-5"
            >
                <!-- Email -->
                <div>
                    <Label for="email"
                           class="block text-xs font-medium text-slate-600 mb-1.5">
                        Email address
                    </Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        required
                        autofocus
                        autocomplete="email"
                        placeholder="you@example.com"
                        class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700 placeholder-slate-300"
                    />
                    <InputError :message="errors.email" class="mt-1 text-xs text-red-500" />
                </div>

                <!-- Password -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <Label for="password" class="text-xs font-medium text-slate-600">
                            Password
                        </Label>
                        <TextLink v-if="canResetPassword" :href="request()"
                                  class="text-xs text-blue-600 hover:text-blue-700 hover:underline">
                            Forgot password?
                        </TextLink>
                    </div>
                    <PasswordInput
                        id="password"
                        name="password"
                        required
                        autocomplete="current-password"
                        placeholder="••••••••"
                        class="w-full text-sm border border-slate-200 rounded-xl px-3 py-2.5 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 text-slate-700"
                    />
                    <InputError :message="errors.password" class="mt-1 text-xs text-red-500" />
                </div>

                <!-- Remember me -->
                <div class="flex items-center gap-2">
                    <Checkbox id="remember" name="remember"
                              class="rounded border-slate-300 text-blue-600" />
                    <Label for="remember" class="text-sm text-slate-500 cursor-pointer">
                        Remember me
                    </Label>
                </div>

                <!-- Submit -->
                <Button
                    type="submit"
                    :disabled="processing"
                    class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition shadow-sm shadow-blue-200 flex items-center justify-center gap-2 mt-1"
                >
                    <Spinner v-if="processing" class="w-4 h-4" />
                    {{ processing ? 'Logging in…' : 'Log In' }}
                </Button>

            </Form>

        </div>

    </div>

</div>
</template>