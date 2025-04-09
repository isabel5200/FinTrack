<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from 'primevue/card';
import DataTable from '@/Components/DataTable.vue';

const props = defineProps({
    transactions: {
        type: Array,
        required: true
    }
});

const columns = [
    { field: 'id', header: 'ID' },
    { field: 'amount', header: 'Amount' },
    { field: 'type', header: 'Type' },
    { field: 'category', header: 'Category' },
    { field: 'description', header: 'Description' },
    { field: 'date', header: 'Date' },
];

onMounted(() => {
    console.log(props.transactions);
});
</script>

<template>

    <Head title="Transactions" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Your transactions
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <Card>
                        <template #title>Transactions</template>
                        <template #content>
                            <DataTable :columns="columns" :data="props.transactions"></DataTable>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
