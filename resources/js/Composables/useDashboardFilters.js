// Dashboard filters composable for managing filter state and query parameters

import { reactive } from 'vue';

export default function useDashboardFilters() {
    // const DEFAULT_FILTERS = {
    //     year: new Date().getFullYear(),
    //     month: null,
    // };

    // const stored = localStorage.getItem('dashboardFilters');
    // const filters = ref(stored ? JSON.parse(stored) : DEFAULT_FILTERS);

    // watch(filters, (newVal) => {
    //     localStorage.setItem('dashboardFilters', JSON.stringify(newVal));
    // }, { deep: true });

    const filters = reactive({
        year: null,
        month: null,
    })

    const setFilter = (key, value) => {
        filters.value[key] = value;
    };

    const getQueryParams = () => {
        // const params = {};

        // if (filters.value.year) params.year = filters.value.year;
        // if (filters.value.month) params.month = filters.value.month;

        // return params;

        return {
            params: { ...filters }
        };
    }

    // const resetFilters = () => {
    //     filters.value = DEFAULT_FILTERS;
    //     localStorage.setItem('dashboardFilters', JSON.stringify(DEFAULT_FILTERS));
    // }

    return {
        filters,
        setFilter,
        getQueryParams,
        // resetFilters,
    }
}
