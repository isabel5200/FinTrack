<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
    icon: {
        type: String,
    },
    collapsed: {
        type: Boolean,
    },
});
</script>

<template>
    <Link :href="href" class="flex items-center gap-3 px-3 py-2 rounded transition duration-300
               hover:bg-gray-200 dark:hover:bg-[#111827]" :class="{ 'bg-gray-200 dark:bg-[#111827]': active }">
    <!-- Icon -->
    <i :class="`fa-solid ${icon} text-lg`"></i>
    <!-- Text -->
    <span class="transition-all duration-300 whitespace-nowrap" :class="{
        'opacity-0 translate-x-[-10px] w-0 overflow-hidden': collapsed,
        'opacity-100 translate-x-0 w-auto': !collapsed
    }">
        <slot />
    </span>
    </Link>
</template>
