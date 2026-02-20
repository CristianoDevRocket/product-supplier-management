<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { useFilters } from '@/Composables/useFilters.js';

const props = defineProps({
    orders: Object,
    filters: Object,
    suppliers: Array,
    statuses: Array,
});

const { filters, applyFilters } = useFilters(
    {
        search: props.filters?.search ?? '',
        status: props.filters?.status ?? '',
        supplier_id: props.filters?.supplier_id ?? '',
        sort: props.filters?.sort ?? 'created_at',
        direction: props.filters?.direction ?? 'desc',
    },
    'orders.index'
);

const columns = [
    { key: 'id', label: '#', sortable: true },
    { key: 'supplier', label: 'Fornecedor', sortable: false },
    { key: 'order_date', label: 'Data', sortable: true },
    { key: 'status', label: 'Status', sortable: true },
    { key: 'total', label: 'Total', sortable: true },
    { key: 'actions', label: 'Acoes', sortable: false },
];

function onSort({ sort, direction }) {
    filters.sort = sort;
    filters.direction = direction;
    applyFilters();
}

function onSearch(value) {
    filters.search = value;
    applyFilters();
}

function formatCurrency(value) {
    return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function formatDate(date) {
    if (!date) return '-';
    return new Date(date + 'T00:00:00').toLocaleDateString('pt-BR');
}
</script>

<template>
    <Head title="Pedidos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Pedidos</h2>
                <Link :href="route('orders.create')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    Novo Pedido
                </Link>
            </div>
        </template>

        <!-- Filters -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <SearchInput :modelValue="filters.search" @update:modelValue="onSearch" placeholder="Buscar por # ou fornecedor..." />
            </div>
            <div class="w-full sm:w-48">
                <select v-model="filters.status" @change="applyFilters()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Todos os status</option>
                    <option v-for="s in statuses" :key="s.value" :value="s.value">{{ s.label }}</option>
                </select>
            </div>
            <div class="w-full sm:w-48">
                <select v-model="filters.supplier_id" @change="applyFilters()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Todos fornecedores</option>
                    <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }}</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <DataTable :columns="columns" :sort="filters.sort" :direction="filters.direction" @sort="onSort">
            <tr v-if="orders.data.length === 0">
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    Nenhum pedido encontrado.
                </td>
            </tr>
            <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ order.id }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ order.supplier?.name ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(order.order_date) }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <StatusBadge :status="order.status" type="order" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">{{ formatCurrency(order.total) }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right">
                    <Link :href="route('orders.show', order.id)" class="text-indigo-600 hover:text-indigo-900">Ver</Link>
                </td>
            </tr>
        </DataTable>

        <!-- Pagination -->
        <div class="mt-6">
            <Pagination :links="orders.links" />
        </div>
    </AuthenticatedLayout>
</template>
