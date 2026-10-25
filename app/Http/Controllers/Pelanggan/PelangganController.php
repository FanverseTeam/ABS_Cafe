<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Support\LandingData;
use Illuminate\Contracts\View\View;

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
     * Halaman Menu (Placeholder).
     */
    public function menu(): View
    {
        return view('pelanggan.placeholder', [
            'title' => 'Menu Kafe',
            'subtitle' => 'Halaman pemesanan menu online pelanggan sedang dipersiapkan dan akan segera hadir.',
            'landing' => LandingData::get(),
        ]);
    }

    /**
     * Halaman Reservasi (Placeholder).
     */
    public function reservasi(): View
    {
        return view('pelanggan.placeholder', [
            'title' => 'Reservasi Meja',
            'subtitle' => 'Layanan reservasi meja kafe secara daring sedang dipersiapkan dan akan segera hadir.',
            'landing' => LandingData::get(),
        ]);
    }
}
