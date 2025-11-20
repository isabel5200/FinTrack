<script setup>
import ThemeToggleNeon from '@/Components/ThemeToggleNeon.vue';

const menu = [
    { label: "Inicio", href: "#" },
    { label: "Transacciones", href: "#" },
    { label: "Presupuestos", href: "#" },
    { label: "Reportes", href: "#" },
];

// function toggleTheme() {
//     const html = document.documentElement
//     html.classList.toggle('dark');

//     console.log('Cambiando');
// }
</script>

<template>
    <div class="min-h-screen flex bg-white text-gray-900 dark:bg-[#0d1117] dark:text-[#e4e8ff]">

        <!-- Sidebar -->
        <aside class="w-64 bg-gray-100 dark:bg-[#0e0f1a] border-r border-gray-200 dark:border-[#1f2937]">
            <div class="p-4 text-xl font-bold
        text-gray-700 dark:text-[#00eaff]">
                FinTrack
            </div>

            <nav class="p-3 space-y-1">
                <a v-for="item in menu" :key="item.label" :href="item.href" class="block px-3 py-2 rounded
            hover:bg-gray-200 dark:hover:bg-[#111827]
            transition">
                    {{ item.label }}
                </a>
            </nav>
        </aside>

        <div class="flex-1 flex flex-col">

            <!-- Header -->
            <header class="h-16 flex items-center justify-between px-6
        bg-gray-100 dark:bg-[#0e0f1a]
        border-b border-gray-200 dark:border-[#1f2937]">

                <h1 class="text-lg font-semibold">Dashboard</h1>

                <div class="flex items-center gap-3">
                    <!-- <span class="text-sm opacity-70"> -->
                    <!-- <button @click="toggleTheme" class="px-3 py-1 rounded bg-gray-300 dark:bg-[#111827]">Cambiar
                            tema</button> -->

                    <!-- <select v-model="theme" @change="setTheme(theme)"
                            class="px-10 py-1 rounded bg-gray-200 dark:bg-[#111827]">
                            <option value="light">Claro</option>
                            <option value="dark">Oscuro</option>
                            <option value="system">Sistema</option>
                        </select>
                    </span> -->
                    <ThemeToggleNeon />
                    <span class="text-sm opacity-70">Hola, Isa</span>
                    <div class="w-8 h-8 rounded-full bg-cyan-500"></div>
                </div>
            </header>

            <main class="p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
