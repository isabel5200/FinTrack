<script setup>
import { FilterMatchMode } from '@primevue/core/api';
import { ref } from 'vue';
import Button from 'primevue/button';
import InputIcon from 'primevue/inputicon';
import IconField from 'primevue/iconfield';
import InputText from 'primevue/inputtext';
// import Toolbar from 'primevue/toolbar';

// Props
const props = defineProps({
    columns: {
        type: Array,
        required: false
    },
    data: {
        type: Array,
        required: false
    },
    rows: {
        type: Number,
        default: 5
    },
    onPage: {
        type: Function,
        default: null
    },
    totalRecords: {
        type: Number,
        default: 0
    },
    lazy: {
        type: Boolean,
        default: false
    },
    onCreate: {
        type: Function,
        default: null
    },
    onView: {
        type: Function,
        default: null
    },
    onEdit: {
        type: Function,
        default: null
    },
    onDelete: {
        type: Function,
        default: null
    }
});

// Variables
const filters = ref([]);

// Methods

// Initialize filters
const initFilters = () => {
    filters.value = {
        global: { value: null, matchMode: FilterMatchMode.CONTAINS }
    }
};

// Handle page change
const handlePage = (e) => {
    if (props.lazy && props.onPage) {
        props.onPage(e);
    }
};

// Handle create
const handleCreate = () => {
    if (props.onCreate) {
        props.onCreate();
    }
};

// Handle view
const handleView = (record) => {
    if (props.onView) {
        props.onView(record);
    }
};

// Handle edit
const handleEdit = (record) => {
    if (props.onEdit) {
        props.onEdit(record);
    }
};

// Handle delete
const handleDelete = (record) => {
    if (props.onDelete) {
        props.onDelete(record);
    }
};

// Truncate description
const truncateDescription = (description) => {
    if (description.length > 50) {
        return description.substring(0, 50) + '...';
    }

    return description;
};

// Initialize filters
initFilters();
</script>

<template>
    <!-- DataTable -->
    <DataTable removableSort paginator
        paginatorTemplate="RowsPerPageDropdown FirstPageLink PrevPageLink CurrentPageReport NextPageLink LastPageLink"
        currentPageReportTemplate="{first} of {last} of {totalRecords}" v-model:filters="filters" :rows="rows"
        :rowsPerPageOptions="[5, 10, 20]" :totalRecords="totalRecords" :lazy="lazy" :value="data" class="text-nowrap"
        @page="handlePage">
        <!-- Table header -->
        <template #header>
            <!-- Add button -->
            <Button raised rounded label="New" type="button" icon="fa-solid fa-circle-plus" severity="primary"
                @click="handleCreate" />
            <!-- Search -->
            <div class="flex justify-end mt-3">
                <IconField>
                    <!-- Icon -->
                    <InputIcon>
                        <i class="fa-solid fa-magnifying-glass" />
                    </InputIcon>
                    <!-- Input search -->
                    <InputText v-model="filters['global'].value" placeholder="Search" />
                </IconField>
            </div>
        </template>
        <!-- Columns -->
        <Column v-for="col of columns" sortable :key="col.field" :field="col.field" :header="col.header"
            :hidden="['id'].includes(col.field)">
            <template #body="slotProps">
                <!-- If description exists -->
                <span v-if="col.field === 'description'">
                    {{ truncateDescription(slotProps.data.description) }}
                </span>
                <span v-else>
                    {{ slotProps.data[col.field] }}
                </span>
            </template>
        </Column>
        <!-- Actions -->
        <Column header="Actions">
            <template #body="slotProps">
                <div class="flex gap-2 items-center">
                    <!-- View button -->
                    <Button raised rounded v-tooltip.top="'View'" type="button" icon="fa-solid fa-eye"
                        severity="primary" size="small" @click="handleView(slotProps.data.id)" />
                    <!-- Edit button -->
                    <Button raised rounded v-tooltip.top="'Edit'" type="button" icon="fa-solid fa-pencil"
                        severity="info" size="small" @click="handleEdit(slotProps.data)" />
                    <!-- Delete button -->
                    <Button raised rounded v-tooltip.top="'Delete'" type="button" icon="fa-solid fa-trash-can"
                        severity="danger" size="small" @click="handleDelete(slotProps.data)" />
                </div>
            </template>
        </Column>
        <!-- Empty -->
        <template #empty>
            No records found
        </template>
    </DataTable>
</template>
