<script setup>
import { ref } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import ConfirmModal from '@/Components/ConfirmModal.vue';

const props = defineProps({
    order: Object,
    availableProducts: Array,
    allowedTransitions: Array,
});

const itemForm = useForm({
    product_id: '',
    quantity: 1,
    unit_price: '',
});

function addItem() {
    itemForm.post(route('orders.add-item', props.order.id), {
        preserveScroll: true,
        onSuccess: () => itemForm.reset(),
    });
}

const showRemoveModal = ref(false);
const removeTarget = ref(null);

function confirmRemove(item) {
    removeTarget.value = item;
    showRemoveModal.value = true;
}

function executeRemove() {
    router.delete(route('orders.remove-item', { order: props.order.id, orderItem: removeTarget.value.id }), {
        preserveScroll: true,
        onFinish: () => {
            showRemoveModal.value = false;
            removeTarget.value = null;
        },
    });
}

function updateStatus(status) {
    router.patch(route('orders.update-status', props.order.id), { status }, {
        preserveScroll: true,
    });
}

function formatCurrency(value) {
    return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function formatDate(date) {
    if (!date) return '-';
    return new Date(date + 'T00:00:00').toLocaleDateString('pt-BR');
}

function formatDateTime(dateStr) {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleString('pt-BR');
}

const statusLabels = {
    open: 'Aberto',
    processing: 'Em Processamento',
    completed: 'Concluido',
    cancelled: 'Cancelado',
};
</script>

<template>
    <Head :title="`Pedido #${order.id}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <Link :href="route('orders.index')" class="text-gray-500 hover:text-gray-700">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </Link>
                    <h2 class="text-xl font-semibold leading-tight text-gray-800">
                        Pedido #{{ order.id }}
                    </h2>
                    <StatusBadge :status="order.status" type="order" />
                </div>
                <div v-if="allowedTransitions.length > 0" class="flex space-x-2">
                    <button
                        v-for="transition in allowedTransitions"
                        :key="transition.value"
                        @click="updateStatus(transition.value)"
                        class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-xs font-semibold uppercase tracking-widest hover:bg-gray-50 transition"
                        :class="{
                            'text-yellow-700 border-yellow-300 hover:bg-yellow-50': transition.value === 'processing',
                            'text-green-700 border-green-300 hover:bg-green-50': transition.value === 'completed',
                            'text-red-700 border-red-300 hover:bg-red-50': transition.value === 'cancelled',
                        }"
                    >
                        {{ transition.label }}
                    </button>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Order info -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Details card -->
                <div class="bg-white shadow-sm rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Detalhes do Pedido</h3>
                    <dl class="grid grid-cols-2 gap-4">
                        <div>
                            <dt class="text-sm text-gray-500">Fornecedor</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ order.supplier?.name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">CNPJ</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ order.supplier?.cnpj }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Data do Pedido</dt>
                            <dd class="text-sm font-medium text-gray-900">{{ formatDate(order.order_date) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm text-gray-500">Total</dt>
                            <dd class="text-lg font-bold text-gray-900">{{ formatCurrency(order.total) }}</dd>
                        </div>
                        <div v-if="order.notes" class="col-span-2">
                            <dt class="text-sm text-gray-500">Observacoes</dt>
                            <dd class="text-sm text-gray-900">{{ order.notes }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Items -->
                <div class="bg-white shadow-sm rounded-lg">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-medium text-gray-900">Itens do Pedido ({{ order.items?.length ?? 0 }})</h3>
                    </div>

                    <!-- Add item form -->
                    <div v-if="order.status === 'open' || order.status === 'processing'" class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <form @submit.prevent="addItem" class="flex flex-col sm:flex-row gap-3 items-end">
                            <div class="flex-1">
                                <SelectInput
                                    v-model="itemForm.product_id"
                                    label="Produto"
                                    :options="availableProducts.map(p => ({ value: p.id, label: `${p.name} (${p.internal_code})` }))"
                                    placeholder="Selecione..."
                                    :error="itemForm.errors.product_id"
                                />
                            </div>
                            <div class="w-24">
                                <TextInput v-model="itemForm.quantity" label="Qtd" type="number" :error="itemForm.errors.quantity" />
                            </div>
                            <div class="w-32">
                                <TextInput v-model="itemForm.unit_price" label="Preco (R$)" type="number" :error="itemForm.errors.unit_price" />
                            </div>
                            <button type="submit" :disabled="itemForm.processing" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase hover:bg-green-700 disabled:opacity-50 transition">
                                Adicionar
                            </button>
                        </form>
                    </div>

                    <!-- Items table -->
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Produto</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Codigo</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Qtd</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Preco Unit.</th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th v-if="order.status === 'open' || order.status === 'processing'" class="px-6 py-3 w-16"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr v-if="!order.items || order.items.length === 0">
                                <td :colspan="order.status === 'open' || order.status === 'processing' ? 6 : 5" class="px-6 py-8 text-center text-gray-500 text-sm">
                                    Nenhum item adicionado.
                                </td>
                            </tr>
                            <tr v-for="item in order.items" :key="item.id" class="hover:bg-gray-50">
                                <td class="px-6 py-3 text-sm text-gray-900">{{ item.product?.name }}</td>
                                <td class="px-6 py-3 text-sm text-gray-500 font-mono">{{ item.product?.internal_code }}</td>
                                <td class="px-6 py-3 text-sm text-gray-900 text-right">{{ item.quantity }}</td>
                                <td class="px-6 py-3 text-sm text-gray-900 text-right">{{ formatCurrency(item.unit_price) }}</td>
                                <td class="px-6 py-3 text-sm font-medium text-gray-900 text-right">{{ formatCurrency(item.total_price) }}</td>
                                <td v-if="order.status === 'open' || order.status === 'processing'" class="px-6 py-3 text-right">
                                    <button @click="confirmRemove(item)" class="text-red-600 hover:text-red-800 text-xs">Remover</button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot v-if="order.items && order.items.length > 0" class="bg-gray-50">
                            <tr>
                                <td :colspan="order.status === 'open' || order.status === 'processing' ? 4 : 3" class="px-6 py-3 text-right text-sm font-bold text-gray-900">Total:</td>
                                <td class="px-6 py-3 text-right text-sm font-bold text-gray-900">{{ formatCurrency(order.total) }}</td>
                                <td v-if="order.status === 'open' || order.status === 'processing'"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Status Timeline -->
            <div class="bg-white shadow-sm rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Historico de Status</h3>
                <div class="flow-root">
                    <ul class="-mb-8">
                        <li v-for="(history, index) in order.status_histories" :key="history.id" class="relative pb-8">
                            <span v-if="index < order.status_histories.length - 1" class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200"></span>
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white"
                                          :class="{
                                              'bg-blue-500': history.new_status === 'open',
                                              'bg-yellow-500': history.new_status === 'processing',
                                              'bg-green-500': history.new_status === 'completed',
                                              'bg-red-500': history.new_status === 'cancelled',
                                          }">
                                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6l4 2" />
                                        </svg>
                                    </span>
                                </div>
                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                    <div>
                                        <p class="text-sm text-gray-900">
                                            <span v-if="history.old_status">{{ statusLabels[history.old_status] ?? history.old_status }}</span>
                                            <span v-if="history.old_status"> &rarr; </span>
                                            <span class="font-medium">{{ statusLabels[history.new_status] ?? history.new_status }}</span>
                                        </p>
                                    </div>
                                    <div class="whitespace-nowrap text-right text-xs text-gray-500">
                                        {{ formatDateTime(history.changed_at) }}
                                    </div>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Remove item modal -->
        <ConfirmModal
            :show="showRemoveModal"
            title="Remover Item"
            :message="`Remover '${removeTarget?.product?.name}' do pedido?`"
            confirmLabel="Remover"
            @confirm="executeRemove"
            @cancel="showRemoveModal = false"
        />
    </AuthenticatedLayout>
</template>
