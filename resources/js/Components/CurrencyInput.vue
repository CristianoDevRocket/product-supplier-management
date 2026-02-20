<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    modelValue: { type: [Number, String], default: 0 },
    label: { type: String, default: '' },
    error: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);
const displayValue = ref(formatDisplay(props.modelValue));

function formatDisplay(value) {
    const num = parseFloat(value) || 0;
    return num.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}

function onInput(event) {
    const raw = event.target.value.replace(/[^\d,]/g, '').replace(',', '.');
    const num = parseFloat(raw) || 0;
    emit('update:modelValue', num);
}

function onBlur() {
    displayValue.value = formatDisplay(props.modelValue);
}

watch(() => props.modelValue, (val) => {
    displayValue.value = formatDisplay(val);
});
</script>

<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-gray-700 mb-1">{{ label }}</label>
        <div class="relative rounded-md shadow-sm">
            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                <span class="text-gray-500 sm:text-sm">R$</span>
            </div>
            <input
                type="text"
                :value="displayValue"
                @input="onInput"
                @blur="onBlur"
                class="block w-full rounded-md border-gray-300 pl-10 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                :class="{ 'border-red-300': error }"
            />
        </div>
        <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>
</template>
