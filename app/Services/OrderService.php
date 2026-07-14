<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderService
{
    /**
     * Get paginated list of orders with optional search filtering.
     */
    public function getPaginated(string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        return Order::query()
            ->with(['customer'])
            ->when($search, function ($query, $search) {
                $query->whereHas('customer', function ($q) use ($search) {
                    $q->where('full_name', 'like', "%{$search}%");
                })
                ->orWhere('status', 'like', "%{$search}%")
                ->orWhereRaw('CAST(id AS TEXT) LIKE ?', ["%{$search}%"]);
            })
            ->orderBy('order_date', 'desc')
            ->paginate($perPage)
            ->withQueryString();
    }

    /**
     * Create an order in a database transaction, updating stock and recalculating prices.
     */
    public function createOrder(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // 1. Verify Customer exists and is active
            $customer = Customer::where('id', $data['customer_id'])
                ->where('is_active', true)
                ->first();

            if (!$customer) {
                throw ValidationException::withMessages([
                    'customer_id' => ['El cliente seleccionado no existe o no está activo.'],
                ]);
            }

            // 2. Create the Order with default values
            $order = Order::create([
                'customer_id' => $customer->id,
                'order_date' => now(),
                'status' => 'PENDING',
                'total' => 0.00,
            ]);

            $total = 0.00;

            // 3. Process items
            foreach ($data['items'] as $item) {
                $productId = $item['product_id'];
                $quantity = (int) $item['quantity'];

                if ($quantity <= 0) {
                    throw ValidationException::withMessages([
                        'items' => ["La cantidad del producto debe ser mayor a 0."],
                    ]);
                }

                // Apply Pessimistic Locking to prevent concurrency race conditions
                $product = Product::where('id', $productId)
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw ValidationException::withMessages([
                        'items' => ["El producto seleccionado no existe."],
                    ]);
                }

                if (!$product->is_active) {
                    throw ValidationException::withMessages([
                        'items' => ["El producto '{$product->name}' no está activo."],
                    ]);
                }

                // Verify stock availability
                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => ["Stock insuficiente para '{$product->name}'. Solicitado: {$quantity}, Disponible: {$product->stock}."],
                    ]);
                }

                // Deduct stock
                $product->decrement('stock', $quantity);

                // Recalculate using database price (ignore frontend prices)
                $unitPrice = $product->price;
                $subTotal = $unitPrice * $quantity;
                $total += $subTotal;

                // Create OrderItem record
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'sub_total' => $subTotal,
                ]);
            }

            // Update final total
            $order->update(['total' => $total]);

            return $order;
        });
    }
}
