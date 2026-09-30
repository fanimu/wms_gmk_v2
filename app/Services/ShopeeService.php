<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class ShopeeService
{
    protected string $partnerId;
    protected string $partnerKey;
    protected string $baseUrl;

    public function __construct()
    {
        $this->partnerId = config('marketplace.shopee.partner_id', '');
        $this->partnerKey = config('marketplace.shopee.partner_key', '');
        $this->baseUrl = config('marketplace.shopee.base_url', 'https://partner.shopeemobile.com');
    }

    /**
     * Menghasilkan signature untuk API Shopee.
     *
     * @param string $apiPath
     * @param int $timestamp
     * @param string|null $accessToken
     * @param string|null $shopId
     * @return string
     */
    public function generateSign(string $apiPath, int $timestamp, ?string $accessToken = '', ?string $shopId = ''): string
    {
        $baseString = $this->partnerId . $apiPath . $timestamp . $accessToken . $shopId;
        return hash_hmac('sha256', $baseString, $this->partnerKey);
    }

    /**
     * Menghasilkan URL otorisasi untuk Shopee.
     *
     * @param string $redirectUrl
     * @return string
     */
    public function getAuthUrl(string $redirectUrl): string
    {
        $apiPath = '/api/v2/shop/auth_partner';
        $timestamp = time();
        $sign = $this->generateSign($apiPath, $timestamp);

        return sprintf(
            '%s%s?partner_id=%s&timestamp=%s&sign=%s&redirect=%s',
            $this->baseUrl,
            $apiPath,
            $this->partnerId,
            $timestamp,
            $sign,
            urlencode($redirectUrl)
        );
    }

    /**
     * Mengambil access token berdasarkan auth code.
     *
     * @param string $authCode
     * @param string $shopId
     * @return array
     * @throws Exception
     */
    public function getToken(string $authCode, string $shopId): array
    {
        $apiPath = '/api/v2/auth/token/get';
        $timestamp = time();
        $sign = $this->generateSign($apiPath, $timestamp);

        $response = Http::post($this->baseUrl . $apiPath . '?partner_id=' . $this->partnerId . '&timestamp=' . $timestamp . '&sign=' . $sign, [
            'code' => $authCode,
            'shop_id' => (int) $shopId,
            'partner_id' => (int) $this->partnerId,
        ]);

        if ($response->failed()) {
            throw new Exception('Gagal mendapatkan token Shopee: ' . $response->body());
        }

        $data = $response->json();

        if (isset($data['error']) && $data['error'] !== '') {
            throw new Exception('Error dari Shopee: ' . ($data['message'] ?? $data['error']));
        }

        return $data;
    }
}
