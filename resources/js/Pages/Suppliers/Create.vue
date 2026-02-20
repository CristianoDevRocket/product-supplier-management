<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import SupplierForm from './_Form.vue';

const form = useForm({
    name: '',
    cnpj: '',
    email: '',
    phone: '',
    status: 'active',
});

function submit() {
    form.post(route('suppliers.store'));
}
</script>

<template>
    <Head title="Novo Fornecedor" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center space-x-4">
                <Link :href="route('suppliers.index')" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                </Link>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">Novo Fornecedor</h2>
            </div>
        </template>

        <div class="max-w-2xl">
            <div class="bg-white shadow-sm rounded-lg p-6">
                <form @submit.prevent="submit">
                    <SupplierForm :form="form" :errors="form.errors" />
                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <Link :href="route('suppliers.index')" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
                            Cancelar
                        </Link>
                        <button type="submit" :disabled="form.processing" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 disabled:opacity-50 transition">
                            Salvar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
