<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CafeSetting;
use App\Models\Category;
use App\Models\Menu;
use App\Models\MenuVariant;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderTimeline;
use App\Models\Reservation;
use App\Models\Table;
use App\Services\OrderService;
use App\Support\LandingData;
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
        return view('pelanggan.beranda', [
            'landing' => LandingData::get(),
        ]);
    }

    /**
     * Halaman Menu Customer (Publik).
     */
    public function menu(Request $request): View
    {
        $searchQuery = trim((string) $request->query('q', ''));
        $categorySlug = trim((string) $request->query('kategori', ''));

        // Deteksi parameter nomor meja dari scan QR (?meja=1 atau ?table=1)
        $tableParam = $request->query('meja') ?: $request->query('table');
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
            ->with([
                'variants' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
                'category',
            ]);

        // Muat seluruh menu untuk payload Alpine.js (pencarian & filter instan client-side)
        $allMenus = (clone $baseMenuQuery)
            ->orderByDesc('is_best_seller')
            ->orderBy('name')
            ->get();

        $allMenusPayload = $allMenus->map(function ($menu) {
            return [
                'id' => $menu->id,
                'category_id' => $menu->category_id,
                'category_slug' => $menu->category?->slug ?? '',
                'category_name' => $menu->category?->name ?? '',
                'name' => $menu->name,
                'description' => $menu->description ?: '',
                'price' => (float) $menu->price,
                'image' => $menu->imageUrl(),
                'is_habis' => $menu->isHabis(),
                'is_best_seller' => (bool) $menu->is_best_seller,
                'price_label' => $menu->customerPriceLabel(),
                'variants' => $menu->variants->map(fn ($v) => [
                    'id' => $v->id,
                    'name' => $v->name,
                    'size' => $v->size,
                    'display_name' => $v->displayName(),
                    'price' => (float) $v->price,
                    'price_label' => 'Rp '.number_format((float) $v->price, 0, ',', '.'),
                    'is_available' => (bool) $v->is_available,
                    'is_default' => (bool) $v->is_default,
                    'is_habis' => $v->isHabis(),
                ])->values()->all(),
            ];
        })->values()->all();

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
            ]);
        }

        // 2. Mode Filter Kategori (?kategori=slug atau ?kategori=rekomendasi)
        if ($isCategoryFilter) {
            if ($categorySlug === 'rekomendasi') {
                $menus = (clone $baseMenuQuery)
                    ->where('is_best_seller', true)
                    ->orderByDesc('sold_count')
                    ->orderBy('name')
                    ->paginate(20)
                    ->withQueryString();

                if ($menus->isEmpty()) {
                    $menus = (clone $baseMenuQuery)
                        ->where('sold_count', '>', 0)
                        ->orderByDesc('sold_count')
                        ->paginate(20)
                        ->withQueryString();
                }

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
                ]);
            }
        }

        // 3. Mode Tampilan Utama Semua Kategori
        // Kelompokkan berdasarkan kategori (tanpa query ulang)
        $menusGroupedByCategory = $allMenus->groupBy('category_id');

        // Rekomendasi: Menu best seller; jika kosong, 4-6 menu terlaris dari pesanan; jika masih kosong disembunyikan
        $recommendedMenus = $allMenus->where('is_best_seller', true)->take(4)->values();
        if ($recommendedMenus->isEmpty()) {
            $recommendedMenus = $allMenus->where('sold_count', '>', 0)
                ->sortByDesc('sold_count')
                ->take(4)
                ->values();
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

        return view('pelanggan.keranjang', [
            'landing' => LandingData::get(),
            'tableNumber' => $tableNumber,
            'activeTable' => $activeTable,
            'allTables' => $allTables,
        ]);
    }

    /**
     * Simpan Pesanan Online / Web dari Pelanggan.
     */
    public function storePesanan(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => 'required|string|max:100',
            'customer_phone' => 'nullable|string|max:20',
            'order_type' => 'required|in:dine_in,take_away',
            'table_number' => 'nullable|integer',
            'notes' => 'nullable|string|max:500',
            'items' => 'required|array|min:1',
            'items.*.menu_id' => 'required|integer',
            'items.*.varian_id' => 'nullable|integer',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.catatan' => 'nullable|string|max:255',
        ]);

        if ($validated['order_type'] === 'dine_in' && empty($validated['table_number'])) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Silakan pilih atau masukkan nomor meja Anda untuk santap di tempat.',
                ], 422);
            }

            return back()->with('error', 'Silakan pilih nomor meja Anda.');
        }

        try {
            $order = DB::transaction(function () use ($validated) {
                $tableNumber = $validated['order_type'] === 'dine_in' ? (int) $validated['table_number'] : null;

                $subtotal = 0;
                $builtItems = [];

                foreach ($validated['items'] as $item) {
                    $menu = Menu::find($item['menu_id']);
                    if (! $menu) {
                        continue;
                    }

                    $variant = ! empty($item['varian_id'])
                        ? MenuVariant::where('id', $item['varian_id'])->where('menu_id', $menu->id)->first()
                        : null;

                    $price = $variant ? (float) $variant->price : (float) $menu->price;
                    $qty = (int) $item['jumlah'];
                    $itemSubtotal = $price * $qty;
                    $subtotal += $itemSubtotal;

                    $displayName = $menu->name.($variant ? " ({$variant->name})" : '');

                    $builtItems[] = [
                        'menu_id' => $menu->id,
                        'variant_id' => $variant?->id,
                        'name' => $displayName,
                        'price' => $price,
                        'quantity' => $qty,
                        'subtotal' => $itemSubtotal,
                        'notes' => $item['catatan'] ?? null,
                    ];
                }

                if (empty($builtItems)) {
                    throw new \Exception('Keranjang belanja kosong atau menu tidak valid.');
                }

                $taxPercent = (float) CafeSetting::get('tax_percent', 10);
                $tax = round($subtotal * $taxPercent / 100, 2);
                $total = round($subtotal + $tax, 2);

                $orderService = app(OrderService::class);
                $orderCode = $orderService->generateCode();

                $order = Order::create([
                    'order_code' => $orderCode,
                    'customer_name' => $validated['customer_name'],
                    'customer_phone' => $validated['customer_phone'] ?? null,
                    'table_number' => $tableNumber,
                    'order_type' => $validated['order_type'],
                    'source' => 'web',
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'service_charge' => 0,
                    'discount' => 0,
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
                        'variant_id' => $item['variant_id'],
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
        $order = Order::where('order_code', $code)->with(['items', 'timelines'])->firstOrFail();

        return view('pelanggan.pesanan-sukses', [
            'landing' => LandingData::get(),
            'order' => $order,
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

        // Slot waktu operasional (09:00 sampai 22:00)
        $timeSlots = [];
        $start = 9;
        $end = 22;
        for ($hour = $start; $hour <= $end; $hour++) {
            $h = str_pad((string) $hour, 2, '0', STR_PAD_LEFT);
            $timeSlots[] = "{$h}:00";
            if ($hour < $end) {
                $timeSlots[] = "{$h}:30";
            }
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

        // 2. Cek apakah meja sudah dipesan oleh reservasi lain pada jam yang berdekatan
        $queryReservations = Reservation::where('table_id', $table->id)
            ->whereDate('reservation_date', $date)
            ->whereIn('status', ['menunggu', 'dikonfirmasi', 'datang']);

        if ($time) {
            $targetTimestamp = strtotime($time);
            $conflictingReservation = $queryReservations->get()->first(function (Reservation $r) use ($targetTimestamp) {
                return abs(strtotime((string) $r->reservation_time) - $targetTimestamp) < 120 * 60;
            });

            if ($conflictingReservation) {
                return [
                    'status' => 'dipesan',
                    'label' => 'Sudah Dipesan',
                    'color' => 'warning',
                    'is_available' => false,
                    'active_reason' => 'Dipesan jam '.substr((string) $conflictingReservation->reservation_time, 0, 5),
                ];
            }
        } else {
            $anyReservation = $queryReservations->first();
            if ($anyReservation && $table->status === 'dipesan') {
                return [
                    'status' => 'dipesan',
                    'label' => 'Sudah Dipesan',
                    'color' => 'warning',
                    'is_available' => false,
                    'active_reason' => 'Sudah dipesan untuk tanggal ini',
                ];
            }
        }

        if ($table->status === 'dipesan') {
            return [
                'status' => 'dipesan',
                'label' => 'Sudah Dipesan',
                'color' => 'warning',
                'is_available' => false,
                'active_reason' => 'Status meja ditandai dipesan',
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

        $newTime = strtotime((string) $data['reservation_time']);

        $conflict = Reservation::where('table_id', $data['table_id'])
            ->whereDate('reservation_date', $data['reservation_date'])
            ->whereIn('status', ['menunggu', 'dikonfirmasi', 'datang'])
            ->get()
            ->contains(fn ($r) => abs(strtotime((string) $r->reservation_time) - $newTime) < 120 * 60);

        if ($conflict) {
            throw ValidationException::withMessages([
                'table_id' => "Meja #{$table->number} sudah dipesan oleh pelanggan lain pada jam yang berdekatan. Silakan pilih meja lain atau sesuaikan jam reservasi.",
            ]);
        }

        // Jika reservasi untuk hari ini dan jam dekat dengan waktu sekarang, pastikan meja tidak sedang dipakai
        if ($data['reservation_date'] === today()->toDateString()) {
            $isNearNow = abs($newTime - strtotime(now()->format('H:i'))) < 60 * 60;
            if ($isNearNow) {
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
