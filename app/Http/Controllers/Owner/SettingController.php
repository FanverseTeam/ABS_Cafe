<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CafeSetting;
use App\Support\LandingData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SettingController extends Controller
{
    /**
     * Kunci pengaturan yang dikelola oleh Owner. Beberapa kunci (pajak, jam
     * operasional, teks nota) sengaja berbagi sumber dengan modul Admin.
     *
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return [
            'target_daily_sales' => '2000000',
            'target_monthly_sales' => '45000000',
            'profit_margin_percent' => '40',
            'tax_percent' => '10',
            'service_charge_active' => '0',
            'service_charge_percent' => '5',
            'price_rounding' => 'none',
            'default_min_stock' => '10',
            'hours_weekday' => '09:00 - 23:00',
            'hours_weekend' => '09:00 - 23:00',
            'last_order' => '22.00 WIB',
            'notif_low_stock' => '1',
            'notif_daily_report' => '1',
            'notif_weekly_report' => '0',
            'receipt_header' => "ABS CAFE - ACCESSORIES & CAFE\nJl. Letjend Suprapto No.44, Kepatihan, Kec. Tulungagung, Kabupaten Tulungagung, Jawa Timur 66219\nTelp: 0896-2010-2010",
            'receipt_footer' => "Terima kasih atas kunjungan Anda!\nFollow Instagram kami: @abs_tulungagung_24\n\"Good Coffee, Better Moments\"",
            'receipt_show_logo' => '1',
            'default_opening_cash' => '100000',
            'require_physical_cash_count' => '0',
        ];
    }

    public function index()
    {
        $defaults = self::defaults();
        $settings = [];
        foreach ($defaults as $key => $default) {
            $settings[$key] = CafeSetting::get($key, $default);
        }

        $user = Auth::user();

        $cafe = [
            'name' => CafeSetting::get('cafe_name', 'ABS Cafe'),
            'tagline' => CafeSetting::get('cafe_tagline', 'Accessories & Cafe'),
            'address' => CafeSetting::get('cafe_address', ''),
            'phone' => CafeSetting::get('cafe_phone', ''),
            'email' => CafeSetting::get('cafe_email', ''),
        ];

        return view('owner.settings.index', compact('settings', 'cafe', 'user'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'target_daily_sales' => 'nullable|numeric|min:0',
            'target_monthly_sales' => 'nullable|numeric|min:0',
            'profit_margin_percent' => 'nullable|numeric|min:0|max:100',
            'tax_percent' => 'nullable|numeric|min:0|max:100',
            'service_charge_percent' => 'nullable|numeric|min:0|max:100',
            'price_rounding' => 'nullable|in:none,100,500,1000',
            'default_min_stock' => 'nullable|integer|min:0',
            'hours_weekday' => 'nullable|string|max:100',
            'hours_weekend' => 'nullable|string|max:100',
            'last_order' => 'nullable|string|max:100',
            'receipt_header' => 'nullable|string',
            'receipt_footer' => 'nullable|string',
            'default_opening_cash' => 'nullable|numeric|min:0',
        ]);

        foreach ([
            'target_daily_sales',
            'target_monthly_sales',
            'profit_margin_percent',
            'tax_percent',
            'service_charge_percent',
            'price_rounding',
            'default_min_stock',
            'hours_weekday',
            'hours_weekend',
            'last_order',
            'receipt_header',
            'receipt_footer',
            'default_opening_cash',
        ] as $key) {
            if (array_key_exists($key, $validated)) {
                CafeSetting::set($key, (string) $validated[$key]);
            }
        }

        CafeSetting::set('service_charge_active', $request->boolean('service_charge_active') ? '1' : '0');
        CafeSetting::set('receipt_show_logo', $request->boolean('receipt_show_logo') ? '1' : '0');
        CafeSetting::set('notif_low_stock', $request->boolean('notif_low_stock') ? '1' : '0');
        CafeSetting::set('notif_daily_report', $request->boolean('notif_daily_report') ? '1' : '0');
        CafeSetting::set('notif_weekly_report', $request->boolean('notif_weekly_report') ? '1' : '0');
        CafeSetting::set('require_physical_cash_count', $request->boolean('require_physical_cash_count') ? '1' : '0');

        LandingData::clearCache();

        $this->log($request, 'Memperbarui Pengaturan Bisnis');

        return back()->with('success', 'Pengaturan bisnis berhasil disimpan.');
    }

    /**
     * Perbarui informasi akun owner (nama, username, telepon).
     */
    public function updateAccount(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:30|min:3|regex:/^[a-z0-9._]+$/|unique:pengguna,username,'.$user->id,
            'phone' => 'nullable|string|max:30',
        ], [
            'username.unique' => 'Username ini sudah dipakai oleh pengguna lain.',
            'username.min' => 'Username minimal 3 karakter.',
            'username.max' => 'Username maksimal 30 karakter.',
            'username.regex' => 'Username hanya boleh mengandung huruf kecil, angka, titik, dan garis bawah.',
        ]);

        $user->update($validated);

        $this->log($request, "Memperbarui akun owner: {$user->name}");

        return back()->with('success', 'Informasi akun berhasil diperbarui.');
    }

    /**
     * Ganti kata sandi owner dengan verifikasi kata sandi saat ini.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'confirmed', Password::min(6)],
        ], [
            'current_password.required' => 'Kata sandi saat ini wajib diisi.',
        ]);

        $user = Auth::user();

        if (! Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($validated['password'])]);

        $this->log($request, 'Mengganti kata sandi owner');

        return back()->with('success', 'Kata sandi berhasil diubah.');
    }

    private function log(Request $request, string $activity): void
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_name' => $user->name,
            'role' => $user->primaryRole(),
            'activity' => $activity,
            'activity_type' => 'sistem',
            'ip_address' => $request->ip(),
        ]);
    }
}
