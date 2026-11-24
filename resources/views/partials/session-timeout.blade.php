{{--
    Skrip deteksi ketidakaktifan (idle timeout) untuk Admin dan Owner.
    Kasir tidak menggunakan skrip ini karena tidak memiliki batas waktu time out.

    Menampilkan modal peringatan 2 menit sebelum batas waktu,
    lalu otomatis logout jika tidak ada interaksi.
--}}

@php
    $timeoutMinutes = (int) (\App\Models\CafeSetting::get('admin_timeout_minutes') ?: config('auth.admin_timeout', 15));
    $warningBeforeSeconds = 120; // 2 menit peringatan sebelum batas waktu
    $timeoutSeconds = $timeoutMinutes * 60;
    $logoutRoute = match(auth()->user()?->primaryRole()) {
        'owner' => route('owner.logout'),
        default => route('admin.logout'),
    };
@endphp

<!-- Modal Peringatan Sesi Akan Berakhir -->
<div id="session-timeout-modal" class="fixed inset-0 z-[9999] hidden items-center justify-center bg-midnight/60 backdrop-blur-sm p-4">
    <div class="bg-beige rounded-3xl shadow-soft-lg border border-vanilla max-w-sm w-full p-6 text-center">
        <div class="w-14 h-14 rounded-2xl bg-apricot/20 text-apricot flex items-center justify-center mx-auto mb-4">
            <i data-lucide="clock" class="w-7 h-7"></i>
        </div>
        <h3 class="font-serif text-lg font-bold text-midnight mb-2">Sesi Akan Berakhir</h3>
        <p class="text-xs text-olive leading-relaxed mb-1">
            Anda tidak aktif selama beberapa waktu. Demi keamanan, sesi Anda akan otomatis berakhir.
        </p>
        <p class="text-sm font-bold text-midnight mb-5">
            Waktu tersisa: <span id="session-countdown" class="text-apricot tabular-nums">02:00</span>
        </p>
        <div class="flex gap-3">
            <button id="session-extend-btn"
                    class="flex-1 py-2.5 rounded-full bg-gradient-to-r from-apricot via-apricot to-apricot hover:brightness-105 text-midnight font-bold text-xs shadow-md transition-all">
                Lanjutkan Sesi
            </button>
            <button id="session-logout-btn"
                    class="flex-1 py-2.5 rounded-full border border-olive/30 text-olive font-semibold text-xs hover:bg-vanilla/50 transition-all">
                Keluar
            </button>
        </div>
    </div>
</div>

<form id="session-timeout-logout-form" action="{{ $logoutRoute }}" method="POST" class="hidden">
    @csrf
</form>

<script>
(function () {
    'use strict';

    const TIMEOUT_SECONDS = {{ $timeoutSeconds }};
    const WARNING_BEFORE = {{ $warningBeforeSeconds }};
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

    let idleSeconds = 0;
    let countdownInterval = null;
    let warningShown = false;

    const modal = document.getElementById('session-timeout-modal');
    const countdownEl = document.getElementById('session-countdown');
    const extendBtn = document.getElementById('session-extend-btn');
    const logoutBtn = document.getElementById('session-logout-btn');
    const logoutForm = document.getElementById('session-timeout-logout-form');

    // Pantau aktivitas pengguna
    const activityEvents = ['mousemove', 'mousedown', 'keydown', 'touchstart', 'scroll', 'click'];

    function resetIdle() {
        if (warningShown) return; // Jangan reset jika modal sudah tampil
        idleSeconds = 0;
    }

    activityEvents.forEach(evt => document.addEventListener(evt, resetIdle, { passive: true }));

    // Cek idle setiap detik
    setInterval(() => {
        idleSeconds++;

        const remaining = TIMEOUT_SECONDS - idleSeconds;

        if (remaining <= 0) {
            // Waktu habis — logout otomatis
            logoutForm.submit();
            return;
        }

        if (remaining <= WARNING_BEFORE && !warningShown) {
            showWarning(remaining);
        }

        if (warningShown && countdownEl) {
            const r = TIMEOUT_SECONDS - idleSeconds;
            if (r > 0) {
                const m = Math.floor(r / 60);
                const s = r % 60;
                countdownEl.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
            } else {
                logoutForm.submit();
            }
        }
    }, 1000);

    function showWarning(remaining) {
        warningShown = true;
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        if (typeof lucide !== 'undefined') lucide.createIcons();

        const m = Math.floor(remaining / 60);
        const s = remaining % 60;
        countdownEl.textContent = String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
    }

    function hideWarning() {
        warningShown = false;
        idleSeconds = 0;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // Tombol "Lanjutkan Sesi" — ping server untuk memperbarui last_activity_time
    extendBtn?.addEventListener('click', () => {
        fetch('{{ route("session.ping") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': CSRF_TOKEN,
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
        }).then(r => {
            if (r.ok) {
                hideWarning();
            } else {
                logoutForm.submit();
            }
        }).catch(() => {
            hideWarning(); // Tetap sembunyikan agar tidak memblokir
        });
    });

    // Tombol "Keluar"
    logoutBtn?.addEventListener('click', () => {
        logoutForm.submit();
    });
})();
</script>
