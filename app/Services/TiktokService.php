<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class TiktokService
{
    protected string $appKey;
    protected string $appSecret;
    protected string $baseUrl;

    public function __construct()
    {
        $this->appKey = config('marketplace.tiktok.app_key', '');
        $this->appSecret = config('marketplace.tiktok.app_secret', '');
        $this->baseUrl = config('marketplace.tiktok.base_url', 'https://open-api.tiktokglobalshop.com');
    }

    /**
     * Menghasilkan URL otorisasi untuk TikTok Shop.
     *
     * @param string $redirectUrl
     * @param string $state
     * @return string
     */
    public function getAuthUrl(string $redirectUrl, string $state = ''): string
    {
        $authUrl = 'https://services.tiktokshop.com/open/authorize';
        
        return sprintf(
            '%s?app_key=%s&state=%s&redirect_uri=%s',
            $authUrl,
            $this->appKey,
            $state,
            urlencode($redirectUrl)
        );
    }

    /**
     * Mengambil access token berdasarkan auth code.
     *
     * @param string $authCode
     * @return array
     * @throws Exception
     */
    public function getToken(string $authCode): array
    {
        $apiPath = '/api/v2/token/get';
        
        // TikTok token get using GET query params or POST json, usually POST json or GET with query
        // According to docs it can be GET with auth_code and grant_type. 
        // Let's use get with query params
        $url = 'https://auth.tiktok-shops.com' . $apiPath . '?app_key=' . $this->appKey . '&app_secret=' . $this->appSecret . '&auth_code=' . $authCode . '&grant_type=authorized_code';

        $response = Http::get($url);

        if ($response->failed()) {
            throw new Exception('Gagal mendapatkan token TikTok: ' . $response->body());
        }

        $data = $response->json();

        if (isset($data['code']) && $data['code'] !== 0) {
            throw new Exception('Error dari TikTok: ' . ($data['message'] ?? 'Unknown error'));
        }

        return $data['data'] ?? [];
    }

    /**
     * Menghasilkan signature untuk API TikTok.
     *
     * @param string $apiPath
     * @param array $params
     * @return string
     */
    public function generateSign(string $apiPath, array $params, string $body = ''): string
    {
        $keysToExclude = ['access_token', 'sign'];
        $signParams = array_diff_key($params, array_flip($keysToExclude));
        
        ksort($signParams);
        
        $stringToBeSigned = $this->appSecret . $apiPath;
        foreach ($signParams as $k => $v) {
            if (is_array($v)) {
                $v = json_encode($v);
            }
            $stringToBeSigned .= $k . $v;
        }
        
        if ($body !== '') {
            $stringToBeSigned .= $body;
        }
        
        $stringToBeSigned .= $this->appSecret;

        return hash_hmac('sha256', $stringToBeSigned, $this->appSecret);
    }

    /**
     * Get Shop Info
     *
     * @param string $accessToken
     * @return array
     * @throws Exception
     */
    public function getShopInfo(string $accessToken): array
    {
        $apiPath = '/authorization/202309/shops';
        $timestamp = time();
        
        $params = [
            'app_key' => $this->appKey,
            'timestamp' => $timestamp,
        ];
        
        $sign = $this->generateSign($apiPath, $params);
        
        $url = $this->baseUrl . $apiPath . '?app_key=' . $this->appKey . '&timestamp=' . $timestamp . '&sign=' . $sign;
        
        $response = Http::withHeaders([
            'x-tts-access-token' => $accessToken,
        ])->get($url);

        if ($response->failed()) {
            throw new Exception('Gagal mendapatkan info toko TikTok: ' . $response->body());
        }

        $data = $response->json();
        
        if (isset($data['code']) && $data['code'] !== 0) {
            throw new Exception('Error dari TikTok: ' . ($data['message'] ?? 'Unknown error'));
        }

        return $data['data']['shops'][0] ?? [];
    }

    public function getOrders(string $accessToken, string $shopCipher, int $timeFrom, int $timeTo): array
    {
        $apiPath = '/order/202309/orders/search';
        $timestamp = time();

        $params = [
            'app_key' => $this->appKey,
            'timestamp' => $timestamp,
            'shop_cipher' => $shopCipher,
        ];

        $bodyArray = [
            'page_size' => 50,
            'create_time_ge' => $timeFrom,
            'create_time_lt' => $timeTo,
        ];
        $bodyJson = json_encode($bodyArray);

        $sign = $this->generateSign($apiPath, $params, $bodyJson);
        
        $url = $this->baseUrl . $apiPath . '?app_key=' . $this->appKey . '&timestamp=' . $timestamp . '&shop_cipher=' . rawurlencode($shopCipher) . '&sign=' . $sign;

        $response = \Illuminate\Support\Facades\Http::withHeaders([
            'x-tts-access-token' => $accessToken,
            'Content-Type' => 'application/json',
        ])->send('POST', $url, [
            'body' => $bodyJson
        ]);

        if ($response->failed()) {
            throw new Exception('Gagal mendapatkan daftar pesanan TikTok: HTTP ' . $response->status() . ' ' . $response->body());
        }

        $data = $response->json();
        if (isset($data['code']) && $data['code'] !== 0) {
            throw new Exception('Error dari TikTok: ' . ($data['message'] ?? 'Unknown error'));
        }

        return $data['data']['orders'] ?? [];
    }
}
