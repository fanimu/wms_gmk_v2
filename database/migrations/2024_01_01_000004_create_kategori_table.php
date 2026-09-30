<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel kategori untuk mengelompokkan produk.
     */
    public function up(): void
    {
        Schema::create('kategori', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori', 100); // e.g. PAKAIAN, AKSESORIS
            $table->string('sub_kategori', 100)->nullable(); // e.g. POLO SHIRT, KAOS
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->unique(['nama_kategori', 'sub_kategori']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori');
    }
};
