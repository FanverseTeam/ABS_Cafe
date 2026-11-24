<?php

namespace App\Support;

use App\Models\CafeSetting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class LandingData
{
    /**
     * Cache duration in seconds (singkat agar perubahan pengaturan langsung tercermin).
     */
    protected const CACHE_TTL = 10;

    /**
     * Nilai bawaan jika database kosong atau key belum diset.
     */
    protected const DEFAULTS = [
        'cafe_name' => 'ABS Cafe',
        'cafe_tagline' => 'Good Coffee, Better Moments.',
        'cafe_address' => 'Jl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219',
        'cafe_hours' => 'Buka Setiap Hari 09.00 - 23.00 WIB',
        'cafe_phone' => '0896-2010-2010',
        'cafe_maps_url' => 'https://maps.app.goo.gl/mvEr6sWCacKH5j677',
        'peta_lat' => '-8.0646338',
        'peta_lng' => '111.9111798',
        'last_order' => '22.00 WIB',
        'street_address' => 'Jl. Letjend Suprapto No.44',
        'postal_code' => '66219',
        'cafe_instagram' => 'abs_tulungagung_24',
        'cafe_tiktok' => 'abs.tulungagung',
        'whatsapp_nomor' => '6289620102010',
        'whatsapp_pesan' => 'Halo ABS Cafe, saya ingin bertanya.',

        'hero_badge' => 'SELAMAT DATANG DI ABS CAFE',
        'hero_title_prefix' => 'Temukan Momen Terbaik di',
        'hero_title_highlight' => 'ABS Cafe',
        'hero_description' => 'Nikmati kopi berkualitas, makanan lezat, dan suasana nyaman yang cocok untuk bekerja, belajar, atau sekadar bersantai bersama orang-orang tercinta.',

        'about_badge' => 'TENTANG KAMI',
        'about_title' => 'ABS Cafe',
        'about_description' => 'ABS Cafe adalah tempat yang dirancang untuk memberikan pengalaman menikmati kopi terbaik dengan suasana yang hangat dan nyaman. Kami percaya, setiap cangkir kopi bisa menjadi awal dari cerita yang indah.',
        'about_tagline' => 'Good Coffee, Better Moments',

        'feature_1_title' => 'Kopi Berkualitas',
        'feature_2_title' => 'Menu Lezat',
        'feature_3_title' => 'Suasana Nyaman',
        'feature_4_title' => 'Cocok untuk Semua',

        'atmosphere_badge' => 'SUASANA',
        'atmosphere_title' => 'Moments Worth Sharing',
        'atmosphere_description' => 'From your first sip of coffee to meaningful moments with friends, ABS Cafe is where every visit becomes a memory.',

        'gallery_badge' => 'SUASANA & MOMEN',
        'gallery_title' => 'Moments Worth Sharing',
        'gallery_description' => 'From your first sip of coffee to meaningful moments with friends, ABS Cafe is where every visit becomes a memory.',

        'location_badge' => 'LOKASI',
        'location_title' => 'Temukan Kami',
        'social_title' => 'Ikuti Kami di Sosial Media',
    ];

    /**
     * Mengambil daftar nilai default landing page.
     *
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return self::DEFAULTS;
    }

    /**
     * Membaca dan menyiapkan data landing page.
     *
     * @return array<string, mixed>
     */
    public static function get(): array
    {
        return Cache::remember('pelanggan_landing_data', self::CACHE_TTL, function () {
            return self::build();
        });
    }

    /**
     * Membersihkan cache landing data.
     */
    public static function clearCache(): void
    {
        Cache::forget('pelanggan_landing_data');
    }

    /**
     * Membangun representasi data landing page.
     *
     * @return array<string, mixed>
     */
    public static function build(): array
    {
        $settings = [];

        try {
            $settings = CafeSetting::pluck('value', 'key')->all();
        } catch (\Throwable $e) {
            // Fallback bila migrasi/tabel belum tersedia
        }

        $resolve = function (string|array $keys, ?string $fallback = null) use ($settings): string {
            $keyList = is_array($keys) ? $keys : [$keys];
            foreach ($keyList as $k) {
                $val = $settings[$k] ?? null;
                if (is_string($val) && trim($val) !== '') {
                    return trim($val);
                }
            }

            $primaryKey = $keyList[0];

            return $fallback ?? self::DEFAULTS[$primaryKey] ?? '';
        };

        $instagram = $resolve(['cafe_instagram', 'instagram_handle', 'instagram']);
        $tiktok = $resolve(['cafe_tiktok', 'tiktok_handle', 'tiktok']);

        $phone = $resolve(['cafe_phone', 'telepon', 'phone']);
        $digitsOnly = preg_replace('/[^\d]/', '', $phone);
        $phoneTel = str_starts_with($digitsOnly, '0')
            ? '+62'.substr($digitsOnly, 1)
            : (str_starts_with($digitsOnly, '62') ? '+'.$digitsOnly : '+6289620102010');

        // Normalisasi nomor WhatsApp: simpan sebagai 628... (tanpa + dan tanda lain)
        $hasWaSetting = array_key_exists('whatsapp_nomor', $settings);
        $waRaw = $hasWaSetting
            ? (string) ($settings['whatsapp_nomor'] ?? '')
            : (self::DEFAULTS['whatsapp_nomor'] ?? '6289620102010');
        $waRaw = trim($waRaw);

        $waDigits = preg_replace('/[^\d]/', '', $waRaw);
        if (str_starts_with($waDigits, '0')) {
            $waDigits = '62'.substr($waDigits, 1);
        } elseif (str_starts_with($waDigits, '+62')) {
            $waDigits = ltrim($waDigits, '+');
        }

        $waValid = preg_match('/^628\d{8,12}$/', $waDigits) === 1;

        $waDisplay = '';
        if ($waValid) {
            $local = '08'.substr($waDigits, 3);
            $waDisplay = preg_replace('/^(\d{4})(\d{4})(\d+)$/', '$1-$2-$3', $local) ?? $local;
        }

        $waPesan = $resolve(['whatsapp_pesan'], self::DEFAULTS['whatsapp_pesan'] ?? 'Halo ABS Cafe, saya ingin bertanya.');
        $waUrl = $waValid
            ? 'https://wa.me/'.$waDigits.'?text='.rawurlencode($waPesan)
            : null;

        $address = $resolve(['cafe_address', 'alamat']);
        $streetAddress = 'Jl. Letjend Suprapto No.44';
        if (str_contains($address, 'Jl. Letjend Suprapto No.44')) {
            $streetAddress = 'Jl. Letjend Suprapto No.44';
        }

        $petaLat = (float) $resolve(['peta_lat', 'latitude'], '-8.0646338');
        $petaLng = (float) $resolve(['peta_lng', 'longitude'], '111.9111798');

        $mapsEmbedUrl = $resolve(['cafe_maps_embed_url', 'maps_embed_url', 'peta_embed_url']);
        if (empty($mapsEmbedUrl)) {
            $mapsEmbedUrl = "https://maps.google.com/maps?q={$petaLat},{$petaLng}&hl=id&z=17&output=embed";
        }

        return [
            'cafe' => [
                'name' => $resolve(['cafe_name', 'nama_cafe']),
                'tagline' => $resolve(['cafe_tagline', 'tagline']),
                'address' => $address,
                'street_address' => $streetAddress,
                'postal_code' => '66219',
                'hours' => $resolve(['cafe_hours', 'jam_operasional']),
                'last_order' => $resolve(['last_order', 'batas_order']),
                'phone' => $phone,
                'phone_tel' => $phoneTel,
                'maps_url' => $resolve(['cafe_maps_url', 'google_maps_url', 'maps_url']),
                'maps_embed_url' => $mapsEmbedUrl,
                'peta_lat' => $petaLat,
                'peta_lng' => $petaLng,
                'opening_hours_schema' => 'Mo-Su 09:00-23:00',
                'instagram' => $instagram,
                'instagram_url' => $instagram !== '' ? 'https://instagram.com/'.ltrim($instagram, '@') : null,
                'tiktok' => $tiktok,
                'tiktok_url' => $tiktok !== '' ? 'https://tiktok.com/@'.ltrim($tiktok, '@') : null,
                'whatsapp_nomor' => $waDigits,
                'whatsapp_display' => $waDisplay,
                'whatsapp_url' => $waUrl,
            ],

            'hero' => [
                'badge' => $resolve('hero_badge'),
                'title_prefix' => $resolve('hero_title_prefix'),
                'title_highlight' => $resolve('hero_title_highlight'),
                'description' => $resolve('hero_description'),
                'bg_photo' => self::resolvePhoto('hero'),
                'cup_photo' => self::resolvePhoto('hero_cup'),
            ],

            'about' => [
                'badge' => $resolve('about_badge'),
                'title' => $resolve('about_title'),
                'description' => $resolve('about_description'),
                'tagline' => $resolve('about_tagline'),
                'photo' => self::resolvePhoto('tentang'),
            ],

            'features' => [
                [
                    'title' => $resolve('feature_1_title'),
                    'icon' => 'coffee',
                ],
                [
                    'title' => $resolve('feature_2_title'),
                    'icon' => 'utensils',
                ],
                [
                    'title' => $resolve('feature_3_title'),
                    'icon' => 'armchair',
                ],
                [
                    'title' => $resolve('feature_4_title'),
                    'icon' => 'users',
                ],
            ],

            'atmosphere' => [
                'badge' => $resolve('atmosphere_badge'),
                'title' => $resolve('atmosphere_title'),
                'description' => $resolve('atmosphere_description'),
                'photo' => self::resolvePhoto('suasana'),
                'gallery' => self::getGallery(),
            ],

            'gallery' => [
                'badge' => $resolve(['gallery_badge', 'atmosphere_badge']),
                'title' => $resolve(['gallery_title', 'atmosphere_title']),
                'description' => $resolve(['gallery_description', 'atmosphere_description']),
                'items' => self::getGallery(),
            ],

            'location' => [
                'badge' => $resolve('location_badge'),
                'title' => $resolve('location_title'),
            ],

            'social' => [
                'title' => $resolve('social_title'),
            ],

            'photos' => [
                'hero' => self::resolvePhoto('hero'),
                'hero_cup' => self::resolvePhoto('hero_cup'),
                'tentang' => self::resolvePhoto('tentang'),
                'suasana' => self::resolvePhoto('suasana'),
            ],
        ];
    }

    /**
     * Resolusi slot foto dengan urutan prioritas:
     * 1. Berkas lokal public/images/pelanggan/{slot}.(webp|jpg|jpeg|png)
     * 2. URL langsung sementara dari config
     * 3. Fallback CSS gradient
     *
     * @return array{src: string, is_local: bool, alt: string, width: int, height: int, preload: bool}
     */
    public static function resolvePhoto(string $slot): array
    {
        $slotConfig = config("pelanggan.photos.{$slot}", []);
        $alt = $slotConfig['alt'] ?? 'Foto ABS Cafe';
        $width = (int) ($slotConfig['width'] ?? 1200);
        $height = (int) ($slotConfig['height'] ?? 800);
        $preload = (bool) ($slotConfig['preload'] ?? false);

        // 1. Cek file lokal standar (format yang didukung browser)
        $extensions = ['webp', 'jpg', 'jpeg', 'png', 'avif', 'gif', 'bmp', 'svg', 'tiff', 'tif', 'ico'];
        foreach ($extensions as $ext) {
            $relativePath = "images/pelanggan/{$slot}.{$ext}";
            $fullPath = public_path($relativePath);
            if (File::exists($fullPath)) {
                return [
                    'src' => asset($relativePath),
                    'is_local' => true,
                    'alt' => $alt,
                    'width' => $width,
                    'height' => $height,
                    'preload' => $preload,
                ];
            }
        }

        // Jika ada file HEIC/HEIF lokal, konversikan ke JPG agar dapat dirender browser
        foreach (['heic', 'heif'] as $ext) {
            $heicRelative = "images/pelanggan/{$slot}.{$ext}";
            $heicFull = public_path($heicRelative);
            if (File::exists($heicFull)) {
                $jpgRelative = "images/pelanggan/{$slot}.jpg";
                $jpgFull = public_path($jpgRelative);
                if (HeicConverter::convertFile($heicFull, $jpgFull)) {
                    @File::delete($heicFull);

                    return [
                        'src' => asset($jpgRelative),
                        'is_local' => true,
                        'alt' => $alt,
                        'width' => $width,
                        'height' => $height,
                        'preload' => $preload,
                    ];
                }
            }
        }

        // 2. Gunakan URL langsung dari config
        $remoteUrl = $slotConfig['url'] ?? '';

        return [
            'src' => $remoteUrl,
            'is_local' => false,
            'alt' => $alt,
            'width' => $width,
            'height' => $height,
            'preload' => $preload,
        ];
    }

    /**
     * Galeri foto suasana bawaan sistem.
     *
     * @return array<int, array<string, string>>
     */
    public static function defaultGallery(): array
    {
        $hasLocalCafe = File::exists(public_path('images/pelanggan/gallery/cafe_portrait.jpg'));

        $items = [
            [
                'id' => 'default_1',
                'image' => $hasLocalCafe ? 'images/pelanggan/gallery/cafe_portrait.jpg' : 'https://images.unsplash.com/photo-1521017432531-fbd92d768814?auto=format&fit=crop&w=1000&q=80',
                'title' => 'Fasad & Sudut Hangat Kafe',
                'alt' => 'Suasana cafe malam hari dengan lampu temaram',
            ],
            [
                'id' => 'default_2',
                'image' => 'https://images.unsplash.com/photo-1554118811-1e0d58224f24?auto=format&fit=crop&w=1000&q=80',
                'title' => 'Teras Santai Sore Hari',
                'alt' => 'Teras outdoor cafe yang asri dan sejuk',
            ],
            [
                'id' => 'default_3',
                'image' => 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?auto=format&fit=crop&w=1000&q=80',
                'title' => 'Momen Syahdu Bersama Teman',
                'alt' => 'Area duduk cafe malam hari berhias lampu gantung cantik',
            ],
            [
                'id' => 'default_4',
                'image' => 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?auto=format&fit=crop&w=1000&q=80',
                'title' => 'Racikan Kopi Pilihan Barista',
                'alt' => 'Barista sedang meracik kopi dengan cermat di coffee bar',
            ],
            [
                'id' => 'default_5',
                'image' => 'https://images.unsplash.com/photo-1559925393-8be0ec4767c8?auto=format&fit=crop&w=1000&q=80',
                'title' => 'Ruang Interior Nyaman & Estetik',
                'alt' => 'Desain interior cafe dengan meja kayu dan suasana tenang',
            ],
        ];

        return $items;
    }

    /**
     * Mengambil item galeri untuk antarmuka publik (URL sudah diselesaikan).
     *
     * @return array<int, array<string, string>>
     */
    public static function getGallery(): array
    {
        $items = self::rawGallery();

        return array_map(function ($item) {
            $img = $item['image'] ?? '';
            if ($img !== '' && ! str_starts_with($img, 'http://') && ! str_starts_with($img, 'https://')) {
                // Jika berkas adalah HEIC/HEIF, arahkan ke berkas JPG yang sudah dikonversi
                if (preg_match('/\.(heic|heif)$/i', $img)) {
                    $jpgCandidate = preg_replace('/\.(heic|heif)$/i', '.jpg', $img);
                    if (File::exists(public_path($jpgCandidate))) {
                        $img = $jpgCandidate;
                    } elseif (File::exists(public_path($img))) {
                        if (HeicConverter::convertFile(public_path($img), public_path($jpgCandidate))) {
                            @File::delete(public_path($img));
                            $img = $jpgCandidate;
                        }
                    }
                }
                $item['image'] = asset(ltrim($img, '/'));
            }

            return $item;
        }, $items);
    }

    /**
     * Mengambil item galeri mentah (untuk admin).
     *
     * @return array<int, array<string, string>>
     */
    public static function rawGallery(): array
    {
        $raw = CafeSetting::get('landing_gallery');
        if ($raw) {
            $decoded = json_decode($raw, true);
            if (is_array($decoded) && count($decoded) > 0) {
                return $decoded;
            }
        }

        return self::defaultGallery();
    }
}
