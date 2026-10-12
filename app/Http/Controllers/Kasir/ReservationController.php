<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('date') ? $request->date('date') : today();
        $status = $request->get('status', 'semua');

        $query = Reservation::with('table')->whereDate('reservation_date', $date);

        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        $reservations = $query->orderBy('reservation_time')->get();

        $counts = [];
        foreach (array_keys(Reservation::STATUSES) as $s) {
            $counts[$s] = Reservation::whereDate('reservation_date', $date)->where('status', $s)->count();
        }

        $tables = Table::withCount(['orders' => fn ($q) => $q->active()])
            ->orderBy('number')
            ->get();

        return view('kasir.reservations.index', compact('reservations', 'tables', 'date', 'status', 'counts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:25',
            'party_size' => 'required|integer|min:1|max:50',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required|string|max:10',
            'table_id' => 'nullable|integer|exists:tables,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $data['code'] = Reservation::generateCode();
        $data['status'] = 'menunggu';
        $data['created_by'] = Auth::id();

        $this->assertTableReservation($data);

        $reservation = Reservation::create($data);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->primaryRole(),
            'activity' => "Membuat reservasi {$reservation->code} atas nama {$reservation->customer_name}",
            'activity_type' => 'reservasi',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Reservasi {$reservation->code} berhasil dibuat.");
    }

    public function update(Request $request, Reservation $reservation)
    {
        $data = $request->validate([
            'customer_name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:25',
            'party_size' => 'required|integer|min:1|max:50',
            'reservation_date' => 'required|date',
            'reservation_time' => 'required|string|max:10',
            'table_id' => 'nullable|integer|exists:tables,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $this->assertTableReservation($data, $reservation->id);

        $reservation->update($data);

        return back()->with('success', "Reservasi {$reservation->code} diperbarui.");
    }

    public function status(Request $request, Reservation $reservation, Table $table = null)
    {
        $data = $request->validate([
            'status' => 'required|in:menunggu,dikonfirmasi,datang,selesai,batal,tidak_datang',
        ]);

        if (in_array($data['status'], ['batal', 'tidak_datang'], true)) {
            if ($reservation->table_id && $reservation->status === 'datang') {
                $this->freeTableIfIdle($reservation->table_id, $reservation->id);
            }
        }

        if ($data['status'] === 'datang' && $reservation->table_id) {
            Table::whereKey($reservation->table_id)->update(['status' => 'terpakai']);
        }

        if ($data['status'] === 'selesai') {
            $this->freeTableIfIdle($reservation->table_id, $reservation->id);
        }

        $reservation->update(['status' => $data['status']]);

        ActivityLog::create([
            'user_name' => Auth::user()->name,
            'role' => Auth::user()->primaryRole(),
            'activity' => "Reservasi {$reservation->code} diperbarui menjadi {$reservation->statusLabel()}",
            'activity_type' => 'reservasi',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', "Status reservasi {$reservation->code} diperbarui.");
    }

    public function destroy(Reservation $reservation)
    {
        $code = $reservation->code;
        $reservation->delete();

        return back()->with('success', "Reservasi {$code} dihapus.");
    }

    /**
     * Meja yang dipilih harus cukup kapasitasnya dan belum dipesan
     * pada jam yang berdekatan (rentang 2 jam).
     *
     * @param  array<string, mixed>  $data
     */
    private function assertTableReservation(array $data, ?int $ignoreId = null): void
    {
        if (empty($data['table_id'])) {
            return;
        }

        $table = Table::find($data['table_id']);
        if (! $table) {
            return;
        }

        if ((int) $data['party_size'] > (int) $table->capacity) {
            throw ValidationException::withMessages([
                'table_id' => "Kapasitas meja #{$table->number} hanya {$table->capacity} orang.",
            ]);
        }

        $newTime = strtotime((string) $data['reservation_time']);

        $conflict = Reservation::where('table_id', $data['table_id'])
            ->whereDate('reservation_date', $data['reservation_date'])
            ->whereIn('status', ['menunggu', 'dikonfirmasi', 'datang'])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->get()
            ->contains(fn ($r) => abs(strtotime((string) $r->reservation_time) - $newTime) < 120 * 60);

        if ($conflict) {
            throw ValidationException::withMessages([
                'table_id' => 'Meja ini sudah dipesan pada jam yang berdekatan.',
            ]);
        }
    }

    private function freeTableIfIdle(?int $tableId, ?int $exceptReservationId = null): void
    {
        if (! $tableId) {
            return;
        }

        $hasOrder = Order::where('table_number', Table::whereKey($tableId)->value('number'))->active()->exists();
        if ($hasOrder) {
            return;
        }

        Table::whereKey($tableId)->update(['status' => 'tersedia']);
    }
}
