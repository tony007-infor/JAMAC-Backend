<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $search = $request->input('search');
        $customers = $this->customerService->getPaginated($search);

        return Inertia::render('Customers/Index', [
            'customers' => $customers,
            'filters' => [
                'search' => $search,
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        $this->customerService->create($request->validated());

        return redirect()->route('customers.index')
            ->with('success', 'Cliente creado con éxito.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        $this->customerService->update($customer, $request->validated());

        return redirect()->route('customers.index')
            ->with('success', 'Cliente actualizado con éxito.');
    }

    /**
     * Remove the specified resource from storage (Soft Delete).
     */
    public function destroy(Customer $customer): RedirectResponse
    {
        $this->customerService->delete($customer);

        return redirect()->route('customers.index')
            ->with('success', 'Cliente eliminado con éxito (lógicamente).');
    }
}
