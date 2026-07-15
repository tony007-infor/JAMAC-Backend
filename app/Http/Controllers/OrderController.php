<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $orders = $this->orderService->getPaginated($search);

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        $customers = Customer::where('is_active', true)->orderBy('full_name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Orders/Create', [
            'customers' => $customers,
            'products' => $products,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request): RedirectResponse
    {
        $this->orderService->createOrder($request->validated());

        return redirect()->route('orders.index')
            ->with('success', 'Pedido creado con éxito.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): Response
    {
        $order->load(['customer', 'items.product']);

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }

    /**
     * Update the status of the specified order.
     */
    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => 'required|in:PENDING,CONFIRMED,DELIVERED,CANCELLED',
        ]);

        // Aquí se actualiza el estado del pedido
        $this->orderService->updateStatus($order, $validated['status']);

        return redirect()->back()
            ->with('success', 'Estado del pedido actualizado a ' . $validated['status']);
    }
}
