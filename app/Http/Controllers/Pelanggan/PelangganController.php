<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTimeline;
use App\Models\Promo;
use App\Models\Reservation;
use App\Models\Table;
use App\Services\CafeScheduleService;
use App\Services\FavoriteMenuService;
use App\Services\OrderService;
use App\Support\LandingData;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PelangganController extends Controller
{
    /**
     * Halaman Beranda / Landing Customer (Publik).
     */
    public function index(): View
    {
        $topFavorites = FavoriteMenuService::getDailyTopFavorites(3);

        return view('pelanggan.beranda', [
            'landing' => LandingData::get(),
            'topFavorites' => $topFavorites,
        ]);
    }

    /**
     * Halaman Menu Customer (Publik).
     */
    public function menu(Request $request): View
    {
        $searchQuery = trim((string) $request->query('q', ''));
        $categorySlug = trim((string) $request->query('kategori', ''));

        // Deteksi parameter nomor meja dari scan QR (?meja=1 atau ?table=1) atau sesi aktif
        $tableParam = $request->query('meja') ?: $request->query('table') ?: session('customer_table_number');
        $tableNumber = is_numeric($tableParam) ? (int) $tableParam : null;

        $activeTable = null;
        if ($tableNumber) {
            $activeTable = Table::where('number', $tableNumber)->first();
            session(['customer_table_number' => $tableNumber]);
        }

        // Ambil semua meja untuk pemilih meja manual (jika datang dari landing page)
        $allTables = Table::orderBy('number', 'asc')->get();

        // Ambil kategori aktif yang memiliki menu (tidak termasuk menu gabungan anak)
        // Diambil langsung dari DB untuk menghindari isu serialisasi objek Eloquent pada cache file
        $categories = Category::where('is_active', true)
            ->whereHas('menus', fn ($q) => $q->whereNull('merged_into_menu_id'))
            ->orderBy('order')
            ->get();

        $baseMenuQuery = Menu::query()
            ->whereNull('merged_into_menu_id')
            ->with(['category']);

        // 3 Menu Terfavorit Otomatis (Penjualan Terbanyak, Diperbarui Harian)
        $topFavorites = FavoriteMenuService::getDailyTopFavorites(3);
        $topFavoriteIds = $topFavorites->pluck('id')->all();

        // Muat seluruh menu untuk payload Alpine.js (pencarian & filter instan client-side)
        $allMenus = (clone $baseMenuQuery)
            ->orderByDesc('sold_count')
            ->orderBy('name')
            ->get();

        $grouped = $allMenus->groupBy(fn ($m) => $m->variant_group ? 'group-'.$m->variant_group : 'single-'.$m->id);

        $allMenusPayload = $grouped->map(function ($groupMembers) use ($topFavoriteIds) {
            $primary = $groupMembers->first();
            $hasGroup = $groupMembers->count() > 1 || filled($primary->variant_group);

            $cardName = ($hasGroup && $primary->variant_group) ? $primary->variant_group : $primary->name;
            $minPrice = (float) $groupMembers->min('price');
            $maxPrice = (float) $groupMembers->max('price');
            $priceLabel = ($hasGroup && $minPrice !== $maxPrice)
                ? 'Mulai Rp '.number_format($minPrice, 0, ',', '.')
                : 'Rp '.number_format($minPrice, 0, ',', '.');

            $allHabis = $groupMembers->every(fn ($m) => $m->isHabis());

            $variants = $hasGroup
                ? $groupMembers->map(fn ($m, $idx) => [
                    'id' => $m->id,
                    'menu_id' => $m->id,
                    'name' => $m->variant_label ?: $m->name,
                    'size' => null,
                    'display_name' => $m->variant_label ?: $m->name,
                    'full_name' => $m->name,
                    'price' => (float) $m->price,
                    'price_label' => 'Rp '.number_format((float) $m->price, 0, ',', '.'),
                    'is_available' => (bool) $m->is_available,
                    'is_default' => $idx === 0,
                    'is_habis' => $m->isHabis(),
                ])->values()->all()
                : [];

            return [
                'id' => $primary->id,
                'category_id' => $primary->category_id,
                'category_slug' => $primary->category?->slug ?? '',
                'category_name' => $primary->category?->name ?? '',
                'name' => $cardName,
                'description' => $primary->description ?: '',
                'price' => $minPrice,
                'image' => $primary->imageUrl(),
                'is_habis' => $allHabis,
                'is_best_seller' => (bool) $groupMembers->contains(fn ($m) => in_array($m->id, $topFavoriteIds, true)),
                'price_label' => $priceLabel,
                'variants' => $variants,
            ];
        })->values()->all();

        try {
            $promos = Promo::where('is_active', true)->orderByDesc('id')->get();
        } catch (ValidationException $e) {
            $msg = collect($e->errors())->flatten()->first() ?: 'Validasi promo gagal.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }

            return back()->with('error', $msg);
        } catch (\Throwable $e) {
            $promos = collect();
        }

        $events = [
            [
                'id' => 1,
                'title' => 'Live Music Night',
                'subtitle' => 'Start 20.00 WIB',
                'schedule' => 'Setiap Jumat Malam',
                'description' => 'Nikmati suasana malam dengan live music dan menu spesial kami!!',
                'image' => asset('images/events/friday-night-party.jpg'),
                'badge' => 'Setiap Jumat',
            ],
            [
                'id' => 2,
                'title' => 'Acoustic Weekend Vibes',
                'subtitle' => 'Start 19.30 WIB',
                'schedule' => 'Sabtu & Minggu',
                'description' => 'Habiskan akhir pekan santai dengan lantunan melodi akustik syahdu ditemani racikan kopi favorit.',
                'image' => null,
                'badge' => 'Weekend',
            ],
        ];

        $selectedCategory = null;
        $isSearching = $searchQuery !== '';
        $isCategoryFilter = $categorySlug !== '' && $categorySlug !== 'semua';

        // 1. Mode Pencarian (?q=...)
        if ($isSearching) {
            $menus = (clone $baseMenuQuery)
                ->where(function ($q) use ($searchQuery) {
                    $q->where('name', 'like', "%{$searchQuery}%")
                        ->orWhere('description', 'like', "%{$searchQuery}%");
                })
                ->orderByDesc('is_best_seller')
                ->orderBy('name')
                ->paginate(20)
                ->withQueryString();

            return view('pelanggan.menu', [
                'landing' => LandingData::get(),
                'categories' => $categories,
                'searchQuery' => $searchQuery,
                'selectedCategorySlug' => $categorySlug,
                'selectedCategory' => null,
                'menus' => $menus,
                'mode' => 'search',
                'tableNumber' => $tableNumber,
                'activeTable' => $activeTable,
                'allTables' => $allTables,
                'allMenusPayload' => $allMenusPayload,
                'promos' => $promos,
                'events' => $events,
            ]);
        }

        // 2. Mode Filter Kategori (?kategori=slug atau ?kategori=rekomendasi)
        if ($isCategoryFilter) {
            if ($categorySlug === 'rekomendasi') {
                $menus = (clone $baseMenuQuery)
                    ->whereIn('id', $topFavoriteIds)
                    ->paginate(20)
                    ->withQueryString();

                return view('pelanggan.menu', [
                    'landing' => LandingData::get(),
                    'categories' => $categories,
                    'searchQuery' => '',
                    'selectedCategorySlug' => 'rekomendasi',
                    'selectedCategory' => null,
                    'menus' => $menus,
                    'mode' => 'rekomendasi',
                    'tableNumber' => $tableNumber,
                    'activeTable' => $activeTable,
                    'allTables' => $allTables,
                    'allMenusPayload' => $allMenusPayload,
                    'promos' => $promos,
                    'events' => $events,
                ]);
            }

            // Cari kategori berdasarkan slug atau id
            $selectedCategory = $categories->first(function ($cat) use ($categorySlug) {
                return $cat->slug === $categorySlug || (string) $cat->id === $categorySlug;
            });

            if ($selectedCategory) {
                $menus = (clone $baseMenuQuery)
                    ->where('category_id', $selectedCategory->id)
                    ->orderByDesc('is_best_seller')
                    ->orderBy('name')
                    ->paginate(20)
                    ->withQueryString();

                return view('pelanggan.menu', [
                    'landing' => LandingData::get(),
                    'categories' => $categories,
                    'searchQuery' => '',
                    'selectedCategorySlug' => $selectedCategory->slug,
                    'selectedCategory' => $selectedCategory,
                    'menus' => $menus,
                    'mode' => 'category',
                    'tableNumber' => $tableNumber,
                    'activeTable' => $activeTable,
                    'allTables' => $allTables,
                    'allMenusPayload' => $allMenusPayload,
                    'promos' => $promos,
                    'events' => $events,
                ]);
            }
        }

        // 3. Mode Tampilan Utama Semua Kategori
        // Kelompokkan berdasarkan kategori (tanpa query ulang)
        $menusGroupedByCategory = $allMenus->groupBy('category_id');

        // Rekomendasi: 3 menu terlaris otomatis berdasarkan penjualan terbanyak (sold_count)
        $recommendedMenus = $topFavorites->take(3)->values();
        if ($recommendedMenus->isEmpty()) {
            $recommendedMenus = $allMenus->sortByDesc('sold_count')->take(3)->values();
        }

        // Siapkan kategori yang memiliki menu dengan maksimal 4 menu per bagian
        $categoriesWithMenus = $categories->filter(function ($cat) use ($menusGroupedByCategory) {
            return $menusGroupedByCategory->has($cat->id) && $menusGroupedByCategory->get($cat->id)->isNotEmpty();
        })->map(function ($cat) use ($menusGroupedByCategory) {
            $cat->preview_menus = $menusGroupedByCategory->get($cat->id)->take(4);

            return $cat;
        })->values();

        return view('pelanggan.menu', [
            'landing' => LandingData::get(),
            'categories' => $categories,
            'searchQuery' => '',
            'selectedCategorySlug' => 'semua',
            'selectedCategory' => null,
            'recommendedMenus' => $recommendedMenus,
            'categoriesWithMenus' => $categoriesWithMenus,
            'mode' => 'all',
            'tableNumber' => $tableNumber,
            'activeTable' => $activeTable,
            'allTables' => $allTables,
            'allMenusPayload' => $allMenusPayload,
            'promos' => $promos,
            'events' => $events,
        ]);
    }

    /**
     * Halaman Keranjang Belanja Pelanggan (Mobile First).
     */
    public function keranjang(Request $request): View
    {
        $tableParam = $request->query('meja') ?: $request->query('table') ?: session('customer_table_number');
        $tableNumber = is_numeric($tableParam) ? (int) $tableParam : null;

        $activeTable = null;
        if ($tableNumber) {
            $activeTable = Table::where('number', $tableNumber)->first();
        }

        $allTables = Table::orderBy('number', 'asc')->get();

        $activePromos = Promo::running()
            ->where('type', '!=', 'non_diskon')
            ->get()
            ->map(fn (Promo $p) => [
                'id' => $p->id,
                'title' => $p->title,
                'code' => $p->code,
                'description' => $p->description,
                'type' => $p->type,
                'value' => (float) $p->value,
                'min_spend' => (float) $p->min_spend,
                'quota' => $p->remainingQuota(),
                'applies_to' => $p->applies_to,
                'target_category_id' => $p->target_category_id,
                'target_menu_id' => $p->target_menu_id,
                'type_label' => $p->typeLabel(),
            ])
            ->values();

        return view('pelanggan.keranjang', [
            'landing' => LandingData::get(),
            'tableNumber' => $tableNumber,
            'activeTable' => $activeTable,
            'allTables' => $allTables,
            'activePromos' => $activePromos,
            'menuCategories' => Menu::pluck('category_id', 'id'),
            'cafeSchedule' => CafeScheduleService::getSchedule(),
        ]);
    }

    /**
     * Simpan Pesanan Online / Web dari Pelanggan.
     */
    public function storePesanan(Request $request): JsonResponse|RedirectResponse
    {
        // Guard: tolak pesanan saat kafe tutup
        if (! CafeScheduleService::isOpen()) {
            $schedule = CafeScheduleService::getSchedule();
            $message = $schedule['message'];

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $message,
                ], 422);
            }

            return redirect()->back()->with('error', $message);
        }

        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'required|string|max:20',
            'order_type' => 'required|in:dine_in,take_away',
            'table_number' => 'nullable|integer',
            'notes' => 'nullable|string|max:500',
            'promo_id' => 'nullable|integer',
            'voucher_code' => 'nullable|string|max:50',
            'no_promo' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|integer',
            'items.*.varian_id' => 'nullable|integer',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.catatan' => 'nullable|string|max:255',
        ]);

        try {
            $order = DB::transaction(function () use ($validated) {
                $tableNumber = ! empty($validated['table_number']) ? (int) $validated['table_number'] : null;

                $subtotal = 0;
                $builtItems = [];

                foreach ($validated['items'] as $item) {
                    $menu = Menu::find($item['menu_id']);
                    if (! $menu) {
                        continue;
                    }

                    $price = (float) $menu->price;
                    $qty = (int) $item['jumlah'];
                    $itemSubtotal = $price * $qty;
                    $subtotal += $itemSubtotal;

                    $builtItems[] = [
                        'menu_id' => $menu->id,
                        'name' => $menu->name,
                        'price' => $price,
                        'quantity' => $qty,
                        'subtotal' => $itemSubtotal,
                        'notes' => $item['catatan'] ?? null,
                    ];
                }

                if (empty($builtItems)) {
                    throw new \Exception('Keranjang belanja kosong atau menu tidak valid.');
                }

                $orderService = app(OrderService::class);
                $promo = null;
                if (! empty($validated['voucher_code'])) {
                    $promo = $orderService->findPromo((string) $validated['voucher_code']);
                } elseif (! empty($validated['promo_id'])) {
                    $promo = $orderService->findPromoById((int) $validated['promo_id']);
                } elseif (empty($validated['no_promo'])) {
                    $promo = $orderService->resolveBestAutoPromo($builtItems, $subtotal);
                }

                ['discount' => $discount] = $orderService->calculateDiscount($promo, $builtItems, $subtotal);

                $total = round(max(0, $subtotal - $discount), 2);

                $orderCode = $orderService->generateCode();

                $order = Order::create([
                    'order_code' => $orderCode,
                    'customer_name' => $validated['customer_name'],
                    'customer_phone' => $validated['customer_phone'] ?? null,
                    'table_number' => $tableNumber,
                    'order_type' => $validated['order_type'],
                    'source' => 'web',
                    'subtotal' => $subtotal,
                    'tax' => 0,
                    'service_charge' => 0,
                    'discount' => $discount,
                    'promo_id' => $promo?->id,
                    'total' => $total,
                    'payment_method' => 'kasir',
                    'payment_status' => 'menunggu_pembayaran',
                    'order_status' => 'baru',
                    'received_at' => now(),
                ]);

                foreach ($builtItems as $item) {
                    OrderItem::create([
                        'order_id' => $order->id,
                        'menu_id' => $item['menu_id'],
                        'name' => $item['name'],
                        'price' => $item['price'],
                        'quantity' => $item['quantity'],
                        'subtotal' => $item['subtotal'],
                        'notes' => $item['notes'],
                    ]);
                }

                OrderTimeline::create([
                    'order_id' => $order->id,
                    'status' => 'DIBUAT',
                    'actor' => 'Pelanggan (Web)',
                    'description' => 'Pesanan dibuat oleh pelanggan melalui Web'.($tableNumber ? " (Meja #{$tableNumber})" : ' (Bawa Pulang)'),
                    'logged_at' => now(),
                ]);

                // Update status meja jika dine-in
                if ($tableNumber) {
                    Table::where('number', $tableNumber)->where('status', 'tersedia')->update(['status' => 'terpakai']);
                }

                return $order;
            });
        } catch (ValidationException $e) {
            $msg = collect($e->errors())->flatten()->first() ?: 'Validasi promo gagal.';
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $msg,
                ], 422);
            }

            return back()->with('error', $msg);
        } catch (\Throwable $e) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal memproses pesanan: '.$e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Gagal memproses pesanan: '.$e->getMessage());
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'order_code' => $order->order_code,
                'redirect_url' => route('pelanggan.pesanan.sukses', $order->order_code),
                'message' => 'Pesanan berhasil dikirim ke kasir!',
            ]);
        }

        return redirect()->route('pelanggan.pesanan.sukses', $order->order_code);
    }

    /**
     * Halaman Sukses / Konfirmasi Pesanan Pelanggan.
     */
    public function orderSukses(string $code): View
    {
        $order = Order::where('order_code', $code)->with(['items', 'timelines', 'promo'])->firstOrFail();

        return view('pelanggan.pesanan-sukses', [
            'landing' => LandingData::get(),
            'order' => $order,
        ]);
    }

    /**
     * Polling Status Pembayaran & Pesanan Pelanggan (JSON).
     */
    public function orderStatus(string $code): JsonResponse
    {
        $order = Order::where('order_code', $code)->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'order_code' => $order->order_code,
            'payment_status' => $order->payment_status,
            'payment_status_label' => $order->payment_status === 'lunas' ? 'Lunas' : 'Menunggu Pembayaran',
            'order_status' => $order->order_status,
            'is_processed' => in_array($order->order_status, ['diproses', 'siap', 'selesai'], true),
            'order_status_label' => match ($order->order_status) {
                'baru', 'menunggu' => 'Menunggu',
                'diterima' => 'Diterima Kasir',
                'diproses' => 'Pesanan Sedang Diproses',
                'siap' => 'Pesanan Siap',
                'selesai' => 'Pesanan Selesai',
                'dibatalkan' => 'Pesanan Dibatalkan',
                default => ucfirst($order->order_status),
            },
            'payment_method' => $order->paymentMethodLabel(),
            'paid_at' => $order->paid_at?->format('d/m/Y H:i'),
            'cashier_name' => $order->cashierLabel(),
        ]);
    }

    /**
     * Halaman Reservasi Meja Pelanggan dengan Denah Real-Time.
     */
    public function reservasi(Request $request): View
    {
        $selectedDate = $request->query('date', today()->toDateString());
        $selectedTime = $request->query('time', now()->format('H:i'));
        $searchCode = trim((string) $request->query('cek', ''));

        $tables = Table::orderBy('number')->get();

        $tableStatusList = $tables->map(function (Table $table) use ($selectedDate, $selectedTime) {
            $realtime = $this->resolveTableRealtimeStatus($table, $selectedDate, $selectedTime);

            return [
                'id' => $table->id,
                'number' => $table->number,
                'capacity' => (int) $table->capacity,
                'area' => $table->area ? ucfirst((string) $table->area) : 'Indoor',
                'location_notes' => $table->location_notes,
                'notes' => $table->notes,
                'status' => $realtime['status'],
                'status_label' => $realtime['label'],
                'status_color' => $realtime['color'],
                'is_available' => $realtime['is_available'],
                'active_reason' => $realtime['active_reason'],
            ];
        });

        // Rekapitulasi status meja
        $summary = [
            'total' => $tables->count(),
            'tersedia' => $tableStatusList->where('status', 'tersedia')->count(),
            'terpakai' => $tableStatusList->where('status', 'terpakai')->count(),
            'dipesan' => $tableStatusList->where('status', 'dipesan')->count(),
            'nonaktif' => $tableStatusList->where('status', 'nonaktif')->count(),
        ];

        // Cari riwayat/status reservasi jika diminta melalui query cek
        $foundReservation = null;
        if ($searchCode !== '') {
            $foundReservation = Reservation::with('table')
                ->where(function ($q) use ($searchCode) {
                    $q->where('code', $searchCode)
                        ->orWhere('phone', $searchCode);
                })
                ->latest('id')
                ->first();
        }

        // Slot waktu operasional berdasarkan jam kafe (09:00 s.d tutup, tiap 30 menit)
        $schedule = CafeScheduleService::getSchedule();
        $openHour = (int) substr($schedule['open_time'], 0, 2);
        $closeHour = (int) substr($schedule['close_time'], 0, 2);

        $timeSlots = [];
        for ($hour = $openHour; $hour < $closeHour; $hour++) {
            $h = str_pad((string) $hour, 2, '0', STR_PAD_LEFT);
            $timeSlots[] = "{$h}:00";
            $timeSlots[] = "{$h}:30";
        }

        return view('pelanggan.reservasi', [
            'landing' => LandingData::get(),
            'tables' => $tables,
            'tableStatusList' => $tableStatusList,
            'summary' => $summary,
            'timeSlots' => $timeSlots,
            'selectedDate' => $selectedDate,
            'selectedTime' => $selectedTime,
            'searchCode' => $searchCode,
            'foundReservation' => $foundReservation,
            'cafeSchedule' => CafeScheduleService::getSchedule(),
        ]);
    }

    /**
     * Endpoint API JSON untuk pembaruan status meja secara real-time.
     */
    public function tableStatusJson(Request $request): JsonResponse
    {
        $date = (string) $request->input('date', today()->toDateString());
        $time = (string) $request->input('time', now()->format('H:i'));

        $tables = Table::orderBy('number')->get();

        $summary = [
            'total' => $tables->count(),
            'tersedia' => 0,
            'terpakai' => 0,
            'dipesan' => 0,
            'nonaktif' => 0,
        ];

        $tableData = $tables->map(function (Table $table) use ($date, $time, &$summary) {
            $realtime = $this->resolveTableRealtimeStatus($table, $date, $time);

            if (isset($summary[$realtime['status']])) {
                $summary[$realtime['status']]++;
            }

            return [
                'id' => $table->id,
                'number' => $table->number,
                'capacity' => (int) $table->capacity,
                'area' => $table->area ? ucfirst((string) $table->area) : 'Indoor',
                'location_notes' => $table->location_notes,
                'status' => $realtime['status'],
                'status_label' => $realtime['label'],
                'status_color' => $realtime['color'],
                'is_available' => $realtime['is_available'],
                'active_reason' => $realtime['active_reason'],
            ];
        });

        return response()->json([
            'success' => true,
            'timestamp' => now()->format('H:i:s'),
            'date' => $date,
            'time' => $time,
            'summary' => $summary,
            'tables' => $tableData,
        ]);
    }

    /**
     * Menyimpan permohonan reservasi dari pelanggan.
     */
    public function storeReservasi(Request $request): RedirectResponse
    {
        // Guard: tolak reservasi saat kafe tutup
        if (! CafeScheduleService::isOpen()) {
            $schedule = CafeScheduleService::getSchedule();

            throw ValidationException::withMessages([
                'reservation_date' => $schedule['message'],
            ]);
        }

        // Validasi jam reservasi harus dalam jendela operasi kafe (hari itu)
        if (! CafeScheduleService::isReservationTimeValid(
            (string) $request->input('reservation_date'),
            (string) $request->input('reservation_time')
        )) {
            $schedule = CafeScheduleService::getSchedule();

            throw ValidationException::withMessages([
                'reservation_time' => 'Jam reservasi di luar jam operasional kafe. Jam operasional hari ini adalah '.$schedule['today_hours'].'.',
            ]);
        }

        $data = $request->validate([
            'customer_name' => 'required|string|max:100',
            'phone' => 'required|string|min:8|max:25',
            'party_size' => 'required|integer|min:1|max:30',
            'reservation_date' => 'required|date|after_or_equal:today',
            'reservation_time' => 'required|string|max:10',
            'table_id' => 'nullable|integer|exists:meja,id',
            'notes' => 'nullable|string|max:500',
        ], [
            'customer_name.required' => 'Nama lengkap wajib diisi.',
            'phone.required' => 'Nomor WhatsApp / telepon wajib diisi.',
            'phone.min' => 'Nomor WhatsApp minimal 8 digit.',
            'party_size.required' => 'Jumlah tamu wajib diisi.',
            'party_size.min' => 'Jumlah tamu minimal 1 orang.',
            'reservation_date.required' => 'Tanggal reservasi wajib dipilih.',
            'reservation_date.after_or_equal' => 'Tanggal reservasi tidak boleh di masa lalu.',
            'reservation_time.required' => 'Jam kedatangan wajib dipilih.',
            'table_id.exists' => 'Meja yang dipilih tidak valid.',
        ]);

        // Jika pelanggan tidak memilih meja tertentu, carikan meja kosong yang sesuai
        if (empty($data['table_id'])) {
            $assignedTable = $this->findBestAvailableTable(
                (int) $data['party_size'],
                (string) $data['reservation_date'],
                (string) $data['reservation_time']
            );

            if (! $assignedTable) {
                throw ValidationException::withMessages([
                    'table_id' => 'Maaf, semua meja untuk kapasitas '.$data['party_size'].' orang sedang penuh pada waktu tersebut. Silakan pilih waktu atau tanggal lain.',
                ]);
            }

            $data['table_id'] = $assignedTable->id;
        } else {
            $this->assertCustomerTableReservation($data);
        }

        $data['code'] = Reservation::generateCode();
        $data['status'] = 'menunggu';
        $data['created_by'] = null;

        $reservation = Reservation::create($data);

        $tableNumber = $reservation->table ? $reservation->table->number : '-';

        ActivityLog::create([
            'user_name' => $reservation->customer_name,
            'role' => 'pelanggan',
            'activity' => "Reservasi daring baru {$reservation->code} oleh {$reservation->customer_name} untuk Meja #{$tableNumber} pada {$reservation->datetimeLabel()}",
            'activity_type' => 'reservasi',
            'ip_address' => $request->ip(),
        ]);

        return redirect()
            ->route('pelanggan.reservasi')
            ->with('reservation_success', true)
            ->with('reservation_code', $reservation->code)
            ->with('reservation_item', [
                'code' => $reservation->code,
                'name' => $reservation->customer_name,
                'phone' => $reservation->phone,
                'party_size' => $reservation->party_size,
                'date' => $reservation->reservation_date->translatedFormat('l, d F Y'),
                'time' => substr((string) $reservation->reservation_time, 0, 5),
                'table_number' => $tableNumber,
                'notes' => $reservation->notes,
                'status_label' => $reservation->statusLabel(),
            ]);
    }

    /**
     * Memeriksa status reservasi berdasarkan kode atau nomor telepon.
     */
    public function checkReservasi(Request $request): JsonResponse|RedirectResponse
    {
        $query = trim((string) $request->input('q', ''));
        $reservation = null;

        if ($query !== '') {
            $reservation = Reservation::with('table')
                ->where(function ($q) use ($query) {
                    $q->where('code', $query)
                        ->orWhere('phone', $query);
                })
                ->latest('id')
                ->first();
        }

        if ($request->wantsJson()) {
            return response()->json([
                'found' => $reservation !== null,
                'reservation' => $reservation ? [
                    'code' => $reservation->code,
                    'customer_name' => $reservation->customer_name,
                    'phone' => $reservation->phone,
                    'party_size' => $reservation->party_size,
                    'reservation_date' => $reservation->reservation_date->translatedFormat('d M Y'),
                    'reservation_time' => substr((string) $reservation->reservation_time, 0, 5),
                    'table_number' => $reservation->table?->number,
                    'status' => $reservation->status,
                    'status_label' => $reservation->statusLabel(),
                    'status_badge' => $reservation->statusBadge(),
                    'notes' => $reservation->notes,
                ] : null,
            ]);
        }

        return redirect()->route('pelanggan.reservasi', ['cek' => $query]);
    }

    /**
     * Menghitung status real-time untuk sebuah meja pada tanggal dan waktu tertentu.
     *
     * @return array{status: string, label: string, color: string, is_available: bool, active_reason: ?string}
     */
    private function resolveTableRealtimeStatus(Table $table, string $date, ?string $time = null): array
    {
        if ($table->status === 'nonaktif') {
            return [
                'status' => 'nonaktif',
                'label' => 'Nonaktif',
                'color' => 'muted',
                'is_available' => false,
                'active_reason' => 'Meja sedang tidak difungsikan',
            ];
        }

        $isToday = $date === today()->toDateString();

        // 1. Cek apakah meja sedang aktif dipakai di sistem saat ini (pesanan aktif atau status terpakai)
        if ($isToday) {
            $hasActiveOrder = Order::where('table_number', $table->number)->active()->exists();
            $hasArrivedReservation = Reservation::where('table_id', $table->id)
                ->whereDate('reservation_date', $date)
                ->where('status', 'datang')
                ->exists();

            if ($table->status === 'terpakai' || $hasActiveOrder || $hasArrivedReservation) {
                return [
                    'status' => 'terpakai',
                    'label' => 'Sedang Terisi',
                    'color' => 'danger',
                    'is_available' => false,
                    'active_reason' => 'Meja saat ini sedang digunakan pelanggan',
                ];
            }
        }

        // 2. Cek apakah meja sedang dalam jendela reservasi aktif (1 jam sebelum s/d 1 jam setelah)
        $targetDt = Carbon::parse("{$date} {$time}");
        $reservations = Reservation::where('table_id', $table->id)
            ->whereIn('status', ['menunggu', 'dikonfirmasi', 'datang'])
            ->get();

        $conflictingReservation = $reservations->first(function (Reservation $r) use ($targetDt) {
            return $targetDt->between($r->occupiedWindowStart(), $r->occupiedWindowEnd());
        });

        if ($conflictingReservation) {
            return [
                'status' => 'terpakai',
                'label' => 'Sedang Terisi',
                'color' => 'danger',
                'is_available' => false,
                'active_reason' => 'Dipesan '.$conflictingReservation->datetimeRangeLabel(),
            ];
        }

        return [
            'status' => 'tersedia',
            'label' => 'Kosong',
            'color' => 'success',
            'is_available' => true,
            'active_reason' => null,
        ];
    }

    /**
     * Memastikan meja yang dipilih pelanggan valid dan tidak bentrok.
     *
     * @param  array<string, mixed>  $data
     */
    private function assertCustomerTableReservation(array $data): void
    {
        if (empty($data['table_id'])) {
            return;
        }

        $table = Table::find($data['table_id']);
        if (! $table) {
            throw ValidationException::withMessages([
                'table_id' => 'Meja yang dipilih tidak ditemukan.',
            ]);
        }

        if ($table->status === 'nonaktif') {
            throw ValidationException::withMessages([
                'table_id' => "Meja #{$table->number} saat ini sedang dinonaktifkan.",
            ]);
        }

        if ((int) $data['party_size'] > (int) $table->capacity) {
            throw ValidationException::withMessages([
                'table_id' => "Kapasitas Meja #{$table->number} maksimal {$table->capacity} orang (Anda memilih {$data['party_size']} orang).",
            ]);
        }

        $startDate = $data['reservation_date'];
        $startTime = substr((string) $data['reservation_time'], 0, 5);
        $newStart = Carbon::parse("{$startDate} {$startTime}");
        $newEnd = $newStart->copy()->addHours(2);
        $newWindowStart = $newStart->copy()->subHour();
        $newWindowEnd = $newEnd->copy()->addHour();

        $reservations = Reservation::where('table_id', $data['table_id'])
            ->whereIn('status', ['menunggu', 'dikonfirmasi', 'datang'])
            ->get();

        foreach ($reservations as $r) {
            if ($newWindowStart->lessThan($r->occupiedWindowEnd()) && $newWindowEnd->greaterThan($r->occupiedWindowStart())) {
                throw ValidationException::withMessages([
                    'table_id' => "Meja #{$table->number} sudah dipesan pada rentang tersebut ({$r->datetimeRangeLabel()}). Meja terisi 1 jam sebelum dan 1 jam setelah reservasi.",
                ]);
            }
        }

        // Jika reservasi untuk hari ini dan jam dekat dengan waktu sekarang, pastikan meja tidak sedang dipakai
        if ($data['reservation_date'] === today()->toDateString()) {
            if (now()->between($newWindowStart, $newWindowEnd)) {
                $isOccupied = $table->status === 'terpakai' || Order::where('table_number', $table->number)->active()->exists();
                if ($isOccupied) {
                    throw ValidationException::withMessages([
                        'table_id' => "Meja #{$table->number} saat ini sedang terisi oleh pelanggan di kafe. Silakan pilih meja lain.",
                    ]);
                }
            }
        }
    }

    /**
     * Menemukan meja terbaik yang kosong dan mencukupi kapasitas.
     */
    private function findBestAvailableTable(int $partySize, string $date, string $time): ?Table
    {
        $tables = Table::where('status', '!=', 'nonaktif')
            ->where('capacity', '>=', $partySize)
            ->orderBy('capacity', 'asc')
            ->orderBy('number', 'asc')
            ->get();

        foreach ($tables as $table) {
            $realtime = $this->resolveTableRealtimeStatus($table, $date, $time);
            if ($realtime['is_available']) {
                return $table;
            }
        }

        return null;
    }
}
