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
import { useToast } from "primevue/usetoast";

const props = defineProps({
    budgets: {
        type: Array,
        required: true
    }
});

const toast = useToast();
const isCreateModalOpen = ref(false);
const categories = ref([]);
const frequencies = [
    { value: 'daily', label: 'Daily' },
    { value: 'weekly', label: 'Weekly' },
    { value: 'monthly', label: 'Monthly' },
];
const columns = [
    { field: 'id', header: 'ID' },
    { field: 'category', header: 'Category' },
    { field: 'max_amount', header: 'Max amount' },
    { field: 'frequency', header: 'Frequency' },
];
const form = useForm({
    id: null,
    category: '',
    max_amount: '',
    frequency: '',
});

const getCategories = async () => {
    try {
        const response = await axios.get(route('categories.getCategories'));

        categories.value = response.data.categories;

        /*
        categories.value = response.data.categories.map(category => ({
            ...category,
            label: `${category.name} (${category.type})`,
        }));
        */
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: `${error.response?.data?.message ?? 'An error occurred'}`,
            life: 3000,
        });

        console.error(error);
    }
};

const resetForm = () => {
    form.reset();
};

const openCreateModal = () => {
    getCategories();

    isCreateModalOpen.value = true;
};

const closeCreateModal = () => {
    resetForm();

    isCreateModalOpen.value = false;
};

const createBudget = () => {
    form.errors = {};
    form.post(route('budgets.store'), {
        onSuccess: () => {
            closeCreateModal();
        },
    });
};

onMounted(() => {
});
</script>

<template>

    <Head title="Budgets" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Budgets
            </h2>
        </template>
        <Modal :show="isCreateModalOpen" :closeable="!form.processing" @close="closeCreateModal">
            <div class="m-5">
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    New budget
                </h2>
                <form @submit.prevent="createBudget" class="mt-3">
                    <InputLabel for="category" value="Category" />
                    <Select id="category" v-model="form.category" :options="categories" optionValue="id"
                        optionLabel="name" placeholder="Select a category" class="mt-1 block w-full" appendTo="self" />
                    <small class="text-sm text-gray-500 dark:text-gray-400">
                        Showing categories with type "Expense" only.
                    </small>
                    <InputError :message="form.errors.category" class="mt-2" />
                    <InputLabel for="max_amount" value="Max amount" class="mt-3" />
                    <InputText id="max_amount" v-model="form.max_amount" type="number" placeholder="Enter a max amount"
                        class="mt-1 block w-full" />
                    <InputError :message="form.errors.max_amount" class="mt-2" />
                    <InputLabel for="frequency" value="Frequency" class="mt-3" />
                    <Select id="frequency" v-model="form.frequency" :options="frequencies" optionValue="value"
                        optionLabel="label" placeholder="Select a frequency" class="mt-1 block w-full"
                        appendTo="self" />
                    <InputError :message="form.errors.frequency" class="mt-2" />
                    <div class="flex justify-end mt-5">
                        <Button raised rounded label="Create" type="submit" icon="fa-solid fa-circle-plus"
                            :loading="form.processing" />
                        <Button raised rounded label="Cancel" icon="fa-solid fa-rectangle-xmark" severity="danger"
                            class="ml-2" @click="closeCreateModal" />
                    </div>
                </form>
            </div>
        </Modal>
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <Card>
                        <template #title>Budgets</template>
                        <template #content>
                            <DataTable :columns="columns" :onCreate="openCreateModal" :data="props.budgets"></DataTable>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
