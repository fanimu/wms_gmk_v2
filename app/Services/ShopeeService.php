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

    /**
     * Get Shop Info
     *
     * @param string $shopId
     * @param string $accessToken
     * @return array
     * @throws Exception
     */
    public function getShopInfo(string $shopId, string $accessToken): array
    {
        $apiPath = '/api/v2/shop/get_shop_info';
        $timestamp = time();
        $sign = $this->generateSign($apiPath, $timestamp, $accessToken, $shopId);

        $url = sprintf(
            '%s%s?partner_id=%s&timestamp=%s&access_token=%s&shop_id=%s&sign=%s',
            $this->baseUrl,
            $apiPath,
            $this->partnerId,
            $timestamp,
            $accessToken,
            $shopId,
            $sign
        );

        $response = Http::get($url);

        if ($response->failed()) {
            throw new Exception('Gagal mendapatkan info toko Shopee: ' . $response->body());
        }

        $data = $response->json();
        
        if (isset($data['error']) && $data['error'] !== '') {
            throw new Exception('Error dari Shopee: ' . ($data['message'] ?? $data['error']));
        }

        return $data;
    }

    public function getOrderList(string $shopId, string $accessToken, int $timeFrom, int $timeTo): array
    {
        $apiPath = '/api/v2/order/get_order_list';
        $timestamp = time();
        $sign = $this->generateSign($apiPath, $timestamp, $accessToken, $shopId);

        $url = sprintf(
            '%s%s?partner_id=%s&timestamp=%s&access_token=%s&shop_id=%s&sign=%s&time_range_field=create_time&time_from=%s&time_to=%s&page_size=50',
            $this->baseUrl,
            $apiPath,
            $this->partnerId,
            $timestamp,
            $accessToken,
            $shopId,
            $sign,
            $timeFrom,
            $timeTo
        );

        $response = \Illuminate\Support\Facades\Http::get($url);

        if ($response->failed()) {
            throw new Exception('Gagal mendapatkan daftar pesanan Shopee: ' . $response->body());
        }

        $data = $response->json();
        if (isset($data['error']) && $data['error'] !== '') {
            throw new Exception('Error dari Shopee: ' . ($data['message'] ?? $data['error']));
        }

        return $data['response']['order_list'] ?? [];
    }

    public function getOrderDetail(string $shopId, string $accessToken, array $orderSnList): array
    {
        $apiPath = '/api/v2/order/get_order_detail';
        $timestamp = time();
        $sign = $this->generateSign($apiPath, $timestamp, $accessToken, $shopId);

        $orderSnString = implode(',', $orderSnList);

        $url = sprintf(
            '%s%s?partner_id=%s&timestamp=%s&access_token=%s&shop_id=%s&sign=%s&response_optional_fields=buyer_user_id,buyer_username,item_list&order_sn_list=%s',
            $this->baseUrl,
            $apiPath,
            $this->partnerId,
            $timestamp,
            $accessToken,
            $shopId,
            $sign,
            $orderSnString
        );

        $response = \Illuminate\Support\Facades\Http::get($url);

        if ($response->failed()) {
            throw new Exception('Gagal mendapatkan detail pesanan Shopee: ' . $response->body());
        }

        $data = $response->json();
        if (isset($data['error']) && $data['error'] !== '') {
            throw new Exception('Error dari Shopee: ' . ($data['message'] ?? $data['error']));
        }

        return $data['response']['order_list'] ?? [];
    }
}
