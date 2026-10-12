<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Order;
use App\Models\Promo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PromoController extends Controller
{
    public function index()
    {
        $promos = Promo::with('targetCategory')->orderBy('created_at', 'desc')->get()->map(function (Promo $promo) {
            $orders = Order::where('promo_id', $promo->id)->where('payment_status', '!=', 'dibatalkan');

            $used = (clone $orders)->count();
            $promo->setAttribute('used_count', $used);
            $promo->setAttribute('total_discount', (float) Order::where('promo_id', $promo->id)->sum('discount'));
            $promo->setAttribute('generated_sales', (float) $orders->sum('total'));
            $promo->setAttribute('remaining_quota', $promo->quota !== null ? max(0, $promo->quota - $used) : null);

            return $promo;
        });

        $categories = Category::where('is_active', true)->get();

        $stats = [
            'active' => $promos->where('is_active', true)->count(),
            'total' => $promos->count(),
            'total_discount' => (float) $promos->sum('total_discount'),
            'generated_sales' => (float) $promos->sum('generated_sales'),
        ];

        return view('owner.promos.index', compact('promos', 'categories', 'stats'));
    }

    public function store(Request $request)
    {
        $validated = $this->validatePromo($request);

        $promo = Promo::create($validated);

        $this->log($request, "Membuat promo baru: {$promo->title}");

        return back()->with('success', "Promo {$promo->title} berhasil dibuat.");
    }

    public function update(Request $request, int $id)
    {
        $promo = Promo::findOrFail($id);
        $validated = $this->validatePromo($request);

        $promo->update($validated);

        $this->log($request, "Memperbarui promo: {$promo->title}");

        return back()->with('success', "Promo {$promo->title} berhasil diperbarui.");
    }

    public function toggleActive(Request $request, int $id)
    {
        $promo = Promo::findOrFail($id);
        $promo->is_active = ! $promo->is_active;
        $promo->save();

        $state = $promo->is_active ? 'diaktifkan' : 'dinonaktifkan';
        $this->log($request, "Promo {$promo->title} {$state}");

        return back()->with('success', "Promo {$promo->title} berhasil {$state}.");
    }

    public function destroy(Request $request, int $id)
    {
        $promo = Promo::findOrFail($id);
        $title = $promo->title;
        $promo->delete();

        $this->log($request, "Menghapus promo: {$title}");

        return back()->with('success', "Promo {$title} telah dihapus.");
    }

    /**
     * @return array<string, mixed>
     */
    private function validatePromo(Request $request): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'type' => 'required|in:persen,nominal,buy_x_get_y',
            'value' => 'required|numeric|min:0',
            'applies_to' => 'required|in:semua,kategori,menu',
            'target_category_id' => 'nullable|exists:categories,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'banner' => 'nullable|string',
            'min_spend' => 'nullable|numeric|min:0',
            'quota' => 'nullable|integer|min:0',
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['min_spend'] = $validated['min_spend'] ?? 0;
        $validated['banner'] = $validated['banner'] ?? null;

        return $validated;
    }

    private function log(Request $request, string $activity): void
    {
        $user = Auth::user();

        ActivityLog::create([
            'user_name' => $user->name,
            'role' => $user->primaryRole(),
            'activity' => $activity,
            'activity_type' => 'promo',
            'ip_address' => $request->ip(),
        ]);
    }
}
