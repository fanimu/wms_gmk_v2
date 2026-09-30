<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Modifikasi tabel users untuk menambahkan role, phone, status aktif, dan last login.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'staff', 'viewer'])->default('staff')->after('email');
            $table->string('phone', 20)->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('phone');
            $table->timestamp('last_login_at')->nullable()->after('remember_token');

            $table->index('role');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropIndex(['is_active']);
            $table->dropColumn(['role', 'phone', 'is_active', 'last_login_at']);
        });
    }
};
