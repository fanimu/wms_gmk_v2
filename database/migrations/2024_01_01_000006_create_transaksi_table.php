<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel transaksi untuk mencatat mutasi stok (masuk, keluar, retur, adjustment).
     */
    public function up(): void
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id();
            $table->string('kode_transaksi', 30)->unique(); // e.g. TRX-IN-20240101-001
            $table->date('tanggal');
            $table->enum('jenis', ['MASUK', 'KELUAR', 'RETUR', 'ADJUSTMENT']);
            $table->foreignId('supplier_id')->nullable()->constrained('supplier')->nullOnDelete();
            $table->foreignId('gudang_id')->nullable()->constrained('gudang')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['DRAFT', 'CONFIRMED', 'CANCELLED'])->default('DRAFT');
            $table->timestamp('confirmed_at')->nullable();
            $table->foreignId('confirmed_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index('tanggal');
            $table->index('jenis');
            $table->index('status');
            $table->index('supplier_id');
            $table->index('gudang_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksi');
    }
};
