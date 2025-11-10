<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import VueApexCharts from "vue3-apexcharts";

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

const expenses = Array.isArray(props.expensesByCategory)
    ? props.expensesByCategory
    : Object.values(props.expensesByCategory);

const series = expenses.map(item => parseFloat(item.percentage));
const labels = expenses.map(item => item.category_name);
const hasData = series.length > 0 && series.some(val => val > 0);

const data = {
    series: hasData ? series : [100], // Pie necesita algo mayor a 0
    chartOptions: {
        chart: {
            width: 380,
            type: 'pie',
            background: 'transparent',
        },
        labels: hasData ? labels : ['Sin datos'],
        colors: hasData ? undefined : ['#e0e0e0'],
        legend: {
            show: hasData,
            position: 'bottom',
        },
        tooltip: {
            enabled: hasData,
        },
        dataLabels: {
            enabled: hasData,
        },
        responsive: [{
            breakpoint: 480,
            options: {
                chart: { width: 200 },
                legend: { position: 'bottom' }
            }
        }],
        plotOptions: {
            pie: {
                expandOnClick: hasData,
            },
        },
    },
};

const trend = Array.isArray(props.incomeExpenseTrend)
    ? props.incomeExpenseTrend
    : Object.values(props.incomeExpenseTrend);

const expenses1 = new Array(12).fill(0);
const incomes = new Array(12).fill(0);

trend.forEach(item => {
    const idx = item.month - 1;

    if (idx >= 0 && idx < 12) {
        expenses1[idx] = parseFloat(item.total_expense);
        incomes[idx] = parseFloat(item.total_income);
    }
});

const incomeExpenseTrend = {
    series: [
        {
            name: "Total expense",
            data: expenses1
        },
        {
            name: "Total income",
            data: incomes
        }
    ],
    chartOptions: {
        chart: {
            height: 350,
            type: 'line',
            zoom: {
                enabled: false
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight'
        },
        title: {
            text: 'Expenses & incomes by Month',
            align: 'left'
        },
        grid: {
            row: {
                colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
                opacity: 0.5
            },
        },
        xaxis: {
            categories: [
                "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
            ],
        }
    },
};

const comparison = Array.isArray(props.monthlyComparison)
    ? props.monthlyComparison
    : Object.values(props.monthlyComparison);


const expenses2 = new Array(12).fill(0);
const incomes2 = new Array(12).fill(0);

comparison.forEach(item => {
    const idx = item.month - 1;

    if (idx >= 0 && idx < 12) {
        expenses2[idx] = parseFloat(item.total_expense);
        incomes2[idx] = parseFloat(item.total_income);
    }
});

const monthlyComparison = {
    series: [
        {
            name: "Total expense",
            data: expenses2
        },
        {
            name: "Total income",
            data: incomes2
        }
    ],
    chartOptions: {
        chart: {
            type: 'bar',
            height: 430
        },
        plotOptions: {
            bar: {
                horizontal: true,
                dataLabels: {
                    position: 'top',
                },
            }
        },
        dataLabels: {
            enabled: true,
            offsetX: -12,
            style: {
                fontSize: '12px',
                colors: ['#fff']
            }
        },
        stroke: {
            show: true,
            width: 1,
            colors: ['#fff']
        },
        tooltip: {
            shared: true,
            intersect: false
        },
        xaxis: {
            categories: [
                "Jan", "Feb", "Mar", "Apr", "May", "Jun",
                "Jul", "Aug", "Sep", "Oct", "Nov", "Dec",
            ],
        },
    },
};

const progress = Array.isArray(props.budgetProgress)
    ? props.budgetProgress
    : Object.values(props.monthlyComparison);

const categories = progress.map(item => item.category_name);
const percents = progress.map(item => parseFloat(item.percent_used));
const totals = progress.map(item => parseFloat(item.total_expense));
const maxAmounts = progress.map(item => parseFloat(item.budget_amount));

const hasData2 = percents.length > 0 && percents.some(val => val > 0);

const budgetProgress = {
    series: hasData2 ? percents : [100],
    chartOptions: {
        chart: {
            height: 350,
            type: 'radialBar',
            background: 'transparent',
        },
        plotOptions: {
            radialBar: {
                dataLabels: {
                    name: {
                        fontSize: '14px',
                    },
                    value: {
                        fontSize: '14px',
                        formatter: val => hasData2 ? `${val}%` : ''
                    },
                    total: {
                        show: true,
                        label: hasData2 ? 'Promedio' : 'Sin datos',
                        formatter: function () {
                            if (!hasData2) return '';
                            const total = percents.reduce((acc, val) => acc + val, 0);
                            return (total / percents.length).toFixed(1) + "%";
                        }
                    }
                }
            }
        },
        labels: hasData2 ? categories : ['Sin datos'],
        colors: hasData2 ? undefined : ['#e0e0e0'],
        legend: {
            show: hasData2,
            position: 'bottom',
        },
        tooltip: {
            enabled: hasData2,
            theme: 'dark',
            y: {
                formatter: function (val, { seriesIndex }) {
                    if (!hasData2) return '';
                    const used = totals[seriesIndex]?.toLocaleString() || 0;
                    const max = maxAmounts[seriesIndex]?.toLocaleString() || 0;
                    return `${val}% usado (${used} de ${max})`;
                }
            }
        },
    },
};

console.log('Totals: ', props.totals);
console.log('expensesByCategory: ', props.expensesByCategory);
console.log('incomeExpenseTrend: ', props.incomeExpenseTrend);
console.log('monthlyComparison: ', props.monthlyComparison);
console.log('budgetProgress: ', props.budgetProgress);
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
                            <apexchart type="pie" width="380" :options="data.chartOptions" :series="data.series">
                            </apexchart>
                            <p v-if="!hasData" class="mt-3 text-gray-500 text-sm italic">
                                No hay gastos registrados aún 😅
                            </p>
                            <apexchart type="line" height="350" :options="incomeExpenseTrend.chartOptions"
                                :series="incomeExpenseTrend.series"></apexchart>
                            <apexchart type="bar" height="430" :options="monthlyComparison.chartOptions"
                                :series="monthlyComparison.series"></apexchart>
                            <apexchart type="radialBar" height="350" :options="budgetProgress.chartOptions"
                                :series="budgetProgress.series">
                            </apexchart>
                            <p v-if="!hasData" class="mt-3 text-gray-500 text-sm italic">
                                No hay presupuestos registrados aún 😅
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
