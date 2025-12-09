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
import useTheme from '@/Composables/useTheme';
import VueApexCharts from "vue3-apexcharts";

const { isDark } = useTheme();
const { chartData } = useCharts();
const apexchart = VueApexCharts;
const { filters, getQueryParams } = useDashboardFilters();

const totals = ref({
    income: 0,
    expense: 0,
    balance: 0,
});
const expensesByCategory = ref([]);
const incomeExpenseTrend = ref([]);
const monthlyComparison = ref([]);
const budgetProgress = ref([]);
const hasExpensesData = ref(false);
const years = ref([]);
const months = ref([]);
const initializing = ref(true);
const isLoadingCharts = ref(false);
const isLoadingYears = ref(false);
const isLoadingMonths = ref(false);

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
    isLoadingYears.value = true;

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
    } finally {
        isLoadingYears.value = false;
    }
};

const getMonths = async () => {
    isLoadingMonths.value = true;

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
    finally {
        isLoadingMonths.value = false;
    }
};

onMounted(async () => {
    initializing.value = true;

    await getYears();
    await getMonths();

    initializing.value = false;

    await getDashboardData();
});

watch(() => isDark.value, () => {
    expensesByCategory.value = [...expensesByCategory.value];
    incomeExpenseTrend.value = [...incomeExpenseTrend.value];
    monthlyComparison.value = [...monthlyComparison.value];
    budgetProgress.value = [...budgetProgress.value];
});

watch(() => filters.year, async () => {
    if (initializing.value) return;

    await getMonths();
    await getDashboardData();
});

watch(() => filters.month, () => {
    if (initializing.value) return;

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
        theme: { mode: isDark.value ? 'dark' : 'light' },
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
    chartData.value.incomeExpenseTrend.options = {
        ...chartData.value.incomeExpenseTrend.options,
        theme: { mode: isDark.value ? 'dark' : 'light' },
    };
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
    chartData.value.monthlyComparison.options = {
        ...chartData.value.monthlyComparison.options,
        theme: { mode: isDark.value ? 'dark' : 'light' },
    };
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
        theme: { mode: isDark.value ? 'dark' : 'light' },
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
        <div class="py-5">
            <div class="mx-auto sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <Card>
                        <!-- Title -->
                        <template #title>Dashboard</template>
                        <!-- Content -->
                        <template #content>
                            <!-- Filters -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Year -->
                                <div>
                                    <InputLabel for="year" value="Year" />
                                    <Select id="year" v-model="filters.year" :options="years" :loading="isLoadingYears"
                                        :disabled="isLoadingYears" optionValue="id" optionLabel="name"
                                        placeholder="Select a year" class="mt-1 block w-full" appendTo="self" />
                                </div>
                                <!-- Month -->
                                <div>
                                    <InputLabel for="month" value="Month" />
                                    <Select id="month" v-model="filters.month" :options="months"
                                        :loading="isLoadingMonths" :disabled="isLoadingMonths" optionValue="id"
                                        optionLabel="name" placeholder="Select a month" class="mt-1 block w-full"
                                        appendTo="self" />
                                </div>
                            </div>
                            <!-- Cards -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                                <!-- Income -->
                                <div
                                    class="p-4 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Income</p>
                                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                                        ${{ totals.income.toLocaleString() }}
                                    </p>
                                </div>
                                <!-- Expense -->
                                <div
                                    class="p-4 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Expense</p>
                                    <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">
                                        ${{ totals.expense.toLocaleString() }}
                                    </p>
                                </div>
                                <!-- Balance -->
                                <div
                                    class="p-4 rounded-xl shadow-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Balance</p>
                                    <p class="text-2xl font-bold mt-1"
                                        :class="totals.balance >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400'">
                                        ${{ totals.balance.toLocaleString() }}
                                    </p>
                                </div>
                            </div>
                            <!-- Skeletons -->
                            <div v-if="isLoadingCharts" class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
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
                            <div v-else class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                                <!-- Pie chart -->
                                <div
                                    class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm">
                                    <h3 class="text-lg font-semibold mb-3">Expenses by Category</h3>

                                    <apexchart type="pie" :options="chartData.expensesByCategory.options"
                                        :series="chartData.expensesByCategory.series" />

                                    <p v-if="!hasExpensesData" class="mt-3 text-gray-500 text-sm italic text-center">
                                        No data available
                                    </p>
                                </div>
                                <!-- Line chart -->
                                <div
                                    class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm">
                                    <h3 class="text-lg font-semibold mb-3">Income vs Expenses Trend</h3>

                                    <apexchart type="line" :options="chartData.incomeExpenseTrend.options"
                                        :series="chartData.incomeExpenseTrend.series" />
                                </div>
                                <!-- Bar chart -->
                                <div
                                    class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm">
                                    <h3 class="text-lg font-semibold mb-3">Monthly Comparison</h3>

                                    <apexchart type="bar" :options="chartData.monthlyComparison.options"
                                        :series="chartData.monthlyComparison.series" />
                                </div>
                                <!-- Radial chart -->
                                <div
                                    class="p-4 rounded-xl bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 shadow-sm">
                                    <h3 class="text-lg font-semibold mb-3">Budget Progress</h3>

                                    <apexchart type="radialBar" :options="chartData.budgetProgress.options"
                                        :series="chartData.budgetProgress.series" />
                                </div>
                            </div>
                        </template>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
