<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketplaceStore;
use App\Services\ShopeeService;
use App\Services\TiktokService;
use Exception;
use Illuminate\Support\Facades\Log;

class MarketplaceController extends Controller
{
    /**
     * Menampilkan daftar toko marketplace yang terhubung.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $stores = MarketplaceStore::all();
        return view('marketplace.index', compact('stores'));
    }

    /**
     * Redirect ke halaman otorisasi Shopee.
     *
     * @param ShopeeService $shopeeService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function connectShopee(ShopeeService $shopeeService)
    {
        $redirectUrl = config('marketplace.shopee.redirect_url') ?: route('marketplace.shopee.callback');
        $authUrl = $shopeeService->getAuthUrl($redirectUrl);
        
        return redirect()->away($authUrl);
    }

    /**
     * Menangani callback dari Shopee dan menyimpan token.
     *
     * @param Request $request
     * @param ShopeeService $shopeeService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function shopeeCallback(Request $request, ShopeeService $shopeeService)
    {
        $code = $request->query('code');
        $shopId = $request->query('shop_id');
        
        if (!$code || !$shopId) {
            return redirect()->route('marketplace.index')
                ->with('error', 'Gagal menghubungkan ke Shopee. Parameter tidak lengkap.');
        }

        try {
            $tokenData = $shopeeService->getToken($code, $shopId);
            
            $warning = '';
            // Coba ambil info toko (nama asli)
            $shopName = 'Shopee Store ' . $shopId;
            try {
                $shopInfo = $shopeeService->getShopInfo($shopId, $tokenData['access_token']);
                if (isset($shopInfo['shop_name']) && !empty($shopInfo['shop_name'])) {
                    $shopName = $shopInfo['shop_name'];
                }
            } catch (Exception $e) {
                Log::warning('Gagal mengambil nama toko Shopee: ' . $e->getMessage());
                $warning = ' (Info: Gagal mengambil profil toko - ' . $e->getMessage() . ')';
            }
            
            $store = MarketplaceStore::withTrashed()->where('platform', 'shopee')->where('shop_id', $shopId)->first();
            
            $storeData = [
                'shop_name' => $shopName,
                'access_token' => $tokenData['access_token'],
                'refresh_token' => $tokenData['refresh_token'],
                'token_expires_at' => now()->addSeconds($tokenData['expire_in']),
                'is_active' => true,
            ];

            if ($store) {
                if ($store->trashed()) {
                    $store->restore();
                }
                $store->update($storeData);
            } else {
                $storeData['platform'] = 'shopee';
                $storeData['shop_id'] = $shopId;
                MarketplaceStore::create($storeData);
            }

            return redirect()->route('marketplace.index')
                ->with('success', 'Toko Shopee berhasil dihubungkan.' . $warning);

        } catch (Exception $e) {
            Log::error('Shopee Callback Error: ' . $e->getMessage());
            return redirect()->route('marketplace.index')
                ->with('error', 'Gagal menghubungkan ke Shopee: ' . $e->getMessage());
        }
    }

    /**
     * Redirect ke halaman otorisasi TikTok.
     *
     * @param TiktokService $tiktokService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function connectTiktok(TiktokService $tiktokService)
    {
        $redirectUrl = config('marketplace.tiktok.redirect_url') ?: route('marketplace.tiktok.callback');
        $authUrl = $tiktokService->getAuthUrl($redirectUrl, 'connect_tiktok');
        
        return redirect()->away($authUrl);
    }

    /**
     * Menangani callback dari TikTok dan menyimpan token.
     *
     * @param Request $request
     * @param TiktokService $tiktokService
     * @return \Illuminate\Http\RedirectResponse
     */
    public function tiktokCallback(Request $request, TiktokService $tiktokService)
    {
        $code = $request->query('code');
        
        if (!$code) {
            return redirect()->route('marketplace.index')
                ->with('error', 'Gagal menghubungkan ke TikTok. Kode otorisasi tidak ditemukan.');
        }

        try {
            $tokenData = $tiktokService->getToken($code);
            
            $openId = $tokenData['open_id'] ?? null;
            $accessToken = $tokenData['access_token'] ?? null;
            $refreshToken = $tokenData['refresh_token'] ?? null;
            $expiresIn = $tokenData['access_token_expire_in'] ?? 0;
            
            if (!$openId || !$accessToken) {
                throw new Exception('Data token tidak valid dari TikTok.');
            }

            $warning = '';
            // Coba ambil info toko (nama asli dan cipher)
            $shopName = 'TikTok Store ' . $openId;
            $shopCipher = $openId; // fallback
            
            try {
                $shopInfo = $tiktokService->getShopInfo($accessToken);
                if (isset($shopInfo['name']) && !empty($shopInfo['name'])) {
                    $shopName = $shopInfo['name'];
                }
                if (isset($shopInfo['cipher']) && !empty($shopInfo['cipher'])) {
                    $shopCipher = $shopInfo['cipher'];
                }
            } catch (Exception $e) {
                Log::warning('Gagal mengambil nama toko TikTok: ' . $e->getMessage());
                $warning = ' (Info: Gagal mengambil profil toko - ' . $e->getMessage() . ')';
            }

            $store = MarketplaceStore::withTrashed()->where('platform', 'tiktok')->where('shop_id', $shopCipher)->first();
            
            $storeData = [
                'shop_name' => $shopName,
                'access_token' => $accessToken,
                'refresh_token' => $refreshToken,
                'token_expires_at' => now()->addSeconds($expiresIn),
                'is_active' => true,
            ];

            if ($store) {
                if ($store->trashed()) {
                    $store->restore();
                }
                $store->update($storeData);
            } else {
                $storeData['platform'] = 'tiktok';
                $storeData['shop_id'] = $shopCipher;
                MarketplaceStore::create($storeData);
            }

            return redirect()->route('marketplace.index')
                ->with('success', 'Toko TikTok berhasil dihubungkan.' . $warning);

        } catch (Exception $e) {
            Log::error('TikTok Callback Error: ' . $e->getMessage());
            return redirect()->route('marketplace.index')
                ->with('error', 'Gagal menghubungkan ke TikTok: ' . $e->getMessage());
        }
    }

    /**
     * Memutuskan koneksi toko.
     */
    public function disconnect($id)
    {
        $store = MarketplaceStore::findOrFail($id);
        $store->delete();
        return redirect()->route('marketplace.index')
            ->with('success', 'Koneksi dengan ' . ucfirst($store->platform) . ' berhasil diputuskan.');
    }

    public function sync($id)
    {
        $store = MarketplaceStore::findOrFail($id);
        
        \App\Jobs\SyncMarketplaceProductsJob::dispatchSync($store->id);
        
        return redirect()->route('marketplace.index')
            ->with('success', 'Sinkronisasi berhasil dijalankan.');
    }

    /**
     * Menampilkan daftar pemetaan produk WMS dengan Marketplace.
     */
    public function mapping()
    {
        $produks = \App\Models\Produk::with('marketplaceProducts')->paginate(20);
        return view('marketplace.mapping', compact('produks'));
    }
}
