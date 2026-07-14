<script setup>
import { ref, computed } from 'vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';

const props = defineProps({
    customers: Array,
    products: Array,
});

// Form state using Inertia useForm
const form = useForm({
    customer_id: '',
    items: [],
});

// Cart local state
const cart = ref([]);

// Search products filter in frontend
const productSearch = ref('');
const filteredProducts = computed(() => {
    if (!productSearch.value) return props.products;
    const query = productSearch.value.toLowerCase();
    return props.products.filter(p => 
        p.name.toLowerCase().includes(query) || 
        (p.description && p.description.toLowerCase().includes(query))
    );
});

// Check if a product is already in the cart
const isInCart = (productId) => {
    return cart.value.some(item => item.product.id === productId);
};

// Add product to cart
const addToCart = (product) => {
    if (product.stock <= 0) return;
    if (isInCart(product.id)) return;
    
    cart.value.push({
        product: product,
        quantity: 1
    });
};

// Remove product from cart
const removeFromCart = (index) => {
    cart.value.splice(index, 1);
};

// Increment quantity
const incrementQty = (index) => {
    const item = cart.value[index];
    if (item.quantity < item.product.stock) {
        item.quantity++;
    }
};

// Decrement quantity
const decrementQty = (index) => {
    const item = cart.value[index];
    if (item.quantity > 1) {
        item.quantity--;
    }
};

// Direct quantity update with stock validation
const updateQty = (index, value) => {
    const item = cart.value[index];
    let qty = parseInt(value);
    if (isNaN(qty) || qty < 1) qty = 1;
    if (qty > item.product.stock) qty = item.product.stock;
    item.quantity = qty;
};

// Cart total in frontend for display
const cartTotal = computed(() => {
    return cart.value.reduce((sum, item) => {
        return sum + (parseFloat(item.product.price) * item.quantity);
    }, 0);
});

// Place the order
const submitOrder = () => {
    // Map cart state to the structure required by StoreOrderRequest
    form.items = cart.value.map(item => ({
        product_id: item.product.id,
        quantity: item.quantity
    }));

    form.post(route('orders.store'), {
        onError: (errors) => {
            // Keep state to allow correcting errors
            console.log(errors);
        }
    });
};
</script>

<template>
    <Head title="Nuevo Pedido" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold leading-tight text-gray-800">
                    Crear Nuevo Pedido
                </h2>
                <Link
                    :href="route('orders.index')"
                    class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition"
                >
                    Cancelar
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Validation Errors Alert -->
                <div v-if="Object.keys(form.errors).length > 0" class="mb-6 rounded-md bg-rose-50 p-4 border border-rose-200">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-rose-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-rose-800">Se encontraron errores al procesar el pedido</h3>
                            <div class="mt-2 text-sm text-rose-700">
                                <ul class="list-disc pl-5 space-y-1">
                                    <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                    <!-- Left: Client & Product Selection -->
                    <div class="space-y-6 lg:col-span-2">
                        <!-- Customer Selection Card -->
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6">
                            <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center">
                                <span class="bg-indigo-50 text-indigo-700 h-7 w-7 rounded-full flex items-center justify-center text-sm font-bold mr-2">1</span>
                                Seleccione el Cliente
                            </h3>
                            <div>
                                <label for="customer_id" class="sr-only">Cliente</label>
                                <select
                                    v-model="form.customer_id"
                                    id="customer_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required
                                >
                                    <option value="" disabled>-- Seleccione un cliente activo --</option>
                                    <option v-for="customer in customers" :key="customer.id" :value="customer.id">
                                        {{ customer.full_name }} ({{ customer.email }})
                                    </option>
                                </select>
                                <div v-if="form.errors.customer_id" class="text-sm text-rose-600 mt-1">
                                    {{ form.errors.customer_id }}
                                </div>
                            </div>
                        </div>

                        <!-- Product Finder Card -->
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
                                <h3 class="text-base font-semibold text-gray-900 flex items-center whitespace-nowrap">
                                    <span class="bg-indigo-50 text-indigo-700 h-7 w-7 rounded-full flex items-center justify-center text-sm font-bold mr-2">2</span>
                                    Agregar Productos
                                </h3>
                                <div class="relative w-full max-w-xs">
                                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </span>
                                    <input
                                        v-model="productSearch"
                                        type="text"
                                        placeholder="Buscar producto..."
                                        class="w-full rounded-md border-gray-300 pl-9 pr-3 py-1.5 text-xs shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    />
                                </div>
                            </div>

                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 max-h-[400px] overflow-y-auto pr-1">
                                <div v-if="filteredProducts.length === 0" class="col-span-2 text-center text-xs text-gray-500 py-6">
                                    No se encontraron productos activos o con inventario.
                                </div>
                                <div
                                    v-for="product in filteredProducts"
                                    :key="product.id"
                                    class="border border-gray-100 rounded-lg p-4 flex flex-col justify-between hover:border-indigo-200 transition"
                                >
                                    <div>
                                        <div class="flex justify-between items-start gap-2">
                                            <h4 class="font-semibold text-gray-900 text-sm leading-tight">{{ product.name }}</h4>
                                            <span class="text-indigo-600 font-bold text-sm">Bs. {{ parseFloat(product.price).toFixed(2) }}</span>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ product.description || 'Sin descripción' }}</p>
                                    </div>
                                    <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-50">
                                        <span class="text-xs" :class="product.stock > 0 ? 'text-gray-500' : 'text-rose-600 font-semibold'">
                                            Stock: {{ product.stock }} uds
                                        </span>
                                        <button
                                            type="button"
                                            @click="addToCart(product)"
                                            :disabled="product.stock <= 0 || isInCart(product.id)"
                                            :class="[
                                                isInCart(product.id)
                                                    ? 'bg-emerald-50 text-emerald-700 border border-emerald-200 cursor-default'
                                                    : product.stock <= 0
                                                        ? 'bg-gray-50 text-gray-400 border border-gray-100 cursor-not-allowed'
                                                        : 'bg-indigo-600 text-white hover:bg-indigo-700 shadow-sm'
                                            ]"
                                            class="rounded px-2.5 py-1 text-xs font-semibold transition"
                                        >
                                            {{ isInCart(product.id) ? 'En Carrito' : product.stock <= 0 ? 'Agotado' : 'Agregar' }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Cart & Submit -->
                    <div class="space-y-6">
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg border border-gray-100 p-6 flex flex-col h-full min-h-[500px] justify-between">
                            <div>
                                <h3 class="text-base font-semibold text-gray-900 mb-4 flex items-center border-b border-gray-100 pb-2">
                                    <svg class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    Carrito de Pedido
                                </h3>

                                <!-- Empty Cart -->
                                <div v-if="cart.length === 0" class="text-center text-gray-400 py-12 flex flex-col items-center">
                                    <svg class="h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                    <span class="text-sm">El carrito está vacío. Agregue productos.</span>
                                </div>

                                <!-- Cart Items List -->
                                <div v-else class="space-y-4 max-h-[350px] overflow-y-auto pr-1">
                                    <div
                                        v-for="(item, index) in cart"
                                        :key="item.product.id"
                                        class="flex items-center justify-between border-b border-gray-50 pb-3"
                                    >
                                        <div class="flex-1 min-w-0 pr-2">
                                            <h4 class="font-medium text-gray-900 text-sm truncate">{{ item.product.name }}</h4>
                                            <p class="text-xs text-indigo-600 font-semibold">Bs. {{ parseFloat(item.product.price).toFixed(2) }} c/u</p>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <!-- Quantity selector controls -->
                                            <div class="flex items-center border border-gray-200 rounded-md">
                                                <button
                                                    type="button"
                                                    @click="decrementQty(index)"
                                                    class="px-2 py-1 text-gray-500 hover:bg-gray-50 transition"
                                                >
                                                    -
                                                </button>
                                                <input
                                                    :value="item.quantity"
                                                    @change="updateQty(index, $event.target.value)"
                                                    type="text"
                                                    class="w-8 text-center text-xs font-semibold border-none focus:ring-0 p-0"
                                                />
                                                <button
                                                    type="button"
                                                    @click="incrementQty(index)"
                                                    class="px-2 py-1 text-gray-500 hover:bg-gray-50 transition"
                                                >
                                                    +
                                                </button>
                                            </div>

                                            <div class="text-right w-16">
                                                <span class="text-sm font-bold text-gray-900 font-mono">
                                                    Bs. {{ (parseFloat(item.product.price) * item.quantity).toFixed(2) }}
                                                </span>
                                            </div>

                                            <button
                                                type="button"
                                                @click="removeFromCart(index)"
                                                class="text-rose-500 hover:text-rose-700 p-1"
                                                title="Quitar"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Cart Summary / Total & Action Button -->
                            <div class="border-t border-gray-100 pt-4 mt-6 space-y-4">
                                <div class="flex items-center justify-between text-base font-bold text-gray-900">
                                    <span>Total (Estimado):</span>
                                    <span class="font-mono text-indigo-600 text-lg">Bs. {{ cartTotal.toFixed(2) }}</span>
                                </div>

                                <div class="bg-gray-50 rounded-md p-3 text-[11px] text-gray-500">
                                    * Los precios finales y subtotales se recalculan estrictamente en el servidor utilizando los datos oficiales del inventario.
                                </div>

                                <button
                                    type="button"
                                    @click="submitOrder"
                                    :disabled="form.processing || cart.length === 0 || !form.customer_id"
                                    class="w-full flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 disabled:opacity-50 transition-all"
                                >
                                    {{ form.processing ? 'Procesando Pedido...' : 'Guardar y Crear Pedido' }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
