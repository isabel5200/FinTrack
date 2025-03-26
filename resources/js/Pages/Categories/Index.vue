<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from 'primevue/card';
import DataTable from '@/Components/DataTable.vue';

// Props
const props = defineProps({
    categories: {
        type: Array,
        required: true
    }
});

// Variables

// Methods

// Vue methods
onMounted(() => {
    console.log(props.categories);
});
</script>

<template>
    <!-- Head title -->

    <Head title="Categories" />
    <!-- Layout -->
    <AuthenticatedLayout>
        <!-- Header -->
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Categories
            </h2>
        </template>
        <!-- Body -->
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <!-- Old title
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        Your categories
                    </div> -->
                    <!-- Card -->
                    <Card>
                        <template #title>Categories</template>
                        <template #content>
                            <DataTable :data="props.categories"></DataTable>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
