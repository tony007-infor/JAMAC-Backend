<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('customer_id')->constrained('customers')->onDelete('restrict');
            $table->timestamp('order_date')->useCurrent();
            $table->string('status')->default('PENDING');
            $table->decimal('total', 10, 2)->default(0.00);
            $table->timestamps();
        });

        // Enforce status values
        DB::statement("ALTER TABLE orders ADD CONSTRAINT orders_status_check CHECK (status IN ('PENDING', 'CONFIRMED', 'DELIVERED', 'CANCELLED'))");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
