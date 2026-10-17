<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\CashAdjustment;
use App\Services\ShiftService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CashAdjustmentController extends Controller
{
    public function __construct(private readonly ShiftService $shifts) {}

    public function index()
    {
        $adjustments = CashAdjustment::with(['user', 'appliedShift'])
            ->latest()
            ->paginate(20);

        $nextOpening = $this->shifts->calculateNextOpeningCash();

        return view('owner.cash_adjustments.index', compact('adjustments', 'nextOpening'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'amount' => 'required|numeric|not_in:0',
            'reason' => 'required|string|max:500',
        ]);

        try {
            $this->shifts->createAdjustment(Auth::user(), (float) $data['amount'], $data['reason']);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }

        return back()->with('success', 'Penyesuaian kas berhasil dicatat dan akan diterapkan pada kas awal shift berikutnya.');
    }
}
