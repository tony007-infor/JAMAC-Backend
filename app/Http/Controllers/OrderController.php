<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $orders = $this->orderService->getPaginated($search);

        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->createOrder($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Pedido creado con éxito.',
            'data' => $order
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Order $order): JsonResponse
    {
        $order->load(['customer', 'items.product']);

        return response()->json($order);
    }

    /**
     * Update the specified resource in storage (status updates).
     */
    public function update(Request $request, Order $order): JsonResponse
    {
        $request->validate([
            'status' => 'required|in:PENDING,CONFIRMED,DELIVERED,CANCELLED',
        ]);

        $this->orderService->updateStatus($order, $request->input('status'));

        $updatedOrder = $order->fresh()->load(['customer', 'items.product']);

        return response()->json([
            'success' => true,
            'message' => 'Estado del pedido actualizado con éxito.',
            'data' => $updatedOrder
        ]);
    }
}
