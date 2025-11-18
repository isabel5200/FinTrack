<script setup>
import { Head } from '@inertiajs/vue3';
import { useCharts } from '@/Composables/useCharts';
import { onMounted, ref, watch } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from 'primevue/card';
import InputLabel from '@/Components/InputLabel.vue';
import Select from 'primevue/select';
import Skeleton from 'primevue/skeleton';
import useDashboardFilters from '@/Composables/useDashboardFilters';
import VueApexCharts from "vue3-apexcharts";

const { chartData } = useCharts();
const apexchart = VueApexCharts;
const { filters, setFilter, getQueryParams } = useDashboardFilters();

const totals = ref([]);
const expensesByCategory = ref([]);
const incomeExpenseTrend = ref([]);
const monthlyComparison = ref([]);
const budgetProgress = ref([]);
const hasExpensesData = ref(false);
const years = ref([]);
const months = ref([]);
const isLoadingCharts = ref(false);

const getDashboardData = async () => {
    try {
        isLoadingCharts.value = true;

        const response = await axios.get(route('dashboard.filter'), getQueryParams());

        totals.value = response.data.totals;
        expensesByCategory.value = response.data.expensesByCategory;
        incomeExpenseTrend.value = response.data.incomeExpenseTrend;
        monthlyComparison.value = response.data.monthlyComparison;
        budgetProgress.value = response.data.budgetProgress;
    } catch (error) {
        toast.add({
            severity: 'error',
            summary: 'Error',
            detail: `${error.response?.data?.message ?? 'An error occurred'}`,
            life: 3000,
        });

        console.error(error);
    } finally {
        isLoadingCharts.value = false;
    }
};

const getYears = async () => {
    try {
        const response = await axios.get(route('dashboard.years'));

        years.value = response.data.years;
        filters.year = response.data.defaultYear;
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

const getMonths = async () => {
    try {
        const response = await axios.get(route('dashboard.months'), {
            params: { year: filters.year }
        });

        months.value = [
            { id: "all", name: "All" },
            ...response.data.months,
        ]
        filters.month = "all";
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

onMounted(async () => {
    await getYears();
    await getMonths();
    await getDashboardData();
});

watch(() => filters.year, async () => {
    await getMonths();
    await getDashboardData();

    console.log('Entrando al watcher de años');
});

watch(() => filters.month, () => {
    getDashboardData();
});

// watch(filters, async () => {
//     const response = await axios.get(route("dashboard.filter"), { params: getQueryParams() });

//     totals.value = response.data.totals;
//     expensesByCategory.value = response.data.expensesByCategory;
//     incomeExpenseTrend.value = response.data.incomeExpenseTrend;
//     monthlyComparison.value = response.data.monthlyComparison;
//     budgetProgress.value = response.data.budgetProgress;
// }, { deep: true });

watch(() => expensesByCategory.value, (newVal) => {
    const data = Array.isArray(newVal) ? newVal : [];
    const series = data.map(item => parseFloat(item.percentage));
    const labels = data.map(item => item.category_name);
    const hasData = series.length > 0 && series.some(val => val > 0);

    hasExpensesData.value = hasData;

    chartData.value.expensesByCategory.series = hasData ? series : [100];
    chartData.value.expensesByCategory.options = {
        ...chartData.value.expensesByCategory.options,
        labels: hasData ? labels : ['No data'],
        colors: hasData ? undefined : ['#e0e0e0'],
        tooltip: { enabled: hasData },
        legend: { show: hasData },
        dataLabels: { enabled: hasData },
        plotOptions: { pie: { expandOnClick: hasData } },
    };
}, { immediate: true });

watch(() => incomeExpenseTrend.value, (newVal) => {
    const data = Array.isArray(newVal) ? newVal : [];
    const expenses = new Array(12).fill(0);
    const incomes = new Array(12).fill(0);

    data.forEach(item => {
        const idx = item.month - 1;

        if (idx >= 0 && idx < 12) {
            expenses[idx] = parseFloat(item.total_expense);
            incomes[idx] = parseFloat(item.total_income);
        }
    });

    chartData.value.incomeExpenseTrend.series = [
        { name: 'Total expense', data: expenses },
        { name: 'Total income', data: incomes },
    ];
}, { immediate: true });

watch(() => monthlyComparison.value, (newVal) => {
    const data = Array.isArray(newVal) ? newVal : [];
    const expenses = new Array(12).fill(0);
    const incomes = new Array(12).fill(0);

    data.forEach(item => {
        const idx = item.month - 1;

        if (idx >= 0 && idx < 12) {
            expenses[idx] = parseFloat(item.total_expense);
            incomes[idx] = parseFloat(item.total_income);
        }
    });

    chartData.value.monthlyComparison.series = [
        { name: 'Total expense', data: expenses },
        { name: 'Total income', data: incomes },
    ];
}, { immediate: true });

watch(() => budgetProgress.value, (newVal) => {
    const data = Array.isArray(newVal) ? newVal : [];
    const categories = data.map(item => item.category_name);
    const percents = data.map(item => parseFloat(item.percent_used));
    const totals = data.map(item => parseFloat(item.total_expense));
    const maxAmounts = data.map(item => parseFloat(item.budget_amount));
    const hasData = percents.length > 0 && percents.some(val => val > 0);

    chartData.value.budgetProgress.series = hasData ? percents : [100];
    chartData.value.budgetProgress.options = {
        ...chartData.value.budgetProgress.options,
        labels: hasData ? categories : ['No data'],
        colors: hasData ? undefined : ['#e0e0e0'],
        tooltip: {
            enabled: hasData,
            theme: 'dark',
            y: {
                formatter: function (val, { seriesIndex }) {
                    if (!hasData) return '';
                    const used = totals[seriesIndex]?.toLocaleString() || 0;
                    const max = maxAmounts[seriesIndex]?.toLocaleString() || 0;
                    return `${val}% used (${used} of ${max})`;
                }
            },
        },
        plotOptions: {
            radialBar: {
                dataLabels: {
                    name: {
                        fontSize: '14px',
                    },
                    value: {
                        fontSize: '14px',
                        formatter: val => hasData ? `${val}%` : ''
                    },
                    total: {
                        show: true,
                        label: hasData ? 'Average' : 'No data',
                        formatter: function () {
                            if (!hasData) return '';
                            const total = percents.reduce((acc, val) => acc + val, 0);
                            return (total / percents.length).toFixed(1) + "%";
                        },
                    },
                },
            },
        },
    };
}, { immediate: true });

// onMounted(() => {
//     const urlParams = new URLSearchParams(window.location.search);

//     if (urlParams.get("year")) {
//         setFilter("year", Number(urlParams.get("year")));
//     }

//     if (urlParams.get("month")) {
//         setFilter("month", Number(urlParams.get("month")));
//     }
// });
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Dashboard
            </h2>
        </template>
        <!-- Body -->
        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <Card>
                        <!-- Title -->
                        <template #title>Dashboard</template>
                        <!-- Content -->
                        <template #content>
                            <!-- Filters -->

                            <!-- Years -->
                            <InputLabel for="year" value="year" />
                            <Select id="year" v-model="filters.year" :options="years" optionValue="id"
                                optionLabel="name" placeholder="Select a year" class="mt-1 block w-full"
                                appendTo="self" />
                            <!-- Months -->
                            <InputLabel for="month" value="month" />
                            <Select id="month" v-model="filters.month" :options="months" optionValue="id"
                                optionLabel="name" placeholder="Select a month" class="mt-1 block w-full"
                                appendTo="self" />
                            <!-- Skeletons -->
                            <div v-if="isLoadingCharts" class="mt-6 space-y-6">
                                <!-- Pie chart skeleton -->
                                <div class="flex justify-center">
                                    <Skeleton shape="circle" width="200px" height="200px" />
                                </div>
                                <!-- Line chart skeleton -->
                                <Skeleton width="100%" height="250px" borderRadius="20px" />
                                <!-- Bar chart skeleton -->
                                <Skeleton width="100%" height="300px" borderRadius="20px" />
                                <!-- Radial chart skeleton -->
                                <div class="flex justify-center">
                                    <Skeleton shape="circle" width="180px" height="180px" />
                                </div>
                            </div>
                            <!-- Charts -->
                            <div v-else>
                                <!-- Pie chart -->
                                <apexchart type="pie" width="380" :options="chartData.expensesByCategory.options"
                                    :series="chartData.expensesByCategory.series" />
                                <!-- Legend if there is no data -->
                                <p v-if="!hasExpensesData" class="mt-3 text-gray-500 text-sm italic">
                                    No data available
                                </p>
                                <!-- Line chart -->
                                <apexchart type="line" height="350" :options="chartData.incomeExpenseTrend.options"
                                    :series="chartData.incomeExpenseTrend.series" />
                                <!-- Bar chart -->
                                <apexchart type="bar" height="430" :options="chartData.monthlyComparison.options"
                                    :series="chartData.monthlyComparison.series" />
                                <!-- Radial chart -->
                                <apexchart type="radialBar" height="350" :options="chartData.budgetProgress.options"
                                    :series="chartData.budgetProgress.series" />
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
