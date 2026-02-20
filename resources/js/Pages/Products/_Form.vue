<script setup>
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
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
        <TextInput
            v-model="form.internal_code"
            label="Codigo Interno"
            :error="errors.internal_code"
        />
        <div class="sm:col-span-2">
            <TextArea
                v-model="form.description"
                label="Descricao"
                :error="errors.description"
                :rows="3"
            />
        </div>
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
