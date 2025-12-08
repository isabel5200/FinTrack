import { ref } from 'vue';

export function useCharts() {
    const chartData = ref({
        expensesByCategory: {
            series: [],
            options: {
                chart: {
                    type: 'pie',
                    background: 'transparent'
                },
                labels: [],
                colors: ['#e0e0e0'],
                legend: { show: false, position: 'bottom' },
                tooltip: { enabled: false },
                dataLabels: { enabled: false },
                plotOptions: { pie: { expandOnClick: false } },
                theme: { mode: 'light' },
            },
        },
        incomeExpenseTrend: {
            series: [
                { name: 'Total expense', data: [] },
                { name: 'Total income', data: [] },
            ],
            options: {
                chart: {
                    type: 'line',
                    zoom: { enabled: false }
                },
                dataLabels: { enabled: false },
                stroke: { curve: 'straight' },
                title: {
                    text: 'Expenses & incomes by Month',
                    align: 'left'
                },
                grid: {
                    row: {
                        colors: ['#f3f3f3', 'transparent'],
                        opacity: 0.5
                    },
                },
                xaxis: {
                    categories: [
                        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                    ],
                },
                theme: { mode: 'light' },
            },
        },
        monthlyComparison: {
            series: [
                { name: 'Total expense', data: [] },
                { name: 'Total income', data: [] },
            ],
            options: {
                chart: { type: 'bar' },
                plotOptions: {
                    bar: {
                        horizontal: true,
                        dataLabels: { position: 'top' }
                    },
                },
                dataLabels: {
                    enabled: true,
                    offsetX: -12,
                    style: { fontSize: '12px', colors: ['#fff'] },
                },
                stroke: {
                    show: true,
                    width: 1,
                    colors: ['#fff']
                },
                xaxis: {
                    categories: [
                        'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                        'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'
                    ],
                },
                theme: { mode: 'light' },
            },
        },
        budgetProgress: {
            series: [100],
            options: {
                chart: {
                    type: 'radialBar',
                    background: 'transparent'
                },
                plotOptions: {
                    radialBar: {
                        dataLabels: {
                            name: { fontSize: '14px' },
                            value: { fontSize: '14px' },
                            total: {
                                show: true,
                                label: 'No data',
                                formatter: () => '',
                            },
                        },
                    },
                },
                labels: ['No data'],
                colors: ['#e0e0e0'],
                tooltip: { enabled: false },
                theme: { mode: 'light' },
            },
        },
    });

    return { chartData };
};
