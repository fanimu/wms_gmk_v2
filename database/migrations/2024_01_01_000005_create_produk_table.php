<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel produk untuk menyimpan data barang/apparel.
     */
    public function up(): void
    {
        Schema::create('produk', function (Blueprint $table) {
            $table->id();
            $table->string('sku', 50)->unique(); // e.g. PL01BK36NK24
            $table->string('nama_produk', 150);
            $table->foreignId('kategori_id')->nullable()->constrained('kategori')->nullOnDelete();
            $table->foreignId('gudang_id')->nullable()->constrained('gudang')->nullOnDelete();
            $table->enum('gender', ['PRIA', 'WANITA', 'UNISEX'])->nullable();
            $table->string('merk', 100)->nullable(); // e.g. Blue Button
            $table->string('warna', 50)->nullable();
            $table->string('ukuran', 20)->nullable(); // S, M, L, XL, XXL
            $table->tinyInteger('urutan_ukuran')->nullable(); // (1=XS, 2=S, 3=M, 4=L, 5=XL, 6=XXL)
            $table->string('grup_produk', 100)->nullable(); // e.g. POLO
            $table->decimal('harga_beli', 15, 2)->default(0);
            $table->decimal('harga_jual', 15, 2)->default(0);
            $table->integer('stok')->default(0); // stok saat ini
            $table->integer('stok_minimum')->default(0);
            $table->string('gambar', 255)->nullable();
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
            $table->index('merk');
            $table->index('gender');
            $table->index('gudang_id');
            $table->index('kategori_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('produk');
    }
};
