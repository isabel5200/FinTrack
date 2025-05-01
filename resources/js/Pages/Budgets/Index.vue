<script setup>
import { Head, useForm } from '@inertiajs/vue3';
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
const isViewModalOpen = ref(false);
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
const viewBudget = ref([]);

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

const getBudget = async (id) => {
    try {
        const response = await axios.get(route('budgets.show', id));

        viewBudget.value = response.data.budget;
        isViewModalOpen.value = true;
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
    <!-- Head title -->

    <Head title="Budgets" />
    <!-- Layout -->
    <AuthenticatedLayout>
        <!-- Header -->
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Budgets
            </h2>
        </template>
        <!-- Create modal -->
        <Modal :show="isCreateModalOpen" :closeable="!form.processing" @close="closeCreateModal">
            <div class="m-5">
                <!-- Title -->
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    New budget
                </h2>
                <!-- Form -->
                <form @submit.prevent="createBudget" class="mt-3">
                    <!-- Category -->
                    <InputLabel for="category" value="Category" />
                    <Select id="category" v-model="form.category" :options="categories" optionValue="id"
                        optionLabel="name" placeholder="Select a category" class="mt-1 block w-full" appendTo="self" />
                    <small class="text-sm text-gray-500 dark:text-gray-400">
                        Showing categories with type "Expense" only.
                    </small>
                    <InputError :message="form.errors.category" class="mt-2" />
                    <!-- Max amount -->
                    <InputLabel for="max_amount" value="Max amount" class="mt-3" />
                    <InputText id="max_amount" v-model="form.max_amount" type="number" placeholder="Enter a max amount"
                        class="mt-1 block w-full" />
                    <InputError :message="form.errors.max_amount" class="mt-2" />
                    <!-- Frequency -->
                    <InputLabel for="frequency" value="Frequency" class="mt-3" />
                    <Select id="frequency" v-model="form.frequency" :options="frequencies" optionValue="value"
                        optionLabel="label" placeholder="Select a frequency" class="mt-1 block w-full"
                        appendTo="self" />
                    <InputError :message="form.errors.frequency" class="mt-2" />
                    <!-- Buttons -->
                    <div class="flex justify-end mt-5">
                        <!-- Submit button -->
                        <Button raised rounded label="Create" type="submit" icon="fa-solid fa-circle-plus"
                            :disabled="!form.category || !form.max_amount || !form.frequency || form.processing"
                            :loading="form.processing" />
                        <!-- Cancel button  -->
                        <Button raised rounded label="Cancel" icon="fa-solid fa-rectangle-xmark" severity="danger"
                            :disabled="form.processing" class="ml-2" @click="closeCreateModal" />
                    </div>
                </form>
            </div>
        </Modal>
        <!-- View modal -->
        <Modal :show="isViewModalOpen" :closeable="true" @close="isViewModalOpen = false">
            <div class="m-5">
                <!-- Title -->
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Budget details
                </h2>
                <!-- Details -->
                <div class="mt-3 space-y-4">
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 w-32">Category:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ viewBudget.category }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 w-32">Max Amount:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ viewBudget.max_amount }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 w-32">Frequency:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ viewBudget.frequency }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 w-32">Created:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ viewBudget.created }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 w-32">Last updated:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ viewBudget.updated }}</span>
                    </div>
                </div>
            </div>
        </Modal>
        <!-- Body -->
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <Card>
                        <!-- Title -->
                        <template #title>Budgets</template>
                        <!-- Content -->
                        <template #content>
                            <!-- DataTable -->
                            <DataTable :columns="columns" :onCreate="openCreateModal" :onView="getBudget"
                                :data="props.budgets"></DataTable>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
