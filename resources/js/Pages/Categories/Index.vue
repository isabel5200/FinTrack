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

// Props
const props = defineProps({
    categories: {
        type: Array,
        required: true
    }
});

// Variables
const confirm = useConfirm();
const toast = useToast();
const visible = ref(false);
const isCreateModalOpen = ref(false);
const isViewModalOpen = ref(false);
const types = [
    { value: 'expense', label: 'Expense' },
    { value: 'income', label: 'Income' },
];
const columns = [
    { field: 'id', header: 'ID' },
    { field: 'name', header: 'Name' },
    { field: 'type', header: 'Type' }
];
const form = useForm({
    id: null,
    name: '',
    type: ''
});
const viewCategory = ref([]);

// Methods
const getCategory = async (id) => {
    try {
        const response = await axios.get(route('categories.show', id));

        viewCategory.value = response.data.category;
        isViewModalOpen.value = true;
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: `${error.response?.data?.message ?? 'An error occurred'}`,
            life: 3000,
        });
    }
};

// Form methods
const resetForm = () => {
    form.errors = {};
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

// Confirm delete category
const confirmDeleteCategory = (id) => {
    confirm.require({
        message: 'Are you sure you want to delete this category?',
        header: 'Confirmation',
        icon: 'fa-solid fa-triangle-exclamation',
        rejectLabel: 'Cancel',
        rejectProps: {
            label: 'Cancel',
            severity: 'secondary',
            outlined: true,
            raised: true,
            rounded: true,
        },
        acceptProps: {
            label: 'Delete',
            severity: 'danger',
            raised: true,
            rounded: true,
        },
        accept: () => deleteCategory(id)
    });
};

// Delete category
const deleteCategory = async (id) => {
    visible.value = true;

    try {
        const response = await axios.delete(route('categories.destroy', id));

        visible.value = false;

        toast.add({
            severity: 'success',
            summary: 'Success',
            detail: `${response?.data?.message}` || 'Category deleted successfully',
            life: 3000,
        });

        router.reload({ only: ['categories'] });
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: `${error.response?.data?.message}` ?? 'An error ocurred',
            life: 3000,
        });

        console.log(error);

        visible.value = false;
    }
};

// Vue methods
onMounted(() => {
});
</script>

<template>
    <!-- Head title -->

    <Head title="Categories" />
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
                    <InputText id="name" v-model="form.name" type="text" placeholder="Enter a name"
                        class="mt-1 block w-full" />
                    <InputError :message="form.errors.name" class="mt-2" />
                    <!-- Type -->
                    <InputLabel for="type" value="Type" class="mt-3" />
                    <Select id="type" v-model="form.type" :options="types" optionValue="value" optionLabel="label"
                        placeholder="Select a type" class="mt-1 block w-full" appendTo="self" />
                    <InputError :message="form.errors.type" class="mt-2" />
                    <!-- Buttons -->
                    <div class="flex justify-end mt-5">
                        <!-- Cancel button -->
                        <Button raised rounded label="Cancel" type="button" icon="fa-solid fa-rectangle-xmark"
                            severity="danger" :disabled="form.processing" class="mr-2" @click="closeCreateModal" />
                        <!-- Submit button -->
                        <Button raised rounded label="Create" type="submit" icon="fa-solid fa-circle-plus"
                            :disabled="!form.name || !form.type || form.processing" :loading="form.processing" />
                    </div>
                </form>
            </div>
        </Modal>
        <!-- View modal -->
        <Modal :show="isViewModalOpen" :closeable="true" @close="isViewModalOpen = false">
            <div class="m-5">
                <!-- Title -->
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Category details
                </h2>
                <!-- Details -->
                <div class="mt-3 space-y-4">
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 w-32">Name:</span>
                        <span class="text-gray-900 dark:text-gray-100">
                            {{ viewCategory.name }}
                        </span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 w-32">Type:</span>
                        <span class="text-gray-900 dark:text-gray-100">
                            {{ viewCategory.type }}
                        </span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 w-32">Created:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ viewCategory.created }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 w-32">Last updated:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ viewCategory.updated }}</span>
                    </div>
                </div>
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
                            <DataTable :columns="columns" :onCreate="openCreateModal" :onView="getCategory"
                                :onDelete="confirmDeleteCategory" :data="props.categories">
                            </DataTable>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
