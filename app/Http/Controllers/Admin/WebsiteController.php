<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CafeSetting;
use App\Models\Table;
use App\Support\HeicConverter;
use App\Support\LandingData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class WebsiteController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'profil');

        $settings = [
            'cafe_name' => CafeSetting::get('cafe_name', 'ABS Cafe'),
            'cafe_tagline' => CafeSetting::get('cafe_tagline', 'Accessories & Cafe'),
            'cafe_logo' => CafeSetting::get('cafe_logo', 'images/logo.png'),
            'cafe_address' => CafeSetting::get('cafe_address', 'Jl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219'),
            'cafe_phone' => CafeSetting::get('cafe_phone', '0896-2010-2010'),
            'cafe_email' => CafeSetting::get('cafe_email', 'halo@abscafe.com'),
            'cafe_maps_url' => CafeSetting::get('cafe_maps_url', 'https://maps.app.goo.gl/mvEr6sWCacKH5j677'),
            'peta_lat' => CafeSetting::get('peta_lat', '-8.0646338'),
            'peta_lng' => CafeSetting::get('peta_lng', '111.9111798'),
            'cafe_hours' => CafeSetting::get('cafe_hours', 'Buka Setiap Hari 09.00 - 23.00 WIB'),
            'last_order' => CafeSetting::get('last_order', '22.00 WIB'),
            'whatsapp_nomor' => CafeSetting::get('whatsapp_nomor', '6289620102010'),
            'whatsapp_pesan' => CafeSetting::get('whatsapp_pesan', 'Halo ABS Cafe, saya ingin bertanya.'),
            'service_charge_active' => CafeSetting::get('service_charge_active', '0'),
            'service_charge_percent' => CafeSetting::get('service_charge_percent', '5'),
            'payment_cash' => CafeSetting::get('payment_cash', '1'),
            'payment_qris' => CafeSetting::get('payment_qris', '1'),
            'payment_debit' => CafeSetting::get('payment_debit', '1'),
            'receipt_header' => CafeSetting::get('receipt_header', "ABS CAFE - ACCESSORIES & CAFE\nJl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219\nTelp: 0896-2010-2010"),
            'receipt_footer' => CafeSetting::get('receipt_footer', "Terima kasih atas kunjungan Anda!\nFollow Instagram kami: @abs_tulungagung_24\n\"Good Coffee, Better Moments\""),
            'receipt_show_logo' => CafeSetting::get('receipt_show_logo', '1'),
            'banner_title' => CafeSetting::get('banner_title', 'Nikmati Kopi Pilihan & Hidangan Spesial'),
            'banner_subtitle' => CafeSetting::get('banner_subtitle', 'Tempat berkumpul hangat dengan aroma kopi terbaik di Tulungagung'),
            'hours_weekday' => CafeSetting::get('hours_weekday', '09:00 - 23:00'),
            'hours_weekend' => CafeSetting::get('hours_weekend', '09:00 - 23:00'),
            'qris_image' => CafeSetting::get('qris_image', 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=00020101021226590014ID.LINKAJA.WWW0118936009110021200000021500000000000005204581253033605802ID5908ABS CAFE6011TULUNGAGUNG61056623362070703A0163041234'),
        ];

        // Data khusus tab Landing Page
        $landingSettings = [];
        $landingPhotos = [];
        $galleryItems = [];
        $isCustomGallery = false;
        if ($tab === 'landing') {
            $defaults = LandingData::defaults();
            $landingKeys = array_keys($defaults);
            foreach ($landingKeys as $key) {
                $landingSettings[$key] = CafeSetting::get($key, $defaults[$key]);
            }

            // Resolve foto slot saat ini
            $photoSlots = ['hero', 'hero_cup', 'tentang', 'suasana'];
            foreach ($photoSlots as $slot) {
                $photoData = LandingData::resolvePhoto($slot);
                $slotConfig = config("pelanggan.photos.{$slot}", []);
                $landingPhotos[$slot] = [
                    'src' => $photoData['src'],
                    'is_local' => $photoData['is_local'],
                    'label' => $slotConfig['label'] ?? ucfirst($slot),
                    'suggested_size' => $slotConfig['suggested_size'] ?? '1200x800',
                ];
            }
            $galleryItems = LandingData::rawGallery();
            $isCustomGallery = CafeSetting::get('landing_gallery') !== null;
        }

        $hostingUrl = $this->normalizeHostUrl(CafeSetting::get('order_base_url'));
        $orderBaseUrl = $hostingUrl.'/menu';
        $tables = Table::orderBy('number', 'asc')->get();

        return view('admin.website.index', compact(
            'settings',
            'tab',
            'landingSettings',
            'landingPhotos',
            'galleryItems',
            'isCustomGallery',
            'hostingUrl',
            'orderBaseUrl',
            'tables'
        ));
    }

    /**
     * Normalisasi link hostingan (Cloudflare, Ngrok, domain custom, IP, dll.).
     * Menghapus slash dan rute /menu di belakang secara otomatis.
     */
    private function normalizeHostUrl(?string $input): string
    {
        $url = trim((string) $input);
        if (empty($url)) {
            return rtrim(url('/'), '/');
        }

        // Jika tidak diawali scheme (http:// atau https://), tambahkan https://
        if (! preg_match('~^https?://~i', $url)) {
            $url = 'https://'.$url;
        }

        // Hapus trailing slash
        $url = rtrim($url, '/');

        // Jika user secara manual memasukkan /menu di akhir, hapus agar base url tetap bersih
        $url = preg_replace('~/menu$~i', '', $url);

        return $url;
    }

    /**
     * Update Pengaturan Base URL QR Pemesanan Meja.
     */
    public function updateQrSetting(Request $request)
    {
        $request->validate([
            'order_base_url' => 'nullable|string|max:255',
        ]);

        $hostingUrl = $this->normalizeHostUrl($request->input('order_base_url'));

        CafeSetting::set('order_base_url', $hostingUrl);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Memperbarui Link Hostingan QR Pemesanan Meja menjadi {$hostingUrl} (Menu: {$hostingUrl}/menu)",
            'activity_type' => 'website',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.website.index', ['tab' => 'qr_meja'])->with('success', 'Link hostingan QR meja berhasil disimpan! Rute /menu otomatis diterapkan.');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'peta_lat' => ['nullable', 'numeric', 'between:-90,90'],
            'peta_lng' => ['nullable', 'numeric', 'between:-180,180'],
            'cafe_maps_url' => [
                'nullable',
                'url',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if ($value === '' || $value === null) {
                        return;
                    }
                    $parsed = parse_url($value);
                    $scheme = $parsed['scheme'] ?? '';
                    $host = $parsed['host'] ?? '';
                    if ($scheme !== 'https') {
                        $fail('Tautan peta harus menggunakan protokol https.');

                        return;
                    }
                    $allowedDomains = ['www.google.com', 'google.com', 'maps.google.com', 'maps.app.goo.gl'];
                    if (! in_array($host, $allowedDomains, true)) {
                        $fail('Tautan peta harus berasal dari domain google.com atau maps.app.goo.gl.');
                    }
                },
            ],
            'whatsapp_nomor' => [
                'nullable',
                'string',
                function (string $attribute, mixed $value, \Closure $fail) {
                    if ($value === '' || $value === null) {
                        return;
                    }
                    $digits = preg_replace('/[^\d]/', '', $value);
                    if (str_starts_with($digits, '0')) {
                        $digits = '62'.substr($digits, 1);
                    }
                    if (! preg_match('/^628\d{8,12}$/', $digits)) {
                        $fail('Nomor WhatsApp tidak valid. Format: 08..., 628..., atau +628... (10-15 digit total).');
                    }
                },
            ],
            'whatsapp_pesan' => ['nullable', 'string', 'max:200'],
        ], [
            'peta_lat.numeric' => 'Latitude harus berupa angka.',
            'peta_lat.between' => 'Latitude harus antara -90 dan 90.',
            'peta_lng.numeric' => 'Longitude harus berupa angka.',
            'peta_lng.between' => 'Longitude harus antara -180 dan 180.',
            'cafe_maps_url.url' => 'Format tautan Google Maps tidak valid.',
        ]);

        CafeSetting::set('cafe_name', $request->input('cafe_name'));
        CafeSetting::set('cafe_tagline', $request->input('cafe_tagline'));

        if ($request->hasFile('cafe_logo')) {
            $file = HeicConverter::convertUploadedFile($request->file('cafe_logo'));
            $ext = strtolower($file->getClientOriginalExtension() ?: 'png');
            $filename = 'logo_'.time().'.'.$ext;
            $targetDir = public_path('images/logo');
            if (! File::isDirectory($targetDir)) {
                File::makeDirectory($targetDir, 0755, true);
            }
            $file->move($targetDir, $filename);
            $logoPath = 'images/logo/'.$filename;
            CafeSetting::set('cafe_logo', $logoPath);

            try {
                @copy(public_path($logoPath), public_path('images/logo.png'));
                @copy(public_path($logoPath), public_path('images/logo-badge.png'));
            } catch (\Throwable $e) {
                // Ignore copy errors
            }
        }

        CafeSetting::set('cafe_address', $request->input('cafe_address'));
        CafeSetting::set('cafe_phone', $request->input('cafe_phone'));
        CafeSetting::set('cafe_email', $request->input('cafe_email'));
        if ($request->has('cafe_maps_url')) {
            CafeSetting::set('cafe_maps_url', $request->input('cafe_maps_url'));
        }
        if ($request->has('peta_lat')) {
            CafeSetting::set('peta_lat', $request->input('peta_lat'));
        }
        if ($request->has('peta_lng')) {
            CafeSetting::set('peta_lng', $request->input('peta_lng'));
        }
        if ($request->has('last_order')) {
            CafeSetting::set('last_order', $request->input('last_order'));
        }
        if ($request->has('cafe_hours')) {
            CafeSetting::set('cafe_hours', $request->input('cafe_hours'));
        }
        if ($request->has('whatsapp_nomor')) {
            $waRaw = $request->input('whatsapp_nomor', '');
            $waDigits = preg_replace('/[^\d]/', '', $waRaw);
            if (str_starts_with($waDigits, '0')) {
                $waDigits = '62'.substr($waDigits, 1);
            }
            CafeSetting::set('whatsapp_nomor', $waDigits);
        }
        if ($request->has('whatsapp_pesan')) {
            CafeSetting::set('whatsapp_pesan', $request->input('whatsapp_pesan', ''));
        }
        CafeSetting::set('hours_weekday', $request->input('hours_weekday'));
        CafeSetting::set('hours_weekend', $request->input('hours_weekend'));

        LandingData::clearCache();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => 'Memperbarui Profil Cafe & Jam Operasional',
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.website.index', ['tab' => 'profil'])->with('success', 'Profil Cafe berhasil disimpan.');
    }

    public function updateTax(Request $request)
    {
        CafeSetting::set('service_charge_active', $request->has('service_charge_active') ? '1' : '0');
        CafeSetting::set('service_charge_percent', $request->input('service_charge_percent', 5));

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => 'Memperbarui Pengaturan Service Charge',
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.website.index', ['tab' => 'pajak'])->with('success', 'Pengaturan Biaya Layanan berhasil disimpan.');
    }

    public function updatePayment(Request $request)
    {
        CafeSetting::set('payment_cash', $request->has('payment_cash') ? '1' : '0');
        CafeSetting::set('payment_qris', $request->has('payment_qris') ? '1' : '0');
        CafeSetting::set('payment_debit', $request->has('payment_debit') ? '1' : '0');
        if ($request->filled('qris_image')) {
            CafeSetting::set('qris_image', $request->input('qris_image'));
        }

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => 'Memperbarui Metode Pembayaran Kasir & QRIS',
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.website.index', ['tab' => 'pembayaran'])->with('success', 'Pengaturan Pembayaran berhasil disimpan.');
    }

    public function updateReceipt(Request $request)
    {
        CafeSetting::set('receipt_header', $request->input('receipt_header'));
        CafeSetting::set('receipt_footer', $request->input('receipt_footer'));
        CafeSetting::set('receipt_show_logo', $request->has('receipt_show_logo') ? '1' : '0');

        LandingData::clearCache();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => 'Memperbarui Format Teks Nota Kasir',
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.website.index', ['tab' => 'nota'])->with('success', 'Pengaturan Nota berhasil disimpan.');
    }

    public function updateDisplay(Request $request)
    {
        CafeSetting::set('banner_title', $request->input('banner_title'));
        CafeSetting::set('banner_subtitle', $request->input('banner_subtitle'));

        LandingData::clearCache();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => 'Memperbarui Pengaturan Banner & Tampilan',
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.website.index', ['tab' => 'tampilan'])->with('success', 'Pengaturan Tampilan berhasil disimpan.');
    }

    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:30|min:3|regex:/^[a-z0-9._]+$/|unique:pengguna,username,'.$user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'username.unique' => 'Username ini sudah dipakai oleh pengguna lain.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.max' => 'Username maksimal 30 karakter.',
            'username.regex' => 'Username hanya boleh mengandung huruf kecil, angka, titik, dan garis bawah.',
        ]);

        $user->name = $request->input('name');
        $user->username = $request->input('username');
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();

        ActivityLog::create([
            'user_name' => $user->name,
            'role' => $user->role,
            'activity' => 'Memperbarui Profil & Akun Admin',
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.website.index', ['tab' => 'akun'])->with('success', 'Profil akun admin berhasil diperbarui.');
    }

    /**
     * Simpan perubahan teks konten landing page.
     */
    public function updateLanding(Request $request)
    {
        $request->validate([
            'hero_badge' => 'nullable|string|max:100',
            'hero_title_prefix' => 'nullable|string|max:200',
            'hero_title_highlight' => 'nullable|string|max:100',
            'hero_description' => 'nullable|string|max:500',
            'about_badge' => 'nullable|string|max:100',
            'about_title' => 'nullable|string|max:200',
            'about_description' => 'nullable|string|max:1000',
            'about_tagline' => 'nullable|string|max:200',
            'feature_1_title' => 'nullable|string|max:100',
            'feature_2_title' => 'nullable|string|max:100',
            'feature_3_title' => 'nullable|string|max:100',
            'feature_4_title' => 'nullable|string|max:100',
            'atmosphere_badge' => 'nullable|string|max:100',
            'atmosphere_title' => 'nullable|string|max:200',
            'atmosphere_description' => 'nullable|string|max:500',
            'gallery_badge' => 'nullable|string|max:100',
            'gallery_title' => 'nullable|string|max:200',
            'gallery_description' => 'nullable|string|max:500',
            'location_badge' => 'nullable|string|max:100',
            'location_title' => 'nullable|string|max:200',
            'social_title' => 'nullable|string|max:200',
            'cafe_instagram' => 'nullable|string|max:100',
            'cafe_tiktok' => 'nullable|string|max:100',
        ]);

        $landingKeys = [
            'hero_badge', 'hero_title_prefix', 'hero_title_highlight', 'hero_description',
            'about_badge', 'about_title', 'about_description', 'about_tagline',
            'feature_1_title', 'feature_2_title', 'feature_3_title', 'feature_4_title',
            'atmosphere_badge', 'atmosphere_title', 'atmosphere_description',
            'gallery_badge', 'gallery_title', 'gallery_description',
            'location_badge', 'location_title',
            'social_title',
            'cafe_instagram', 'cafe_tiktok',
        ];

        foreach ($landingKeys as $key) {
            if ($request->has($key)) {
                CafeSetting::set($key, $request->input($key, ''));
            }
        }

        LandingData::clearCache();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => 'Memperbarui Konten Teks Landing Page',
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.website.index', ['tab' => 'landing'])->with('success', 'Konten landing page berhasil disimpan.');
    }

    /**
     * Upload foto landing page untuk slot tertentu.
     */
    public function updateLandingPhoto(Request $request)
    {
        $allowedSlots = ['hero', 'hero_cup', 'tentang', 'suasana'];

        $request->validate([
            'slot' => 'required|string|in:'.implode(',', $allowedSlots),
            'photo' => 'required|file|max:51200|extensions:jpg,jpeg,png,webp,gif,svg,bmp,avif,heic,heif,tiff,tif,ico',
        ], [
            'photo.required' => 'Pilih file foto untuk diupload.',
            'photo.file' => 'File harus berupa gambar.',
            'photo.extensions' => 'Format foto harus berupa file gambar (JPG, PNG, WebP, HEIC, HEIF, AVIF, dll).',
            'photo.max' => 'Ukuran foto maksimal 50 MB.',
        ]);

        $slot = $request->input('slot');
        $file = HeicConverter::convertUploadedFile($request->file('photo'));
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');

        $targetDir = public_path('images/pelanggan');
        if (! File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        // Hapus file lama dengan ekstensi berbeda
        $extensions = ['webp', 'jpg', 'jpeg', 'png', 'heic', 'heif', 'avif', 'gif', 'bmp', 'svg', 'tiff', 'tif', 'ico'];
        foreach ($extensions as $ext) {
            $oldFile = $targetDir.DIRECTORY_SEPARATOR."{$slot}.{$ext}";
            if (File::exists($oldFile)) {
                File::delete($oldFile);
            }
        }

        // Simpan file baru
        $file->move($targetDir, "{$slot}.{$extension}");

        LandingData::clearCache();

        $slotLabels = [
            'hero' => 'Latar Hero',
            'hero_cup' => 'Cangkir Kopi Hero',
            'tentang' => 'Tentang Kami',
            'suasana' => 'Suasana Kafe',
        ];

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => 'Mengupload foto Landing Page: '.($slotLabels[$slot] ?? $slot),
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.website.index', ['tab' => 'landing'])->with('success', 'Foto "'.($slotLabels[$slot] ?? $slot).'" berhasil diupload.');
    }

    /**
     * Hapus foto landing page lokal (kembali ke default Unsplash).
     */
    public function deleteLandingPhoto(Request $request, string $slot)
    {
        $allowedSlots = ['hero', 'hero_cup', 'tentang', 'suasana'];
        if (! in_array($slot, $allowedSlots, true)) {
            abort(404);
        }

        $targetDir = public_path('images/pelanggan');
        $extensions = ['webp', 'jpg', 'jpeg', 'png', 'heic', 'heif', 'avif', 'gif', 'bmp', 'svg', 'tiff', 'tif', 'ico'];
        $deleted = false;

        foreach ($extensions as $ext) {
            $filePath = $targetDir.DIRECTORY_SEPARATOR."{$slot}.{$ext}";
            if (File::exists($filePath)) {
                File::delete($filePath);
                $deleted = true;
            }
        }

        LandingData::clearCache();

        $slotLabels = [
            'hero' => 'Latar Hero',
            'hero_cup' => 'Cangkir Kopi Hero',
            'tentang' => 'Tentang Kami',
            'suasana' => 'Suasana Kafe',
        ];

        if ($deleted) {
            ActivityLog::create([
                'user_name' => Auth::user()->name,
                'role' => Auth::user()->role,
                'activity' => 'Menghapus foto Landing Page: '.($slotLabels[$slot] ?? $slot).' (kembali ke default)',
                'activity_type' => 'sistem',
                'ip_address' => $request->ip(),
            ]);
        }

        return redirect()->route('admin.website.index', ['tab' => 'landing'])->with('success', 'Foto "'.($slotLabels[$slot] ?? $slot).'" berhasil dihapus. Akan menggunakan foto default.');
    }

    /**
     * Tambah foto baru ke galeri carousel landing page.
     */
    public function addGalleryPhoto(Request $request)
    {
        $request->validate([
            'photo' => 'required|file|max:51200|extensions:jpg,jpeg,png,webp,gif,svg,bmp,avif,heic,heif,tiff,tif,ico',
            'title' => 'nullable|string|max:100',
        ], [
            'photo.required' => 'Pilih file foto galeri untuk diupload.',
            'photo.file' => 'File harus berupa gambar.',
            'photo.extensions' => 'Format foto harus berupa file gambar (JPG, PNG, WebP, HEIC, HEIF, AVIF, dll).',
            'photo.max' => 'Ukuran foto maksimal 50 MB.',
        ]);

        $file = HeicConverter::convertUploadedFile($request->file('photo'));
        $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
        $filename = 'gallery_'.time().'_'.substr(md5(uniqid()), 0, 8).'.'.$extension;

        $targetDir = public_path('images/pelanggan/gallery');
        if (! File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $file->move($targetDir, $filename);

        $items = LandingData::rawGallery();
        $title = trim((string) $request->input('title', ''));
        if ($title === '') {
            $title = 'Foto '.(count($items) + 1);
        }

        $newItem = [
            'id' => 'gal_'.uniqid(),
            'image' => 'images/pelanggan/gallery/'.$filename,
            'title' => $title,
            'alt' => $title,
        ];

        $items[] = $newItem;
        CafeSetting::set('landing_gallery', json_encode($items));
        LandingData::clearCache();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => 'Menambahkan foto baru ke Galeri Suasana Landing Page: '.$title,
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.website.index', ['tab' => 'landing'])->with('success', 'Foto "'.$title.'" berhasil ditambahkan ke galeri carousel.');
    }

    /**
     * Update keterangan / foto galeri carousel.
     */
    public function updateGalleryPhoto(Request $request, string $id)
    {
        $request->validate([
            'title' => 'nullable|string|max:100',
            'photo' => 'nullable|file|max:51200|extensions:jpg,jpeg,png,webp,gif,svg,bmp,avif,heic,heif,tiff,tif,ico',
        ], [
            'photo.file' => 'File harus berupa gambar.',
            'photo.extensions' => 'Format foto harus berupa file gambar (JPG, PNG, WebP, HEIC, HEIF, AVIF, dll).',
            'photo.max' => 'Ukuran foto maksimal 50 MB.',
        ]);

        $items = LandingData::rawGallery();
        $found = false;

        foreach ($items as &$item) {
            if (($item['id'] ?? '') === $id) {
                $item['title'] = trim((string) $request->input('title', ''));
                if ($item['title'] !== '') {
                    $item['alt'] = $item['title'];
                }

                if ($request->hasFile('photo')) {
                    $file = HeicConverter::convertUploadedFile($request->file('photo'));
                    $extension = strtolower($file->getClientOriginalExtension() ?: 'jpg');
                    $filename = 'gallery_'.time().'_'.substr(md5(uniqid()), 0, 8).'.'.$extension;
                    $targetDir = public_path('images/pelanggan/gallery');
                    if (! File::isDirectory($targetDir)) {
                        File::makeDirectory($targetDir, 0755, true);
                    }
                    $file->move($targetDir, $filename);

                    // Hapus file lama jika lokal
                    $oldImage = $item['image'] ?? '';
                    if ($oldImage && ! str_starts_with($oldImage, 'http') && File::exists(public_path($oldImage))) {
                        File::delete(public_path($oldImage));
                    }

                    $item['image'] = 'images/pelanggan/gallery/'.$filename;
                }

                $found = true;
                break;
            }
        }
        unset($item);

        if ($found) {
            CafeSetting::set('landing_gallery', json_encode($items));
            LandingData::clearCache();

            ActivityLog::create([
                'user_name' => Auth::user()->name,
                'role' => Auth::user()->role,
                'activity' => 'Memperbarui foto galeri landing page ID: '.$id,
                'activity_type' => 'sistem',
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('admin.website.index', ['tab' => 'landing'])->with('success', 'Data foto galeri berhasil diperbarui.');
        }

        return redirect()->route('admin.website.index', ['tab' => 'landing'])->with('error', 'Item galeri tidak ditemukan.');
    }

    /**
     * Hapus foto dari galeri carousel landing page.
     */
    public function deleteGalleryPhoto(Request $request, string $id)
    {
        $items = LandingData::rawGallery();
        $filtered = [];
        $deleted = false;

        foreach ($items as $item) {
            if (($item['id'] ?? '') === $id) {
                $imagePath = $item['image'] ?? '';
                if ($imagePath && ! str_starts_with($imagePath, 'http') && File::exists(public_path($imagePath))) {
                    File::delete(public_path($imagePath));
                }
                $deleted = true;
            } else {
                $filtered[] = $item;
            }
        }

        if ($deleted) {
            CafeSetting::set('landing_gallery', json_encode(array_values($filtered)));
            LandingData::clearCache();

            ActivityLog::create([
                'user_name' => Auth::user()->name,
                'role' => Auth::user()->role,
                'activity' => 'Menghapus foto dari galeri carousel landing page',
                'activity_type' => 'sistem',
                'ip_address' => $request->ip(),
            ]);

            return redirect()->route('admin.website.index', ['tab' => 'landing'])->with('success', 'Foto galeri berhasil dihapus.');
        }

        return redirect()->route('admin.website.index', ['tab' => 'landing'])->with('error', 'Item galeri tidak ditemukan.');
    }

    /**
     * Reset galeri carousel ke default bawaan sistem.
     */
    public function resetGallery(Request $request)
    {
        CafeSetting::where('key', 'landing_gallery')->delete();
        LandingData::clearCache();

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => 'Mereset galeri carousel landing page ke default bawaan',
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.website.index', ['tab' => 'landing'])->with('success', 'Galeri foto carousel berhasil dikembalikan ke default bawaan.');
    }
}
