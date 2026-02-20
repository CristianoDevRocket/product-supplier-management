<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { useFilters } from '@/Composables/useFilters.js';

const props = defineProps({
    products: Object,
    filters: Object,
});

const { filters, applyFilters } = useFilters(
    { search: props.filters?.search ?? '', status: props.filters?.status ?? '', sort: props.filters?.sort ?? 'name', direction: props.filters?.direction ?? 'asc' },
    'products.index'
);

const columns = [
    { key: 'name', label: 'Nome', sortable: true },
    { key: 'internal_code', label: 'Codigo Interno', sortable: true },
    { key: 'description', label: 'Descricao', sortable: false },
    { key: 'status', label: 'Status', sortable: true },
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

const showDeleteModal = ref(false);
const deleteTarget = ref(null);

function confirmDelete(product) {
    deleteTarget.value = product;
    showDeleteModal.value = true;
}

function executeDelete() {
    router.delete(route('products.destroy', deleteTarget.value.id), {
        onFinish: () => {
            showDeleteModal.value = false;
            deleteTarget.value = null;
        },
    });
}
</script>

<template>
    <Head title="Produtos" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Produtos</h2>
                <Link :href="route('products.create')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    Novo Produto
                </Link>
            </div>
        </template>

        <!-- Filters -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <SearchInput :modelValue="filters.search" @update:modelValue="onSearch" placeholder="Buscar por nome, codigo ou descricao..." />
            </div>
            <div class="w-full sm:w-48">
                <select v-model="filters.status" @change="applyFilters()" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Todos os status</option>
                    <option value="active">Ativo</option>
                    <option value="inactive">Inativo</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <DataTable :columns="columns" :sort="filters.sort" :direction="filters.direction" @sort="onSort">
            <tr v-if="products.data.length === 0">
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                    Nenhum produto encontrado.
                </td>
            </tr>
            <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ product.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 font-mono">{{ product.internal_code }}</td>
                <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">{{ product.description || '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <StatusBadge :status="product.status" type="entity" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                    <Link :href="route('products.edit', product.id)" class="text-indigo-600 hover:text-indigo-900">Editar</Link>
                    <button @click="confirmDelete(product)" class="text-red-600 hover:text-red-900">Excluir</button>
                </td>
            </tr>
        </DataTable>

        <!-- Pagination -->
        <div class="mt-6">
            <Pagination :links="products.links" />
        </div>

        <!-- Delete confirmation modal -->
        <ConfirmModal
            :show="showDeleteModal"
            title="Excluir Produto"
            :message="`Tem certeza que deseja excluir o produto '${deleteTarget?.name}'?`"
            confirmLabel="Excluir"
            @confirm="executeDelete"
            @cancel="showDeleteModal = false"
        />
    </AuthenticatedLayout>
</template>
