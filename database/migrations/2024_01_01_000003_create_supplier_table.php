<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel supplier untuk menyimpan data master pemasok.
     */
    public function up(): void
    {
        Schema::create('supplier', function (Blueprint $table) {
            $table->id();
            $table->string('kode_supplier', 20)->unique(); // format: SUP-001
            $table->string('nama_supplier', 100);
            $table->string('kontak_person', 100)->nullable(); // nama kontak
            $table->string('telepon', 20)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('supplier');
    }
};
