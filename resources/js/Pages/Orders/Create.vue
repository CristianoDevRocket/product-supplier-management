<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';

const props = defineProps({
    suppliers: Array,
});

const today = new Date().toISOString().split('T')[0];

const form = useForm({
    supplier_id: '',
    order_date: today,
    notes: '',
});

function submit() {
    form.post(route('orders.store'));
}
</script>

<template>
    <Head title="Novo Pedido" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link :href="route('orders.index')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Novo Pedido</h2>
            </div>
        </template>

        <div class="max-w-2xl">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form @submit.prevent="submit">
                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                        <SelectInput
                            v-model="form.supplier_id"
                            label="Fornecedor"
                            :options="suppliers.map(s => ({ value: s.id, label: `${s.name} (${s.cnpj})` }))"
                            placeholder="Selecione um fornecedor..."
                            :error="form.errors.supplier_id"
                        />
                        <TextInput
                            v-model="form.order_date"
                            label="Data do Pedido"
                            type="date"
                            :error="form.errors.order_date"
                        />
                        <div class="sm:col-span-2">
                            <TextArea
                                v-model="form.notes"
                                label="Observacoes"
                                :error="form.errors.notes"
                                :rows="3"
                            />
                        </div>
                    </div>
                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <Link :href="route('orders.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
                            Cancelar
                        </Link>
                        <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50 transition">
                            Criar Pedido
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
