<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search');
        $customers = $this->customerService->getPaginated($search);

        return response()->json($customers);
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer): JsonResponse
    {
        return response()->json($customer);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request): JsonResponse
    {
        $customer = $this->customerService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cliente creado con éxito.',
            'data' => $customer
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): JsonResponse
    {
        $updatedCustomer = $this->customerService->update($customer, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Cliente actualizado con éxito.',
            'data' => $updatedCustomer
        ]);
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy(Customer $customer): JsonResponse
    {
        $this->customerService->delete($customer);

        return response()->json([
            'success' => true,
            'message' => 'Cliente eliminado con éxito (lógicamente).'
        ]);
    }
}
