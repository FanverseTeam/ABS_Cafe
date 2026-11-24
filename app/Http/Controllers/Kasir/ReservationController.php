<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Reservation;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $date = $request->filled('date') ? $request->date('date') : today();
        $dateStr = $date->toDateString();
        $status = $request->get('status', 'semua');

        // Reservasi yang mencakup tanggal yang dipilih (bisa single day maupun multi-hari)
        $query = Reservation::with('table')
            ->where(function ($q) use ($dateStr) {
                $q->whereDate('reservation_date', '<=', $dateStr)
                    ->where(function ($endQ) use ($dateStr) {
                        $endQ->whereDate('reservation_end_date', '>=', $dateStr)
                            ->orWhere(function ($nullQ) use ($dateStr) {
                                $nullQ->whereNull('reservation_end_date')
                                    ->whereDate('reservation_date', $dateStr);
                            });
                    });
            });

        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        $reservations = $query->orderBy('reservation_date')->orderBy('reservation_time')->get();

        $counts = [];
        foreach (array_keys(Reservation::STATUSES) as $s) {
            $counts[$s] = Reservation::where(function ($q) use ($dateStr) {
                $q->whereDate('reservation_date', '<=', $dateStr)
                    ->where(function ($endQ) use ($dateStr) {
                        $endQ->whereDate('reservation_end_date', '>=', $dateStr)
                            ->orWhere(function ($nullQ) use ($dateStr) {
                                $nullQ->whereNull('reservation_end_date')
                                    ->whereDate('reservation_date', $dateStr);
                            });
                    });
            })->where('status', $s)->count();
        }

        $tables = Table::with(['reservations' => fn ($q) => $q->whereIn('status', ['menunggu', 'dikonfirmasi', 'datang'])])
            ->withCount(['orders' => fn ($q) => $q->active()])
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
            'reservation_end_date' => 'nullable|date|after_or_equal:reservation_date',
            'reservation_end_time' => 'nullable|string|max:10',
            'table_id' => 'nullable|integer|exists:meja,id',
            'notes' => 'nullable|string|max:500',
        ]);

        if (empty($data['reservation_end_date'])) {
            $data['reservation_end_date'] = $data['reservation_date'];
        }
        if (empty($data['reservation_end_time'])) {
            $data['reservation_end_time'] = date('H:i', strtotime($data['reservation_time']) + 7200);
        }

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
            'reservation_end_date' => 'nullable|date|after_or_equal:reservation_date',
            'reservation_end_time' => 'nullable|string|max:10',
            'table_id' => 'nullable|integer|exists:meja,id',
            'notes' => 'nullable|string|max:500',
        ]);

        if (empty($data['reservation_end_date'])) {
            $data['reservation_end_date'] = $data['reservation_date'];
        }
        if (empty($data['reservation_end_time'])) {
            $data['reservation_end_time'] = date('H:i', strtotime($data['reservation_time']) + 7200);
        }

        $this->assertTableReservation($data, $reservation->id);

        $reservation->update($data);

        return back()->with('success', "Reservasi {$reservation->code} diperbarui.");
    }

    public function status(Request $request, Reservation $reservation, ?Table $table = null)
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
     * Meja yang dipilih harus cukup kapasitasnya dan belum dipesan pada jendela waktu yang bertabrakan.
     * Jendela terisi: 1 jam sebelum reservasi mulai sampai 1 jam setelah reservasi selesai.
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

        $startDate = $data['reservation_date'];
        $startTime = substr((string) $data['reservation_time'], 0, 5);
        $newStart = Carbon::parse("{$startDate} {$startTime}");

        $endDate = ! empty($data['reservation_end_date']) ? $data['reservation_end_date'] : $startDate;
        if (! empty($data['reservation_end_time'])) {
            $endTime = substr((string) $data['reservation_end_time'], 0, 5);
            $newEnd = Carbon::parse("{$endDate} {$endTime}");
        } else {
            $newEnd = $newStart->copy()->addHours(2);
        }

        if ($newEnd->lessThanOrEqualTo($newStart)) {
            throw ValidationException::withMessages([
                'reservation_end_time' => 'Waktu selesai reservasi harus setelah waktu mulai.',
            ]);
        }

        $newWindowStart = $newStart->copy()->subHour();
        $newWindowEnd = $newEnd->copy()->addHour();

        $existingReservations = Reservation::where('table_id', $data['table_id'])
            ->whereIn('status', ['menunggu', 'dikonfirmasi', 'datang'])
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->get();

        foreach ($existingReservations as $r) {
            $existingWindowStart = $r->occupiedWindowStart();
            $existingWindowEnd = $r->occupiedWindowEnd();

            // Cek tabrakan rentang: startA < endB && endA > startB
            if ($newWindowStart->lessThan($existingWindowEnd) && $newWindowEnd->greaterThan($existingWindowStart)) {
                $range = $r->datetimeRangeLabel();
                throw ValidationException::withMessages([
                    'table_id' => "Meja #{$table->number} sudah dipesan pada rentang ({$range}). Meja terisi otomatis 1 jam sebelum jam mulai sampai 1 jam setelah jam selesai.",
                ]);
            }
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
