<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from "primevue/usetoast";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from 'primevue/card';
import DataTable from '@/Components/DataTable.vue';
import Dialog from 'primevue/dialog';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputText from 'primevue/inputtext';
import ProgressSpinner from 'primevue/progressspinner';
import Select from 'primevue/select';

const props = defineProps({
    budgets: {
        type: Array,
        required: true
    }
});

const confirm = useConfirm();
const toast = useToast();
const visible = ref(false);
const isViewModalOpen = ref(false);
const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
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

const resetForm = () => {
    form.errors = {};
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

const openEditModal = (id) => {
    getCategories();
    getBudgetForEdit(id);
};

const closeEditModal = () => {
    resetForm();

    isEditModalOpen.value = false;
};

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

const createBudget = () => {
    form.errors = {};
    form.post(route('budgets.store'), {
        onSuccess: () => {
            closeCreateModal();
        },
    });
};

const getBudgetForEdit = async (id) => {
    try {
        const response = await axios.get(route('budgets.edit', id));

        form.id = response.data.budget.id;
        form.category = response.data.budget.category;
        form.max_amount = response.data.budget.max_amount;
        form.frequency = response.data.budget.frequency;

        isEditModalOpen.value = true;
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

// Confirm delete budget
const confirmDeleteBudget = (id) => {
    confirm.require({
        message: 'Are you sure you want to delete this budget?',
        header: 'Confirmation',
        icon: 'fa-solid fa-triangle-exclamation',
        rejectLabel: 'Cancel',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true,
            raised: true,
            rounded: true
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger',
            raised: true,
            rounded: true
        },
        accept: () => deleteBudget(id)
    });
};

// Delete budget
const deleteBudget = async (id) => {
    visible.value = true;

    try {
        const response = await axios.delete(route('budgets.destroy', id));

        visible.value = false;

        toast.add({
            severity: 'success',
            summary: 'Success',
            detail: `${response?.data?.message}` || 'Budget deleted successfully',
            life: 3000,
        });

        router.reload({ only: ['budgets'] });
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: `${error.response?.data?.message ?? 'An error occurred'}`,
            life: 3000,
        });

        console.error(error);

        visible.value = false;
    }
};

onMounted(() => {
});
</script>

<template>
    <!-- Head title -->

    <Head title="Budgets" />
    <!-- Layout -->
    <AuthenticatedLayout>
        <!-- Confirm dialog -->
        <ConfirmDialog />
        <!-- Loading dialog -->
        <Dialog v-model:visible="visible" modal pt:root:class="!border-0" :closable="false" :closeOnEscape="false">
            <template #container="{ closeCallback }">
                <div class="flex items-center px-3 py-3 gap-3 rounded-2xl">
                    <ProgressSpinner style="width: 20px; height: 20px;" strokeWidth="8" fill="transparent"
                        animationDuration="1.5s" aria-label="Custom ProgressSpinner" :unstyled="false" />
                    <h1 class="font-bold text-base text-blue-500 dark:text-blue-200">Loading... Please wait.</h1>
                </div>
            </template>
        </Dialog>
        <!-- Header -->
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Budgets
            </h2>
        </template>
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
                        <!-- Cancel button  -->
                        <Button raised rounded label="Cancel" icon="fa-solid fa-rectangle-xmark" severity="danger"
                            :disabled="form.processing" class="mr-2" @click="closeCreateModal" />
                        <!-- Submit button -->
                        <Button raised rounded label="Create" type="submit" icon="fa-solid fa-circle-plus"
                            :disabled="!form.category || !form.max_amount || !form.frequency || form.processing"
                            :loading="form.processing" />
                    </div>
                </form>
            </div>
        </Modal>
        <!-- Edit modal -->
        <Modal :show="isEditModalOpen" :closeable="!form.processing" @close="closeEditModal">
            <div class="m-5">
                <!-- Title -->
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Edit budget
                </h2>
                <!-- Form -->
                <form @submit.prevent="updateBudget" class="mt-3">
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
                        <!-- Cancel button  -->
                        <Button raised rounded label="Cancel" icon="fa-solid fa-floppy-disk" severity="danger"
                            :disabled="form.processing" class="mr-2" @click="closeEditModal" />
                        <!-- Submit button -->
                        <Button raised rounded label="Save" type="submit" icon="fa-solid fa-circle-plus"
                            :disabled="!form.category || !form.max_amount || !form.frequency || form.processing"
                            :loading="form.processing" />
                    </div>
                </form>
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
                            <DataTable :columns="columns" :onView="getBudget" :onCreate="openCreateModal"
                                :onEdit="openEditModal" :onDelete="confirmDeleteBudget" :data="props.budgets">
                            </DataTable>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
