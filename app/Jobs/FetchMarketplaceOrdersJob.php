<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\MarketplaceStore;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Produk;
use Illuminate\Support\Facades\Log;

class FetchMarketplaceOrdersJob implements ShouldQueue
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
    public function handle(): void
    {
        $store = MarketplaceStore::find($this->storeId);
        
        if (!$store) {
            return;
        }
        
        Log::info("Fetching orders for store: {$store->name} ({$store->platform})");

        $timeFrom = now()->subDays(3)->timestamp; // Tarik pesanan 3 hari terakhir
        $timeTo = now()->timestamp;
        
        $fetchedOrders = [];

        try {
            if ($store->platform === 'shopee') {
                $shopeeService = app(\App\Services\ShopeeService::class);
                $orders = $shopeeService->getOrderList($store->shop_id, $store->access_token, $timeFrom, $timeTo);
                
                if (!empty($orders)) {
                    $orderSnList = array_column($orders, 'order_sn');
                    $orderDetails = $shopeeService->getOrderDetail($store->shop_id, $store->access_token, $orderSnList);
                    
                    foreach ($orderDetails as $detail) {
                        $items = [];
                        foreach ($detail['item_list'] as $item) {
                            $items[] = [
                                'sku' => $item['model_sku'] ?: $item['item_sku'],
                                'quantity' => $item['model_quantity_purchased'],
                                'price' => $item['model_discounted_price'],
                            ];
                        }
                        
                        $fetchedOrders[] = [
                            'order_number' => $detail['order_sn'],
                            'total_amount' => $detail['total_amount'],
                            'buyer_name' => $detail['buyer_username'] ?? 'Shopee Buyer',
                            'order_date' => date('Y-m-d H:i:s', $detail['create_time']),
                            'items' => $items,
                        ];
                    }
                }
                
            } elseif ($store->platform === 'tiktok') {
                $tiktokService = app(\App\Services\TiktokService::class);
                $orders = $tiktokService->getOrders($store->access_token, $store->shop_id, $timeFrom, $timeTo);
                
                foreach ($orders as $order) {
                    $items = [];
                    foreach ($order['item_list'] as $item) {
                        $items[] = [
                            'sku' => $item['sku_id'], // or seller_sku depending on tiktok API response
                            'quantity' => $item['quantity'],
                            'price' => $item['sku_original_price'],
                        ];
                    }
                    
                    $fetchedOrders[] = [
                        'order_number' => $order['order_id'],
                        'total_amount' => $order['payment']['total_amount'] ?? 0,
                        'buyer_name' => $order['buyer_email'] ?? 'TikTok Buyer',
                        'order_date' => date('Y-m-d H:i:s', $order['create_time'] / 1000), // TikTok returns ms
                        'items' => $items,
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::error("Failed fetching live orders for {$store->platform}: " . $e->getMessage());
            return;
        }

        foreach ($fetchedOrders as $orderData) {
            $existingOrder = Order::where('order_number', $orderData['order_number'])->first();
            
            if (!$existingOrder) {
                $order = Order::create([
                    'order_number' => $orderData['order_number'],
                    'platform' => $store->platform,
                    'status' => 'pending',
                    'total_amount' => $orderData['total_amount'],
                    'buyer_name' => $orderData['buyer_name'],
                    'order_date' => $orderData['order_date'],
                ]);

                foreach ($orderData['items'] as $itemData) {
                    $produk = Produk::where('sku', $itemData['sku'])->first();
                    
                    if ($produk) {
                        OrderItem::create([
                            'order_id' => $order->id,
                            'produk_id' => $produk->id,
                            'product_name' => $produk->nama_produk,
                            'sku' => $produk->sku,
                            'quantity' => $itemData['quantity'],
                            'price' => $itemData['price'],
                        ]);
                        
                        // Deduct stock
                        $oldStok = $produk->stok;
                        $produk->stok -= $itemData['quantity'];
                        $produk->save();
                        
                        // Add Activity Log
                        \App\Models\ActivityLog::create([
                            'user_id' => 1, // System admin for background job
                            'action' => 'ORDER_SYNC',
                            'module' => 'Pesanan',
                            'description' => "Stok otomatis berkurang {$itemData['quantity']} pcs dari pesanan #{$order->order_number} ({$store->platform})",
                            'model_type' => get_class($produk),
                            'model_id' => $produk->id,
                            'old_data' => ['stok' => $oldStok],
                            'new_data' => ['stok' => $produk->stok],
                            'ip_address' => '127.0.0.1',
                        ]);
                    } else {
                        Log::warning("Product with SKU {$itemData['sku']} not found for order {$order->order_number}");
                    }
                }
            }
        }
    }
}
