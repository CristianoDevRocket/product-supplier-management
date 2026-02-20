<script setup>
defineProps({
    modelValue: { type: String, default: '' },
    label: { type: String, default: 'CNPJ' },
    error: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

function formatCnpj(value) {
    const digits = value.replace(/\D/g, '').slice(0, 14);
    let formatted = digits;
    if (digits.length > 2) formatted = digits.slice(0, 2) + '.' + digits.slice(2);
    if (digits.length > 5) formatted = formatted.slice(0, 6) + '.' + formatted.slice(6);
    if (digits.length > 8) formatted = formatted.slice(0, 10) + '/' + formatted.slice(10);
    if (digits.length > 12) formatted = formatted.slice(0, 15) + '-' + formatted.slice(15);
    return formatted;
}

function onInput(event) {
    const formatted = formatCnpj(event.target.value);
    event.target.value = formatted;
    emit('update:modelValue', formatted);
}
</script>

<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">{{ label }}</label>
        <input
            type="text"
            :value="modelValue"
            @input="onInput"
            placeholder="XX.XXX.XXX/XXXX-XX"
            maxlength="18"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            :class="{ 'border-red-300': error }"
        />
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>
