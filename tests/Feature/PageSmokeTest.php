<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Smoke test: memastikan semua halaman utama bisa dibuka (HTTP 200)
 * oleh admin tanpa error. Jalankan sebelum setiap deploy.
 */
class PageSmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public static function pageProvider(): array
    {
        return [
            'dashboard'          => ['/dashboard'],
            'gudang index'       => ['/gudang'],
            'gudang create'      => ['/gudang/create'],
            'gudang show'        => ['/gudang/1'],
            'gudang edit'        => ['/gudang/1/edit'],
            'supplier index'     => ['/supplier'],
            'supplier create'    => ['/supplier/create'],
            'supplier show'      => ['/supplier/1'],
            'supplier edit'      => ['/supplier/1/edit'],
            'produk index'       => ['/produk'],
            'produk create'      => ['/produk/create'],
            'transaksi index'    => ['/transaksi'],
            'transaksi create'   => ['/transaksi/create'],
            'marketplace index'  => ['/marketplace'],
            'laporan stok'       => ['/laporan/stok'],
            'laporan transaksi'  => ['/laporan/transaksi'],
            'laporan penjualan'  => ['/laporan/penjualan'],
            'profile'            => ['/profile'],
        ];
    }

    /**
     * @dataProvider pageProvider
     */
    public function test_halaman_bisa_dibuka_oleh_admin(string $url): void
    {
        $admin = User::where('email', 'admin@gmk.co.id')->firstOrFail();

        $this->actingAs($admin)->get($url)->assertOk();
    }
}
