<?php

namespace App\Http\Controllers;

use App\Models\Order;
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
}
