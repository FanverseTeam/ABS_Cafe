<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Stand QR Code Meja - ABS Cafe</title>
    @vite(['resources/css/app.css'])
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f5f2eb;
            color: #290024;
            padding: 24px;
        }

        .no-print-bar {
            max-width: 900px;
            margin: 0 auto 24px;
            background: #ffffff;
            padding: 16px 24px;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
        }

        .btn-primary {
            background-color: #290024;
            color: #ffffff;
        }

        .btn-primary:hover {
            background-color: #775533;
        }

        .btn-secondary {
            background-color: #ede8e0;
            color: #290024;
        }

        .sheet-container {
            max-width: 900px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 24px;
        }

        .qr-card {
            background: #ffffff;
            border: 2px solid #290024;
            border-radius: 24px;
            padding: 32px 24px;
            text-align: center;
            position: relative;
            box-shadow: 0 8px 30px rgba(41, 0, 36, 0.06);
            display: flex;
            flex-direction: column;
            align-items: center;
            page-break-inside: avoid;
        }

        .qr-card-header {
            margin-bottom: 16px;
        }

        .brand-title {
            font-family: 'Noto Serif', Georgia, serif;
            font-size: 22px;
            font-weight: 800;
            color: #290024;
            letter-spacing: 0.5px;
        }

        .brand-sub {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #775533;
            font-weight: 700;
            margin-top: 2px;
        }

        .table-pill {
            background: #290024;
            color: #E3DEA4;
            padding: 6px 20px;
            border-radius: 9999px;
            font-size: 15px;
            font-weight: 800;
            letter-spacing: 1px;
            margin: 12px 0 16px;
            display: inline-block;
        }

        .qr-frame {
            background: #faf8f5;
            padding: 16px;
            border-radius: 20px;
            border: 2px dashed #D4954D;
            margin-bottom: 16px;
        }

        .qr-img {
            width: 220px;
            height: 220px;
            display: block;
        }

        .scan-instruction {
            font-family: 'Noto Serif', Georgia, serif;
            font-size: 18px;
            font-weight: 700;
            color: #290024;
            margin-bottom: 12px;
        }

        .steps-box {
            background: #faf8f5;
            border-radius: 12px;
            padding: 12px 16px;
            width: 100%;
            text-align: left;
            font-size: 11px;
            color: #5C4033;
            line-height: 1.6;
        }

        .steps-box ol {
            padding-left: 16px;
        }

        .card-footer-url {
            margin-top: 14px;
            font-size: 10px;
            color: #8c827a;
            font-family: monospace;
            word-break: break-all;
        }

        @media print {
            body {
                background: #ffffff;
                padding: 0;
            }

            .no-print-bar {
                display: none !important;
            }

            .sheet-container {
                max-width: 100%;
                gap: 16px;
            }

            .qr-card {
                box-shadow: none;
                border: 2px solid #290024;
                page-break-inside: avoid;
                margin-bottom: 20px;
            }
        }
    </style>
</head>
<body>

    <!-- Bar Kontrol (Hanya tampil di layar) -->
    <div class="no-print-bar">
        <div>
            <h2 style="font-size: 16px; font-weight: 800; color: #290024;">Siap Cetak Stand Meja ({{ $tables->count() }} Meja)</h2>
            <p style="font-size: 12px; color: #775533; margin-top: 2px;">Ukuran dioptimalkan untuk dicetak di kertas A4 lalu dipotong / dipajang di stand meja.</p>
        </div>
        <div style="display: flex; gap: 8px;">
            <button onclick="window.print()" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"></polyline>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                    <rect x="6" y="14" width="12" height="8"></rect>
                </svg>
                <span>Cetak Sekarang (Print)</span>
            </button>
            <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
        </div>
    </div>

    <!-- Kontainer Kartu Meja Siap Cetak -->
    <div class="sheet-container">
        @foreach($tables as $table)
            @php
                $orderUrl = rtrim($orderBaseUrl, '/') . '?meja=' . $table->number;
                $qrApiUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=350x350&data=' . urlencode($orderUrl);
            @endphp
            <div class="qr-card">
                <div class="qr-card-header">
                    <div class="brand-title">ABS CAFE</div>
                    <div class="brand-sub">Accessories &amp; Cafe</div>
                </div>

                <div class="table-pill">
                    MEJA #{{ $table->number }}
                </div>

                <div class="qr-frame">
                    <img class="qr-img" src="{{ $qrApiUrl }}" alt="QR Code Meja #{{ $table->number }}">
                </div>

                <div class="scan-instruction">
                    Scan Untuk Memesan
                </div>

                <div class="steps-box">
                    <ol>
                        <li>Arahkan kamera ponsel Anda ke QR code di atas</li>
                        <li>Pilih makanan &amp; minuman kesukaan Anda di menu</li>
                        <li>Kirim pesanan, barista kami akan mengantar ke meja ini</li>
                    </ol>
                </div>

                <div class="card-footer-url">
                    {{ $orderUrl }}
                </div>
            </div>
        @endforeach
    </div>

</body>
</html>
