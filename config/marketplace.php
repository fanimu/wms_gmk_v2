<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Konfigurasi Integrasi Marketplace
    |--------------------------------------------------------------------------
    |
    | File ini berisi pengaturan untuk koneksi API ke Shopee dan TikTok Shop.
    |
    */

    'shopee' => [
        'partner_id' => env('SHOPEE_PARTNER_ID'),
        'partner_key' => env('SHOPEE_PARTNER_KEY'),
        'base_url' => env('SHOPEE_API_URL', 'https://partner.shopeemobile.com'),
        'redirect_url' => env('SHOPEE_REDIRECT_URL'),
    ],

    'tiktok' => [
        'app_key' => env('TIKTOK_APP_KEY'),
        'app_secret' => env('TIKTOK_APP_SECRET'),
        'base_url' => env('TIKTOK_API_URL', 'https://open-api.tiktokglobalshop.com'),
        'redirect_url' => env('TIKTOK_REDIRECT_URL'),
    ],

];
