<script setup>
import { Head } from '@inertiajs/vue3';
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import preview from '@/assets/dashboard-preview.png';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});

const currentYear = new Date().getFullYear();
</script>

<template>

    <Head title="Home" />

    <main class="min-h-screen  bg-gray-950 text-gray-100 flex flex-col">
        <div class="flex-grow">
            <!-- Header -->
            <header class="w-full py-8">
                <div class="mx-auto px-4 flex md:items-start items-center md:justify-end justify-start">
                </div>
                <div class="container mx-auto px-4 flex items-center justify-between">
                    <!-- Logo + Title -->
                    <div class="flex items-center gap-3">
                        <!-- Logo -->
                        <ApplicationLogo />
                        <!-- Title -->
                        <div>
                            <h1 class="text-2xl font-semibold">FinTrack</h1>
                            <p class="text-sm text-gray-300 opacity-80">Personal finance, simply organized</p>
                        </div>
                    </div>
                    <!-- Guest -->
                    <nav v-if="canLogin" class="flex items-center gap-4">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')"
                            class="py-2 px-4 rounded-md hover:bg-gray-800 transition">
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link :href="route('login')" class="py-2 px-4 rounded-md hover:bg-gray-800 transition">
                                Sign in
                            </Link>

                            <Link v-if="canRegister" :href="route('register')"
                                class="py-2 px-4 rounded-md bg-indigo-600 text-white font-medium hover:bg-indigo-500 transition">
                                Create account
                            </Link>
                        </template>
                    </nav>
                </div>
            </header>
            <!-- Hero section -->
            <section class="container mx-auto px-4 py-16">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-10 items-center">
                    <!-- Text -->
                    <div class="md:col-span-6">
                        <h2 class="text-4xl md:text-5xl font-bold mb-6 leading-tight">
                            Manage your finances with ease
                        </h2>

                        <p class="text-lg text-gray-300 mb-8 max-w-md">
                            Track expenses, schedule payments, and get a clear monthly overview — all in one simple
                            dashboard.
                        </p>
                        <!-- CTAs -->
                        <div class="flex gap-4 mb-10">
                            <Link href="/register"
                                class="px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-500 transition">
                                Get started
                            </Link>

                            <Link href="/login"
                                class="px-6 py-3 border border-gray-700 rounded-lg hover:bg-gray-800 transition">
                                Sign in
                            </Link>
                        </div>
                        <!-- Feature list -->
                        <ul class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-gray-300">
                            <li class="flex items-start gap-3"><span class="text-indigo-400 text-xl">✓</span> Quick
                                expense logging</li>
                            <li class="flex items-start gap-3"><span class="text-indigo-400 text-xl">⏰</span> Organize
                                monthly payment tasks</li>
                            <li class="flex items-start gap-3"><span class="text-indigo-400 text-xl">📊</span> Clear
                                monthly summaries</li>
                            <li class="flex items-start gap-3"><span class="text-indigo-400 text-xl">🔒</span> Secure
                                and privacy-focused</li>
                        </ul>
                    </div>
                    <!-- Preview placeholder -->
                    <div class="md:col-span-6">
                        <div class="bg-gray-900 border border-gray-800 rounded-xl p-6 shadow-lg">
                            <p class="text-center text-sm text-gray-500 mb-3">Dashboard</p>

                            <div class="p-3 bg-gray-800 rounded-lg overflow-hidden shadow-inner">
                                <img :src="preview" alt="Dashboard preview" class="w-full rounded-lg object-cover" />
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <!-- Footer -->
        <footer class="py-6 text-center text-gray-500 text-sm">
            © {{ currentYear }} FinTrack
        </footer>
    </main>
</template>
