<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel untuk konfigurasi toko di marketplace (Shopee, TikTok, dll).
     */
    public function up(): void
    {
        Schema::create('marketplace_stores', function (Blueprint $table) {
            $table->id();
            $table->enum('platform', ['shopee', 'tiktok']);
            $table->string('shop_id', 100);
            $table->string('shop_name', 150);
            $table->text('access_token')->nullable(); // terenkripsi
            $table->text('refresh_token')->nullable(); // terenkripsi
            $table->timestamp('token_expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_sync_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['platform', 'shop_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('marketplace_stores');
    }
};
