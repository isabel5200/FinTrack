<script setup>
import { ref } from 'vue';
import useTheme from '@/Composables/useTheme';

const { theme, setTheme } = useTheme();

const open = ref(false);
</script>

<template>
    <!-- Switch desktop -->
    <div class="hidden md:block">
        <div class="relative w-40 h-10 rounded-full p-1 flex items-center select-none
                    bg-gray-300 dark:bg-[#111827] shadow-inner">
            <!-- Background glow -->
            <div class="absolute top-0 bottom-0 w-1/3 bg-neon/30 blur-xl transition-all duration-300 rounded-full"
                :class="{
                    'left-0': theme === 'light',
                    'left-1/3': theme === 'system',
                    'left-2/3': theme === 'dark'
                }">
            </div>
            <!-- Sliding button -->
            <div class="absolute top-1 bottom-1 w-1/3 bg-neon text-black font-bold flex items-center justify-center
                        rounded-full cursor-pointer transition-all duration-300" :class="{
                            'left-1': theme === 'light',
                            'left-[calc(33%+4px)]': theme === 'system',
                            'left-[calc(66%+4px)]': theme === 'dark'
                        }">
                <!-- Icon -->
                <i :class="{
                    'fa-solid fa-sun': theme === 'light',
                    'fa-solid fa-desktop': theme === 'system',
                    'fa-solid fa-moon': theme === 'dark'
                }">
                </i>
            </div>
            <!-- Clickable areas -->
            <div class="flex w-full h-full relative z-10">
                <div class="w-1/3 h-full cursor-pointer" @click="setTheme('light')"></div>
                <div class="w-1/3 h-full cursor-pointer" @click="setTheme('system')"></div>
                <div class="w-1/3 h-full cursor-pointer" @click="setTheme('dark')"></div>
            </div>
        </div>
    </div>
    <!-- Dropdown mobile -->
    <div class="relative block md:hidden select-none z-10">
        <!-- Open/Close button -->
        <button @click="open = !open" class="flex items-center gap-2 px-4 py-2 rounded-xl
                   bg-gray-300 dark:bg-[#111827]
                   backdrop-blur-md shadow-inner
                   text-black dark:text-white
                   transition hover:scale-105 active:scale-95">
            <i :class="{
                'fa-solid fa-sun text-yellow-400': theme === 'light',
                'fa-solid fa-desktop text-blue-400': theme === 'system',
                'fa-solid fa-moon text-purple-400': theme === 'dark'
            }"></i>
            <i class="fa-solid fa-chevron-down text-sm transition" :class="{ 'rotate-180': open }"></i>
        </button>
        <!-- Floating list -->
        <div v-show="open" class="absolute left-0 mt-2 w-40 rounded-xl
                   bg-gray-300 dark:bg-[#111827]
                   shadow-lg backdrop-blur-xl border border-white/10
                   overflow-hidden animate-dropdown">
            <div class="flex flex-col">
                <!-- Light -->
                <button @click="setTheme('light'); open = false"
                    class="flex items-center gap-2 px-4 py-2 hover:bg-white/20 transition">
                    <i class="fa-solid fa-sun text-yellow-400"></i> Light
                </button>
                <!-- System -->
                <button @click="setTheme('system'); open = false"
                    class="flex items-center gap-2 px-4 py-2 hover:bg-white/20 transition">
                    <i class="fa-solid fa-desktop text-blue-400"></i> System
                </button>
                <!-- Dark -->
                <button @click="setTheme('dark'); open = false"
                    class="flex items-center gap-2 px-4 py-2 hover:bg-white/20 transition">
                    <i class="fa-solid fa-moon text-purple-400"></i> Dark
                </button>
            </div>
        </div>
    </div>
</template>
