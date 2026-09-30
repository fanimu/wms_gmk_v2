<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel gudang untuk menyimpan data master gudang.
     */
    public function up(): void
    {
        Schema::create('gudang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_gudang', 20)->unique(); // format: GDG-001
            $table->string('nama_gudang', 100);
            $table->string('lokasi', 255);
            $table->string('penanggung_jawab', 100)->nullable(); // person in charge
            $table->string('telepon', 20)->nullable();
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
        Schema::dropIfExists('gudang');
    }
};
