<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel untuk sinkronisasi produk dengan marketplace.
     */
    public function up(): void
    {
        Schema::create('marketplace_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produk_id')->constrained('produk')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('marketplace_stores')->cascadeOnDelete();
            $table->string('platform_product_id', 100)->nullable();
            $table->decimal('platform_price', 15, 2)->default(0);
            $table->integer('platform_stock')->default(0);
            $table->enum('sync_status', ['synced', 'pending', 'error'])->default('pending');
            $table->timestamp('last_synced_at')->nullable();
            $table->text('sync_error')->nullable(); // pesan error terakhir
            $table->timestamps();

            $table->unique(['produk_id', 'store_id']);
            $table->index('sync_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_products');
    }
};
