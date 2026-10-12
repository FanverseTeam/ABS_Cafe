<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CafeSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'hours_weekday' => '09:00 - 22:00',
            'hours_weekend' => '09:00 - 23:30',
            'notif_low_stock' => '1',
            'notif_daily_report' => '1',
            'notif_weekly_report' => '0',
            'receipt_header' => "ABS CAFE - ACCESSORIES & CAFE\nJl. Ki Mangunsarkoro No. 88, Boyolangu, Tulungagung\nTelp: 0812-3456-7890",
            'receipt_footer' => "Terima kasih atas kunjungan Anda!\nFollow Instagram kami: @abscafe.tulungagung",
            'receipt_show_logo' => '1',
        ];
    }

    public function index()
    {
        $defaults = self::defaults();
        $settings = [];
        foreach ($defaults as $key => $default) {
            $settings[$key] = CafeSetting::get($key, $default);
        }

        $cafe = [
            'name' => CafeSetting::get('cafe_name', 'ABS Cafe'),
            'tagline' => CafeSetting::get('cafe_tagline', 'Accessories & Cafe'),
            'address' => CafeSetting::get('cafe_address', ''),
            'phone' => CafeSetting::get('cafe_phone', ''),
            'email' => CafeSetting::get('cafe_email', ''),
        ];

        return view('owner.settings.index', compact('settings', 'cafe'));
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
            'receipt_header' => 'nullable|string',
            'receipt_footer' => 'nullable|string',
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
            'receipt_header',
            'receipt_footer',
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

        $this->log($request, 'Memperbarui Pengaturan Bisnis');

        return back()->with('success', 'Pengaturan bisnis berhasil disimpan.');
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
