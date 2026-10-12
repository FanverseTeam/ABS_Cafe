<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\CafeSetting;
use App\Models\ActivityLog;

class SettingController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->input('tab', 'profil');

        $settings = [
            'cafe_name' => CafeSetting::get('cafe_name', 'ABS Cafe'),
            'cafe_tagline' => CafeSetting::get('cafe_tagline', 'Accessories & Cafe'),
            'cafe_address' => CafeSetting::get('cafe_address', 'Jl. Ki Mangunsarkoro No. 88, Boyolangu, Tulungagung'),
            'cafe_phone' => CafeSetting::get('cafe_phone', '0812-3456-7890'),
            'cafe_email' => CafeSetting::get('cafe_email', 'halo@abscafe.com'),
            'tax_percent' => CafeSetting::get('tax_percent', '10'),
            'service_charge_active' => CafeSetting::get('service_charge_active', '0'),
            'service_charge_percent' => CafeSetting::get('service_charge_percent', '5'),
            'payment_cash' => CafeSetting::get('payment_cash', '1'),
            'payment_qris' => CafeSetting::get('payment_qris', '1'),
            'payment_debit' => CafeSetting::get('payment_debit', '1'),
            'receipt_header' => CafeSetting::get('receipt_header', "ABS CAFE - ACCESSORIES & CAFE\nJl. Ki Mangunsarkoro No. 88, Boyolangu, Tulungagung\nTelp: 0812-3456-7890"),
            'receipt_footer' => CafeSetting::get('receipt_footer', "Terima kasih atas kunjungan Anda!\nFollow Instagram kami: @abscafe.tulungagung\n\"Good Coffee, Good Mood\""),
            'receipt_show_logo' => CafeSetting::get('receipt_show_logo', '1'),
            'banner_title' => CafeSetting::get('banner_title', 'Nikmati Kopi Pilihan & Aksesoris Estetis'),
            'banner_subtitle' => CafeSetting::get('banner_subtitle', 'Tempat berkumpul hangat dengan aroma kopi terbaik di Boyolangu Tulungagung'),
            'hours_weekday' => CafeSetting::get('hours_weekday', '09:00 - 22:00'),
            'hours_weekend' => CafeSetting::get('hours_weekend', '09:00 - 23:30'),
            'qris_image' => CafeSetting::get('qris_image', 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=00020101021226590014ID.LINKAJA.WWW0118936009110021200000021500000000000005204581253033605802ID5908ABS CAFE6011TULUNGAGUNG61056623362070703A0163041234'),
        ];

        return view('admin.settings.index', compact('settings', 'tab'));
    }

    public function updateProfile(Request $request)
    {
        CafeSetting::set('cafe_name', $request->input('cafe_name'));
        CafeSetting::set('cafe_tagline', $request->input('cafe_tagline'));
        CafeSetting::set('cafe_address', $request->input('cafe_address'));
        CafeSetting::set('cafe_phone', $request->input('cafe_phone'));
        CafeSetting::set('cafe_email', $request->input('cafe_email'));
        CafeSetting::set('hours_weekday', $request->input('hours_weekday'));
        CafeSetting::set('hours_weekend', $request->input('hours_weekend'));

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Memperbarui Profil Cafe & Jam Operasional",
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.settings', ['tab' => 'profil'])->with('success', 'Profil Cafe berhasil disimpan.');
    }

    public function updateTax(Request $request)
    {
        CafeSetting::set('tax_percent', $request->input('tax_percent', 10));
        CafeSetting::set('service_charge_active', $request->has('service_charge_active') ? '1' : '0');
        CafeSetting::set('service_charge_percent', $request->input('service_charge_percent', 5));

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Memperbarui Pengaturan Pajak & Service Charge",
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.settings', ['tab' => 'pajak'])->with('success', 'Pengaturan Pajak & Biaya berhasil disimpan.');
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
            'activity' => "Memperbarui Metode Pembayaran Kasir & QRIS",
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.settings', ['tab' => 'pembayaran'])->with('success', 'Pengaturan Pembayaran berhasil disimpan.');
    }

    public function updateReceipt(Request $request)
    {
        CafeSetting::set('receipt_header', $request->input('receipt_header'));
        CafeSetting::set('receipt_footer', $request->input('receipt_footer'));
        CafeSetting::set('receipt_show_logo', $request->has('receipt_show_logo') ? '1' : '0');

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Memperbarui Format Teks Nota Kasir",
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.settings', ['tab' => 'nota'])->with('success', 'Pengaturan Nota berhasil disimpan.');
    }

    public function updateDisplay(Request $request)
    {
        CafeSetting::set('banner_title', $request->input('banner_title'));
        CafeSetting::set('banner_subtitle', $request->input('banner_subtitle'));

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->role,
            'activity' => "Memperbarui Pengaturan Banner & Tampilan",
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.settings', ['tab' => 'tampilan'])->with('success', 'Pengaturan Tampilan berhasil disimpan.');
    }

    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user->name = $request->input('name');
        $user->email = $request->input('email');
        if ($request->filled('password')) {
            $user->password = Hash::make($request->input('password'));
        }
        $user->save();

        ActivityLog::create([
            'user_name' => $user->name,
            'role' => $user->role,
            'activity' => "Memperbarui Profil & Akun Admin",
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->route('admin.settings', ['tab' => 'akun'])->with('success', 'Profil akun admin berhasil diperbarui.');
    }
}
