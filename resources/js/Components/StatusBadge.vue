<script setup>
const props = defineProps({
    status: { type: String, required: true },
    type: { type: String, default: 'entity' }, // entity or order
});

const colors = {
    entity: {
        active: 'bg-green-100 text-green-800',
        inactive: 'bg-red-100 text-red-800',
    },
    order: {
        open: 'bg-blue-100 text-blue-800',
        processing: 'bg-yellow-100 text-yellow-800',
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
    },
};

const labels = {
    entity: {
        active: 'Ativo',
        inactive: 'Inativo',
    },
    order: {
        open: 'Aberto',
        processing: 'Em Processamento',
        completed: 'Concluido',
        cancelled: 'Cancelado',
    },
};
</script>

<template>
    <span
        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium"
        :class="colors[type]?.[status] ?? 'bg-gray-100 text-gray-800'"
    >
        {{ labels[type]?.[status] ?? status }}
    </span>
</template>
