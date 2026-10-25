<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CafeSetting;
use App\Support\LandingData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SettingController extends Controller
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
            'cafe_maps_url' => CafeSetting::get('cafe_maps_url', 'https://maps.app.goo.gl/pPSXXjtJptYwrDB37'),
            'peta_lat' => CafeSetting::get('peta_lat', '-8.0646338'),
            'peta_lng' => CafeSetting::get('peta_lng', '111.9111798'),
            'cafe_hours' => CafeSetting::get('cafe_hours', 'Buka Setiap Hari 09.00 - 23.00 WIB'),
            'last_order' => CafeSetting::get('last_order', '22.00 WIB'),
            'tax_percent' => CafeSetting::get('tax_percent', '10'),
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

        return view('admin.settings.index', compact('settings', 'tab'));
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
        ], [
            'peta_lat.numeric' => 'Latitude harus berupa angka.',
            'peta_lat.between' => 'Latitude harus antara -90 dan 90.',
            'peta_lng.numeric' => 'Longitude harus berupa angka.',
            'peta_lng.between' => 'Longitude harus antara -180 dan 180.',
            'cafe_maps_url.url' => 'Format tautan Google Maps tidak valid.',
        ]);

        CafeSetting::set('cafe_name', $request->input('cafe_name'));
        CafeSetting::set('cafe_tagline', $request->input('cafe_tagline'));
        CafeSetting::set('cafe_logo', $request->input('cafe_logo') ?: 'images/logo.png');
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

        return redirect()->route('admin.settings.index', ['tab' => 'profil'])->with('success', 'Profil Cafe berhasil disimpan.');
    }

    public function updateTax(Request $request)
    {
        CafeSetting::set('tax_percent', $request->input('tax_percent', 10));
        CafeSetting::set('service_charge_active', $request->has('service_charge_active') ? '1' : '0');
        CafeSetting::set('service_charge_percent', $request->input('service_charge_percent', 5));

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => 'Memperbarui Pengaturan Pajak & Service Charge',
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.settings.index', ['tab' => 'pajak'])->with('success', 'Pengaturan Pajak & Biaya berhasil disimpan.');
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

        return redirect()->route('admin.settings.index', ['tab' => 'pembayaran'])->with('success', 'Pengaturan Pembayaran berhasil disimpan.');
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

        return redirect()->route('admin.settings.index', ['tab' => 'nota'])->with('success', 'Pengaturan Nota berhasil disimpan.');
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

        return redirect()->route('admin.settings.index', ['tab' => 'tampilan'])->with('success', 'Pengaturan Tampilan berhasil disimpan.');
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

        return redirect()->route('admin.settings.index', ['tab' => 'akun'])->with('success', 'Profil akun admin berhasil diperbarui.');
    }
}
