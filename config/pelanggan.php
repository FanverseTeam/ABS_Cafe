<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Slot Foto Publik (Pelanggan)
    |--------------------------------------------------------------------------
    |
    | Setiap slot memiliki prioritas pemuatan:
    | 1. Berkas lokal di public/images/pelanggan/{slot}.(webp|jpg|jpeg|png)
    | 2. URL langsung sementara (Unsplash https aman dengan parameter crop)
    | 3. Fallback CSS gradient Midnight Violet - Olive Wood saat gagal memuat
    |
    | Untuk mengganti foto, cukup letakkan berkas dengan nama slot di:
    | public/images/pelanggan/{slot}.webp atau .jpg
    |
    */

    'photos' => [
        'hero' => [
            'label' => 'Latar Hero Kafe',
            'suggested_size' => '1920x1080 (16:9)',
            'alt' => 'Suasana interior kafe ABS Cafe yang hangat dan nyaman',
            'width' => 1200,
            'height' => 800,
            'preload' => true,
            'url' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=1200&q=75',
        ],

        'hero_cup' => [
            'label' => 'Cangkir Kopi Hero',
            'suggested_size' => '800x800 (1:1)',
            'alt' => 'Secangkir kopi hangat ABS Cafe dengan latte art',
            'width' => 800,
            'height' => 800,
            'preload' => false,
            'url' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?auto=format&fit=crop&w=800&q=75',
        ],

        'tentang' => [
            'label' => 'Tentang Kami Kafe',
            'suggested_size' => '1000x900 (bingkai organik)',
            'alt' => 'Ruang duduk kafe ABS Cafe bernuansa kayu alami',
            'width' => 1000,
            'height' => 900,
            'preload' => false,
            'url' => 'https://images.unsplash.com/photo-1559925393-8be0ec4767c8?auto=format&fit=crop&w=1000&q=75',
        ],

        'suasana' => [
            'label' => 'Suasana Kafe Elegan',
            'suggested_size' => '1400x700 (2:1 lebar)',
            'alt' => 'Pemandangan lebar area duduk kafe yang tenang dan modern',
            'width' => 1400,
            'height' => 700,
            'preload' => false,
            'url' => 'https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&w=1400&q=75',
        ],
    ],

];
