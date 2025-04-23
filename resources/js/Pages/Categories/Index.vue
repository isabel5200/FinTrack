<script setup>
import { Head, router, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from 'primevue/card';
import DataTable from '@/Components/DataTable.vue';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';

// Props
const props = defineProps({
    categories: {
        type: Array,
        required: true
    }
});

// Variables
const isCreateModalOpen = ref(false);
const types = [
    { value: 'income', label: 'Income' },
    { value: 'expense', label: 'Expense' },
];
const columns = [
    { field: 'id', header: 'ID' },
    { field: 'name', header: 'Name' },
    { field: 'type', header: 'Type' }
];

// Methods

// Form methods
const form = useForm({
    id: null,
    name: '',
    type: ''
});

const resetForm = () => {
    form.reset();
};

const openCreateModal = () => {
    isCreateModalOpen.value = true;
};

const closeCreateModal = () => {
    resetForm();

    isCreateModalOpen.value = false;
};

const createCategory = () => {
    form.errors = {};
    form.post(route('categories.store'), {
        onSuccess: () => {
            closeCreateModal();
        },
    });
};

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
        <!-- Create modal -->
        <Modal :show="isCreateModalOpen" :closeable="!form.processing" @close="closeCreateModal">
            <div class="m-5">
                <!-- Title -->
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    New category
                </h2>
                <!-- Form -->
                <form @submit.prevent="createCategory" class="mt-3">
                    <!-- Name -->
                    <InputLabel for="name" value="Name" />
                    <InputText id="name" v-model="form.name" type="text" placeholder="Enter a name" class="mt-1 block w-full" />
                    <InputError :message="form.errors.name" class="mt-2" />
                    <!-- Type -->
                    <InputLabel for="type" value="Type" class="mt-3" />
                    <Select id="type" v-model="form.type" :options="types" optionValue="value" optionLabel="label" placeholder="Select a type" class="mt-1 block w-full" appendTo="self" />
                    <InputError :message="form.errors.type" class="mt-2" />
                    <!-- Buttons -->
                    <div class="flex justify-end mt-5">
                        <!-- Submit button -->
                        <Button raised rounded label="Create" type="submit" icon="fa-solid fa-circle-plus" severity="success" :loading="form.processing" />
                        <!-- Cancel button -->
                        <Button raised rounded label="Cancel" type="button" icon="fa-solid fa-rectangle-xmark" severity="danger" :disabled="form.processing" class="ml-2" @click="closeCreateModal" />
                    </div>
                </form>
            </div>
        </Modal>
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
                        <!-- Title -->
                        <template #title>Categories</template>
                        <!-- Content -->
                        <template #content>
                            <!-- DataTable -->
                            <DataTable :columns="columns" :onCreate="openCreateModal" :data="props.categories">
                            </DataTable>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
