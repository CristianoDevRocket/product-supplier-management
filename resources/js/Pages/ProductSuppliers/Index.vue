<script setup>
import { ref, computed } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';

const props = defineProps({
    suppliers: Array,
    selectedSupplierId: Number,
    linkedProducts: Array,
    availableProducts: Array,
});

const selectedSupplier = ref(props.selectedSupplierId ?? '');
const selectedLinked = ref([]);
const selectedAvailable = ref([]);

function onSupplierChange() {
    selectedLinked.value = [];
    selectedAvailable.value = [];
    router.get(route('product-suppliers.index'), { supplier_id: selectedSupplier.value || undefined }, {
        preserveState: false,
    });
}

function linkProduct(productId) {
    router.post(route('product-suppliers.store'), {
        product_id: productId,
        supplier_id: selectedSupplier.value,
    });
}

function unlinkProduct(productId) {
    router.delete(route('product-suppliers.destroy', { product: productId, supplier: selectedSupplier.value }));
}

function bulkLink() {
    if (selectedAvailable.value.length === 0) return;
    router.post(route('product-suppliers.bulk-link'), {
        product_ids: selectedAvailable.value,
        supplier_ids: [selectedSupplier.value],
    });
}

function bulkUnlink() {
    if (selectedLinked.value.length === 0) return;
    router.post(route('product-suppliers.bulk-unlink'), {
        product_ids: selectedLinked.value,
        supplier_ids: [selectedSupplier.value],
    });
}

function toggleLinked(id) {
    const idx = selectedLinked.value.indexOf(id);
    if (idx === -1) selectedLinked.value.push(id);
    else selectedLinked.value.splice(idx, 1);
}

function toggleAvailable(id) {
    const idx = selectedAvailable.value.indexOf(id);
    if (idx === -1) selectedAvailable.value.push(id);
    else selectedAvailable.value.splice(idx, 1);
}

function selectAllLinked() {
    if (selectedLinked.value.length === props.linkedProducts.length) {
        selectedLinked.value = [];
    } else {
        selectedLinked.value = props.linkedProducts.map(p => p.id);
    }
}

function selectAllAvailable() {
    if (selectedAvailable.value.length === props.availableProducts.length) {
        selectedAvailable.value = [];
    } else {
        selectedAvailable.value = props.availableProducts.map(p => p.id);
    }
}
</script>

<template>
    <Head title="Vinculos Produto-Fornecedor" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Vinculos Produto-Fornecedor</h2>
        </template>

        <!-- Supplier selector -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-1">Selecione um Fornecedor</label>
            <select v-model="selectedSupplier" @change="onSupplierChange" class="block w-full sm:w-96 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Selecione...</option>
                <option v-for="s in suppliers" :key="s.id" :value="s.id">{{ s.name }} ({{ s.cnpj }})</option>
            </select>
        </div>

        <!-- Dual panel -->
        <div v-if="selectedSupplier" class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Linked products -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">
                        Produtos Vinculados ({{ linkedProducts.length }})
                    </h3>
                    <button
                        v-if="selectedLinked.length > 0"
                        @click="bulkUnlink"
                        class="inline-flex items-center px-3 py-1 bg-red-600 text-white text-xs font-semibold rounded hover:bg-red-700 transition"
                    >
                        Desvincular ({{ selectedLinked.length }})
                    </button>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <div v-if="linkedProducts.length === 0" class="px-4 py-8 text-center text-gray-500 text-sm">
                        Nenhum produto vinculado.
                    </div>
                    <table v-else class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 w-10">
                                    <input type="checkbox" @change="selectAllLinked" :checked="selectedLinked.length === linkedProducts.length && linkedProducts.length > 0" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Codigo</th>
                                <th class="px-4 py-2 w-20"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="product in linkedProducts" :key="product.id" class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <input type="checkbox" :checked="selectedLinked.includes(product.id)" @change="toggleLinked(product.id)" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ product.name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500 font-mono">{{ product.internal_code }}</td>
                                <td class="px-4 py-2 text-right">
                                    <button @click="unlinkProduct(product.id)" class="text-red-600 hover:text-red-800 text-xs font-medium">
                                        Remover
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Available products -->
            <div class="bg-white shadow-sm rounded-lg">
                <div class="px-4 py-3 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-sm font-semibold text-gray-700">
                        Produtos Disponiveis ({{ availableProducts.length }})
                    </h3>
                    <button
                        v-if="selectedAvailable.length > 0"
                        @click="bulkLink"
                        class="inline-flex items-center px-3 py-1 bg-green-600 text-white text-xs font-semibold rounded hover:bg-green-700 transition"
                    >
                        Vincular ({{ selectedAvailable.length }})
                    </button>
                </div>
                <div class="max-h-96 overflow-y-auto">
                    <div v-if="availableProducts.length === 0" class="px-4 py-8 text-center text-gray-500 text-sm">
                        Nenhum produto disponivel.
                    </div>
                    <table v-else class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 w-10">
                                    <input type="checkbox" @change="selectAllAvailable" :checked="selectedAvailable.length === availableProducts.length && availableProducts.length > 0" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                </th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Codigo</th>
                                <th class="px-4 py-2 w-20"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="product in availableProducts" :key="product.id" class="hover:bg-gray-50">
                                <td class="px-4 py-2">
                                    <input type="checkbox" :checked="selectedAvailable.includes(product.id)" @change="toggleAvailable(product.id)" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500" />
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900">{{ product.name }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500 font-mono">{{ product.internal_code }}</td>
                                <td class="px-4 py-2 text-right">
                                    <button @click="linkProduct(product.id)" class="text-green-600 hover:text-green-800 text-xs font-medium">
                                        Vincular
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div v-else class="bg-white shadow-sm rounded-lg p-8 text-center text-gray-500">
            Selecione um fornecedor para gerenciar os vinculos de produtos.
        </div>
    </AuthenticatedLayout>
</template>
