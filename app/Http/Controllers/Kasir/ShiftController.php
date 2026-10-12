<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\CashMovement;
use App\Models\CashShift;
use App\Models\Order;
use App\Services\ShiftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftController extends Controller
{
    public function __construct(private readonly ShiftService $shifts)
    {
    }

    /**
     * Riwayat shift + rekap shift aktif.
     */
    public function index()
    {
        $user = Auth::user();
        $activeShift = $this->shifts->activeFor($user);
        $summary = $activeShift ? $this->shifts->summary($activeShift) : null;

        $history = CashShift::where('user_id', $user->id)
            ->where('status', 'tutup')
            ->latest('opened_at')
            ->paginate(10);

        return view('kasir.shifts.index', compact('activeShift', 'summary', 'history'));
    }

    public function showOpen()
    {
        if ($shift = $this->shifts->activeFor(Auth::user())) {
            return redirect()->route('kasir.shift.index')
                ->with('error', 'Shift Anda sudah aktif. Tutup terlebih dahulu untuk membuka shift baru.');
        }

        return view('kasir.shifts.open');
    }

    public function open(Request $request)
    {
        $data = $request->validate([
            'opening_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $shift = $this->shifts->open(Auth::user(), (float) $data['opening_cash'], $data['notes'] ?? null);

        $this->log($request, "Membuka shift dengan kas awal Rp ".number_format((float) $data['opening_cash'], 0, ',', '.'));

        return redirect()->route('kasir.dashboard')
            ->with('success', 'Shift berhasil dibuka. Selamat bekerja!');
    }

    public function showClose()
    {
        $shift = $this->shifts->activeFor(Auth::user());

        if (! $shift) {
            return redirect()->route('kasir.shift.open')
                ->with('error', 'Belum ada shift aktif untuk ditutup.');
        }

        $summary = $this->shifts->summary($shift);
        $movements = $shift->cashMovements()->with('user')->latest()->get();

        return view('kasir.shifts.close', compact('shift', 'summary', 'movements'));
    }

    public function close(Request $request)
    {
        $data = $request->validate([
            'closing_cash' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $shift = $this->shifts->activeFor(Auth::user());
        if (! $shift) {
            return redirect()->route('kasir.shift.open')->with('error', 'Belum ada shift aktif untuk ditutup.');
        }

        try {
            $closed = $this->shifts->close($shift, (float) $data['closing_cash'], $data['notes'] ?? null);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->with('error', collect($e->errors())->flatten()->first());
        }

        $this->log($request, "Menutup shift. Kas fisik Rp ".number_format((float) $data['closing_cash'], 0, ',', '.'));

        return redirect()->route('kasir.shift.receipt', $closed)
            ->with('success', 'Shift berhasil ditutup. Berikut rekap kasnya.');
    }

    public function movement(Request $request)
    {
        $shift = $this->shifts->activeFor(Auth::user());
        if (! $shift) {
            return redirect()->route('kasir.shift.open')->with('error', 'Belum ada shift aktif.');
        }

        $data = $request->validate([
            'type' => 'required|in:masuk,keluar',
            'amount' => 'required|numeric|min:1',
            'reason' => 'required|string|max:200',
        ]);

        $this->shifts->recordMovement($shift, Auth::user(), $data['type'], (float) $data['amount'], $data['reason']);

        $this->log($request, ucfirst($data['type']).' kas Rp '.number_format((float) $data['amount'], 0, ',', '.')." ({$data['reason']})");

        return back()->with('success', 'Catatan kas berhasil disimpan.');
    }

    public function receipt(CashShift $shift)
    {
        if ($shift->user_id !== Auth::id()) {
            abort(403);
        }

        $summary = $this->shifts->summary($shift);
        $movements = $shift->cashMovements()->with('user')->latest()->get();

        return view('kasir.shifts.receipt', compact('shift', 'summary', 'movements'));
    }

    public function history(CashShift $shift)
    {
        if ($shift->user_id !== Auth::id()) {
            abort(403);
        }

        $shift->load('user');
        $summary = $this->shifts->summary($shift);
        $movements = $shift->cashMovements()->with('user')->latest()->get();
        $orders = Order::with('items')->where('cash_shift_id', $shift->id)->latest()->get();

        return view('kasir.shifts.show', compact('shift', 'summary', 'movements', 'orders'));
    }

    private function log(Request $request, string $activity): void
    {
        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->primaryRole(),
            'activity' => $activity,
            'activity_type' => 'shift',
            'ip_address' => $request->ip(),
        ]);
    }
}
