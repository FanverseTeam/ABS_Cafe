<!-- Modal Scan QR Pesanan (Kasir) -->
<div id="scan-qr-modal" class="hidden fixed inset-0 z-[80] flex items-end sm:items-center justify-center p-0 sm:p-4" aria-modal="true" role="dialog">
    <div class="fixed inset-0 bg-[#290024]/60 backdrop-blur-sm transition-opacity" onclick="closeScanModal()"></div>

    <div class="relative bg-[#F2F5E2] rounded-t-[2rem] sm:rounded-3xl shadow-soft-lg w-full max-w-lg border-t sm:border border-[#E3DEA4] z-10 max-h-[92vh] flex flex-col overflow-hidden pb-[max(1rem,env(safe-area-inset-bottom))] animate-in fade-in zoom-in-95 duration-200">
        <!-- Handle for mobile swipe/close -->
        <div class="sm:hidden w-full pt-3 pb-1 flex justify-center cursor-pointer shrink-0" onclick="closeScanModal()">
            <div class="w-12 h-1 bg-[#775533]/30 rounded-full"></div>
        </div>

        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-[#E3DEA4]/70 flex items-center justify-between sticky top-0 bg-[#F2F5E2] z-10 shrink-0">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-2xl bg-[#290024] text-cafe-gold flex items-center justify-center shadow-sm">
                    <i data-lucide="scan" class="w-5 h-5 text-[#D4954D]"></i>
                </div>
                <div>
                    <h3 class="font-serif font-bold text-base text-[#290024]">Scan QR Pesanan</h3>
                    <p class="text-[11px] text-[#775533]">Verifikasi pesanan dan ubah status ke <strong>Sedang Diproses</strong></p>
                </div>
            </div>
            <button type="button" onclick="closeScanModal()" class="w-8 h-8 rounded-full bg-[#E3DEA4]/60 hover:bg-[#E3DEA4] text-[#290024] flex items-center justify-center transition-colors">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6 space-y-5 overflow-y-auto flex-1">
            <!-- Pilihan Metode: Kamera (Utama) & Input Manual (Cadangan) -->
            <div class="flex p-1 bg-[#E3DEA4]/40 rounded-2xl border border-[#E3DEA4]/80 text-xs font-bold text-[#775533]">
                <button type="button" id="tab-btn-camera" onclick="switchScanTab('camera')" class="flex-1 py-2 rounded-xl transition-all flex items-center justify-center gap-1.5 bg-[#290024] text-[#F2F5E2] shadow-sm">
                    <i data-lucide="camera" class="w-4 h-4 text-[#D4954D]"></i>
                    <span>Kamera</span>
                </button>
                <button type="button" id="tab-btn-scanner" onclick="switchScanTab('scanner')" class="flex-1 py-2 rounded-xl transition-all flex items-center justify-center gap-1.5 hover:text-[#290024]">
                    <i data-lucide="keyboard" class="w-4 h-4"></i>
                    <span>Input Kode Manual</span>
                </button>
            </div>

            <!-- Tab 1 (Utama): Kamera HP (Foto Langsung & Live Stream) -->
            <div id="scan-tab-camera" class="space-y-4">
                {{-- Input Kamera Bawaan HP (capture="environment") --}}
                {{-- 100% Selalu bisa di HP tanpa hambatan izin HTTPS/HTTP --}}
                <input type="file"
                       id="native-camera-input"
                       accept="image/*,.heic,.heif,.HEIC,.HEIF,image/heic,image/heif"
                       capture="environment"
                       class="hidden"
                       onchange="handleNativeCameraFile(this)">

                <div class="p-4 rounded-2xl bg-white border-2 border-[#D4954D] shadow-sm space-y-2.5 text-center">
                    <div class="w-12 h-12 rounded-full bg-[#D4954D]/15 text-[#290024] flex items-center justify-center mx-auto">
                        <i data-lucide="camera" class="w-6 h-6 text-[#D4954D]"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-xs text-[#290024]">Buka Kamera Bawaan HP</h4>
                        <p class="text-[11px] text-[#775533] mt-0.5">
                            Buka kamera ponsel Anda untuk memotret QR Code pesanan pelanggan secara langsung dan instan.
                        </p>
                    </div>
                    <button type="button"
                            onclick="triggerNativeCamera()"
                            class="w-full py-3 px-4 rounded-full bg-[#D4954D] hover:bg-[#D4954D]/90 text-[#290024] font-bold text-xs flex items-center justify-center gap-2 transition-all shadow-md active:scale-95">
                        <i data-lucide="aperture" class="w-4 h-4"></i>
                        <span>Ambil Foto QR dengan Kamera HP</span>
                    </button>
                </div>

                {{-- Opsi Live Camera Stream --}}
                <div class="border-t border-[#E3DEA4] pt-3 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold text-[#290024] flex items-center gap-1.5">
                            <i data-lucide="video" class="w-3.5 h-3.5 text-[#D4954D]"></i>
                            <span>Atau Kamera Video Langsung</span>
                        </span>
                        <button type="button" onclick="startCameraScanner(true)" class="text-[10px] font-bold text-[#D4954D] hover:underline flex items-center gap-1">
                            <i data-lucide="refresh-cw" class="w-3 h-3"></i>
                            <span>Minta Ulang Izin</span>
                        </button>
                    </div>

                    <div id="insecure-context-notice" class="hidden p-3 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900 space-y-1">
                        <p class="font-bold flex items-center gap-1.5">
                            <i data-lucide="alert-triangle" class="w-4 h-4 text-amber-600 shrink-0"></i>
                            <span>Koneksi HTTP Lokal (Bukan HTTPS)</span>
                        </p>
                        <p class="text-[10px] text-amber-800 leading-snug">
                            Browser ponsel (Chrome/Safari) secara otomatis membatasi <em>video stream langsung</em> pada alamat IP lokal HTTP. Silakan gunakan tombol <strong>"Ambil Foto QR dengan Kamera HP"</strong> di atas karena bekerja 100% di semua ponsel.
                        </p>
                    </div>

                    <!-- Area Kamera Video Langsung (Kotak Persegi Proporsional untuk QR Code) -->
                    <div class="relative w-full rounded-2xl overflow-hidden bg-black/95 border-2 border-[#E3DEA4] shadow-inner">
                        <div id="qr-camera-reader" class="w-full"></div>
                        <div id="qr-camera-placeholder" class="text-white/80 p-8 text-center space-y-2 flex flex-col items-center justify-center min-h-[240px]">
                            <i data-lucide="camera" class="w-10 h-10 mx-auto text-[#D4954D]"></i>
                            <p class="text-xs">Klik "Nyalakan Kamera Langsung" di bawah untuk memulai stream video.</p>
                        </div>
                    </div>

                    <p class="text-[11px] text-[#775533] text-center">
                        Posisikan QR Code di dalam kotak target persegi agar terbaca otomatis.
                    </p>

                    <div class="flex items-center justify-between gap-2">
                        <button type="button" onclick="toggleCameraScanning()" id="btn-camera-toggle" class="flex-1 py-2 px-3 rounded-full bg-[#290024] text-[#F2F5E2] text-xs font-bold hover:bg-[#775533] transition-colors flex items-center justify-center gap-1.5">
                            <i data-lucide="video" class="w-3.5 h-3.5 text-[#D4954D]"></i>
                            <span id="btn-camera-toggle-text">Nyalakan Kamera Langsung</span>
                        </button>
                        <button type="button" onclick="switchCameraFacing()" class="py-2 px-3 rounded-full bg-[#E3DEA4] text-[#290024] text-xs font-bold hover:bg-[#D4954D] transition-colors" title="Ganti Kamera">
                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                        </button>
                    </div>
                </div>

                {{-- Tombol Beralih ke Input Manual jika kamera trouble --}}
                <div class="text-center pt-1 border-t border-[#E3DEA4]/60">
                    <button type="button" onclick="switchScanTab('scanner')" class="text-[11px] text-[#775533] hover:text-[#290024] font-semibold underline flex items-center justify-center gap-1.5 mx-auto py-1">
                        <i data-lucide="keyboard" class="w-3.5 h-3.5"></i>
                        <span>Kamera bermasalah? Beralih ke Input Kode Manual</span>
                    </button>
                </div>
            </div>

            <!-- Tab 2 (Cadangan): Input Manual / Scanner Barcode USB -->
            <div id="scan-tab-scanner" class="hidden space-y-4">
                <div class="p-3 bg-amber-50/80 border border-amber-200/80 rounded-2xl text-[11px] text-amber-900 flex items-start gap-2">
                    <i data-lucide="shield-alert" class="w-4 h-4 text-amber-600 shrink-0 mt-0.5"></i>
                    <div>
                        <span class="font-bold">Opsi Cadangan</span>
                        <p class="text-[10px] text-amber-800 mt-0.5">Gunakan form ini jika kamera ponsel mengalami kendala izin atau Anda menggunakan scanner barcode USB/Bluetooth kasir.</p>
                    </div>
                </div>

                <form id="scan-qr-form" onsubmit="handleScanSubmit(event)" class="space-y-3">
                    @csrf
                    <div>
                        <label for="scan-qr-input" class="block text-xs font-bold text-[#290024] mb-1.5">
                            Ketik Kode Pesanan (atau Scan Barcode)
                        </label>
                        <div class="relative">
                            <input type="text"
                                   id="scan-qr-input"
                                   name="code"
                                   placeholder="Contoh: #ORD-00006 atau tempel link QR"
                                   autocomplete="off"
                                   class="w-full text-sm font-mono font-bold bg-white text-[#290024] placeholder:font-sans placeholder:font-normal placeholder:text-[#775533]/50 px-4 py-3 rounded-2xl border-2 border-[#E3DEA4] focus:border-[#D4954D] focus:ring-2 focus:ring-[#D4954D]/20 outline-none transition-all shadow-inner">
                            <button type="button" onclick="clearScanInput()" class="absolute right-3 top-1/2 -translate-y-1/2 text-[#775533] hover:text-[#290024] p-1">
                                <i data-lucide="delete" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <p class="text-[11px] text-[#775533] mt-1.5 flex items-center gap-1">
                            <i data-lucide="info" class="w-3.5 h-3.5 text-[#D4954D] shrink-0"></i>
                            <span>Kompatibel dengan 2D barcode scanner USB/Bluetooth (otomatis Enter).</span>
                        </p>
                    </div>

                    <button type="submit"
                            id="scan-submit-btn"
                            class="w-full py-3 px-4 rounded-full bg-[#290024] hover:bg-[#775533] text-[#F2F5E2] font-bold text-xs flex items-center justify-center gap-2 transition-all shadow-md">
                        <i data-lucide="check-circle-2" class="w-4 h-4 text-[#D4954D]"></i>
                        <span>Verifikasi &amp; Mulai Proses</span>
                    </button>
                </form>

                <div class="text-center pt-1 border-t border-[#E3DEA4]/60">
                    <button type="button" onclick="switchScanTab('camera')" class="text-[11px] text-[#775533] hover:text-[#290024] font-semibold underline flex items-center justify-center gap-1.5 mx-auto py-1">
                        <i data-lucide="camera" class="w-3.5 h-3.5"></i>
                        <span>Kembali ke Pemindaian Kamera</span>
                    </button>
                </div>
            </div>

            <!-- Alert / Status Result -->
            <div id="scan-result-box" class="hidden p-3.5 rounded-2xl text-xs font-medium space-y-1"></div>
        </div>
    </div>
</div>

{{-- Script HTML5 QR Code library dari CDN --}}
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<style>
    #qr-camera-reader {
        width: 100% !important;
        border: none !important;
        background: transparent !important;
        position: relative;
    }
    #qr-camera-reader video {
        width: 100% !important;
        height: auto !important;
        max-height: 360px !important;
        object-fit: cover !important;
        border-radius: 0.875rem !important;
        display: block !important;
        margin: 0 auto !important;
    }
    #qr-camera-reader canvas {
        border-radius: 0.875rem !important;
    }
    #qr-camera-reader__scan_region {
        min-height: 220px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    #qr-camera-reader__dashboard,
    #qr-camera-reader__status_span {
        display: none !important;
    }
</style>

<script>
let html5QrScanner = null;
let isCameraActive = false;
let currentFacingMode = "environment";
let availableCameras = [];
let currentCameraIndex = 0;

function getCameraConfig() {
    return {
        fps: 15,
        qrbox: function(viewfinderWidth, viewfinderHeight) {
            // Selalu bujur sangkar (persegi kotak) 1:1 sempurna
            const minEdge = Math.min(viewfinderWidth, viewfinderHeight);
            const boxSize = Math.max(180, Math.floor(minEdge * 0.72));
            return {
                width: boxSize,
                height: boxSize
            };
        },
        experimentalFeatures: {
            useBarCodeDetectorIfSupported: true
        }
    };
}

function openScanModal() {
    const modal = document.getElementById('scan-qr-modal');
    if (!modal) return;
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');

    resetScanResult();

    // Default: langsung membuka tab Kamera
    switchScanTab('camera');

    // Cek apakah konteks aman (HTTPS atau localhost)
    checkSecureContext();

    lucide.createIcons();
}

function closeScanModal() {
    const modal = document.getElementById('scan-qr-modal');
    if (!modal) return;
    modal.classList.add('hidden');
    document.body.classList.remove('overflow-hidden');

    stopCameraScanner();
}

function clearScanInput() {
    const inp = document.getElementById('scan-qr-input');
    if (inp) {
        inp.value = '';
        inp.focus();
    }
    resetScanResult();
}

function switchScanTab(tab) {
    const tabScanner = document.getElementById('scan-tab-scanner');
    const tabCamera = document.getElementById('scan-tab-camera');
    const btnScanner = document.getElementById('tab-btn-scanner');
    const btnCamera = document.getElementById('tab-btn-camera');

    if (tab === 'scanner') {
        // Pindah ke Input Manual (Cadangan)
        tabScanner.classList.remove('hidden');
        tabCamera.classList.add('hidden');

        btnScanner.className = 'flex-1 py-2 rounded-xl transition-all flex items-center justify-center gap-1.5 bg-[#290024] text-[#F2F5E2] shadow-sm';
        btnCamera.className = 'flex-1 py-2 rounded-xl transition-all flex items-center justify-center gap-1.5 hover:text-[#290024]';

        stopCameraScanner();
        setTimeout(() => {
            const inp = document.getElementById('scan-qr-input');
            if (inp) {
                inp.focus();
                inp.select();
            }
        }, 100);
    } else {
        // Pindah ke Kamera (Utama)
        tabScanner.classList.add('hidden');
        tabCamera.classList.remove('hidden');

        btnCamera.className = 'flex-1 py-2 rounded-xl transition-all flex items-center justify-center gap-1.5 bg-[#290024] text-[#F2F5E2] shadow-sm';
        btnScanner.className = 'flex-1 py-2 rounded-xl transition-all flex items-center justify-center gap-1.5 hover:text-[#290024]';

        checkSecureContext();

        // Coba nyalakan live stream kamera jika didukung
        const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
        const isSecure = window.isSecureContext || window.location.protocol === 'https:' || isLocalhost;
        if (isSecure && !isCameraActive) {
            startCameraScanner(false);
        }
    }
    lucide.createIcons();
}

function checkSecureContext() {
    const notice = document.getElementById('insecure-context-notice');
    if (!notice) return;

    // Jika berjalan di HTTP dan bukan localhost/127.0.0.1 (misal via IP Wi-Fi hp)
    const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    const isSecure = window.isSecureContext || window.location.protocol === 'https:' || isLocalhost;

    if (!isSecure) {
        notice.classList.remove('hidden');
    } else {
        notice.classList.add('hidden');
    }
}

function resetScanResult() {
    const res = document.getElementById('scan-result-box');
    if (res) {
        res.className = 'hidden p-3.5 rounded-2xl text-xs font-medium space-y-1';
        res.innerHTML = '';
    }
}

function showScanResult(type, title, message) {
    const res = document.getElementById('scan-result-box');
    if (!res) return;

    res.classList.remove('hidden');
    if (type === 'success') {
        res.className = 'p-3.5 rounded-2xl text-xs font-medium space-y-1 bg-emerald-100/90 text-emerald-900 border border-emerald-300 animate-in fade-in';
        res.innerHTML = `
            <div class="flex items-center gap-2 font-bold text-emerald-900">
                <i data-lucide="check-circle" class="w-4 h-4 text-emerald-700"></i>
                <span>${title}</span>
            </div>
            <p class="text-[11px] text-emerald-800 leading-snug">${message}</p>
        `;
    } else if (type === 'loading') {
        res.className = 'p-3.5 rounded-2xl text-xs font-medium space-y-1 bg-[#E3DEA4]/50 text-[#290024] border border-[#E3DEA4] animate-pulse';
        res.innerHTML = `
            <div class="flex items-center gap-2 font-bold">
                <i data-lucide="loader-2" class="w-4 h-4 text-[#D4954D] animate-spin"></i>
                <span>${title}</span>
            </div>
            <p class="text-[11px] text-[#775533] leading-snug">${message}</p>
        `;
    } else {
        res.className = 'p-3.5 rounded-2xl text-xs font-medium space-y-1 bg-red-100/90 text-red-900 border border-red-300 animate-in fade-in';
        res.innerHTML = `
            <div class="flex items-center gap-2 font-bold text-red-900">
                <i data-lucide="alert-circle" class="w-4 h-4 text-red-700"></i>
                <span>${title}</span>
            </div>
            <p class="text-[11px] text-red-800 leading-snug">${message}</p>
        `;
    }
    lucide.createIcons();
}

function processScanCode(code) {
    if (!code || !code.trim()) {
        showScanResult('error', 'Kode Kosong', 'Silakan arahkan scanner atau masukkan kode pesanan.');
        return;
    }

    showScanResult('loading', 'Memverifikasi...', 'Mengecek pesanan dan mengupdate status ke Sedang Diproses...');

    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    fetch(`{{ route('kasir.orders.scan.submit') }}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrf,
        },
        body: JSON.stringify({ code: code.trim() })
    })
    .then(async response => {
        const data = await response.json().catch(() => null);
        if (!response.ok) {
            throw new Error((data && data.message) ? data.message : 'Pesanan tidak ditemukan atau kode tidak valid.');
        }
        return data;
    })
    .then(data => {
        if (data && data.success) {
            showScanResult('success', 'Berhasil Di-scan!', data.message);
            stopCameraScanner();

            // Beep audio feedback
            playScanBeep();

            setTimeout(() => {
                if (data.checkout_url) {
                    window.location.href = data.checkout_url;
                } else {
                    window.location.reload();
                }
            }, 800);
        } else {
            showScanResult('error', 'Gagal', (data && data.message) ? data.message : 'Terjadi kesalahan.');
        }
    })
    .catch(err => {
        showScanResult('error', 'Tidak Ditemukan', err.message || 'Pesanan tidak ditemukan.');
    });
}

function handleScanSubmit(e) {
    e.preventDefault();
    const input = document.getElementById('scan-qr-input');
    if (!input) return;
    processScanCode(input.value);
}

function playScanBeep() {
    try {
        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(880, audioCtx.currentTime);
        gain.gain.setValueAtTime(0.15, audioCtx.currentTime);
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.12);
    } catch (e) {
        // Audio fallback
    }
}

// ==========================================================
// 1. Kamera Bawaan HP via File Input (capture="environment")
// 100% Selalu bisa di HP tanpa perlu HTTPS / izin rumit!
// ==========================================================
function triggerNativeCamera() {
    const fileInput = document.getElementById('native-camera-input');
    if (fileInput) {
        fileInput.value = ''; // Reset file input
        fileInput.click();
    }
}

function handleNativeCameraFile(input) {
    if (!input.files || input.files.length === 0) return;

    const file = input.files[0];
    showScanResult('loading', 'Membaca Foto QR...', 'Sedang memproses foto dari kamera HP...');

    if (typeof Html5Qrcode === 'undefined') {
        showScanResult('error', 'Pustaka Belum Siap', 'Pustaka pemindai belum termuat sempurna.');
        return;
    }

    if (!html5QrScanner) {
        html5QrScanner = new Html5Qrcode("qr-camera-reader");
    }

    html5QrScanner.scanFile(file, true)
        .then(decodedText => {
            processScanCode(decodedText);
        })
        .catch(err => {
            showScanResult('error', 'QR Tidak Terdeteksi', 'QR Code tidak terbaca jelas pada foto. Pastikan kamera fokus ke QR Code lalu coba foto ulang.');
        });
}

// ==========================================================
// 2. Kamera Video Langsung (Stream WebRTC)
// ==========================================================
function startCameraScanner(forcePrompt = false) {
    if (typeof Html5Qrcode === 'undefined') {
        showScanResult('error', 'Kamera Tidak Tersedia', 'Pustaka kamera belum siap. Silakan gunakan tombol "Ambil Foto QR dengan Kamera HP".');
        return;
    }

    if (!html5QrScanner) {
        html5QrScanner = new Html5Qrcode("qr-camera-reader");
    }

    const config = getCameraConfig();

    const onScanSuccess = (decodedText) => {
        processScanCode(decodedText);
    };

    showScanResult('loading', 'Memulai Kamera...', 'Meminta izin akses kamera perangkat...');

    // Jika browser mendukung mediaDevices, coba minta izin dulu
    if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia && forcePrompt) {
        navigator.mediaDevices.getUserMedia({ video: { facingMode: currentFacingMode } })
            .then(stream => {
                stream.getTracks().forEach(t => t.stop());
                startCameraWithCameras(config, onScanSuccess);
            })
            .catch(err => {
                handleCameraError(err);
            });
    } else {
        startCameraWithCameras(config, onScanSuccess);
    }
}

function startCameraWithCameras(config, onScanSuccess) {
    Html5Qrcode.getCameras().then(cameras => {
        if (cameras && cameras.length) {
            availableCameras = cameras;
            // Pilih kamera belakang jika ada
            const backCamIndex = cameras.findIndex(c => /back|rear|environment|belakang/i.test(c.label));
            currentCameraIndex = backCamIndex !== -1 ? backCamIndex : (cameras.length - 1);
            const cameraId = cameras[currentCameraIndex].id;

            html5QrScanner.start(cameraId, config, onScanSuccess, () => {})
                .then(() => onCameraStarted())
                .catch(err => startCameraFallback(config, onScanSuccess));
        } else {
            startCameraFallback(config, onScanSuccess);
        }
    }).catch(err => {
        startCameraFallback(config, onScanSuccess);
    });
}

function startCameraFallback(config, onScanSuccess) {
    html5QrScanner.start({ facingMode: currentFacingMode }, config, onScanSuccess, () => {})
        .then(() => onCameraStarted())
        .catch(err => handleCameraError(err));
}

function onCameraStarted() {
    isCameraActive = true;
    resetScanResult();

    const placeholder = document.getElementById('qr-camera-placeholder');
    if (placeholder) placeholder.classList.add('hidden');

    const btnText = document.getElementById('btn-camera-toggle-text');
    if (btnText) btnText.textContent = 'Hentikan Kamera Langsung';
    lucide.createIcons();
}

function handleCameraError(err) {
    isCameraActive = false;

    const placeholder = document.getElementById('qr-camera-placeholder');
    if (placeholder) placeholder.classList.remove('hidden');

    const isLocalhost = window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1';
    const isSecure = window.isSecureContext || window.location.protocol === 'https:' || isLocalhost;

    let msg = 'Izin kamera ditolak atau tidak didukung oleh browser.';
    if (!isSecure) {
        msg = 'Browser membatasi video stream pada koneksi HTTP lokal. Silakan gunakan tombol "Ambil Foto QR dengan Kamera HP" di atas.';
    } else {
        msg = 'Pastikan Anda telah mengizinkan akses kamera pada setelan situs browser ponsel Anda, atau gunakan tombol "Ambil Foto QR dengan Kamera HP".';
    }

    showScanResult('error', 'Akses Kamera Terkendala', msg);
}

function stopCameraScanner() {
    if (html5QrScanner && isCameraActive) {
        html5QrScanner.stop().then(() => {
            isCameraActive = false;
            const placeholder = document.getElementById('qr-camera-placeholder');
            if (placeholder) placeholder.classList.remove('hidden');

            const btnText = document.getElementById('btn-camera-toggle-text');
            if (btnText) btnText.textContent = 'Nyalakan Kamera Langsung';
        }).catch(() => {
            isCameraActive = false;
        });
    }
}

function toggleCameraScanning() {
    if (isCameraActive) {
        stopCameraScanner();
    } else {
        startCameraScanner();
    }
}

function switchCameraFacing() {
    if (availableCameras && availableCameras.length > 1) {
        currentCameraIndex = (currentCameraIndex + 1) % availableCameras.length;
        const cameraId = availableCameras[currentCameraIndex].id;
        if (isCameraActive) {
            stopCameraScanner();
            setTimeout(() => {
                const config = getCameraConfig();
                html5QrScanner.start(cameraId, config, (decodedText) => processScanCode(decodedText), () => {})
                    .then(() => onCameraStarted())
                    .catch(err => handleCameraError(err));
            }, 300);
        }
    } else {
        currentFacingMode = currentFacingMode === "environment" ? "user" : "environment";
        if (isCameraActive) {
            stopCameraScanner();
            setTimeout(() => startCameraScanner(), 300);
        }
    }
}
</script>
