<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Customer $customer;
    protected Product $product1;
    protected Product $product2;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a user for authentication
        $this->user = User::factory()->create();

        // Create an active customer
        $this->customer = Customer::create([
            'full_name' => 'Juan Perez',
            'email' => 'juan@example.com',
            'phone' => '123456789',
            'is_active' => true,
        ]);

        // Create products
        $this->product1 = Product::create([
            'name' => 'Laptop HP',
            'description' => 'Laptop de oficina',
            'price' => 800.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        $this->product2 = Product::create([
            'name' => 'Mouse USB',
            'description' => 'Mouse óptico',
            'price' => 20.00,
            'stock' => 5,
            'is_active' => true,
        ]);
    }

    public function test_an_authenticated_user_can_create_a_valid_order()
    {
        $response = $this->actingAs($this->user)->post(route('orders.store'), [
            'customer_id' => $this->customer->id,
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 2,
                ],
                [
                    'product_id' => $this->product2->id,
                    'quantity' => 3,
                ],
            ],
        ]);

        $response->assertStatus(201);
        $response->assertJsonPath('success', true);

        // Assert order was written to database
        $this->assertDatabaseCount('orders', 1);
        $order = Order::first();
        
        // Assert total calculated strictly on backend (800 * 2 + 20 * 3 = 1660.00)
        $this->assertEquals(1660.00, $order->total);
        $this->assertEquals('PENDING', $order->status);

        // Assert order items count
        $this->assertDatabaseCount('order_items', 2);

        // Assert stock decremented
        $this->assertEquals(8, $this->product1->fresh()->stock);
        $this->assertEquals(2, $this->product2->fresh()->stock);
    }

    public function test_order_creation_ignores_frontend_prices()
    {
        // Try sending mock prices from frontend (e.g. price $10.00 instead of $800.00)
        $response = $this->actingAs($this->user)->post(route('orders.store'), [
            'customer_id' => $this->customer->id,
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 1,
                    'unit_price' => 10.00, // Frontend spoofed price
                ],
            ],
        ]);

        $response->assertStatus(201);

        // Backend price should be $800.00, not $10.00
        $order = Order::first();
        $this->assertEquals(800.00, $order->total);
        
        $item = $order->items()->first();
        $this->assertEquals(800.00, $item->unit_price);
        $this->assertEquals(800.00, $item->sub_total);
    }

    public function test_order_fails_and_rolls_back_if_any_product_is_out_of_stock()
    {
        // Product 2 has only 5 in stock. We request 6.
        $response = $this->actingAs($this->user)->post(route('orders.store'), [
            'customer_id' => $this->customer->id,
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 1, // Valid quantity (stock is 10)
                ],
                [
                    'product_id' => $this->product2->id,
                    'quantity' => 6, // Invalid quantity (stock is 5)
                ],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['items']);
        
        // Assert transaction rolled back (No orders created)
        $this->assertDatabaseCount('orders', 0);
        $this->assertDatabaseCount('order_items', 0);

        // Assert stocks are unmodified (No stock deducted because of rollback)
        $this->assertEquals(10, $this->product1->fresh()->stock);
        $this->assertEquals(5, $this->product2->fresh()->stock);
    }

    public function test_order_fails_if_customer_is_inactive()
    {
        $this->customer->update(['is_active' => false]);

        $response = $this->actingAs($this->user)->post(route('orders.store'), [
            'customer_id' => $this->customer->id,
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 1,
                ],
            ],
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['customer_id']);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_order_fails_if_cart_is_empty()
    {
        $response = $this->actingAs($this->user)->post(route('orders.store'), [
            'customer_id' => $this->customer->id,
            'items' => [], // Empty items
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['items']);
        $this->assertDatabaseCount('orders', 0);
    }

    public function test_an_authenticated_user_can_update_order_status()
    {
        // Creamos un pedido de prueba
        $order = Order::create([
            'customer_id' => $this->customer->id,
            'total' => 1660.00,
            'status' => 'PENDING',
            'order_date' => now(),
        ]);

        // Intentamos cambiar el estado a CONFIRMED
        $response = $this->actingAs($this->user)->patch(route('orders.update', $order->id), [
            'status' => 'CONFIRMED',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('CONFIRMED', $order->fresh()->status);
    }

    public function test_cancelling_an_order_restores_product_stock()
    {
        // Creamos un pedido pendiente en la base de datos
        $order = Order::create([
            'customer_id' => $this->customer->id,
            'total' => 800.00,
            'status' => 'PENDING',
            'order_date' => now(),
        ]);

        // Le asociamos un producto con cantidad 2 (el stock inicial en setUp es 10)
        $order->items()->create([
            'product_id' => $this->product1->id,
            'quantity' => 2,
            'unit_price' => 400.00,
            'sub_total' => 800.00,
        ]);

        // Cancelamos el pedido
        $response = $this->actingAs($this->user)->patch(route('orders.update', $order->id), [
            'status' => 'CANCELLED',
        ]);

        $response->assertStatus(200);
        $this->assertEquals('CANCELLED', $order->fresh()->status);
        
        // REGLA DE NEGOCIO: El stock debió restaurarse (10 originales + 2 devueltos = 12)
        $this->assertEquals(12, $this->product1->fresh()->stock);
    }

    public function test_unauthenticated_requests_are_blocked()
    {
        $response = $this->postJson(route('orders.store'), [
            'customer_id' => $this->customer->id,
            'items' => [
                [
                    'product_id' => $this->product1->id,
                    'quantity' => 1,
                ]
            ]
        ]);

        $response->assertStatus(401);
    }
}
