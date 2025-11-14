<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import VueApexCharts from "vue3-apexcharts";
import { useCharts } from '@/Composables/useCharts';
import { ref, watch } from 'vue';

const { chartData } = useCharts();

const props = defineProps({
    totals: {
        type: Object,
        required: true
    },
    expensesByCategory: {
        type: Array,
        required: true
    },
    incomeExpenseTrend: {
        type: Array,
        required: true
    },
    monthlyComparison: {
        type: Array,
        required: true
    },
    budgetProgress: {
        type: Array,
        required: true
    }
});

const apexchart = VueApexCharts;
const hasExpensesData = ref(false);

console.log('Totals: ', props.totals);
console.log('expensesByCategory: ', props.expensesByCategory);
console.log('incomeExpenseTrend: ', props.incomeExpenseTrend);
console.log('monthlyComparison: ', props.monthlyComparison);
console.log('budgetProgress: ', props.budgetProgress);

watch(() => props.expensesByCategory, (newVal) => {
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

watch(() => props.incomeExpenseTrend, (newVal) => {
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

watch(() => props.monthlyComparison, (newVal) => {
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

watch(() => props.budgetProgress, (newVal) => {
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
                    if (!hasData2) return '';
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
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="card flex justify-center">
                        <div>
                            <apexchart type="pie" width="380" :options="chartData.expensesByCategory.options"
                                :series="chartData.expensesByCategory.series">
                            </apexchart>
                            <p v-if="!hasExpensesData" class="mt-3 text-gray-500 text-sm italic">
                                No data available
                            </p>
                            <apexchart type="line" height="350" :options="chartData.incomeExpenseTrend.options"
                                :series="chartData.incomeExpenseTrend.series"></apexchart>
                            <apexchart type="bar" height="430" :options="chartData.monthlyComparison.options"
                                :series="chartData.monthlyComparison.series"></apexchart>
                            <apexchart type="radialBar" height="350" :options="chartData.budgetProgress.options"
                                :series="chartData.budgetProgress.series">
                            </apexchart>
                            <!-- <p v-if="!hasData" class="mt-3 text-gray-500 text-sm italic">
                                No data available
                            </p>  -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
