<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DataTable from '@/Components/DataTable.vue';
import Pagination from '@/Components/Pagination.vue';
import SearchInput from '@/Components/SearchInput.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import SelectInput from '@/Components/SelectInput.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';
import { useFilters } from '@/Composables/useFilters.js';

const props = defineProps({
    suppliers: Object,
    filters: Object,
});

const { filters, applyFilters, resetFilters } = useFilters(
    { search: props.filters?.search ?? '', status: props.filters?.status ?? '', sort: props.filters?.sort ?? 'name', direction: props.filters?.direction ?? 'asc' },
    'suppliers.index'
);

const columns = [
    { key: 'name', label: 'Nome', sortable: true },
    { key: 'cnpj', label: 'CNPJ', sortable: false },
    { key: 'email', label: 'E-mail', sortable: true },
    { key: 'phone', label: 'Telefone', sortable: false },
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

function confirmDelete(supplier) {
    deleteTarget.value = supplier;
    showDeleteModal.value = true;
}

function executeDelete() {
    router.delete(route('suppliers.destroy', deleteTarget.value.id), {
        onFinish: () => {
            showDeleteModal.value = false;
            deleteTarget.value = null;
        },
    });
}
</script>

<template>
    <Head title="Fornecedores" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Fornecedores</h2>
                <Link :href="route('suppliers.create')" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                    Novo Fornecedor
                </Link>
            </div>
        </template>

        <!-- Filters -->
        <div class="mb-6 flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <SearchInput :modelValue="filters.search" @update:modelValue="onSearch" placeholder="Buscar por nome, CNPJ ou e-mail..." />
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
            <tr v-if="suppliers.data.length === 0">
                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                    Nenhum fornecedor encontrado.
                </td>
            </tr>
            <tr v-for="supplier in suppliers.data" :key="supplier.id" class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ supplier.name }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ supplier.cnpj }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ supplier.email }}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ supplier.phone }}</td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <StatusBadge :status="supplier.status" type="entity" />
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-right space-x-2">
                    <Link :href="route('suppliers.edit', supplier.id)" class="text-indigo-600 hover:text-indigo-900">Editar</Link>
                    <button @click="confirmDelete(supplier)" class="text-red-600 hover:text-red-900">Excluir</button>
                </td>
            </tr>
        </DataTable>

        <!-- Pagination -->
        <div class="mt-6">
            <Pagination :links="suppliers.links" />
        </div>

        <!-- Delete confirmation modal -->
        <ConfirmModal
            :show="showDeleteModal"
            title="Excluir Fornecedor"
            :message="`Tem certeza que deseja excluir o fornecedor '${deleteTarget?.name}'?`"
            confirmLabel="Excluir"
            @confirm="executeDelete"
            @cancel="showDeleteModal = false"
        />
    </AuthenticatedLayout>
</template>
