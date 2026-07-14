<script setup>
import { ref, watch } from 'vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    customers: Object,
    filters: Object,
});

// Search functionality
const search = ref(props.filters.search || '');
let searchTimeout;
watch(search, (value) => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        router.get(route('customers.index'), { search: value }, {
            preserveState: true,
            replace: true,
        });
    }, 300);
});

// Modal state & Form handling
const isModalOpen = ref(false);
const isEditing = ref(false);
const editingCustomer = ref(null);

const form = useForm({
    full_name: '',
    email: '',
    phone: '',
    is_active: true,
});

const openCreateModal = () => {
    isEditing.value = false;
    editingCustomer.value = null;
    form.reset();
    form.clearErrors();
    isModalOpen.value = true;
};

const openEditModal = (customer) => {
    isEditing.value = true;
    editingCustomer.value = customer;
    form.clearErrors();
    form.full_name = customer.full_name;
    form.email = customer.email;
    form.phone = customer.phone || '';
    form.is_active = customer.is_active;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
};

const submitForm = () => {
    if (isEditing.value) {
        form.put(route('customers.update', editingCustomer.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('customers.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteCustomer = (customer) => {
    if (confirm(`¿Estás seguro de que deseas eliminar al cliente "${customer.full_name}"?`)) {
        router.delete(route('customers.destroy', customer.id));
    }
};
</script>

<template>
    <Head title="Clientes" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Gestión de Clientes
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
                                placeholder="Buscar cliente por nombre, email o teléfono..."
                                class="w-full rounded-md border-gray-300 pl-10 pr-4 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                            />
                        </div>
                        <button
                            @click="openCreateModal"
                            class="inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:w-auto transition-all"
                        >
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Nuevo Cliente
                        </button>
                    </div>

                    <!-- Customers Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full table-auto text-left">
                            <thead class="bg-gray-50 text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-3">Nombre</th>
                                    <th class="px-6 py-3">Email</th>
                                    <th class="px-6 py-3">Teléfono</th>
                                    <th class="px-6 py-3">Estado</th>
                                    <th class="px-6 py-3">Creado</th>
                                    <th class="px-6 py-3 text-right">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                                <tr v-if="customers.data.length === 0">
                                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                                        No se encontraron clientes registrados.
                                    </td>
                                </tr>
                                <tr v-for="customer in customers.data" :key="customer.id" class="hover:bg-gray-50/50 transition">
                                    <td class="whitespace-nowrap px-6 py-4 font-medium text-gray-900">
                                        {{ customer.full_name }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-gray-500">
                                        {{ customer.email }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-gray-500">
                                        {{ customer.phone || 'N/A' }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            :class="[
                                                customer.is_active
                                                    ? 'bg-emerald-50 text-emerald-700 ring-emerald-600/20'
                                                    : 'bg-rose-50 text-rose-700 ring-rose-600/20'
                                            ]"
                                            class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset"
                                        >
                                            {{ customer.is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-gray-500">
                                        {{ new Date(customer.created_at).toLocaleDateString() }}
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right">
                                        <button
                                            @click="openEditModal(customer)"
                                            class="text-indigo-600 hover:text-indigo-900 font-semibold mr-3 transition"
                                        >
                                            Editar
                                        </button>
                                        <button
                                            @click="deleteCustomer(customer)"
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
                    <div class="flex items-center justify-between border-t border-gray-100 px-6 py-4" v-if="customers.links.length > 3">
                        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700">
                                    Mostrando del
                                    <span class="font-medium">{{ customers.from || 0 }}</span>
                                    al
                                    <span class="font-medium">{{ customers.to || 0 }}</span>
                                    de
                                    <span class="font-medium">{{ customers.total }}</span>
                                    resultados
                                </p>
                            </div>
                            <div>
                                <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                                    <Link
                                        v-for="(link, index) in customers.links"
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

        <!-- Form Modal -->
        <Modal :show="isModalOpen" @close="closeModal">
            <div class="p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    {{ isEditing ? 'Editar Cliente' : 'Nuevo Cliente' }}
                </h3>
                <form @submit.prevent="submitForm">
                    <div class="space-y-4">
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

                        <div class="flex items-center">
                            <input
                                v-model="form.is_active"
                                id="is_active"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                            />
                            <label for="is_active" class="ml-2 block text-sm font-medium text-gray-900">
                                Cliente Activo
                            </label>
                            <div v-if="form.errors.is_active" class="text-sm text-rose-600 mt-1">
                                {{ form.errors.is_active }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3 border-t border-gray-100 pt-4">
                        <button
                            type="button"
                            @click="closeModal"
                            class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-50 transition"
                        >
                            {{ form.processing ? 'Guardando...' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>
