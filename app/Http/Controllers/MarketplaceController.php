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
            
            MarketplaceStore::updateOrCreate(
                [
                    'platform' => 'shopee',
                    'shop_id' => $shopId,
                ],
                [
                    'shop_name' => 'Shopee Store ' . $shopId,
                    'access_token' => $tokenData['access_token'],
                    'refresh_token' => $tokenData['refresh_token'],
                    'token_expires_at' => now()->addSeconds($tokenData['expire_in']),
                    'status' => 'active',
                ]
            );

            return redirect()->route('marketplace.index')
                ->with('success', 'Toko Shopee berhasil dihubungkan.');

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

            MarketplaceStore::updateOrCreate(
                [
                    'platform' => 'tiktok',
                    'shop_id' => $openId,
                ],
                [
                    'shop_name' => 'TikTok Store ' . $openId,
                    'access_token' => $accessToken,
                    'refresh_token' => $refreshToken,
                    'token_expires_at' => now()->addSeconds($expiresIn),
                    'status' => 'active',
                ]
            );

            return redirect()->route('marketplace.index')
                ->with('success', 'Toko TikTok berhasil dihubungkan.');

        } catch (Exception $e) {
            Log::error('TikTok Callback Error: ' . $e->getMessage());
            return redirect()->route('marketplace.index')
                ->with('error', 'Gagal menghubungkan ke TikTok: ' . $e->getMessage());
        }
    }
}
