<script setup>
defineProps({
    modelValue: { type: String, default: '' },
    label: { type: String, default: 'Telefone' },
    error: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

function formatPhone(value) {
    const digits = value.replace(/\D/g, '').slice(0, 11);
    if (digits.length === 0) return '';
    if (digits.length <= 2) return '(' + digits;
    if (digits.length <= 6) return '(' + digits.slice(0, 2) + ') ' + digits.slice(2);
    if (digits.length <= 10) {
        return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 6) + '-' + digits.slice(6);
    }
    return '(' + digits.slice(0, 2) + ') ' + digits.slice(2, 7) + '-' + digits.slice(7);
}

function onInput(event) {
    const formatted = formatPhone(event.target.value);
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
            placeholder="(XX) XXXXX-XXXX"
            maxlength="15"
            class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            :class="{ 'border-red-300': error }"
        />
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>
