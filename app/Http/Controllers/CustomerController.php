<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

/**
 * @OA\Info(
 *     title="Phoenix Orders - API JAMAC",
 *     version="1.0.0",
 *     description="Documentación de la API para la gestión de Clientes, Productos y Pedidos."
 * )
 * @OA\Server(
 *     url="http://localhost:8000",
 *     description="Servidor de Desarrollo Local"
 * )
 */


class CustomerController extends Controller
{
    public function __construct(
        protected CustomerService $customerService
    ) {}

    #[OA\Get(
        path: "/api/customers",
        summary: "Obtener lista de clientes",
        tags: ["Clientes"]
    )]
    #[OA\Response(
        response: 200,
        description: "Lista de clientes devuelta con éxito."
    )]
    #[OA\Response(
        response: 500,
        description: "Error interno del servidor."
    )]

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

    #[OA\Post(
        path: "/api/customers",
        summary: "Registrar un nuevo cliente",
        tags: ["Clientes"]
    )]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["name", "email"],
            properties: [
                new OA\Property(property: "name", type: "string", example: "Juan Pérez"),
                new OA\Property(property: "email", type: "string", example: "juan.perez@example.com"),
                new OA\Property(property: "phone", type: "string", example: "+59171234567")
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Cliente creado exitosamente."
    )]

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
