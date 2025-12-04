<script setup>
import { ref, onBeforeUnmount, onMounted, watchEffect } from 'vue';
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import NavLink from '@/Components/NavLink.vue';
import ThemeToggleNeon from '@/Components/ThemeToggleNeon.vue';
import Toast from 'primevue/toast';

const toast = useToast();
const isCollapsed = ref(false);
const isMobileOpen = ref(false);
const showingNavigationDropdown = ref(false);
const dropdownRef = ref(null);

// Toggles the sidebar differently depending on screen size:
// - On mobile: opens/closes the full sidebar
// - On desktop: collapses/expands the sidebar width
const toggleSidebar = () => {
    if (window.innerWidth < 768) {
        isMobileOpen.value = !isMobileOpen.value;
    } else {
        isCollapsed.value = !isCollapsed.value;
    }
};

// Closes the user dropdown when clicking outside of it
// Checks if the click target is NOT inside the dropdown container
const handleClickOutside = (e) => {
    if (dropdownRef.value && !dropdownRef.value.contains(e.target)) {
        showingNavigationDropdown.value = false;
    }
};

// Display flash messages
watchEffect(() => {
    let flash = usePage().props.flash;

    if (flash.success) {
        toast.add({ severity: 'success', summary: 'Success', detail: flash.success, life: 5000 });

        usePage().props.flash.success = null;
    }
    if (flash.error) {
        toast.add({ severity: 'error', summary: 'Error', detail: flash.error, life: 5000 });

        usePage().props.flash.error = null;
    }
    if (flash.warning) {
        toast.add({ severity: 'warn', summary: 'Warning', detail: flash.warning, life: 5000 });

        usePage().props.flash.warning = null;
    }
    if (flash.info) {
        toast.add({ severity: 'info', summary: 'Information', detail: flash.info, life: 5000 });

        usePage().props.flash.info = null;
    }
})

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
    <Toast />
    <div class="h-screen flex bg-white text-gray-900 dark:bg-dark dark:text-[#e4e8ff]">
        <!-- Sidebar -->
        <aside class="bg-gray-100 dark:bg-sidebar border-r border-gray-200 dark:border-[#1f2937]
           transition-all duration-300
           fixed md:static h-full top-0 left-0 z-40" :class="[
            isMobileOpen
                ? 'translate-x-0 w-64'
                : '-translate-x-full md:translate-x-0',
            isCollapsed && !isMobileOpen ? 'md:w-20' : 'md:w-64'
        ]">
            <!-- Title -->
            <div class="p-4 flex items-center gap-3 transition-all" :class="isCollapsed ? 'justify-center' : ''">
                <!-- Logo -->
                <Link :href="route('dashboard')">
                <ApplicationLogo />
                </Link>
                <!-- FinTrack title -->
                <span v-if="!isCollapsed" class="text-xl font-bold text-gray-700 dark:text-neon transition-all">
                    FinTrack
                </span>
            </div>
            <!-- Navigation links -->
            <nav class="p-3 space-y-1">
                <!-- Dashboard -->
                <NavLink icon="fa-chart-line" :href="route('dashboard')" :active="route().current('dashboard')"
                    :collapsed="isCollapsed">
                    Dashboard
                </NavLink>
                <!-- Budgets -->
                <NavLink icon="fa-wallet" :href="route('budgets.index')" :active="route().current('budgets.index')"
                    :collapsed="isCollapsed">
                    Budgets
                </NavLink>
                <!-- Categories -->
                <NavLink icon="fa-tags" :href="route('categories.index')" :active="route().current('categories.index')"
                    :collapsed="isCollapsed">
                    Categories
                </NavLink>
                <!-- Transactions -->
                <NavLink icon="fa-exchange-alt" :href="route('transactions.index')"
                    :active="route().current('transactions.index')" :collapsed="isCollapsed">
                    Transactions
                </NavLink>
            </nav>
        </aside>
        <!-- Mobile overlay (appears only when the sidebar is open on mobile) -->
        <div v-if="isMobileOpen" @click="isMobileOpen = false" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-30 md:hidden
           transition-opacity duration-300">
        </div>
        <!-- Main content wrapper -->
        <div class="flex-1 flex flex-col w-full transition-all duration-300">
            <!-- Header / Top navigation bar -->
            <header
                class="h-16 flex items-center justify-between px-6 bg-gray-100 dark:bg-sidebar border-b border-gray-200 dark:border-[#1f2937]">
                <!-- Sidebar toggle button (desktop only) -->
                <button @click="toggleSidebar" class="p-2 rounded hover:bg-gray-200 dark:hover:bg-[#111827]
                   transition-all duration-300 md:inline-block hidden">
                    <!-- Hamburger icon that rotates when collapsed -->
                    <i class="fa-solid fa-bars text-xl transition-all duration-300"
                        :class="{ 'rotate-180': isCollapsed }">
                    </i>
                </button>
                <!-- Sidebar toggle button (mobile only) -->
                <button @click="isMobileOpen = true" class="p-2 md:hidden">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <!-- Page Heading -->
                <header v-if="$slots.header">
                    <div class="text-lg font-semibold">
                        <slot name="header" />
                    </div>
                </header>
                <!-- Right-side controls (theme switcher, username, avatar) -->
                <div class="flex items-center gap-4">
                    <!-- Dark/light mode toggle -->
                    <ThemeToggleNeon />
                    <div class="flex justify-center items-center gap-2">
                        <!-- Username -->
                        <span class="text-sm opacity-70"> {{ $page.props.auth.user.name }}
                        </span>
                    </div>
                    <!-- Profile settings container -->
                    <div class="relative select-none" ref="dropdownRef">
                        <!-- Avatar button -->
                        <button @click.stop="showingNavigationDropdown = !showingNavigationDropdown"
                            class="relative z-20 w-9 h-9 rounded-full bg-gray-200 text-gray-600 shadow-inner backdrop-blur-md flex items-center justify-center transition-all hover:scale-105 active:scale-95 hover:ring-2 hover:ring-cyan-300/50">
                            <i class="fa-solid fa-user text-lg"></i>
                            <!-- Flechita -->
                            <i class="fa-solid fa-chevron-down text-[10px] absolute -bottom-[3px] -right-[3px] bg-gray-300 rounded-full p-[2px] transition-transform"
                                :class="{ 'rotate-180': showingNavigationDropdown }"></i>
                        </button>
                        <!-- Transition -->
                        <transition enter-active-class="transition ease-out duration-150"
                            enter-from-class="opacity-0 -translate-y-2 scale-95"
                            enter-to-class="opacity-100 translate-y-0 scale-100"
                            leave-active-class="transition ease-in duration-120"
                            leave-from-class="opacity-100 translate-y-0 scale-100"
                            leave-to-class="opacity-0 -translate-y-2 scale-95">
                            <!-- Dropdown -->
                            <div v-show="showingNavigationDropdown"
                                class="absolute right-0 mt-2 w-40 rounded-xl bg-gray-200 dark:bg-[#0e0f1a] shadow-lg backdrop-blur-xl border border-white/10 overflow-hidden z-10">
                                <!-- Profile -->
                                <Link :href="route('profile.edit')">
                                <button
                                    class="flex items-center w-40 gap-2 px-4 py-2 hover:bg-white/40 dark:hover:bg-[#111827] transition">
                                    Profile
                                </button>
                                </Link>
                                <!-- Logout -->
                                <Link :href="route('logout')" method="post" as="button">
                                <button
                                    class="flex items-center w-40 gap-2 px-4 py-2 hover:bg-white/40 dark:hover:bg-[#111827] transition">
                                    Log out
                                    <i class="fa-solid fa-arrow-right-from-bracket ml-auto opacity-80"></i>
                                </button>
                                </Link>
                            </div>
                        </transition>
                    </div>
                </div>
            </header>
            <!-- Page content container -->
            <main>
                <!-- Slot where each page injects its own content -->
                <slot />
            </main>
        </div>
    </div>
</template>
