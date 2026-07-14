<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const form = useForm({
    full_name: '',
    email: '',
    phone: '',
    is_active: true,
});

const submitForm = () => {
    form.post(route('customers.store'));
};
</script>

<template>
    <Head title="Nuevo Cliente" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Registrar Nuevo Cliente
                </h2>
                <Link
                    :href="route('customers.index')"
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
                                <label for="full_name" class="block text-sm font-medium text-gray-700">Nombre Completo</label>
                                <input
                                    v-model="form.full_name"
                                    id="full_name"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required
                                />
                                <div v-if="form.errors.full_name" class="text-sm text-rose-600 mt-1">
                                    {{ form.errors.full_name }}
                                </div>
                            </div>

                            <!-- Email Input -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                                <input
                                    v-model="form.email"
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required
                                />
                                <div v-if="form.errors.email" class="text-sm text-rose-600 mt-1">
                                    {{ form.errors.email }}
                                </div>
                            </div>

                            <!-- Phone Input -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Teléfono (Opcional)</label>
                                <input
                                    v-model="form.phone"
                                    id="phone"
                                    type="text"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                />
                                <div v-if="form.errors.phone" class="text-sm text-rose-600 mt-1">
                                    {{ form.errors.phone }}
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
                                    Cliente Activo
                                </label>
                                <div v-if="form.errors.is_active" class="text-sm text-rose-600 mt-1">
                                    {{ form.errors.is_active }}
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="mt-8 flex items-center justify-end gap-3 border-t border-gray-100 pt-4">
                            <Link
                                :href="route('customers.index')"
                                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 transition"
                            >
                                {{ form.processing ? 'Registrando...' : 'Registrar Cliente' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
