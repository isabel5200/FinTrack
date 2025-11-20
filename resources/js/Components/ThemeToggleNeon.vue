<script setup>
import useTheme from '@/Composables/useTheme';

const { theme, setTheme } = useTheme();
</script>

<template>
    <div class="relative w-40 h-10 rounded-full p-1 flex items-center select-none
                bg-gray-300 dark:bg-[#111827] shadow-inner">
        <!-- Glow del fondo -->
        <div class="absolute top-0 bottom-0 w-1/3 bg-neon/30 blur-xl transition-all duration-300 rounded-full" :class="{
            'left-0': theme === 'light',
            'left-1/3': theme === 'system',
            'left-2/3': theme === 'dark'
        }"></div>
        <!-- Botón deslizante -->
        <div class="absolute top-1 bottom-1 w-1/3 bg-neon text-black font-bold flex items-center justify-center
                    rounded-full cursor-pointer transition-all duration-300" :class="{
                        'left-1': theme === 'light',
                        'left-[calc(33%+4px)]': theme === 'system',
                        'left-[calc(66%+4px)]': theme === 'dark'
                    }">
            {{ theme === 'light' ? 'Claro' : theme === 'dark' ? 'Oscuro' : 'Sistema' }}
        </div>
        <!-- Áreas clicables -->
        <div class="flex w-full h-full relative z-10">
            <div class="w-1/3 h-full cursor-pointer" @click="setTheme('light')"></div>
            <div class="w-1/3 h-full cursor-pointer" @click="setTheme('system')"></div>
            <div class="w-1/3 h-full cursor-pointer" @click="setTheme('dark')"></div>
        </div>
    </div>
</template>
