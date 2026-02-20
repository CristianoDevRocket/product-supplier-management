<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import StatusBadge from '@/Components/StatusBadge.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: {
        type: Object,
        default: () => ({
            suppliers: 0, products: 0, orders: 0, openOrders: 0, processingOrders: 0, totalRevenue: 0,
        }),
    },
    recentOrders: { type: Array, default: () => [] },
});

function formatCurrency(value) {
    return parseFloat(value || 0).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
}

function formatDate(date) {
    if (!date) return '-';
    return new Date(date + 'T00:00:00').toLocaleDateString('pt-BR');
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Dashboard</h2>
        </template>

        <!-- Stats cards -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6">
            <Link :href="route('suppliers.index')" class="block bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-indigo-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Fornecedores</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.suppliers }}</p>
                        </div>
                    </div>
                </div>
            </Link>

            <Link :href="route('products.index')" class="block bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Produtos</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.products }}</p>
                        </div>
                    </div>
                </div>
            </Link>

            <Link :href="route('orders.index')" class="block bg-white overflow-hidden shadow-sm rounded-lg hover:shadow-md transition-shadow">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Total Pedidos</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.orders }}</p>
                        </div>
                    </div>
                </div>
            </Link>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Em Aberto</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.openOrders }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-orange-500 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Processando</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.processingOrders }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-emerald-600 rounded-md p-3">
                            <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm text-gray-500">Faturamento</p>
                            <p class="text-lg font-bold text-gray-900">{{ formatCurrency(stats.totalRevenue) }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent orders -->
        <div class="mt-8 bg-white shadow-sm rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Pedidos Recentes</h3>
                <Link :href="route('orders.index')" class="text-sm text-indigo-600 hover:text-indigo-800">Ver todos</Link>
            </div>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Fornecedor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Data</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    <tr v-if="recentOrders.length === 0">
                        <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">Nenhum pedido encontrado.</td>
                    </tr>
                    <tr v-for="order in recentOrders" :key="order.id" class="hover:bg-gray-50">
                        <td class="px-6 py-3 text-sm">
                            <Link :href="route('orders.show', order.id)" class="text-indigo-600 hover:text-indigo-900 font-medium">#{{ order.id }}</Link>
                        </td>
                        <td class="px-6 py-3 text-sm text-gray-900">{{ order.supplier?.name }}</td>
                        <td class="px-6 py-3 text-sm text-gray-500">{{ formatDate(order.order_date) }}</td>
                        <td class="px-6 py-3"><StatusBadge :status="order.status" type="order" /></td>
                        <td class="px-6 py-3 text-sm text-right font-medium">{{ formatCurrency(order.total) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AuthenticatedLayout>
</template>
