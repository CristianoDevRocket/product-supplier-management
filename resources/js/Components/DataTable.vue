<script setup>
import { router } from '@inertiajs/vue3';

const props = defineProps({
    columns: { type: Array, required: true }, // [{ key, label, sortable? }]
    sort: { type: String, default: '' },
    direction: { type: String, default: 'asc' },
});

const emit = defineEmits(['sort']);

function toggleSort(column) {
    if (!column.sortable) return;
    const newDirection = props.sort === column.key && props.direction === 'asc' ? 'desc' : 'asc';
    emit('sort', { sort: column.key, direction: newDirection });
}
</script>

<template>
    <div class="overflow-x-auto bg-white shadow-sm rounded-lg">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th
                        v-for="column in columns"
                        :key="column.key"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider"
                        :class="{ 'cursor-pointer hover:text-gray-700 select-none': column.sortable }"
                        @click="toggleSort(column)"
                    >
                        <div class="flex items-center space-x-1">
                            <span>{{ column.label }}</span>
                            <span v-if="column.sortable && sort === column.key" class="text-indigo-600">
                                <svg v-if="direction === 'asc'" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"/></svg>
                                <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </span>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <slot />
            </tbody>
        </table>
    </div>
</template>
