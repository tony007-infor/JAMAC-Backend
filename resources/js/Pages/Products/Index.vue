<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    products: Object,
    filters: Object,
});

// Search functionality
const search = ref(props.filters.search || '');
let searchTimeout;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('products.index'), { search: value }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

// Logical Delete (Soft Delete) with Confirm Check
const deleteProduct = (product) => {
    if (confirm(`¿Estás seguro de que deseas desactivar/eliminar lógicamente el producto "${product.name}"?`)) {
        router.delete(route('products.destroy', product.id));
    }
};
</script>

<template>
    <Head title="Productos" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Gestión de Productos
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
                                placeholder="Buscar producto por nombre o descripción..."
                                class="w-full rounded-md border-gray-300 pl-10 pr-4 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <Link
                            :href="route('products.create')"
                            class="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:w-auto transition-all"
                        >
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nuevo Producto
                        </Link>
                    </div>

                    <!-- Products Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto text-left">
                            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3">Nombre</th>
                                    <th class="px-6 py-3">Descripción</th>
                                    <th class="px-6 py-3">Precio</th>
                                    <th class="px-6 py-3">Stock</th>
                                    <th class="px-6 py-3">Estado</th>
                                    <th class="px-6 py-3">Creado</th>
                                    <th class="px-6 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                                <tr v-if="products.data.length === 0">
                                    <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                                        No se encontraron productos registrados.
                                    </td>
                                </tr>
                                <tr v-for="product in products.data" :key="product.id" class="hover:bg-gray-50/50 transition">
                                    <td class="whitespace-nowrap px-6 py-4 font-medium text-gray-900">
                                        {{ product.name }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 max-w-xs truncate">
                                        {{ product.description || 'Sin descripción' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 font-semibold text-gray-900">
                                        ${{ parseFloat(product.price).toFixed(2) }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            v-if="product.stock === 0"
                                            class="inline-flex items-center rounded-md bg-rose-100 px-2.5 py-0.5 text-xs font-medium text-rose-800"
                                        >
                                            Agotado
                                        </span>
                                        <span
                                            v-else-if="product.stock <= 5"
                                            class="inline-flex items-center rounded-md bg-amber-100 px-2.5 py-0.5 text-xs font-medium text-amber-800"
                                        >
                                            Bajo Stock ({{ product.stock }})
                                        </span>
                                        <span
                                            v-else
                                            class="text-gray-900 font-medium"
                                        >
                                            {{ product.stock }} uds
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            :class="[
                                                product.is_active
                                                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
                                                    : 'bg-rose-50 text-rose-700 ring-rose-600/20'
                                            ]"
                                            class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                                        >
                                            {{ product.is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-gray-500">
                                        {{ new Date(product.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <Link
                                            :href="route('products.edit', product.id)"
                                            class="text-indigo-600 hover:text-indigo-900 font-semibold mr-3 transition"
                                        >
                                            Editar
                                        </Link>
                                        <button
                                            @click="deleteProduct(product)"
                                            class="text-rose-600 hover:text-rose-900 font-semibold transition"
                                        >
                                            Eliminar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Links -->
                    <div class="flex items-center justify-between border-t border-gray-100 px-6 py-4" v-if="products.links.length > 3">
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Mostrando del
                                    <span class="font-medium">{{ products.from || 0 }}</span>
                                    al
                                    <span class="font-medium">{{ products.to || 0 }}</span>
                                    de
                                    <span class="font-medium">{{ products.total }}</span>
                                    resultados
                                </p>
                            </div>
                            <div>
                                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                    <Link
                                        v-for="(link, index) in products.links"
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
