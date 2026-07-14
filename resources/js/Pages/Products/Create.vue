<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    name: '',
    description: '',
    price: '',
    stock: 0,
    is_active: true,
});

const submitForm = () => {
    form.post(route('products.store'));
};
</script>

<template>
    <Head title="Nuevo Producto" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Registrar Nuevo Producto
                </h2>
                <Link
                    :href="route('products.index')"
                    class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition"
                >
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Volver
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6">
                    <form @submit.prevent="submitForm">
                        <div class="space-y-6">
                            <!-- Name Input -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nombre del Producto</label>
                                <input
                                    v-model="form.name"
                                    id="name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required
                                />
                                <div v-if="form.errors.name" class="text-sm text-rose-600 mt-1">
                                    {{ form.errors.name }}
                                </div>
                            </div>

                            <!-- Description Input -->
                            <div>
                                <label for="description" class="block text-sm font-medium text-gray-700">Descripción (Opcional)</label>
                                <textarea
                                    v-model="form.description"
                                    id="description"
                                    rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                ></textarea>
                                <div v-if="form.errors.description" class="text-sm text-rose-600 mt-1">
                                    {{ form.errors.description }}
                                </div>
                            </div>

                            <!-- Price and Stock Inputs in Grid -->
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <label for="price" class="block text-sm font-medium text-gray-700">Precio (Bs.)</label>
                                    <input
                                        v-model="form.price"
                                        id="price"
                                        type="number"
                                        step="0.01"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        required
                                    />
                                    <div v-if="form.errors.price" class="text-sm text-rose-600 mt-1">
                                        {{ form.errors.price }}
                                    </div>
                                </div>

                                <div>
                                    <label for="stock" class="block text-sm font-medium text-gray-700">Inventario (Stock)</label>
                                    <input
                                        v-model="form.stock"
                                        id="stock"
                                        type="number"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                        required
                                    />
                                    <div v-if="form.errors.stock" class="text-sm text-rose-600 mt-1">
                                        {{ form.errors.stock }}
                                    </div>
                                </div>
                            </div>

                            <!-- Active Status Toggle -->
                            <div class="flex items-center">
                                <input
                                    v-model="form.is_active"
                                    id="is_active"
                                    type="checkbox"
                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                />
                                <label for="is_active" class="ml-2 block text-sm font-medium text-gray-900 font-semibold select-none">
                                    Producto Activo
                                </label>
                                <div v-if="form.errors.is_active" class="text-sm text-rose-600 mt-1">
                                    {{ form.errors.is_active }}
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                            <Link
                                :href="route('products.index')"
                                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 transition"
                            >
                                {{ form.processing ? 'Registrando...' : 'Registrar Producto' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
