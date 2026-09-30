<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel pesanan (order) dari marketplace atau manual.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 50)->unique();
            $table->foreignId('store_id')->nullable()->constrained('marketplace_stores')->nullOnDelete();
            $table->enum('platform', ['shopee', 'tiktok', 'manual']);
            $table->string('platform_order_id', 100)->nullable();
            $table->enum('status', ['pending', 'processing', 'shipped', 'delivered', 'cancelled', 'returned'])->default('pending');
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('shipping_fee', 15, 2)->default(0);
            $table->string('buyer_name', 150)->nullable();
            $table->string('buyer_phone', 20)->nullable();
            $table->text('buyer_address')->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->string('shipping_provider', 100)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('order_date')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('platform');
            $table->index('status');
            $table->index('store_id');
            $table->index('order_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
