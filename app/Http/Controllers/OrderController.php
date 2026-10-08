<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\MarketplaceStore;
use App\Jobs\FetchMarketplaceOrdersJob;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Menampilkan daftar pesanan.
     */
    public function index(Request $request)
    {
        $query = Order::with('items')->orderBy('order_date', 'desc');

        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        if ($request->filled('platform') && $request->platform !== 'semua') {
            $query->where('platform', $request->platform);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('order.index', compact('orders'));
    }

    /**
     * Menampilkan detail pesanan.
     */
    public function show($id)
    {
        $order = Order::with('items.produk')->findOrFail($id);

        return view('order.show', compact('order'));
    }

    /**
     * Memperbarui status pesanan.
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled,returned'
        ]);

        // Catatan: Untuk saat ini kita hanya mengupdate status pesanan tanpa mengurangi stok fisik secara kompleks.
        $order->status = $request->status;
        $order->save();

        return redirect()->back()->with('success', 'Status pesanan berhasil diperbarui menjadi ' . $request->status . '.');
    }

    /**
     * Menarik pesanan terbaru dari marketplace.
     */
    public function fetch(Request $request)
    {
        $stores = MarketplaceStore::all();
        $errors = [];
        
        foreach ($stores as $store) {
            try {
                FetchMarketplaceOrdersJob::dispatchSync($store->id);
            } catch (\Exception $e) {
                $errors[] = $store->platform . ': ' . $e->getMessage();
            }
        }
        
        if (!empty($errors)) {
            return redirect()->back()->with('error', 'Ada masalah saat menarik data asli: ' . implode(' | ', $errors));
        }
        
        return redirect()->back()->with('success', 'Berhasil menarik pesanan asli dari marketplace.');
    }

    public function clearOrders()
    {
        \App\Models\OrderItem::truncate();
        \App\Models\Order::query()->forceDelete();
        
        return redirect()->back()->with('success', 'Semua pesanan (termasuk data dummy) berhasil dibersihkan.');
    }
}
