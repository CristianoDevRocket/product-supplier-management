<script setup>
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const showMobileMenu = ref(false);

const navigation = [
    { name: 'Dashboard', route: 'dashboard', icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Fornecedores', route: 'suppliers.index', icon: 'M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4' },
    { name: 'Produtos', route: 'products.index', icon: 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4' },
    { name: 'Vinculos', route: 'product-suppliers.index', icon: 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1' },
    { name: 'Pedidos', route: 'orders.index', icon: 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01' },
];

const page = usePage();
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <!-- Mobile menu button -->
        <div class="lg:hidden fixed top-0 left-0 right-0 z-30 bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between">
            <span class="text-lg font-semibold text-gray-800">Gestao P&F</span>
            <button @click="showMobileMenu = !showMobileMenu" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path v-if="!showMobileMenu" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Sidebar overlay -->
        <div v-if="showMobileMenu" @click="showMobileMenu = false" class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"></div>

        <!-- Sidebar -->
        <aside :class="[showMobileMenu ? 'translate-x-0' : '-translate-x-full', 'lg:translate-x-0']"
               class="fixed top-0 left-0 z-30 h-full w-64 bg-white border-r border-gray-200 transition-transform duration-200 ease-in-out">
            <div class="flex flex-col h-full">
                <div class="flex items-center h-16 px-6 border-b border-gray-200">
                    <Link :href="route('dashboard')" class="text-xl font-bold text-indigo-600">
                        Gestao P&F
                    </Link>
                </div>
                <nav class="flex-1 px-4 py-4 space-y-1 overflow-y-auto">
                    <Link v-for="item in navigation" :key="item.name"
                          :href="route(item.route)"
                          @click="showMobileMenu = false"
                          :class="[
                              route().current(item.route.replace('.index', '.*')) || route().current(item.route)
                                  ? 'bg-indigo-50 text-indigo-700 border-indigo-500'
                                  : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900 border-transparent',
                              'group flex items-center px-3 py-2 text-sm font-medium rounded-md border-l-4 transition-colors'
                          ]">
                        <svg class="mr-3 h-5 w-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" :d="item.icon" />
                        </svg>
                        {{ item.name }}
                    </Link>
                </nav>
                <div class="px-4 py-4 border-t border-gray-200">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-indigo-500 rounded-full flex items-center justify-center">
                                <span class="text-white text-sm font-medium">{{ page.props.auth?.user?.name?.charAt(0) ?? 'U' }}</span>
                            </div>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-gray-700">{{ page.props.auth?.user?.name ?? 'Usuario' }}</p>
                            <Link :href="route('logout')" method="post" as="button" class="text-xs text-gray-500 hover:text-gray-700">
                                Sair
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main content -->
        <div class="lg:pl-64">
            <div class="lg:hidden h-14"></div>

            <!-- Flash messages -->
            <div v-if="page.props.flash?.success || page.props.flash?.error || page.props.flash?.info" class="px-4 sm:px-6 lg:px-8 pt-4">
                <div v-if="page.props.flash?.success" class="rounded-md bg-green-50 p-4 mb-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                        <p class="ml-3 text-sm font-medium text-green-800">{{ page.props.flash.success }}</p>
                    </div>
                </div>
                <div v-if="page.props.flash?.info" class="rounded-md bg-blue-50 p-4 mb-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a.75.75 0 000 1.5h.253a.25.25 0 01.244.304l-.459 2.066A1.75 1.75 0 0010.747 15H11a.75.75 0 000-1.5h-.253a.25.25 0 01-.244-.304l.459-2.066A1.75 1.75 0 009.253 9H9z" clip-rule="evenodd" />
                        </svg>
                        <p class="ml-3 text-sm font-medium text-blue-800">{{ page.props.flash.info }}</p>
                    </div>
                </div>
                <div v-if="page.props.flash?.error" class="rounded-md bg-red-50 p-4 mb-4">
                    <div class="flex">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.28 7.22a.75.75 0 00-1.06 1.06L8.94 10l-1.72 1.72a.75.75 0 101.06 1.06L10 11.06l1.72 1.72a.75.75 0 101.06-1.06L11.06 10l1.72-1.72a.75.75 0 00-1.06-1.06L10 8.94 8.28 7.22z" clip-rule="evenodd" />
                        </svg>
                        <p class="ml-3 text-sm font-medium text-red-800">{{ page.props.flash.error }}</p>
                    </div>
                </div>
            </div>

            <!-- Page header -->
            <header v-if="$slots.header" class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8 py-4">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page content -->
            <main class="px-4 sm:px-6 lg:px-8 py-6">
                <slot />
            </main>
        </div>
    </div>
</template>
