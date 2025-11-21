<script setup>
import { ref } from 'vue';
import ThemeToggleNeon from '@/Components/ThemeToggleNeon.vue';

const menu = [
    { label: "Inicio", href: "#" },
    { label: "Transacciones", href: "#" },
    { label: "Presupuestos", href: "#" },
    { label: "Reportes", href: "#" },
];
const isCollapsed = ref(false);
const isMobileOpen = ref(false);

const toggleSidebar = () => {
    if (window.innerWidth < 768) {
        isMobileOpen.value = !isMobileOpen.value;
    } else {
        isCollapsed.value = !isCollapsed.value;
    }
};
</script>

<template>
    <div class="h-screen flex bg-white text-gray-900 dark:bg-[#0d1117] dark:text-[#e4e8ff]">
        <!-- Sidebar -->
        <aside class="bg-gray-100 dark:bg-[#0e0f1a] border-r border-gray-200 dark:border-[#1f2937]
           transition-all duration-300
           fixed md:static h-full top-0 left-0 z-40" :class="[
            isMobileOpen
                ? 'translate-x-0 w-64'
                : '-translate-x-full md:translate-x-0',
            isCollapsed && !isMobileOpen ? 'md:w-20' : 'md:w-64'
        ]">
            <div class="p-4 text-xl font-bold text-gray-700 dark:text-neon transition-all"
                :class="isCollapsed ? 'text-center' : ''">
                {{ isCollapsed ? 'FT' : 'FinTrack' }}
            </div>
            <!-- Navigation -->
            <nav class="p-3 space-y-1">
                <a v-for="item in menu" :key="item.label" :href="item.href"
                    class="flex items-center gap-3 px-3 py-2 rounded hover:bg-gray-200 dark:hover:bg-[#111827] transition duration-300">
                    <!-- Iconito fake por ahora -->
                    <div class="w-5 h-5 bg-neon rounded"></div>
                    <!-- Ocultar texto si está colapsado -->
                    <span class="whitespace-nowrap transition-all duration-300" :class="{
                        'opacity-0 translate-x-[-10px] w-0': isCollapsed,
                        'opacity-100 translate-x-[0px] w-auto': !isCollapsed
                    }">
                        {{ item.label }}
                    </span>
                </a>
            </nav>
        </aside>
        <div v-if="isMobileOpen" @click="isMobileOpen = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 md:hidden
           transition-opacity duration-300"></div>
        <div class="flex-1 flex flex-col transition-all duration-300">
            <!-- Header -->
            <header class="h-16 flex items-center justify-between px-6
        bg-gray-100 dark:bg-[#0e0f1a]
        border-b border-gray-200 dark:border-[#1f2937]">
                <!-- Collapse sidebard -->
                <!-- Botón desktop -->
                <button @click="toggleSidebar" class="p-2 rounded hover:bg-gray-200 dark:hover:bg-[#111827]
    transition-all duration-300 md:inline-block hidden">
                    <i class="fa-solid fa-bars text-xl transition-all duration-300"
                        :class="{ 'rotate-180': isCollapsed }">
                    </i>
                </button>
                <!-- Botón móvil -->
                <button @click="isMobileOpen = true" class="p-2 md:hidden">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <!-- Title -->
                <h1 class="text-lg font-semibold">Dashboard</h1>
                <!-- Dark mode and username -->
                <div class="flex items-center gap-4">
                    <ThemeToggleNeon />
                    <span class="text-sm opacity-70">Isabel Lovera</span>
                    <div class="w-8 h-8 rounded-full bg-cyan-500"></div>
                </div>
            </header>
            <!-- Content -->
            <main class="p-6">
                <slot />
            </main>
        </div>
    </div>
</template>
