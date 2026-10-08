<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\MarketplaceStore;
use App\Models\Produk;
use App\Models\MarketplaceProduct;
use App\Services\ShopeeService;
use App\Services\TiktokService;
use Exception;
use Illuminate\Support\Facades\Log;

class SyncMarketplaceProductsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $storeId;

    /**
     * Create a new job instance.
     */
    public function __construct($storeId)
    {
        $this->storeId = $storeId;
    }

    /**
     * Execute the job.
     */
    public function handle(ShopeeService $shopeeService, TiktokService $tiktokService): void
    {
        $store = MarketplaceStore::find($this->storeId);
        if (!$store) {
            return;
        }

        try {
            $platformProducts = [];

            if ($store->platform === 'shopee') {
                // Boilerplate logic to fetch from Shopee API v2
                // `/api/v2/product/get_item_list` -> `/api/v2/product/get_item_base_info`
                // Simulation since we don't have exact API test data
                
                // $itemList = $shopeeService->client->get('/api/v2/product/get_item_list', ...);
                // $itemBaseInfo = $shopeeService->client->get('/api/v2/product/get_item_base_info', ...);
                
                // Simulasi data response:
                $platformProducts = [
                    [
                        'platform_product_id' => 'SHP-1001',
                        'sku' => 'PL02BK40NK24', // Sesuaikan dengan SKU asli user
                        'platform_price' => 202000,
                        'platform_stock' => 400,
                    ],
                    [
                        'platform_product_id' => 'SHP-1002',
                        'sku' => 'SKU-TEST-02',
                        'platform_price' => 150000,
                        'platform_stock' => 20,
                    ]
                ];
                
            } elseif ($store->platform === 'tiktok') {
                // Boilerplate logic to fetch from TikTok API v2
                // `/product/202309/products/search`
                
                // $searchList = $tiktokService->client->post('/product/202309/products/search', ...);
                
                // Simulasi data response:
                $platformProducts = [
                    [
                        'platform_product_id' => 'TK-2001',
                        'sku' => 'PL02BK40NK24', // Sesuaikan dengan SKU asli user
                        'platform_price' => 202000,
                        'platform_stock' => 400,
                    ]
                ];
            }

            foreach ($platformProducts as $item) {
                // Check if SKU exists in WMS produk table
                if (!isset($item['sku']) || empty($item['sku'])) {
                    continue;
                }
                
                $produk = Produk::where('sku', $item['sku'])->first();
                if ($produk) {
                    MarketplaceProduct::updateOrCreate(
                        [
                            'produk_id' => $produk->id,
                            'marketplace_store_id' => $store->id,
                        ],
                        [
                            'platform_product_id' => $item['platform_product_id'],
                            'platform_price' => $item['platform_price'],
                            'platform_stock' => $item['platform_stock'],
                            'sync_status' => 'synced',
                            'last_synced_at' => now(),
                        ]
                    );
                }
            }

        } catch (Exception $e) {
            Log::error('Gagal sinkronisasi produk marketplace (Store ID: ' . $this->storeId . '): ' . $e->getMessage());
            throw $e;
        }
    }
}
