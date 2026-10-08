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

        // Dummy orders response simulating a marketplace API call
        $dummyOrders = [
            [
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'total_amount' => 150000,
                'buyer_name' => 'Budi Santoso',
                'items' => [
                    ['sku' => 'PL02BK40NK24', 'quantity' => 1, 'price' => 100000],
                    ['sku' => 'SKU-TEST-02', 'quantity' => 1, 'price' => 50000],
                ]
            ],
            [
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'total_amount' => 100000,
                'buyer_name' => 'Siti Aminah',
                'items' => [
                    ['sku' => 'PL02BK40NK24', 'quantity' => 1, 'price' => 100000],
                ]
            ]
        ];

        foreach ($dummyOrders as $orderData) {
            $existingOrder = Order::where('order_number', $orderData['order_number'])->first();
            
            if (!$existingOrder) {
                $order = Order::create([
                    'order_number' => $orderData['order_number'],
                    'platform' => $store->platform,
                    'status' => 'pending',
                    'total_amount' => $orderData['total_amount'],
                    'buyer_name' => $orderData['buyer_name'],
                    'order_date' => now(),
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
