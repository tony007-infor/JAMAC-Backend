<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    order: Object,
});

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
    <Head title="Detalle de Pedido" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Detalle del Pedido #{{ order.id.substring(0, 8) }}
                </h2>
                <Link
                    :href="route('orders.index')"
                    class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition"
                >
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver a Pedidos
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8 space-y-6">
                <!-- Order Summary & Customer Details Cards -->
                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <!-- General Details Card -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6 space-y-4">
                        <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-2">Información del Pedido</h3>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <div class="text-gray-500">ID Completo:</div>
                            <div class="font-mono text-xs text-gray-900 break-all select-all">{{ order.id }}</div>

                            <div class="text-gray-500">Fecha:</div>
                            <div class="font-medium text-gray-900">{{ new Date(order.order_date).toLocaleString() }}</div>

                            <div class="text-gray-500">Estado:</div>
                            <div>
                                <span
                                    :class="getStatusClasses(order.status)"
                                    class="inline-flex items-center rounded-md px-2.5 py-0.5 text-xs font-medium ring-1 ring-inset"
                                >
                                    {{ getStatusLabel(order.status) }}
                                </span>
                            </div>

                            <div class="text-gray-500">Total Facturado:</div>
                            <div class="font-bold text-lg text-gray-900">Bs. {{ parseFloat(order.total).toFixed(2) }}</div>
                        </div>
                    </div>

                    <!-- Customer Details Card -->
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6 space-y-4 md:col-span-2">
                        <h3 class="text-base font-semibold text-gray-900 border-b border-gray-100 pb-2">Datos del Cliente</h3>
                        <div v-if="order.customer" class="grid grid-cols-1 gap-y-4 gap-x-6 sm:grid-cols-2 text-sm">
                            <div>
                                <div class="text-gray-500">Nombre Completo:</div>
                                <div class="font-medium text-gray-900 text-base mt-0.5">{{ order.customer.full_name }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Correo Electrónico:</div>
                                <div class="font-medium text-gray-900 mt-0.5">{{ order.customer.email }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Teléfono:</div>
                                <div class="font-medium text-gray-900 mt-0.5">{{ order.customer.phone || 'N/A' }}</div>
                            </div>
                            <div>
                                <div class="text-gray-500">Estado del Cliente:</div>
                                <div class="mt-1">
                                    <span
                                        :class="order.customer.is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'"
                                        class="inline-flex items-center rounded px-2 py-0.5 text-xs font-medium"
                                    >
                                        {{ order.customer.is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-sm text-rose-600 font-semibold">
                            Atención: Este cliente ha sido eliminado físicamente de la base de datos externa.
                        </div>
                    </div>
                </div>

                <!-- Products in Order Card -->
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg border border-gray-100">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-base font-semibold text-gray-900">Productos Incluidos</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto text-left text-sm text-gray-700">
                            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3">Producto</th>
                                    <th class="px-6 py-3 text-center">Cantidad</th>
                                    <th class="px-6 py-3 text-right">Precio Unitario</th>
                                    <th class="px-6 py-3 text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="item in order.items" :key="item.id" class="hover:bg-gray-50/30 transition">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">
                                            {{ item.product ? item.product.name : 'Producto Eliminado' }}
                                        </div>
                                        <div class="text-xs text-gray-500 font-mono mt-0.5" v-if="item.product">
                                            ID: {{ item.product.id }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center font-medium text-gray-900">
                                        {{ item.quantity }}
                                    </td>
                                    <td class="px-6 py-4 text-right text-gray-500 font-mono">
                                        Bs. {{ parseFloat(item.unit_price).toFixed(2) }}
                                    </td>
                                    <td class="px-6 py-4 text-right font-bold text-gray-900 font-mono">
                                        Bs. {{ parseFloat(item.sub_total).toFixed(2) }}
                                    </td>
                                </tr>
                                <!-- Final Summary Row -->
                                <tr class="bg-gray-50/50">
                                    <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-900 uppercase">
                                        Total del Pedido:
                                    </td>
                                    <td class="px-6 py-4 text-right font-black text-indigo-600 text-lg font-mono">
                                        Bs. {{ parseFloat(order.total).toFixed(2) }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Security / Concurrency Notice -->
                <div class="rounded-md bg-blue-50 p-4 border border-blue-100 shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 2h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-blue-800">Nota de Integridad & Concurrencia</h3>
                            <div class="mt-2 text-xs text-blue-700 space-y-1">
                                <p>Este pedido fue procesado de forma estrictamente segura:</p>
                                <ul class="list-disc pl-5 space-y-0.5 mt-1">
                                    <li>Precios bloqueados desde la base de datos al momento del registro, ignorando valores del frontend.</li>
                                    <li>Lógica transaccional con bloqueos de fila (`lockForUpdate`) para evitar condiciones de carrera en el control de stock.</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
