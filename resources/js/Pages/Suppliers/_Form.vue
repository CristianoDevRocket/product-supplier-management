<script setup>
import TextInput from '@/Components/TextInput.vue';
import CnpjInput from '@/Components/CnpjInput.vue';
import PhoneInput from '@/Components/PhoneInput.vue';
import SelectInput from '@/Components/SelectInput.vue';

defineProps({
    form: { type: Object, required: true },
    errors: { type: Object, default: () => ({}) },
});
</script>

<template>
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        <TextInput
            v-model="form.name"
            label="Nome"
            :error="errors.name"
        />
        <CnpjInput
            v-model="form.cnpj"
            :error="errors.cnpj"
        />
        <TextInput
            v-model="form.email"
            label="E-mail"
            type="email"
            :error="errors.email"
        />
        <PhoneInput
            v-model="form.phone"
            :error="errors.phone"
        />
        <SelectInput
            v-model="form.status"
            label="Status"
            :options="[
                { value: 'active', label: 'Ativo' },
                { value: 'inactive', label: 'Inativo' },
            ]"
            :error="errors.status"
        />
    </div>
</template>
