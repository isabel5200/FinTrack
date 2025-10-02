<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { useConfirm } from 'primevue/useconfirm';
import { useToast } from "primevue/usetoast";
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from 'primevue/card';
import DataTable from '@/Components/DataTable.vue';
import DatePicker from 'primevue/datepicker';
import FileUpload from 'primevue/fileupload';
import Modal from '@/Components/Modal.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputText from 'primevue/inputtext';
import Select from 'primevue/select';
import Textarea from 'primevue/textarea';

const props = defineProps({
    transactions: {
        type: Array,
        required: true
    }
});

const confirm = useConfirm();
const toast = useToast();
const isCreateModalOpen = ref(false);
const isViewModalOpen = ref(false);
const isLoading = ref(false);
const categories = ref([]);
const types = [
    { value: 'expense', label: 'Expense' },
    { value: 'income', label: 'Income' },
];
const columns = [
    { field: 'id', header: 'ID' },
    { field: 'amount', header: 'Amount' },
    { field: 'type', header: 'Type' },
    { field: 'category', header: 'Category' },
    { field: 'description', header: 'Description' },
    { field: 'date', header: 'Date' },
];
const form = useForm({
    id: null,
    amount: '',
    type: '',
    category: '',
    description: '',
    attachment: null,
    date: '',
});
const viewTransaction = ref([]);

// Get categories based on the selected type
const getCategories = async () => {
    isLoading.value = true;

    try {
        const response = await axios.post(route('categories.getCategoriesByType', { type: form.type }));
        isLoading.value = false;

        if (response.data?.categories?.length === 0) {
            toast.add({
                severity: 'info',
                summary: 'Info',
                detail: 'No categories found for the selected type.',
                life: 3000,
            });
        } else if (response.data?.categories) {
            categories.value = response.data.categories;
        }
    } catch (error) {
        isLoading.value = false;

        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: `${error.response?.data?.message ?? 'An error occurred'}`,
            life: 3000,
        });

        console.error(error);
    } finally {
        isLoading.value = false;
    }
};

// View transaction details
const getTransaction = async (id) => {
    try {
        const response = await axios.get(route('transactions.show', id));

        viewTransaction.value = response.data.transaction;
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

// Confirm delete transaction
const confirmDeleteTransaction = (id) => {
    confirm.require({
        message: 'Are you sure you want to delete this transaction?',
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
        accept: () => deleteTransaction(id)
    });
};

// Delete transaction
const deleteTransaction = async (id) => {
    try {
        await axios.delete(route('transactions.destroy', id));

        toast.add({
            severity: 'success',
            summary: 'Success',
            detail: 'Transaction deleted successfully.',
            life: 3000,
        });
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
    isCreateModalOpen.value = true;
};

const closeCreateModal = () => {
    resetForm();
    categories.value = [];

    isCreateModalOpen.value = false;
};

const onSelectAttachment = (e) => {
    const file = e.files ? e.files[0] : null;

    form.attachment = file;
};

const createTransaction = () => {
    form.errors = {};
    form.post(route('transactions.store'), {
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

    <Head title="Transactions" />
    <!-- Layout -->
    <AuthenticatedLayout>
        <!-- Confirm dialog -->
        <ConfirmDialog />
        <!-- Header -->
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Your transactions
            </h2>
        </template>
        <!-- Create modal -->
        <Modal :show="isCreateModalOpen" :closeable="!form.processing" @close="closeCreateModal">
            <div class="m-5">
                <!-- Title -->
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    New transaction
                </h2>
                <!-- Form -->
                <form @submit.prevent="createTransaction" class="mt-3">
                    <!-- Amount -->
                    <InputLabel for="amount" value="Amount" />
                    <InputText id="amount" v-model="form.amount" type="number" placeholder="Enter an amount"
                        class="mt-1 block w-full" />
                    <InputError :message="form.errors.amount" class="mt-2" />
                    <!-- Type -->
                    <InputLabel for="type" value="Type" class="mt-3" />
                    <Select id="type" v-model="form.type" :options="types" optionValue="value" optionLabel="label"
                        placeholder="Select a type" class="mt-1 block w-full" appendTo="self" @change="getCategories" />
                    <InputError :message="form.errors.type" class="mt-2" />
                    <!-- Category -->
                    <InputLabel for="category" value="Category" class="mt-3" />
                    <Select id="category" v-model="form.category" :options="categories" optionValue="id"
                        optionLabel="name" placeholder="Select a category"
                        :disabled="categories.length === 0 || isLoading" :loading="isLoading" class="mt-1 block w-full"
                        appendTo="self" />
                    <InputError :message="form.errors.category" class="mt-2" />
                    <small class="text-sm text-gray-500 dark:text-gray-400">
                        Showing categories depending on the selected type.
                    </small>
                    <!-- Description -->
                    <InputLabel for="description" value="Description" class="mt-3" />
                    <Textarea id="description" v-model="form.description" placeholder="Enter a description"
                        :autoResize="false" class="mt-1 block w-full" rows="3"></Textarea>
                    <InputError :message="form.errors.description" class="mt-2" />
                    <!-- Attachment -->
                    <InputLabel for="attachment" value="File" class="mt-3" />
                    <small class="text-sm text-gray-500 dark:text-gray-400">
                        Attach a file (optional). Available formats: JPG, JPEG, PNG, WEBP, PDF
                    </small>
                    <FileUpload mode="basic" name="attachment" accept="image/*,application/pdf" :auto="false"
                        chooseLabel="Select a file" :maxFileSize="2000000" @select="onSelectAttachment"
                        class="mt-1 block w-full" />
                    <InputError :message="form.errors.attachment" class="mt-2" />
                    <!-- Date -->
                    <InputLabel for="date" value="Date" class="mt-3" />
                    <DatePicker id="date" v-model="form.date" dateFormat="dd/mm/yy" placeholder="Enter a date"
                        class="mt-1 block w-full" />
                    <InputError :message="form.errors.date" class="mt-2" />
                    <!-- Buttons -->
                    <div class="flex justify-end mt-5">
                        <!-- Cancel button -->
                        <Button raised rounded label="Cancel" icon="fa-solid fa-rectangle-xmark" severity="danger"
                            :disabled="form.processing" class="mr-2" @click="closeCreateModal" />
                        <!-- Submit button -->
                        <Button raised rounded label="Create" type="submit" icon="fa-solid fa-circle-plus"
                            :disabled="!form.amount || !form.type || !form.category || !form.date || form.processing"
                            :loading="form.processing" />
                    </div>
                </form>
            </div>
        </Modal>
        <!-- View modal -->
        <Modal :show="isViewModalOpen" :closeable="true" @close="isViewModalOpen = false">
            <div class="m-5">
                <!-- Title -->
                <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Transaction details
                </h2>
                <!-- Details -->
                <div class="mt-3 space-y-4">
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 w-32">Amount:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ viewTransaction.amount }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 w-32">Type:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ viewTransaction.type }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 w-32">Category:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ viewTransaction.category }}</span>
                    </div>
                    <div v-if="viewTransaction.attachment_view" class="flex items-center">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 w-32">Attachment:</span>
                        <a :href="viewTransaction.attachment_view" target="_blank"
                            class="text-blue-500 hover:underline dark:text-blue-400">
                            <Button label="View" icon="fa-solid fa-eye" severity="info" size="small" />
                        </a>
                        <a :href="viewTransaction.attachment_download" target="_blank"
                            class="text-blue-500 hover:underline dark:text-blue-400">
                            <Button label="Download" icon="fa-solid fa-download" severity="success" size="small"
                                class="ml-2" />
                        </a>
                    </div>
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 w-32">Description:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ viewTransaction.description }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 w-32">Date:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ viewTransaction.date }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 w-32">Created:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ viewTransaction.created }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="font-semibold text-gray-700 dark:text-gray-300 w-32">Last updated:</span>
                        <span class="text-gray-900 dark:text-gray-100">{{ viewTransaction.updated }}</span>
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
                        <template #title>Transactions</template>
                        <!-- Content -->
                        <template #content>
                            <!-- DataTable -->
                            <DataTable :columns="columns" :onCreate="openCreateModal" :onView="getTransaction"
                                :onDelete="confirmDeleteTransaction" :data="props.transactions">
                            </DataTable>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
