<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    orders: Object,
    filters: Object,
});

// Search functionality
const search = ref(props.filters.search || '');
let searchTimeout;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('orders.index'), { search: value }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

// Helper for status badge styling
const getStatusClasses = (status) => {
    switch (status) {
        case 'PENDING':
            return 'bg-amber-50 text-amber-700 ring-amber-600/10';
        case 'CONFIRMED':
            return 'bg-blue-50 text-blue-700 ring-blue-600/10';
        case 'DELIVERED':
            return 'bg-emerald-50 text-emerald-700 ring-emerald-600/10';
        case 'CANCELLED':
            return 'bg-rose-50 text-rose-700 ring-rose-600/10';
        default:
            return 'bg-gray-50 text-gray-700 ring-gray-600/10';
    }
};

const getStatusLabel = (status) => {
    switch (status) {
        case 'PENDING':
            return 'Pendiente';
        case 'CONFIRMED':
            return 'Confirmado';
        case 'DELIVERED':
            return 'Entregado';
        case 'CANCELLED':
            return 'Cancelado';
        default:
            return status;
    }
};
</script>

<template>
    <Head title="Pedidos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Historial de Pedidos
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg border border-gray-100">
                    <!-- Actions Panel -->
                    <div class="flex flex-col items-center justify-between gap-4 p-6 sm:flex-row border-b border-gray-100">
                        <div class="relative w-full max-w-md">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input
                                v-model="search"
                                type="text"
                                placeholder="Buscar pedido por cliente, estado o ID..."
                                class="w-full rounded-md border-gray-300 pl-10 pr-4 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <Link
                            :href="route('orders.create')"
                            class="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:w-auto transition-all"
                        >
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nuevo Pedido
                        </Link>
                    </div>

                    <!-- Orders Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto text-left">
                            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3">ID Pedido</th>
                                    <th class="px-6 py-3">Cliente</th>
                                    <th class="px-6 py-3">Fecha</th>
                                    <th class="px-6 py-3">Estado</th>
                                    <th class="px-6 py-3">Total</th>
                                    <th class="px-6 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                                <tr v-if="orders.data.length === 0">
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                        No se encontraron pedidos registrados.
                                    </td>
                                </tr>
                                <tr v-for="order in orders.data" :key="order.id" class="hover:bg-gray-50/50 transition">
                                    <td class="whitespace-nowrap px-6 py-4 font-mono text-xs text-indigo-600 font-semibold">
                                        <Link :href="route('orders.show', order.id)" class="hover:underline">
                                            {{ order.id.substring(0, 8) }}...
                                        </Link>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 font-medium text-gray-900">
                                        {{ order.customer ? order.customer.full_name : 'Cliente Eliminado' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-gray-500">
                                        {{ new Date(order.order_date).toLocaleString() }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            :class="getStatusClasses(order.status)"
                                            class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset"
                                        >
                                            {{ getStatusLabel(order.status) }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 font-bold text-gray-900">
                                        ${{ parseFloat(order.total).toFixed(2) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <Link
                                            :href="route('orders.show', order.id)"
                                            class="inline-flex items-center rounded bg-white px-2.5 py-1 text-xs font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition"
                                        >
                                            Ver Detalles
                                        </Link>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    <div class="flex items-center justify-between border-t border-gray-100 px-6 py-4" v-if="orders.links.length > 3">
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Mostrando del
                                    <span class="font-medium">{{ orders.from || 0 }}</span>
                                    al
                                    <span class="font-medium">{{ orders.to || 0 }}</span>
                                    de
                                    <span class="font-medium">{{ orders.total }}</span>
                                    resultados
                                </p>
                            </div>
                            <div>
                                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                    <Link
                                        v-for="(link, index) in orders.links"
                                        :key="index"
                                        :href="link.url || '#'"
                                        v-html="link.label"
                                        :disabled="!link.url"
                                        :class="[
                                            link.active
                                                ? 'z-10 bg-indigo-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600'
                                                : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0',
                                            !link.url ? 'opacity-50 cursor-not-allowed' : '',
                                            'relative inline-flex items-center px-4 py-2 text-sm font-semibold'
                                        ]"
                                    />
                                </nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
