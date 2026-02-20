import { reactive, watch } from 'vue';
import { router } from '@inertiajs/vue3';

export function useFilters(initialFilters, routeName) {
    const filters = reactive({ ...initialFilters });

    function applyFilters() {
        const params = {};
        for (const [key, value] of Object.entries(filters)) {
            if (value !== '' && value !== null && value !== undefined) {
                params[key] = value;
            }
        }
        router.get(route(routeName), params, {
            preserveState: true,
            preserveScroll: true,
        });
    }

    function resetFilters() {
        for (const key of Object.keys(filters)) {
            filters[key] = '';
        }
        applyFilters();
    }

    return { filters, applyFilters, resetFilters };
}
